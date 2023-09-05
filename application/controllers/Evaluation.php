<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Evaluation extends CI_Controller{
    //put your code here
    public function __construct()
    {
        parent::__construct();
        $this->load->database('phy');

        $this->load->model('volunteer_select_model');
        $this->load->model('volunteer_manage_model');

        session_start();
        $_SESSION['userID'] = isset($_SESSION['userID'])?$_SESSION['userID']:-1;

        if($_SESSION['userID'] == '-1' || $_SESSION['role_id'] != '19'){
            die('您無此權限');
        }

        $userID = $_SESSION['userID'];
        $user = $this->db->where('id',$userID)
                         ->get('users')
                         ->row();
        $this->user = $user;
        
        $left['list'] = $this->volunteer_manage_model->get_volunteer_category_detail2();
        $this->load->view('volunteer_manage/header',$left);

    }    
    
    public function index() { 
        $year = intval($this->input->post('year'));
        $helf = intval($this->input->post('helf'));
        $name = addslashes(trim($this->input->post('firstname')));
        $category = $this->input->post('category');
        $batch = addslashes($this->input->post('batch'));

        $info = array();
        $data['helf_name'] = '';
        if((!empty($year) && !empty($helf)) || !empty($name) || !empty($category)){
            if($batch == 'batch'){
                $this->volunteer_select_model->batchUndertakerStatus($year, $helf, $category);
                $this->volunteer_select_model->batchUndertakerStatusSpecial($year, $helf, $category);
            }

            if($helf == '1'){
                $start_date = ($year+1911).'-01-01';
                $end_date = ($year+1911).'-06-30'; 
                $data['helf_name'] = '上半年';
            } else if($helf == '2'){
                $start_date = ($year+1911).'-07-01';
                $end_date = ($year+1911).'-12-31'; 
                $data['helf_name'] = '下半年';
            } 
            $filterStatus = $this->input->post('status');

            if ($filterStatus == 'all' ) {
                $info = $this->volunteer_select_model->getUserApplyVolunteerCategory($start_date, $end_date, $category, $name, $year, $helf);
            } else {
                $this->load->model('Evaluation_model');
                if(!empty($name) ) {
                    $userId = $this->Evaluation_model->getUserID($name);
                    $info = $this->Evaluation_model->getEval($year, $helf, $category, $userId);
                } else {
                    $info = $this->Evaluation_model->getEval($year, $helf, $category);
                }
            }
        }

        $data['query_year'] = $year;
        $data['query_helf'] = $helf;
        $data['query_name'] = $name;
        if(!empty($category)){
            $data['query_category'] = $category;
        } else {
            $data['query_category'] = array();
        }

        $data['info'] = $info; 
        $data['status'] = isset($filterStatus) ? $filterStatus : 'all';

        $data['leader'] = !empty($this->user->evaluation_category_leader)?explode('|',$this->user->evaluation_category_leader):array();
        $data['category'] = $this->volunteer_manage_model->get_volunteer_category_detail2();
        $data['setup_url'] = base_url('/evaluation/setup');
        $data['download_url'] = base_url('/evaluation/download');
        $data['downloadAllYear_url'] = base_url('/evaluation/downloadAllYear');
        $data['downloadAll_url'] = base_url('/evaluation/downloadAll');

        $this->load->view('volunteer_manage/evaluation',$data);
        $this->load->view('volunteer_manage/footer');
    }

    public function setup(){
        $data = array();

        $data['set_list']  = $this->db->order_by('id','desc')->get('evaluation_setup')->result(); 

        $this->load->view('volunteer_manage/evaluation_setup',$data);
        $this->load->view('volunteer_manage/footer');
    }

    public function ajax_insert_evaluation()
    {
        $insert_data = array(
            'year'      => intval($this->input->post('year')),
            'helf'      => intval($this->input->post('helf')),
            'category'      => intval($this->input->post('category')),
            'start_time'     => addslashes($this->input->post('startTime')).' 00:00:00',
            'end_time'       => addslashes($this->input->post('endTime')).' 23:59:59',
            'create_time' => date('Y-m-d H:i:s')
        );
        
        if($this->db->insert('evaluation_setup',$insert_data)){
            $result = array(
                'code'          => '100' ,
            ) ;
        } else {
            $result = array(
                'code'          => '0' ,
            ) ;
        }
       
        echo json_encode($result) ;
        exit ;
    }

    public function ajax_delete_evaluation()
    {
        if($this->db->where('id',intval($this->input->post('id')))->delete('evaluation_setup')){
            $result = array(
                'code'          => '100' ,
            ) ;
        } else {
            $result = array(
                'code'          => '0' ,
            ) ;
        }
        
        echo json_encode($result) ;
        exit ;
    }

    public function save()
    {
        $year = intval($this->input->post('year'));
        $helf = intval($this->input->post('helf'));
        $category = intval($this->input->post('category'));
        $uid = intval($this->input->post('uid'));
        $grade = intval($this->input->post('grade'));
        $type = intval($this->input->post('type'));

        if(!empty($year) && !empty($helf) && !empty($category) && !empty($uid) && !empty($grade) && !empty($type)){
            $check = $this->volunteer_select_model->check_self_evaluation($year, $helf, $category, $uid);

            if(!$check){
                $status = $this->volunteer_select_model->insert_self_evaluation($type, $year, $helf, $category, $grade, $uid);
            } else {
                $status = $this->volunteer_select_model->update_self_evaluation($type, $year, $helf, $category, $grade, $uid);
            }

            if($status){
                if($type == 1 || $type == 2 || $type == 3 || $type == 4){
                    $info = $this->volunteer_select_model->get_self_evaluation($year, $helf, $category, $uid);

                    if(!empty($info)){
                        $self_grade = 0;
                        if(!empty($info[0]['top_grade']) && !empty($info[0]['bottom_grade'])){
                            $self_grade = ($info[0]['top_grade']+$info[0]['bottom_grade'])*0.2;
                            $self_grade = $self_grade;
                        }

                        $undertaker_grade = 0;
                        if(!empty($info[0]['undertaker_top_grade']) && !empty($info[0]['undertaker_bottom_grade'])){
                            $undertaker_grade = ($info[0]['undertaker_top_grade']+$info[0]['undertaker_bottom_grade'])*0.4;
                            $undertaker_grade = $undertaker_grade;
                        }

                        $leader_grade = 0;
                        if(!empty($info[0]['leader_top_grade']) && !empty($info[0]['leader_bottom_grade'])){
                            $leader_grade = ($info[0]['leader_top_grade']+$info[0]['leader_bottom_grade'])*0.4;
                            $leader_grade = $leader_grade;
                        }

                        $total_grade = 0;
                        if(isset($self_grade) && $self_grade > 0){
                            $total_grade += $self_grade;
                        }

                        if(isset($undertaker_grade) && $undertaker_grade > 0){
                            $total_grade += $undertaker_grade;
                        }

                        if(isset($leader_grade) && $leader_grade > 0){
                            $total_grade += $leader_grade;
                        }

                        $total_grade = round($total_grade);
                        if($total_grade == 0){
                            $rank = '';
                        } else if($total_grade >= 90){
                            $rank = '特優';
                        } else if($total_grade >= 80 && $total_grade < 90){
                            $rank = '優等';
                        }  else if($total_grade >= 70 && $total_grade < 80){
                            $rank = '適任';
                        }  else if($total_grade >= 60 && $total_grade < 70){
                            $rank = '待觀察';
                        } else if($total_grade < 60){
                            $rank = '不適任';
                        } 

                        if(!empty($info[0]['top_grade']) && !empty($info[0]['bottom_grade']) && !empty($info[0]['undertaker_top_grade']) && !empty($info[0]['undertaker_bottom_grade']) && !empty($info[0]['leader_top_grade']) && !empty($info[0]['leader_bottom_grade'])){
                            if($total_grade >= 60){
                                $this->volunteer_select_model->evaluationAgainSave($year, $helf, $category, $uid, '1');
                                $again = '同意再任';
                            } else {
                                $this->volunteer_select_model->evaluationAgainSave($year, $helf, $category, $uid, '2');
                                $again = '不同意再任';
                            }
                        }

                        if(isset($again) && !empty($again)){
                            $result = array(
                                'code'          => $total_grade ,
                                'rank'          => $rank,
                                'again'         => $again,
                                'seid'          => $info[0]['id']
                            ) ;
                        } else {
                            $result = array(
                                'code'          => $total_grade ,
                                'rank'          => $rank
                            ) ;
                        }
                    } else {
                        $result = array(
                            'code'          => '100' ,
                            'rank'          => ''
                        ) ;
                    }
                } else {
                    $result = array(
                        'code'          => '100' ,
                    ) ;
                }
               
                echo json_encode($result) ;
                exit ;
            }
        }

        $result = array(
            'code'          => '0' ,
        ) ;

        echo json_encode($result) ;
        exit ;
    }

    public function againSave()
    {
        $type = intval($this->input->post('id'));
        $list = $this->input->post('list');
        
        if(!empty($type) && !empty($list)){
            for($i=0;$i<count($list);$i++){
                $list[$i] = intval($list[$i]);
            }

            $status = $this->volunteer_select_model->againSave($type, $list);

            if($status){
                $result = array(
                    'code'          => '100' ,
                ) ;

                echo json_encode($result) ;
                exit ;
            }
        }

        $result = array(
            'code'          => '0' ,
        ) ;

        echo json_encode($result) ;
        exit ;

    }

    public function download(){
        require_once('resource/phpword/bootstrap.php');

        $seid = $this->input->post('seid');

        $detail = $this->volunteer_select_model->getEvaluationDetail($seid);

        if(!empty($detail)){
            if($detail[0]['category'] == 1){
                $file = '/www/html/eda/manage/resource/template/evaluation1.docx';
            } else if($detail[0]['category'] == 2){
                $file = '/www/html/eda/manage/resource/template/evaluation2.docx';
            } else if($detail[0]['category'] == 3){
                $file = '/www/html/eda/manage/resource/template/evaluation3.docx';
            } else if($detail[0]['category'] == 4){
                $file = '/www/html/eda/manage/resource/template/evaluation4.docx';
            } else {
                die();
            }
         
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($file);
            
            $templateProcessor->setValue('category_name1', $detail[0]['category_name']);
            $templateProcessor->setValue('category_name2', $detail[0]['category_name']);
            $templateProcessor->setValue('category_name3', $detail[0]['category_name']);
            
            if($detail[0]['helf'] == '1'){
                $date_name = $detail[0]['year'].'年1月1日至'.$detail[0]['year'].'年6月30日';
            } else if($detail[0]['helf'] == '2'){
                $date_name = $detail[0]['year'].'年7月1日至'.$detail[0]['year'].'年12月31日';
            }

            $templateProcessor->setValue('date_name',  $date_name);
            $templateProcessor->setValue('user_name', $detail[0]['user_name']);

            if($detail[0]['helf'] == '1'){
                $start_date = ($detail[0]['year']+1911).'-01-01';
                $end_date = ($detail[0]['year']+1911).'-06-30'; 
            } else if($detail[0]['helf'] == '2'){
                $start_date = ($detail[0]['year']+1911).'-07-01';
                $end_date = ($detail[0]['year']+1911).'-12-31'; 
               
            }
            $total_hours = $this->volunteer_select_model->getTotalHours($detail[0]['uid'], $start_date, $end_date, $detail[0]['category']);
            $templateProcessor->setValue('hours', $total_hours);

            $templateProcessor->setValue('self_top_grade', $detail[0]['top_grade']);
            $templateProcessor->setValue('self_bottom_grade', $detail[0]['bottom_grade']);
            $templateProcessor->setValue('undertaker_top_grade', $detail[0]['undertaker_top_grade']);
            $templateProcessor->setValue('undertaker_bottom_grade', $detail[0]['undertaker_bottom_grade']);
            $templateProcessor->setValue('leader_top_grade', $detail[0]['leader_top_grade']);
            $templateProcessor->setValue('leader_bottom_grade', $detail[0]['leader_bottom_grade']);
    
            $self_grade = '';
            $final_self_grade = '';
            if(!empty($detail[0]['top_grade']) && !empty($detail[0]['bottom_grade'])){
                $self_grade = ($detail[0]['top_grade']+$detail[0]['bottom_grade']);
                $final_self_grade = $self_grade*0.2;
            }
            $templateProcessor->setValue('total_self_grade', $self_grade);
            $templateProcessor->setValue('final_self_grade', $final_self_grade);

            $undertaker_grade = '';
            $final_undertaker_grade = '';
            if(!empty($detail[0]['undertaker_top_grade']) && !empty($detail[0]['undertaker_bottom_grade'])){
                $undertaker_grade = ($detail[0]['undertaker_top_grade']+$detail[0]['undertaker_bottom_grade']);
                $final_undertaker_grade = $undertaker_grade*0.4;
            }
            $templateProcessor->setValue('total_undertaker_grade', $undertaker_grade);
            $templateProcessor->setValue('final_undertaker_grade', $final_undertaker_grade);

            $leader_grade = '';
            $final_leader_grade = '';
            if(!empty($detail[0]['leader_top_grade']) && !empty($detail[0]['leader_bottom_grade'])){
                $leader_grade = ($detail[0]['leader_top_grade']+$detail[0]['leader_bottom_grade']);
                $final_leader_grade = $leader_grade*0.4;
            }
            $templateProcessor->setValue('total_leader_grade', $leader_grade);
            $templateProcessor->setValue('final_leader_grade', $final_leader_grade);

            $total_grade = 0;
            if(isset($final_self_grade) && intval($final_self_grade) > 0){
                $total_grade += $final_self_grade;
            }

            if(isset($final_undertaker_grade) && intval($final_undertaker_grade) > 0){
                $total_grade += $final_undertaker_grade;
            }

            if(isset($final_leader_grade) && intval($final_leader_grade) > 0){
                $total_grade += $final_leader_grade;
            }

            $total_grade = round($total_grade);
            $templateProcessor->setValue('final_grade', $total_grade);
            
            if($total_grade == 0){
                $rank = '';
            } else if($total_grade >= 90){
                $rank = '特優';
            } else if($total_grade >= 80 && $total_grade < 90){
                $rank = '優等';
            }  else if($total_grade >= 70 && $total_grade < 80){
                $rank = '適任';
            }  else if($total_grade >= 60 && $total_grade < 70){
                $rank = '待觀察';
            } else if($total_grade < 60){
                $rank = '不適任';
            } 

            $templateProcessor->setValue('rank', $rank);

            if($detail[0]['again'] == 1){
                $templateProcessor->setValue('againY', '☑');
                $templateProcessor->setValue('againN', '☐');
            } else if($detail[0]['again'] == 2){
                $templateProcessor->setValue('againY', '☐');
                $templateProcessor->setValue('againN', '☑');
            } else {
                $templateProcessor->setValue('againY', '☐');
                $templateProcessor->setValue('againN', '☐');
            }

            $templateProcessor->setImageValue('signature', ['path' => $detail[0]['signature'], 'width' => 100]);

            $save_path = "/www/html/eda/manage/resource/evaluation/".$detail[0]['uid']."/";
            $helf_name = ($detail[0]['helf']==1)?'上半年':'下半年';
            $file_name = $detail[0]['year'].'年'.$helf_name.$detail[0]['category_name'].'-志工考核表.docx';
            $this->TMkdir($save_path,0777);

            $templateProcessor->saveAs($save_path.$file_name);  

            $this->load->helper('file');
            $file_helper = new file_helper();
            $file_helper->download($save_path.$file_name);
        }

        exit;
    }

    public function downloadAllYear(){
        require_once('resource/phpword/bootstrap.php');

        $seid = $this->input->post('seidAllYear');

        $info = $this->volunteer_select_model->getEvaluation($seid);

        if(!empty($info)){
            $detail = $this->volunteer_select_model->getEvaluationAllYearDetail($info);
        }
        
        if(!empty($detail)){
            for($i=0;$i<count($detail);$i++){
                if($detail[$i]['category'] == 1){
                    $file = '/www/html/eda/manage/resource/template/evaluation1.docx';
                } else if($detail[$i]['category'] == 2){
                    $file = '/www/html/eda/manage/resource/template/evaluation2.docx';
                } else if($detail[$i]['category'] == 3){
                    $file = '/www/html/eda/manage/resource/template/evaluation3.docx';
                } else if($detail[$i]['category'] == 4){
                    $file = '/www/html/eda/manage/resource/template/evaluation4.docx';
                } else {
                    die();
                }
               
                $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($file);
                
                $templateProcessor->setValue('category_name1', $detail[$i]['category_name']);
                $templateProcessor->setValue('category_name2', $detail[$i]['category_name']);
                $templateProcessor->setValue('category_name3', $detail[$i]['category_name']);
                
                if($detail[$i]['helf'] == '1'){
                    $date_name = $detail[$i]['year'].'年1月1日至'.$detail[$i]['year'].'年6月30日';
                } else if($detail[$i]['helf'] == '2'){
                    $date_name = $detail[$i]['year'].'年7月1日至'.$detail[$i]['year'].'年12月31日';
                }

                $templateProcessor->setValue('date_name',  $date_name);
                $templateProcessor->setValue('user_name', $detail[$i]['user_name']);

                if($detail[$i]['helf'] == '1'){
                    $start_date = ($detail[$i]['year']+1911).'-01-01';
                    $end_date = ($detail[$i]['year']+1911).'-06-30'; 
                } else if($detail[$i]['helf'] == '2'){
                    $start_date = ($detail[$i]['year']+1911).'-07-01';
                    $end_date = ($detail[$i]['year']+1911).'-12-31'; 
                   
                }
                $total_hours = $this->volunteer_select_model->getTotalHours($detail[$i]['uid'], $start_date, $end_date, $detail[$i]['category']);

                $templateProcessor->setValue('hours', $total_hours);
                $templateProcessor->setValue('self_top_grade', $detail[$i]['top_grade']);
                $templateProcessor->setValue('self_bottom_grade', $detail[$i]['bottom_grade']);
                $templateProcessor->setValue('undertaker_top_grade', $detail[$i]['undertaker_top_grade']);
                $templateProcessor->setValue('undertaker_bottom_grade', $detail[$i]['undertaker_bottom_grade']);
                $templateProcessor->setValue('leader_top_grade', $detail[$i]['leader_top_grade']);
                $templateProcessor->setValue('leader_bottom_grade', $detail[$i]['leader_bottom_grade']);
        
                $self_grade = '';
                $final_self_grade = '';
                if(!empty($detail[$i]['top_grade']) && !empty($detail[$i]['bottom_grade'])){
                    $self_grade = ($detail[$i]['top_grade']+$detail[$i]['bottom_grade']);
                    $final_self_grade = $self_grade*0.2;
                }
                $templateProcessor->setValue('total_self_grade', $self_grade);
                $templateProcessor->setValue('final_self_grade', $final_self_grade);

                $undertaker_grade = '';
                $final_undertaker_grade = '';
                if(!empty($detail[$i]['undertaker_top_grade']) && !empty($detail[$i]['undertaker_bottom_grade'])){
                    $undertaker_grade = ($detail[$i]['undertaker_top_grade']+$detail[$i]['undertaker_bottom_grade']);
                    $final_undertaker_grade = $undertaker_grade*0.4;
                }
                $templateProcessor->setValue('total_undertaker_grade', $undertaker_grade);
                $templateProcessor->setValue('final_undertaker_grade', $final_undertaker_grade);

                $leader_grade = '';
                $final_leader_grade = '';
                if(!empty($detail[$i]['leader_top_grade']) && !empty($detail[$i]['leader_bottom_grade'])){
                    $leader_grade = ($detail[$i]['leader_top_grade']+$detail[$i]['leader_bottom_grade']);
                    $final_leader_grade = $leader_grade*0.4;
                }
                $templateProcessor->setValue('total_leader_grade', $leader_grade);
                $templateProcessor->setValue('final_leader_grade', $final_leader_grade);

                $total_grade = 0;
                if(isset($final_self_grade) && intval($final_self_grade) > 0){
                    $total_grade += $final_self_grade;
                }

                if(isset($final_undertaker_grade) && intval($final_undertaker_grade) > 0){
                    $total_grade += $final_undertaker_grade;
                }

                if(isset($final_leader_grade) && intval($final_leader_grade) > 0){
                    $total_grade += $final_leader_grade;
                }

                $total_grade = round($total_grade);
                $templateProcessor->setValue('final_grade', $total_grade);
                
                if($total_grade == 0){
                    $rank = '';
                } else if($total_grade >= 90){
                    $rank = '特優';
                } else if($total_grade >= 80 && $total_grade < 90){
                    $rank = '優等';
                }  else if($total_grade >= 70 && $total_grade < 80){
                    $rank = '適任';
                }  else if($total_grade >= 60 && $total_grade < 70){
                    $rank = '待觀察';
                } else if($total_grade < 60){
                    $rank = '不適任';
                } 

                $templateProcessor->setValue('rank', $rank);

                if($detail[$i]['again'] == 1){
                    $templateProcessor->setValue('againY', '☑');
                    $templateProcessor->setValue('againN', '☐');
                } else if($detail[$i]['again'] == 2){
                    $templateProcessor->setValue('againY', '☐');
                    $templateProcessor->setValue('againN', '☑');
                } else {
                    $templateProcessor->setValue('againY', '☐');
                    $templateProcessor->setValue('againN', '☐');
                }

                $templateProcessor->setImageValue('signature', ['path' => $detail[$i]['signature'], 'width' => 100]);

                $save_path = "/www/html/eda/manage/resource/evaluation/".$detail[$i]['uid']."/";
                $helf_name = ($detail[$i]['helf']==1)?'上半年':'下半年';
                $file_name = $detail[$i]['year'].'年'.$helf_name.$detail[$i]['category_name'].'-志工考核表.docx';
                $file_list[] = $file_name;
                $this->TMkdir($save_path,0777);

                $templateProcessor->saveAs($save_path.$file_name);  
            }

            if(!empty($file_list)){
                $this->load->helper('file');
                $file_helper = new file_helper();
                $zip_name = date("YmdHis").".zip";
                $zip_result = $file_helper->zipFile($zip_name, $file_list, '', $save_path);
                
                if ($zip_result["status"] == true){
                    $file_helper->download($zip_result["output"], true);

                    if(file_exists($zip_result["output"])){
                        unlink($zip_result["output"]);
                    }
                }
            }
        }

        exit;
    }

    public function downloadAll(){
        require_once('resource/phpword/bootstrap.php');

        $year = intval($this->input->post('AllYear'));
        $helf = intval($this->input->post('AllHelf'));
        $name = addslashes(trim($this->input->post('AllFirstname')));
        $category = $this->input->post('category');

        $detail = array();
        if((!empty($year) && !empty($helf)) || !empty($name) || !empty($category)){
            if($helf == '1'){
                $start_date = ($year+1911).'-01-01';
                $end_date = ($year+1911).'-06-30'; 
                $date_name = $year.'年1月1日至'.$detail[$i]['year'].'年6月30日';
            } else if($helf == '2'){
                $start_date = ($year+1911).'-07-01';
                $end_date = ($year+1911).'-12-31'; 
                $date_name = $year.'年7月1日至'.$detail[$i]['year'].'年12月31日';
            } 

            $detail = $this->volunteer_select_model->getUserApplyVolunteerCategory($start_date, $end_date, $category, $name, $year, $helf);
        }
        
        if(!empty($detail)){
            $save_path = "/www/html/eda/manage/resource/evaluation/".date('YmdHis')."/";
            $this->TMkdir($save_path,0777);

            for($i=0;$i<count($detail);$i++){
                if(!empty($detail[$i]['seid'])){
                    if($detail[$i]['category_id'] == 1){
                        $file = '/www/html/eda/manage/resource/template/evaluation1.docx';
                    } else if($detail[$i]['category_id'] == 2){
                        $file = '/www/html/eda/manage/resource/template/evaluation2.docx';
                    } else if($detail[$i]['category_id'] == 3){
                        $file = '/www/html/eda/manage/resource/template/evaluation3.docx';
                    } else if($detail[$i]['category_id'] == 4){
                        $file = '/www/html/eda/manage/resource/template/evaluation4.docx';
                    } else {
                        die();
                    }
                    
                    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($file);
                    
                    $templateProcessor->setValue('category_name1', $detail[$i]['category_name']);
                    $templateProcessor->setValue('category_name2', $detail[$i]['category_name']);
                    $templateProcessor->setValue('category_name3', $detail[$i]['category_name']);

                    $templateProcessor->setValue('date_name',  $date_name);
                    $templateProcessor->setValue('user_name', $detail[$i]['user_name']);

                    $total_hours = $this->volunteer_select_model->getTotalHours($detail[$i]['uid'], $start_date, $end_date, $detail[$i]['category_id']);

                    $templateProcessor->setValue('hours', $total_hours);
                    $templateProcessor->setValue('self_top_grade', $detail[$i]['top_grade']);
                    $templateProcessor->setValue('self_bottom_grade', $detail[$i]['bottom_grade']);
                    $templateProcessor->setValue('undertaker_top_grade', $detail[$i]['undertaker_top_grade']);
                    $templateProcessor->setValue('undertaker_bottom_grade', $detail[$i]['undertaker_bottom_grade']);
                    $templateProcessor->setValue('leader_top_grade', $detail[$i]['leader_top_grade']);
                    $templateProcessor->setValue('leader_bottom_grade', $detail[$i]['leader_bottom_grade']);
            
                    $self_grade = '';
                    $final_self_grade = '';
                    if(!empty($detail[$i]['top_grade']) && !empty($detail[$i]['bottom_grade'])){
                        $self_grade = ($detail[$i]['top_grade']+$detail[$i]['bottom_grade']);
                        $final_self_grade = $self_grade*0.2;
                    }
                    $templateProcessor->setValue('total_self_grade', $self_grade);
                    $templateProcessor->setValue('final_self_grade', $final_self_grade);

                    $undertaker_grade = '';
                    $final_undertaker_grade = '';
                    if(!empty($detail[$i]['undertaker_top_grade']) && !empty($detail[$i]['undertaker_bottom_grade'])){
                        $undertaker_grade = ($detail[$i]['undertaker_top_grade']+$detail[$i]['undertaker_bottom_grade']);
                        $final_undertaker_grade = $undertaker_grade*0.4;
                    }
                    $templateProcessor->setValue('total_undertaker_grade', $undertaker_grade);
                    $templateProcessor->setValue('final_undertaker_grade', $final_undertaker_grade);

                    $leader_grade = '';
                    $final_leader_grade = '';
                    if(!empty($detail[$i]['leader_top_grade']) && !empty($detail[$i]['leader_bottom_grade'])){
                        $leader_grade = ($detail[$i]['leader_top_grade']+$detail[$i]['leader_bottom_grade']);
                        $final_leader_grade = $leader_grade*0.4;
                    }
                    $templateProcessor->setValue('total_leader_grade', $leader_grade);
                    $templateProcessor->setValue('final_leader_grade', $final_leader_grade);

                    $total_grade = 0;
                    if(isset($final_self_grade) && intval($final_self_grade) > 0){
                        $total_grade += $final_self_grade;
                    }

                    if(isset($final_undertaker_grade) && intval($final_undertaker_grade) > 0){
                        $total_grade += $final_undertaker_grade;
                    }

                    if(isset($final_leader_grade) && intval($final_leader_grade) > 0){
                        $total_grade += $final_leader_grade;
                    }

                    $total_grade = round($total_grade);
                    $templateProcessor->setValue('final_grade', $total_grade);
                    
                    if($total_grade == 0){
                        $rank = '';
                    } else if($total_grade >= 90){
                        $rank = '特優';
                    } else if($total_grade >= 80 && $total_grade < 90){
                        $rank = '優等';
                    }  else if($total_grade >= 70 && $total_grade < 80){
                        $rank = '適任';
                    }  else if($total_grade >= 60 && $total_grade < 70){
                        $rank = '待觀察';
                    } else if($total_grade < 60){
                        $rank = '不適任';
                    } 

                    $templateProcessor->setValue('rank', $rank);

                    if($detail[$i]['again'] == 1){
                        $templateProcessor->setValue('againY', '☑');
                        $templateProcessor->setValue('againN', '☐');
                    } else if($detail[$i]['again'] == 2){
                        $templateProcessor->setValue('againY', '☐');
                        $templateProcessor->setValue('againN', '☑');
                    } else {
                        $templateProcessor->setValue('againY', '☐');
                        $templateProcessor->setValue('againN', '☐');
                    }

                    $templateProcessor->setImageValue('signature', ['path' => $detail[$i]['signature'], 'width' => 100]);

                    $helf_name = ($helf==1)?'上半年':'下半年';
                    $file_name = $year.'年'.$helf_name.$detail[$i]['category_name'].'_'.$detail[$i]['user_name'].'-志工考核表.docx';
                    $file_list[] = $file_name;
                
                    $templateProcessor->saveAs($save_path.$file_name);  
                }
            }

            if(!empty($file_list)){
                $this->load->helper('file');
                $file_helper = new file_helper();
                $zip_name = date("YmdHis").".zip";
                $zip_result = $file_helper->zipFile($zip_name, $file_list, '', $save_path);
                
                if ($zip_result["status"] == true){
                    $file_helper->download($zip_result["output"], true);

                    if(file_exists($zip_result["output"])){
                        unlink($zip_result["output"]);
                    }
                }
            }
        }

        exit;
    }

    private function TMkdir($pathname,$mode)
    {
        if (is_dir($pathname))
            return false;
    
        do
        {
            if (!($tFlag=@mkdir($pathname,$mode)))
            {
                $path_d=substr($pathname,0,strrpos($pathname,"/"));
                $old = umask(0);
                TMkdir($path_d,$mode);
                umask($old);
            }
        }
        while (!$tFlag);
    
        return true;
    }
}