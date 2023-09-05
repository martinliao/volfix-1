<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Download extends CI_Controller{
    //put your code here
    public function __construct()
    {
        parent::__construct();
        $this->load->database('phy');

        $this->load->model('volunteer_manage_model');
        
        session_start();
        $_SESSION['userID'] = isset($_SESSION['userID'])?$_SESSION['userID']:-1;
        $_SESSION['role_id'] = isset($_SESSION['role_id'])?$_SESSION['role_id']:null;

        if($_SESSION['userID'] == '-1' || $_SESSION['role_id'] != '19'){
            die('您無此權限');
        }

        $userID = $_SESSION['userID'];
        $user = $this->db->where('id',$userID)
                         ->get('users')
                         ->row();
        $this->user = $user;
    }    


    public function subsidy_detail()
    {
        $_post = $this->input->post();
        $downloadExcel = isset($_post['downloadtype']) ? ($_post['downloadtype'] == 'excel' ? true : false) : false;
        $year = intval($this->input->post('year'));
        if ( $year=='' || !is_numeric($year) ) {
            $year = '110' ;
        }
        // 西元 ?
        $year = $year + 1911 ;
       
        $season = intval($this->input->post('season'));
        
        $start_month = intval($this->input->post('start_month'));
        $end_month = intval($this->input->post('end_month'));
        $firstname = addslashes(trim($this->input->post('firstname')));
        $category = $this->input->post('category');
        $show_types = $_post['show_type'];
        $showVaildate = false;
        foreach($show_types as $key => $value) {
            if(strcasecmp($value, 'all') == 0){
                $showVaildate = false;
                break;
            }
            if(strcasecmp($value, '2') == 0){
                $showVaildate = true;
            }
        }
        $data = array();
        if(!empty($season)){
            if ( $season=='1' ) {
                $start  = $year . '-01-01' ;
                $end    = $year . '-03-31' ;
                $data['start_month'] = 1;
                $data['end_month'] = 3;
            } else if ( $season=='2' ) {
                $start  = $year . '-04-01' ;
                $end    = $year . '-06-30' ;
                $data['start_month'] = 4;
                $data['end_month'] = 6;
            } else if ( $season=='3' ) {
                $start  = $year . '-07-01' ;
                $end    = $year . '-09-30' ;
                $data['start_month'] = 7;
                $data['end_month'] = 9;
            } else if ( $season=='4' ) {
                $start  = $year . '-10-01' ;
                $end    = $year . '-12-31' ;
                $data['start_month'] = 10;
                $data['end_month'] = 12;
            } 
            $data['row_span'] = 4;
        } else if(!empty($start_month) && !empty($end_month) && $end_month >= $start_month){
            if($start_month == $end_month){
                $data['row_span'] = 2;
            } else {
                $data['row_span'] = $end_month - $start_month + 2;
            }

            if($start_month <= 9){
                $start_month = '0'.$start_month;
            }

            if($end_month <= 9){
                $end_month = '0'.$end_month;
            }

            $start = $year.'-'.$start_month.'-01';
            $end = $year.'-'.$end_month.'-01';
            $end = date('Y-m-t',strtotime($end));
            $data['start_month'] = intval($start_month);
            $data['end_month'] = intval($end_month);
        } else {
            $start = $year.'-01-01';
            $end = $year.'-12-31';
            $data['row_span'] = 13;
            $data['start_month'] = 1;
            $data['end_month'] = 12;
        }

        $where = '';
        if(count($category) > 0){
            if($category[0] != 'all'){
                $category_list = implode(",",$category);
                $where .= sprintf(' and volunteer_category.id in (%s)', addslashes($category_list));
            }
        }

        if(!empty($firstname)){
            $where .= sprintf(" and users.name = '%s'", $firstname);
        }
        
        $joinStr = '';
        if($showVaildate) {
            $joinStr = "LEFT JOIN `sign_log` signOn On signOn.id = (
Select s1.id FROM sign_log AS s1 Where s1.idno=users.idNo AND DATE_FORMAT( s1.sign_time, '%Y-%m-%d' ) = volunteer_calendar.date AND SUBTIME(s1.sign_time, '00:30:00') <= TIMESTAMP(volunteer_calendar.date, volunteer_calendar_apply.start_time) 
Order by s1.sign_time LIMIT 1 ) 
                        LEFT JOIN `sign_log` signOff On signOff.id = (
Select s2.id FROM sign_log AS s2 Where s2.idno=users.idNo AND DATE_FORMAT( s2.sign_time, '%Y-%m-%d' ) = volunteer_calendar.date AND ADDTIME(s2.sign_time, '00:30:00') >= TIMESTAMP(volunteer_calendar.date, volunteer_calendar_apply.end_time) 
Order by s2.sign_time desc LIMIT 1 ) ";
            $where .= " AND (signOn.sign_time is NOT null And signOff.sign_time is NOT null)";
        }
        
        $sql = sprintf("SELECT 
                            volunteer_calendar_apply.userId, users.name as firstname, users.idNo, users.address, volunteer_classroom.volunteerID, volunteer_category.name, user_signature.signature, MONTH(volunteer_calendar.date) month, count(1) as cnt
                        FROM volunteer_calendar_apply as volunteer_calendar_apply
                        JOIN users on volunteer_calendar_apply.userID = users.id
                        JOIN volunteer_calendar as volunteer_calendar on volunteer_calendar_apply.calendarID = volunteer_calendar.id 
                        JOIN volunteer_classroom on volunteer_calendar.vcID = volunteer_classroom.id
                        JOIN volunteer_category on volunteer_classroom.volunteerID = volunteer_category.id
                        %s
                        LEFT JOIN `user_signature` as user_signature  on users.id = user_signature.user_id 
                        where volunteer_calendar.date >= '%s' 
                        AND volunteer_calendar.date <= '%s' 
                        AND volunteer_calendar_apply.got_it = 1 
                        %s
                        group by volunteer_calendar_apply.userId, volunteer_classroom.volunteerID,MONTH(volunteer_calendar.date)
                        order by users.idNo, volunteer_classroom.volunteerID,MONTH(volunteer_calendar.date)
                        ", $joinStr, addslashes($start), addslashes($end),$where);

        $getData = $this->db->query( $sql )->result_array();

        $last_data = array();
        $total = 0;
        $userid_list = array();
        for($i=0;$i<count($getData);$i++){
            if(!in_array($getData[$i]['userId'],$userid_list)){
                $userid_list[] = intval($getData[$i]['userId']);
            }
           
            $total += $getData[$i]['cnt']*120;
            if(isset($last_data[$getData[$i]['idNo']]['amount'])){
                $last_data[$getData[$i]['idNo']]['amount'] += $getData[$i]['cnt']*120;
            } else {
                $last_data[$getData[$i]['idNo']]['amount'] = $getData[$i]['cnt']*120;
            }

            $last_data[$getData[$i]['idNo']]['firstname'] = $getData[$i]['firstname'];
            $last_data[$getData[$i]['idNo']]['idNo'] = $getData[$i]['idNo'];
            $last_data[$getData[$i]['idNo']]['address'] = $getData[$i]['address'];
            $last_data[$getData[$i]['idNo']]['signature'] = $getData[$i]['signature'];
            $last_data[$getData[$i]['idNo']]['category'][$getData[$i]['month']][$getData[$i]['volunteerID']] = $getData[$i]['name'];
            $last_data[$getData[$i]['idNo']]['count'][$getData[$i]['month']][$getData[$i]['volunteerID']] = $getData[$i]['cnt'];
        }

        if(count($userid_list) > 0){
            $sql = sprintf("select users.name,users.idNo,users.address,user_signature.signature from users LEFT JOIN `user_signature` as user_signature  on users.id = user_signature.user_id  where users.`role_id` = '20' and users.address != '' and users.id not in (%s)", implode(',',$userid_list));
            
            $userData = $this->db->query( $sql )->result_array();
        }
       
        $data['year'] = $year;
        $data['total_amount'] = $total;
        $data['getData'] = $last_data;
        $data['userData'] = $userData;
        if ($downloadExcel) {
            $this->load->view('volunteer_manage/subsidy_excel', $data);
        } else {
            $this->load->view('volunteer_manage/subsidy_detail',$data);
        }
    }







    
}
