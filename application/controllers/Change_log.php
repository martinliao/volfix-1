<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Change_log extends CI_Controller{
    //put your code here
    public function __construct()
    {
        parent::__construct();
        $this->load->database('phy');

        $this->load->model('volunteer_select_model');
        $this->load->model('volunteer_manage_model');

        session_start();
        $_SESSION['userID'] = isset($_SESSION['userID'])?$_SESSION['userID']:-1;

        if( (strcmp(ENVIRONMENT, 'production') != 0) ){ 
            $_SESSION['role_id'] = 19;
            $_SESSION['userID'] = $this->config->item('eda_manage_testrun_id'); // e.g. 90
        }

        if($_SESSION['userID'] == '-1' || $_SESSION['role_id'] != '19'){
            die('您無此權限');
        }

        $left['list'] = $this->volunteer_manage_model->get_volunteer_category_detail();
        $this->load->view('volunteer_manage/header',$left);

    }    
    
    public function index() { 
        $data['list'] = array();
        $year = $month = null;
        $timesup = false;
        if (isPost()) {
            $_post = array_map("htmlspecialchars", $this->input->post());
            $year = $_post['year'];
            $month = $_post['month'];
            $timesup = isset($_post['forty8']) ? strcasecmp($_post['forty8'], 'on') == 0 : FALSE;

            if(!empty($year) && !empty($month)){
                $query_start = ($year+1911).'-'.$month.'-01';
                $query_start = date('Y-m-d', strtotime($query_start));
                $query_end =  date('Y-m-t', strtotime($query_start));
                $query_end = date('Y-m-d',strtotime($query_end.'+1 days'));

                $data['list'] = $this->volunteer_select_model->get_log($query_start,$query_end, $timesup);
            }
            $data['year'] = $year;
            $data['month'] = $month;
            $data['forty8'] = $timesup;
        }

        $this->load->view('volunteer_manage/change_log',$data);
        $this->load->view('volunteer_manage/footer');
    }
}
