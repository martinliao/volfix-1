<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Banned extends CI_Controller{
    //put your code here
    public function __construct()
    {
        parent::__construct();
        $this->load->database('phy');
        $this->load->model('volunteer_manage_model');


        $left['list'] = $this->volunteer_manage_model->get_volunteer_category_detail();
        $this->load->view('banuser/header',$left);
        session_start();
        $_SESSION['userID'] = isset($_SESSION['userID'])?$_SESSION['userID']:-1;
        $_SESSION['role_id'] = isset($_SESSION['role_id'])?$_SESSION['role_id']:null;

        // 測試機DEBUG用
        //if($_SERVER['HTTP_HOST'] == '172.16.10.13')
        if( strcmp(ENVIRONMENT, 'production') != 0 )
        {
            // $_SESSION['userID'] = 1;
            // $_SESSION['role_id'] = 36;
            // demo
            $_SESSION['userID'] = 8;    
            $_SESSION['role_id'] = 19;
            $_SESSION['userID'] = $this->config->item('eda_manage_testrun_id'); // e.g. 90
        }
        

        if($_SESSION['userID'] == '-1' || $_SESSION['role_id'] != '19'){
            die('您無此權限');
        }

        $userID = $_SESSION['userID'];
        $user = $this->db->where('id',$userID)
                         ->get('users')
                         ->row();
        $this->user = $user;
    }
    
    /**
     * 停權設定
     */
    public function ban_users()
    {
        $banMonths = 3;
        $banHours = 48;
        $banGate = 2;
        $banStrat = date('Y-m-d', strtotime('first day of +1 month'));
        $banEnd = date('Y-m-d', strtotime('last day of next month'));

        $data = array();
        $_today = new DateTime();
        $_today->modify("last day of this month");
        $_startMonthStr = "-".$banMonths." Months";
        //$data['defaultMonth'] = $_today->format("m");
        $data['defaultMonth'] = date( "m" , strtotime ( $_startMonthStr ));
        $data['page_name'] = 'index';
        $data['link_save'] = base_url('banned/ban_users');
        //$_post = $this->input->post();
        $_post = array_map("htmlspecialchars", $this->input->post());
        $cat1 = $cat2 = $cat3 = $cat4 = $cat5 = null;

        $data['list'] = array();
        if ( ! empty($_post['month_start'])) {
            $data['defaultMonth'] = $_post['month_start'];
            $banGates= array(
                '1' => $_post['category1'],
                '2' => $_post['category2'],
                '3' => $_post['category3'],
                '4' => $_post['category4'],
                '7' => $_post['category7']
            );
            $cat1 = $_post['category1'];
            $cat2 = $_post['category2'];
            $cat3 = $_post['category3'];
            $cat4 = $_post['category4'];
            $cat7 = $_post['category7'];
            $this->load->model('volunteer_select_model');
            $categoryMaps = $this->volunteer_select_model->getCategoryMaps();
            $year = '112';
            $month = $_post['month_start'];
            $query_start = ($year+1911).'-'.$month.'-01';
            $query_start = date('Y-m-d', strtotime($query_start));
            $data['query_start'] = $query_start;
            //$query_end =  date('Y-m-t', strtotime($query_start)); // get the last date for the month.
            $query_end = $_today->format("Y-m-d");
            $data['query_end'] = $query_end;
            $query_end = date('Y-m-d', strtotime($query_end.'+1 days'));
            $dataList = array();
            $logRows = $this->volunteer_select_model->getLog4BanUser($query_start, $query_end);
            // Add User idNo
            foreach($logRows as $log) {
                //if ($log['cancels'] >= $banGate ) { // Over the limit
                //}
                if (!empty($log['firstname'])) {
                    $cateId = array_search($log['category'], $categoryMaps);
                    $log['ban_gate'] = isset($banGates[$cateId]) ? $banGates[$cateId] : '';
                    if($u = $this->volunteer_select_model->getUserByName($log['firstname'])) {
                        $_newRow = array_merge($log, array('idNo' => $u->idNo));
                        if ($bans = $this->volunteer_select_model->getBansByUserID($u->idNo)) {
                            foreach($bans as $ban) {
                                if ($ban->name == $log['category']) {
                                    $_newRow = array_merge($_newRow, array('ban_start' => $ban->start_date, 'ban_end' => $ban->end_date, 'category_id' => $ban->category_id, 'category_name' => $ban->name));
                                }
                            }
                        }
                        $dataList[] = (object) $_newRow;
                    }
                }
            }
            $data['list'] = $dataList;
            $data['ban_times'] = json_encode($banGates);
        }
        $data['ban_start'] = $banStrat;
        $data['ban_end'] = $banEnd;
        $this->load->view('banuser/banuser_list', $data);
    }

    public function ban_edit() {
        $this->load->model('volunteer_select_model');
        //$this->load->view('banuser/banuser_edit', $data);
        $idNo = $this->input->post('idNo');
        $categories = $this->input->post('category');
        $data = $this->input->post();
        $success = !empty($categories);
        $errors = [];
        $banData['idNo'] = $idNo;
        $banData['start_date'] = $data['ban_start'];
        $banData['end_date'] = $data['ban_end'];
        if (!empty($categories) && !empty($idNo) && !empty($data['ban_start']) && !empty($data['ban_end'])) {
            foreach($categories as $categoryId) {
                //$category = $this->volunteer_select_model->getCategoryByName($categoryName);
                //$categoryId = $category->id;
                $banData['category_id'] = $categoryId;
                if ( ! $this->volunteer_select_model->setBanUser($banData)) {
                    $success =false;
                }
            }
        }
        if (empty($categories)) {
            $errors[] = '沒有選擇停權類別';
        }
        if (empty($data['ban_start'])) {
            $errors[] = '沒有停權起日';
        }
        if (empty($data['ban_end'])) {
            $errors[] = '沒有停權迄日';
        }
        if ( $success ) {
            $result = array('success'=>$success, 'msg'=> '設定完成.');
        } else {
            $result = array('success'=>$success, 'msg'=> join(", ", $errors) . ' 設定失敗.');
        }
        json_response($result);
    }

    function ban_resume() {
        $year = '112';
        $_today = new DateTime();
        $_today->modify("last day of this month");

        $data = array();
        $data['page_name'] = 'ban_resume';

        $_get = $this->input->get();
        $newGet = array_map("htmlspecialchars", $_get);
        $this->load->model('volunteer_select_model');
        if (isset($newGet['idNo']) && isset($newGet['month'])) {
            $data['idNo'] = $newGet['idNo'];
            $_user = $this->volunteer_select_model->getUserById($newGet['idNo']);
			$userName = $_user->name;
            $data['user_name'] = $userName;
            $month = $newGet['month'];
            $query_start = ($year+1911).'-'.$month.'-01';
            $query_start = date('Y-m-d', strtotime($query_start));
            $data['query_start'] = $query_start;
            $query_end = $_today->format("Y-m-d");
            $data['query_end'] = $query_end;
            $query_end = date('Y-m-d', strtotime($query_end.'+1 days'));
            $data['list'] = $this->getNewList($this->volunteer_select_model->getUserLogs($query_start, $query_end, $userName));
        }
        $this->load->view('banuser/banuser_resume', $data);
    }

    private function getNewList($resumeList) {
        $newList = array();
        foreach($resumeList as $item) {
            $_item = (array)$item;
            $_serviceTime = $item->course_date;
            $category = $this->volunteer_select_model->getCategoryByName($item->category);
            if ($item->type == '上午') {
                $_serviceTime .= ' ' . $category->morning_start . '~' . $category->morning_end;
            } else if ($item->type == '下午') {
                $_serviceTime .= ' ' . $category->afternoon_start . '~' . $category->afternoon_end;
            }
            $newList[] = (object)array_merge($_item, array('service_time' => $_serviceTime));
        }
        return $newList;
    }

    function resume_edit() {
        $year = '112';
        $_today = new DateTime();
        $_today->modify("last day of this month");

        $data = array();
        $data['page_name'] = 'resume_update';
        $_post = $this->input->post();
        $this->load->model('volunteer_select_model');
        $_updateSuccess = false;
        if (isset($_post)) {
            $newPost = array_map("htmlspecialchars", $_post);
            foreach($newPost as $key => $value) {
                $item = explode("_", $key);
                if ( $item[0] == 'resume') {
                    $id = trim($item[1]);
                    $_updateSuccess = $this->volunteer_select_model->update_log($id, $value);
                }
            }
            if ($_updateSuccess) {
                $data['response'] = "更新成功.";
            }
            if (isset($newPost['idNo']) && isset($newPost['query_start']) && isset($newPost['query_end'])) {
                $data['idNo'] = $newPost['idNo'];
                if (!isset($newPost['user_name'])) {
                    $_user = $this->volunteer_select_model->getUserById($newGet['idNo']);
                    $userName = $_user->name;
                } else {
                    $userName = $newPost['user_name'];
                }
                $data['user_name'] = $userName;
                $query_start = $newPost['query_start'];
                $data['query_start'] = $query_start;
                $query_end = $newPost['query_end'];
                $data['query_end'] = $query_end;
                //$query_end = date('Y-m-d', strtotime($query_end.'+1 days'));
                $data['list'] = $this->getNewList($this->volunteer_select_model->getUserLogs($query_start, $query_end, $userName));
            }
        }
        $this->load->view('banuser/banuser_resume', $data);
    }

}
