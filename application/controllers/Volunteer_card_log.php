<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Volunteer_card_log extends CI_Controller{
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
        
        $left['list'] = $this->volunteer_manage_model->get_volunteer_category_detail2();
        $this->load->view('volunteer_manage/header',$left);

    }    
    
    public function index() { 
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $name = $this->input->post('firstname');
        $category = $this->input->post('category');

        $info = array();
        if((!empty($start_date) && !empty($end_date)) || !empty($name) || !empty($category)){
            $signList = $this->volunteer_select_model->get_sign_log_list($start_date,$end_date,$name,$category);
            
            $tmp_count = count($signList);
            for($i=0;$i<$tmp_count;$i++){
                $tmp_key = $signList[$i]['idno'].$signList[$i]['sign_date'];
                if (empty($signList[$i]['idno']) || empty($signList[$i]['sign_date'])) {
                    $tmp_key = $signList[$i]['userIdNo'] . $signList[$i]['date'];
                }
                
                if(isset($info[$tmp_key])){
                    if($signList[$i]['status'] == 'Y'){
                        $signList[$i]['sign_time'] = $signList[$i]['sign_time'].'<font style="color:red">(補)</font>';
                    } 
                    if(!in_array($signList[$i]['sign_time'], $info[$tmp_key]['sign_time'])){
                        $info[$tmp_key]['sign_time'][] = $signList[$i]['sign_time'];
                    }
                    if($signList[$i]['type'] == '1'){
                        if(!isset($info[$tmp_key]['category'][1]['name'])){
                            $info[$tmp_key]['category'][1]['category_id'] = $signList[$i]['volunteerID'];
                            $info[$tmp_key]['category'][1]['name'] = '上午 '. $signList[$i]['category_name'];
                            $info[$tmp_key]['category'][1]['hours'] = $signList[$i]['hours'];
                            $info[$tmp_key]['total_hours'] += $signList[$i]['hours'];
                        }
                    } else if($signList[$i]['type'] == '2'){
                        if(!isset($info[$tmp_key]['category'][2]['name'])){
                            $info[$tmp_key]['category'][2]['category_id'] = $signList[$i]['volunteerID'];
                            $info[$tmp_key]['category'][2]['name'] = '下午 '. $signList[$i]['category_name'];
                            $info[$tmp_key]['category'][2]['hours'] = $signList[$i]['hours'];
                            $info[$tmp_key]['total_hours'] += $signList[$i]['hours'];
                        }
                    }
                } else {
                    $info[$tmp_key]['uid'] = $signList[$i]['uid'];
                    $info[$tmp_key]['name'] = $signList[$i]['name'];
                    $info[$tmp_key]['sign_date'] = $signList[$i]['sign_date'];

                    if($signList[$i]['status'] == 'Y'){
                        $info[$tmp_key]['sign_time'][] = $signList[$i]['sign_time'].'<font style="color:red">(補)</font>';
                    } else {
                        $info[$tmp_key]['sign_time'][] = $signList[$i]['sign_time'];
                    }
                    
                    if($signList[$i]['type'] == '1'){
                        $info[$tmp_key]['category'][1]['category_id'] = $signList[$i]['volunteerID'];
                        $info[$tmp_key]['category'][1]['name'] = '上午 '. $signList[$i]['category_name'];
                        $info[$tmp_key]['category'][1]['hours'] = $signList[$i]['hours'];
                    } else if($signList[$i]['type'] == '2'){
                        $info[$tmp_key]['category'][2]['category_id'] = $signList[$i]['volunteerID'];
                        $info[$tmp_key]['category'][2]['name'] = '下午 '. $signList[$i]['category_name'];
                        $info[$tmp_key]['category'][2]['hours'] = $signList[$i]['hours'];
                    }
                    
                    $info[$tmp_key]['total_hours'] = $signList[$i]['hours'];
                }
                if (! isset($info[$tmp_key]['sign_date'])){
                    $info[$tmp_key]['sign_date'] = $signList[$i]['date'];
                }
            }

        }

        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['name'] = $name;
        if(!empty($category)){
            $data['query_category'] = $category;
        } else {
            $data['query_category'] = array();
        }

        $data['info'] = array_values($info); 
        $data['category'] = $this->volunteer_manage_model->get_volunteer_category_detail2();

        $data['eda_url'] = $this->config->item('eda_url');
        $this->load->view('volunteer_manage/volunteer_card_log',$data);
        $this->load->view('volunteer_manage/footer');
    }

    function sign($id,$sign_date){
        $hour = $this->input->post('hour');
        $minute = $this->input->post('minute');
        $second = $this->input->post('second');
        $idno = $this->volunteer_select_model->getVolunteerIdno(intval($id));

        if(!empty($hour) && !empty($minute) && !empty($second) && !empty($sign_date) && !empty($idno)){
            $result = $this->volunteer_select_model->add_card_log_new($idno,$hour,$minute,$second,$sign_date);

            if($result){
                echo '<script>
                    alert("補登完成");
                    window.opener.location.reload();
                    window.close();
                </script>';
            } else {
                echo '<script>
                    alert("補登失敗，請再試一次");
                    window.close();
                </script>';
            }
        }

        $data['id'] = $id;
        $data['sign_date'] = $sign_date;
        $this->load->view('volunteer_manage/sign',$data);
    }

    public function export(){
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $name = $this->input->post('firstname');
        $category = $this->input->post('category');

        $info = array();
        if((!empty($start_date) && !empty($end_date)) || !empty($name) || !empty($category)){
            $signList = $this->volunteer_select_model->get_sign_log_list($start_date,$end_date,$name,$category);
           
            $tmp_count = count($signList);
            for($i=0;$i<$tmp_count;$i++){
                $tmp_key = $signList[$i]['idno'].$signList[$i]['sign_date'];
                if(isset($info[$tmp_key])){
                    if($signList[$i]['status'] == 'Y'){
                        $signList[$i]['sign_time'] = $signList[$i]['sign_time'].'<font style="color:red">(補)</font>';
                    } 
                    if(!in_array($signList[$i]['sign_time'], $info[$tmp_key]['sign_time'])){
                        $info[$tmp_key]['sign_time'][] = $signList[$i]['sign_time'];
                    }
                    if($signList[$i]['type'] == '1'){
                        if(!isset($info[$tmp_key]['category'][1]['name'])){
                            $info[$tmp_key]['category'][1]['category_id'] = $signList[$i]['volunteerID'];
                            $info[$tmp_key]['category'][1]['name'] = '上午 '. $signList[$i]['category_name'];
                            $info[$tmp_key]['category'][1]['hours'] = $signList[$i]['hours'];
                            $info[$tmp_key]['total_hours'] += $signList[$i]['hours'];
                        }
                    } else if($signList[$i]['type'] == '2'){
                        if(!isset($info[$tmp_key]['category'][2]['name'])){
                            $info[$tmp_key]['category'][2]['category_id'] = $signList[$i]['volunteerID'];
                            $info[$tmp_key]['category'][2]['name'] = '下午 '. $signList[$i]['category_name'];
                            $info[$tmp_key]['category'][2]['hours'] = $signList[$i]['hours'];
                            $info[$tmp_key]['total_hours'] += $signList[$i]['hours'];
                        }
                    }
                } else {
                    $info[$tmp_key]['uid'] = $signList[$i]['uid'];
                    $info[$tmp_key]['name'] = $signList[$i]['name'];
                    $info[$tmp_key]['sign_date'] = $signList[$i]['sign_date'];
                    $info[$tmp_key]['sign_time'][] = $signList[$i]['sign_time'];

                    if($signList[$i]['type'] == '1'){
                        $info[$tmp_key]['category'][1]['category_id'] = $signList[$i]['volunteerID'];
                        $info[$tmp_key]['category'][1]['name'] = '上午 '. $signList[$i]['category_name'];
                        $info[$tmp_key]['category'][1]['hours'] = $signList[$i]['hours'];
                    } else if($signList[$i]['type'] == '2'){
                        $info[$tmp_key]['category'][2]['category_id'] = $signList[$i]['volunteerID'];
                        $info[$tmp_key]['category'][2]['name'] = '下午 '. $signList[$i]['category_name'];
                        $info[$tmp_key]['category'][2]['hours'] = $signList[$i]['hours'];
                    }
                    
                    $info[$tmp_key]['total_hours'] = $signList[$i]['hours'];
                }
            }

            $category = $this->volunteer_manage_model->get_volunteer_category_detail2();
            $categoryList = array();
            $categoryList[1]['name'] = '班務';
            $categoryList[1]['total_hours'] = 0;
            for($i=0;$i<count($category);$i++) { 
              $categoryList[$category[$i]->id]['name'] = $category[$i]->name;
              $categoryList[$category[$i]->id]['total_hours'] = 0;
            }

            $info = array_values($info);
            $this->load->library('Download_xlsx');
            $this->download_xlsx->sign_log_report($info,$categoryList);

        }
    }
}
