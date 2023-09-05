<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Volunteer_manage extends CI_Controller{
    //put your code here
    public function __construct()
    {
        parent::__construct();
        $this->load->database('phy');
        $this->load->model('volunteer_manage_model');


        $left['list'] = $this->volunteer_manage_model->get_volunteer_category_detail();
        $this->load->view('volunteer_manage/header',$left);
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
    
    public function index() { 
        $this->load->view('volunteer_manage/index');
        $this->load->view('volunteer_manage/footer');
    }

    public function volunteer_category_manage(){
        $data['list'] = $this->volunteer_manage_model->get_volunteer_category_detail();
        $this->load->view('volunteer_manage/volunteer_category_manage',$data);
        $this->load->view('volunteer_manage/footer');
    }

    public function volunteer_category_edit(){
        $id = $this->uri->segment(3);
        $data = array();


        if(!empty($_POST)){
        // seeData($_POST,1);
            if($_POST['id'] > 0){
                $result = $this->volunteer_manage_model->upd_volunteer_category_detail($_POST);
            } else {
                $result = $this->volunteer_manage_model->add_volunteer_category_detail($_POST);
            }

            if($result){
                $url = '"'.base_url().'volunteer_manage/volunteer_category_manage'.'"';
                echo "<script>
                    alert('儲存成功');
                    location.href = $url;
                </script>";
            }
            
        }

        if($id > 0){
            $data['detail'] = $this->volunteer_manage_model->get_volunteer_category_detail(intval($id));
            if(empty($data['detail'])){
                redirect(base_url().'volunteer_manage/volunteer_category_manage');
            }
        }

        $data['id'] = $id;
        $this->load->view('volunteer_manage/volunteer_category_edit',$data);
        $this->load->view('volunteer_manage/footer');
    }

    public function volunteer_category_del(){
        $id = $this->uri->segment(3);

        if($id > 0){
            $result = $this->volunteer_manage_model->del_volunteer_category_detail($id);

            if($result){
                $url = '"'.base_url().'volunteer_manage/volunteer_category_manage'.'"';
                echo "<script>
                    alert('刪除成功');
                    location.href = $url;
                </script>";
            }
        } else {
            $url = '"'.base_url().'volunteer_manage/volunteer_category_manage'.'"';
            echo "<script>
                alert('刪除成功');
                location.href = $url;
            </script>";
        }
    }

    public function scheduling_setup(){
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $data['year'] = $year;
        $data['month'] = $month;
        $data['list'] = array();

        if(!empty($year) && !empty($month)){
            $query_start = ($year+1911).'-'.$month.'-01';
            $query_start = date('Y-m-d', strtotime($query_start));
            $query_end =  date('Y-m-t', strtotime($query_start));

            $data['list'] = $this->volunteer_manage_model->get_course_detail($query_start,$query_end);
        }

        $this->load->view('volunteer_manage/scheduling_setup',$data);
        $this->load->view('volunteer_manage/footer');
    }

    public function scheduling_setup_edit(){
        $id = $this->uri->segment(3);

        $course = false;
        if($id)
            $course = $this->db->where('id',$id)->get('course')->row();

        if(!$course)
        {
            $url = '"'.base_url().'volunteer_manage/'.'"';
            echo "<script>
                alert('無此班期資料');
                location.href = $url;
            </script>";            
        }

        $data['course'] = $course;
        $select = array(
            'calendar.*'
        );
        $list = $this->db->select(implode(',',$select))
                         ->from('course')
                         ->join('volunteer_calendar calendar','course.id = calendar.courseID')
                         ->join('volunteer_classroom vc','vc.id = calendar.vcID')
                         ->join('volunteer_category v','vc.volunteerID = v.id AND v.others = 0')
                         ->where('course.id',$id)
                         ->order_by('date asc,type asc')
                         ->get()
                         ->result();
        // seeData($list,1);
        $list = $list?$list:array();
        $tmp_list = array();
        foreach ($list as $key => $each)
        {
            $each->start_time = date('H:i',strtotime($each->start_time));
            $each->end_time = date('H:i',strtotime($each->end_time));
            $tmp_list[$each->date][$each->type] = $each;
        }
        $data['list'] = $tmp_list;

        $this->load->view('volunteer_manage/scheduling_setup_edit',$data);
        $this->load->view('volunteer_manage/footer');
    }


    public function save(){
        $post = $this->input->post();


        $courseID = $this->input->post('id');
        $need = $this->input->post('need');
        $course = $this->db->where('id',$courseID)->get('course')->row();

        $y=ROCdate('Y',strtotime($course->start_date));
        $m=ROCdate('m',strtotime($course->start_date));

        $this->db->where('id',$courseID)
                 ->update('course',array('need'=>$need));

        $this->db->where('id',$courseID)
                 ->update('course',array('change'=>1));

        $calendar = $this->input->post('calendar');

        foreach ($calendar as $calendarID => $data)
        {
            $data['status'] = isset($data['status'])&&$data['status']>0?1:0;
            $this->db->where('id',$calendarID)
                     ->update('volunteer_calendar',$data);            
        }

        $url = '"'.base_url().'volunteer_manage/scheduling_setup?year='.$y.'&month='.$m.'"';
        echo "<script>
            alert('更新完成');
            location.href = $url;
        </script>";
    }


    public function apply_time_setting(){
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $data['list'] = array();
        $data['year'] = $year;
        $data['month'] = $month;
        $data['first_day'] = '';
        $data['last_day'] = '';
        if(!empty($year) && !empty($month)){
            $query_start = ($year+1911).'-'.$month.'-01';
            $query_start = date('Y-m-d', strtotime($query_start));
            $query_end =  date('Y-m-t', strtotime($query_start));

            $data['list'] = $this->volunteer_manage_model->get_course_detail($query_start,$query_end);

            $data['first_day'] = date('Y-m-01', strtotime($query_start));
            $data['last_day'] = $query_end;
        }

        $this->load->view('volunteer_manage/apply_time_setting',$data);
        $this->load->view('volunteer_manage/footer');
    }

    public function apply_time_setting_edit(){
        $id = $this->uri->segment(3);

        $course = false;
        if($id)
            $course = $this->db->where('id',$id)->get('course')->row();

        if(!$course)
        {
            $url = '"'.base_url().'volunteer_manage/'.'"';
            echo "<script>
                alert('無此班期資料');
                location.href = $url;
            </script>";            
        }

        $data['course'] = $course;

        $note = $this->db->where('id',1)->get('system_value')->row();
        $note =$note?$note->value:'';
        $data['note'] = $note;

        $select = array(
            'calendar.*'
        );

        $this->load->view('volunteer_manage/apply_time_setting_edit',$data);
        $this->load->view('volunteer_manage/footer');
    }

    public function apply_time_setting_save(){
        $apply_start = $this->input->post('apply_start');
        $apply_end = $this->input->post('apply_end');
        $apply_user = $this->input->post('apply_user');
        $note = $this->input->post('note');
        $courseID = $this->input->post('courseID');

        $course = $this->db->where('id',key($courseID))->get('course')->row();

        // seeData($course,1);

        $y=ROCdate('Y',strtotime($course->start_date));
        $m=ROCdate('m',strtotime($course->start_date));

        if($apply_start && $apply_end && !empty($courseID))
        {
            $update = array(
                'apply_start'=>$apply_start,
                'apply_end'=>$apply_end,
                'apply_user'=>$apply_user
            );

            foreach ($courseID as $id => $do)
            {
                if($do)
                {
                    $this->db->where('id',$id)->update('course',$update);
                }
            }

            if(isset($note))
            {
                $this->db->where('id',1)->update('system_value',array('value'=>$note));
            }

            $url = '"'.base_url('volunteer_manage/apply_time_setting?year='.$y.'&month='.$m).'"';
            echo "
            <script>
                alert('更新完成');
                location.href = $url;
            </script>"; 
        }
        else
        {
            $url = '"'.base_url('volunteer_manage/apply_time_setting?year='.$y.'&month='.$m).'"';
            echo "
            <script>
                alert('請填寫報名日期限制、選擇班別後再點選送出');
                location.href = $url;
            </script>"; 

        }
    }


    public function apply_time_setting_save_2(){
        $limit_start = $this->input->post('limit_start');
        $limit_end = $this->input->post('limit_end');
        $limit_user = $this->input->post('limit_user');
        $note = $this->input->post('note');
        $courseID = $this->input->post('courseID');

        $course = $this->db->where('id',key($courseID))->get('course')->row();

        // seeData($course,1);

        $y=ROCdate('Y',strtotime($course->start_date));
        $m=ROCdate('m',strtotime($course->start_date));

        if($limit_start && $limit_end && !empty($courseID))
        {
            $update = array(
                'limit_start'=>$limit_start,
                'limit_end'=>$limit_end,
                'limit_user'=>$limit_user
            );

            foreach ($courseID as $id => $do)
            {
                if($do)
                {
                    $this->db->where('id',$id)->update('course',$update);
                }
            }

            if(isset($note))
            {
                $this->db->where('id',1)->update('system_value',array('value'=>$note));
            }

            $url = '"'.base_url('volunteer_manage/apply_time_setting?year='.$y.'&month='.$m).'"';
            echo "
            <script>
                alert('更新完成');
                location.href = $url;
            </script>"; 
        }
        else
        {
            $url = '"'.base_url('volunteer_manage/apply_time_setting?year='.$y.'&month='.$m).'"';
            echo "
            <script>
                alert('請填寫報名日期限制、選擇班別後再點選送出');
                location.href = $url;
            </script>"; 

        }
    }


    public function scheduling_setup_others($vID){

        $default = array();

        $v_data = $this->db->select('v.*,vc.id vcID')
                           ->where('others',1)
                           ->join('volunteer_classroom vc','vc.volunteerID = v.id')
                           ->get('volunteer_category v')
                           ->result();

        $v_data = $v_data?$v_data:array();

        foreach ($v_data as $key => $each)
        {
            if(isset($default[$each->id]))
                continue;
            $default[$each->id] = array(
                1=>$each->morning_status,
                2=>$each->afternoon_status,
                3=>$each->night_status,
                'vcID'=>$each->vcID
            );

            if(!$each->morning_use)
                unset($default[$each->id][1]);

            if(!$each->afternoon_use)
                unset($default[$each->id][2]);

            if(!$each->night_use)
                unset($default[$each->id][3]);
        }
        // seeData($default,1);
        // $default = array(
        //     // 
        //     2=>array(
        //         1=>1,
        //         2=>0,
        //         'vcID'=>12,
        //     ),
        //     // 圖書
        //     3=>array(
        //         1=>0,
        //         2=>1,
        //         'vcID'=>13,
        //     ),
        //     // 客服
        //     4=>array(
        //         1=>1,
        //         2=>1,
        //         'vcID'=>14,
        //     ),
        //     // 園藝
        //     5=>array(
        //         1=>1,
        //         2=>1,
        //         'vcID'=>15,
        //     ),
        // );
        
        $volunteer_data = $this->db->where('id',$vID)
                                   ->where('others',1)
                                   ->get('volunteer_category')
                                   ->row();

        $search = $this->input->post('search');
        $default_setting = $this->input->post('default_setting');
        $default_setting = $default_setting?$default_setting:array();

        $data['list'] = array();
        $data['vID'] = $vID;
        $data['default_form'] = false;
        $data['default_setting'] = $default_setting;
        $data['default'] = $default[$vID];
        $data['volunteer_data'] = $volunteer_data;
        $data['reflash'] = false;

        // 如果有送設定植過來
        if(!empty($default_setting))
        {
            $data['reflash'] = $this->setting_default($default_setting);
        }

        if(!empty($search))
        {
            $WEEK_INDEX = array(
                'Sunday' => 0,
                'Monday' => 1,
                'Tuesday' => 2,
                'Wednesday' => 3,
                'Thursday' => 4,
                'Friday' => 5,
                'Saturday' => 6,
            );

            $data['default_form'] = true;
            $month_start = ($search['year']+1911).'-'.$search['month'].'-1';

            $month_start = date('Y-m-d',strtotime($month_start));
            $month_end = date('Y-m-t',strtotime($month_start));
            
            $tmp_list = $this->db->where('vcID',$data['default']['vcID'])
                                      ->where('date >=',$month_start)
                                      ->where('date <=',$month_end)
                                      ->order_by('date asc,type asc')
                                      ->get('volunteer_calendar')
                                      ->result();
            $tmp_list = $tmp_list?$tmp_list:array();

            $first_week_date = null;
            $first_date = null;
            foreach ($tmp_list as $each)
            {
                if(!isset($first_week_date))
                {
                    $first_date = $each->date;
                    $first_week_date = $WEEK_INDEX[date('l',strtotime($each->date))];
                }
                $data['list'][$each->date][$each->type] = $each;
            }
            // $data['first_week_date'] = $WEEK_INDEX[date('l',strtotime($each->date))];

            $data['empty_date'] = array();

            for($i = $first_week_date; $i > 0 ;$i--)
            {
                $now_date = isset($now_date)?date('Y-m-d',strtotime($now_date.'- 1 day')):date('Y-m-d',strtotime($first_date.'- 1 day'));
                $data['empty_date'][] = $now_date;
            }

            // seeData($data['empty_date']);
        }


        $data['search'] = array(
            'year'=>isset($search['year'])?$search['year']:null,
            'month'=>isset($search['month'])?$search['month']:null,
        );

        $this->load->view('volunteer_manage/scheduling_setup_others',$data);
        $this->load->view('volunteer_manage/footer');
    }

    //----- 0313 鵬  志工服務人次備註設定按鈕要做的事---------------------
    public function setVolinteer_service_personnel()
    {
        $default_setting = $this->input->post();
        // $default_setting = json_decode($default_setting,true);
        // echo "<script>alert('" . $default_setting['vcID'] . "')</script>";
        if($default_setting['vcID'] && isset($default_setting['special_note'])) //$default_setting['vcID'] && isset($default_setting['special_note']
        {
            $this->db->query('UPDATE volunteer_category SET special_note = \''.addslashes($default_setting['special_note']).'\' WHERE id = (SELECT volunteerID FROM volunteer_classroom WHERE id = \''.addslashes($default_setting['vcID']).'\')');
        }
    }
    //------------------------------------------------------------------

    public function setting_default($default_setting)
    {
        // echo '<pre>';
        // print_r($default_setting);
        // die();

        $month_start = ($default_setting['year']+1911).'-'.$default_setting['month'].'-1';

        $month_start = date('Y-m-d',strtotime($month_start));
        $month_end = date('Y-m-t',strtotime($month_start));

        for($unix_date = strtotime($month_start) ; $unix_date<= strtotime($month_end) ; $unix_date+=(24*60*60))
        {
            foreach ($default_setting['type'] as $type => $time_setting)
            {
                $date = date('Y-m-d',$unix_date);

                $date_data = $this->db->where('vcID',$default_setting['vcID'])
                                      ->where('date',$date)
                                      ->where('type',$type)
                                      ->get('volunteer_calendar')
                                      ->row();

                // 如果那天不存在於日曆上，直接insert
                if(empty($date_data))
                {
                    $insert_data = array(
                        'vcID'=>$default_setting['vcID'],
                        'date'=>$date,
                        'type'=>$type,
                        'day'=>date("w",strtotime($date)),
                        'num_got_it'=>'1',
                        'num_waiting'=>'0',
                    );
                    $this->db->insert('volunteer_calendar',$insert_data);

                    $id = $this->db->insert_id();
                }
                else
                {
                    $id = $date_data->id;
                }


                // 依據 default_setting update
                $update_data = array(
                    'start_time'=>$time_setting['start'],
                    'end_time'=>$time_setting['end'],
                );
                $week_key = date('l',$unix_date);

                $status = true;
                // 如果固定星期幾有設定
                if(isset($default_setting['week'][$week_key]))
                {
                    $status = $default_setting['week'][$week_key];
                }

                $status = $status && $default_setting['type_status'][$type];
                $update_data['status'] = $status;

                $this->db->where('id',$id)->update('volunteer_calendar',$update_data);

                $volunteer_id = $this->db->where('id',$default_setting['vcID'])
                                      ->get('volunteer_classroom')
                                      ->row();

                if($volunteer_id->volunteerID > 0){
                    if($type == '1'){
                        $update_array = array(
                            'morning_start'=>$time_setting['start'],
                            'morning_end'=>$time_setting['end'],
                        );
                        $this->db->where('id',$volunteer_id->volunteerID)->update('volunteer_category',$update_array);
                    } elseif ($type == '2') {
                        $update_array = array(
                            'afternoon_start'=>$time_setting['start'],
                            'afternoon_end'=>$time_setting['end'],
                        );
                        $this->db->where('id',$volunteer_id->volunteerID)->update('volunteer_category',$update_array);
                    }  
                }
            }
        }
        return true;
    }

    public function single_setting(){
        $id = $this->input->post('id');
        $data = $this->input->post();
        unset($data['id']);

        $this->db->where('id',$id)
                 ->update('volunteer_calendar',$data);
        $action_str = '';

        if(isset($data['status']))
            $action_str = '開放狀態';

        if(isset($data['person']))
            $action_str = '服務人次';

        json_response(array('status'=>true,'msg'=>$action_str.'設定完成'));
    }



    public function long_range_key_word(){
        $long_range_key_word = $this->db->get('long_range_key_word')->result();
        $data['long_range_key_word'] = $long_range_key_word?$long_range_key_word:array();

        $this->load->view('volunteer_manage/long_range_key_word',$data);
        $this->load->view('volunteer_manage/footer');

    }
    public function long_range_key_word_edit($id=null){

        $data = $this->db->where('id',$id)
                         ->get('long_range_key_word')
                         ->row();

        
        $this->load->view('volunteer_manage/long_range_key_word_edit',$data);
        $this->load->view('volunteer_manage/footer');
    }

    public function long_range_key_word_save(){
        $id = $this->input->post('id');
        $data['key_word'] = $this->input->post('key_word');
        $data['apply_start'] = $this->input->post('apply_start');
        $data['apply_end'] = $this->input->post('apply_end');

        if($id)
        {
            $this->db->where('id',$id)->update('long_range_key_word',$data);
        }
        else
        {
            $this->db->insert('long_range_key_word',$data);
        }

        $url = '"'.base_url('volunteer_manage/long_range_key_word').'"';

        echo "
        <script>
            alert('更新完成');
            location.href = $url;
        </script>"; 
    }
    public function long_range_key_word_remove(){
        $key_wordID = $this->input->post('key_wordID');
        $url = '"'.base_url('volunteer_manage/long_range_key_word').'"';
        if(!$key_wordID)
        {
            json_response(array('status'=>false,'msg'=>'操作錯誤，請返回上一步'));
        }
        else
        {
            $this->db->where('id',$key_wordID)->delete('long_range_key_word');

            json_response(array('status'=>true,'msg'=>'更新完成'));  
        }

    }
    public function long_range_user(){

        $users = $this->db->where('long_range',1)
                          ->get('users')
                          ->result();
        $tmp_user_list = $this->db->where('long_range = 0 OR long_range IS NULL')
                          ->get('users')
                          ->result();


        $users = $users?$users:array();
        $tmp_user_list = $tmp_user_list?$tmp_user_list:array();
        $user_list = array();
        foreach ($tmp_user_list as $each) {
            $user_list[$each->id] = $each->name;
        }

        $data['users'] = $users;
        $data['user_list'] = $user_list;

        $this->load->view('volunteer_manage/long_range_user',$data);
        $this->load->view('volunteer_manage/footer');
    }
    public function long_range_user_add(){
        $url = '"'.base_url('volunteer_manage/long_range_user').'"';

        $userID = $this->input->post('userID');
        if(!$userID)
        {
            echo"
                <script>
                    alert('操作錯誤！請選擇會員！');
                    location.href = $url;
                </script>
            ";
        }
        else
        {
            $this->db->where('id',$userID)->update('users',array('long_range'=>1));

            echo "
            <script>
                alert('更新完成');
                location.href = $url;
            </script>";
        }
    }

    public function long_range_user_remove(){
        $userID = $this->input->post('userID');
        $url = '"'.base_url('volunteer_manage/long_range_user').'"';

        if(!$userID)
        {
            json_response(array('status'=>false,'msg'=>'操作錯誤，請返回上一步'));
        }
        else
        {
            $this->db->where('id',$userID)->update('users',array('long_range'=>0));

            json_response(array('status'=>true,'msg'=>'更新完成'));            
        }
    }

    public function evaluation_leader_user(){
        $users = $this->db->where('evaluation_category_leader is not null',null)
                          ->get('users')
                          ->result();
        $tmp_user_list = $this->db->where('role_id=19 and evaluation_category_leader IS NULL')
                          ->get('users')
                          ->result();


        $users = $users?$users:array();
        $tmp_user_list = $tmp_user_list?$tmp_user_list:array();
        $user_list = array();
        foreach ($tmp_user_list as $each) {
            $user_list[$each->id] = $each->name;
        }
        
        foreach ($users as $each) {
            $category_name_list = '';
            $category_list = explode('|',$each->evaluation_category_leader);
            
            for($i=0;$i<count($category_list);$i++){
                $category_name = $this->volunteer_manage_model->get_volunteer_category(intval($category_list[$i]));

                if(!empty($category_name)){
                    $category_name_list .= $category_name['name'].',';
                }
            }
            if(!empty($category_name_list)){
                $each->category_list = substr($category_name_list, 0 , -1);
            } else {
                $each->category_list = '';
            }
        }

        $data['users'] = $users;
        $data['user_list'] = $user_list;
        $data['category'] = $this->volunteer_manage_model->get_volunteer_category_detail2();
        $data['query_category'] = ! empty($data['category']) ? $data['category'] : array();
        
        $this->load->view('volunteer_manage/evaluation_leader_user',$data);
        $this->load->view('volunteer_manage/footer');
    }

    public function evaluation_leader_user_add(){
        $url = '"'.base_url('volunteer_manage/evaluation_leader_user').'"';

        $userID = intval($this->input->post('userID'));
        $category = $this->input->post('category');

        if(!$userID)
        {
            echo"
                <script>
                    alert('操作錯誤！請選擇會員！');
                    location.href = $url;
                </script>
            ";
        }
        else
        {
            if(!empty($category)){
                $category_list = array();
    
                for($i=0;$i<count($category);$i++){
                    if($category[$i] != 'all'){
                        $category_list[] = intval($category[$i]);
                    } 
                }

                $evaluation_category = implode('|', $category_list);

                $this->db->where('id',$userID)->update('users',array('evaluation_category_leader'=>$evaluation_category));

                echo "
                <script>
                    alert('更新完成');
                    location.href = $url;
                </script>";
            } else {
                echo"
                    <script>
                        alert('操作錯誤！請選擇類別！');
                        location.href = $url;
                    </script>
                ";
            }
        }
    }

    public function evaluation_leader_user_remove(){
        $userID = intval($this->input->post('userID'));
        $url = '"'.base_url('volunteer_manage/evaluation_leader_user').'"';

        if(!$userID)
        {
            json_response(array('status'=>false,'msg'=>'操作錯誤，請返回上一步'));
        }
        else
        {
            $this->db->where('id',$userID)->update('users',array('evaluation_category_leader'=>null));

            json_response(array('status'=>true,'msg'=>'更新完成'));            
        }
    }

    public function apply_time_setting_others($vID){
        $volunteer_data = $this->db->where('id',$vID)
                                   ->where('others',1)
                                   ->get('volunteer_category')
                                   ->row();
        $note = $this->db->where('id',1)->get('system_value')->row();
        $note =$note?$note->value:'';
        $data['note'] = $note;

        if(!$volunteer_data)
            die('錯誤的志工資料');


        $year = ($this->input->post('year')+1911);
        $month = $this->input->post('month');

        $setting = null;
        if($year && $month)
        {
            $setting = $this->db->where('year',$year)
                                ->where('month',$month)
                                ->where('volunteerID',$vID)
                                ->get('volunteer_apply_setting')
                                ->row();
            if(!($setting))
            {
                $this->db->insert('volunteer_apply_setting',array('year'=>$year,'month'=>$month,'volunteerID'=>$vID));

                $setting = $this->db->where('year',$year)
                                    ->where('month',$month)
                                    ->where('volunteerID',$vID)
                                    ->get('volunteer_apply_setting')
                                    ->row();            
            }
        }

        $data['volunteer_data'] = $volunteer_data;
        $data['setting'] = $setting;
        $data['search'] = array(
            'year'=>$year,
            'month'=>$month,
        );

        $this->load->view('volunteer_manage/apply_time_setting_edit_others',$data);
        $this->load->view('volunteer_manage/footer');
    }

    public function apply_time_setting_others_save(){
        
        $id = $this->input->post('id');
        $vID = $this->input->post('vID');
        $update['apply_start'] = $this->input->post('apply_start');
        $update['apply_end'] = $this->input->post('apply_end');

        $url = '"'.base_url('volunteer_manage/apply_time_setting_others/'.$vID).'"';
        if(empty($update['apply_start']) || empty($update['apply_end']))
        {
            echo "
            <script>
                alert('更新失敗，日期不得為空');
                location.href = $url;
            </script>";
            die();
        }

        $this->db->where('id',$id)->update('volunteer_apply_setting',$update);

        $note = $this->input->post('note');
        $note = $note?$note:'';
        $this->db->where('id',1)->update('system_value',array('value'=>$note));
        echo "
        <script>
            alert('更新完成');
            location.href = $url;
        </script>";
    }




    public function publish($default_month_date=null){
        $default_month_date = time()-86400*date('w')+(date('w')>0?86400:-6*86400);
        $tmp_v_list = $this->db->get('volunteer_category')->result();
        $v_list = array();
        $v_list['all']=1;
        foreach ($tmp_v_list as $each)
        {
            $v_list[] = 'vID['.$each->id.']=1';
        }
        $vID_str = implode('&',$v_list);
        redirect(base_url('volunteer_manage/publish_detail/'.$default_month_date.'?default=1&'.$vID_str));
        

        // $default_month_date = isset($default_month_date)?date('Y-m-d',$default_month_date):date('Y-m-d',strtotime(date('Y-m-01').'+1 month'));
        // $week_list = $this->volunteer_manage_model->get_week_list($default_month_date);

        // $vc_list = $this->volunteer_manage_model->get_vc_list();

        // $tmp_v_list = $this->db->get('volunteer_category')->result();
        // $v_list = array();
        // foreach ($tmp_v_list as $each)
        // {
        //     $v_list[$each->id]['name'] = $each->name;
        //     $v_list[$each->id]['checked'] = true;
        // }

        // $start = current($week_list);
        // $end = end($week_list);
        // $calendar_list = $this->volunteer_manage_model->get_calendar_list($start,$end,$this->user->long_range);
        // $apply_data = $this->volunteer_manage_model->get_all_apply_data($start,$end);

        // reset($week_list);
        // // seeData($apply_data,1);
        // // seeData($calendar_list);

        // $note = $this->db->where('id',1)->get('system_value')->row();
        // $note =$note?$note->value:'';
        

        // $data['userID'] = $this->user->id;
        // $data['note'] = $note;
        // $data['week_list'] = $week_list;
        // $data['vc_list'] = $vc_list;
        // $data['v_list'] = $v_list;
        // $data['calendar_list'] = $calendar_list;
        // $data['apply_data'] = $apply_data;
        
        // $this->load->view('volunteer_manage/publish',$data);
        // $this->load->view('volunteer_manage/footer');
    }

    public function publish_detail($default_month_date=null){
        $data['export_date'] = $default_month_date;

        if(!empty($default_month_date)){
            $data['default_month'] = date('m',$default_month_date);
        }

        // 用POST查詢的話，就算完後導轉
        if($this->input->post())
        {
            $default_month_date = ($this->input->post('year')+1911).'-'.$this->input->post('month_start').'-01';
            $default_month_date = strtotime($default_month_date);
            
            $vID_str = $this->input->post('vID')?$this->input->post('vID'):array();
            $tmp_str = array();

            foreach ($vID_str as $key => $value)
                {$tmp_str[] = 'vID['.$key.']='.$value;}
            

            $vID_str = implode('&',$tmp_str);

            if($this->input->post('not_show')){
                $vID_str .= '&not=1';
            }

            // if($this->input->post('export')){
            //     $vID_str .= '&export=1';
            // }
            redirect(base_url('volunteer_manage/publish_detail/'.$default_month_date.'?'.$vID_str));
        }

        // 用GET接參數
        $vID_str = $this->input->get('vID')?$this->input->get('vID'):array();
        $not_show = $this->input->get('not')?$this->input->get('not'):0;
        $default = $this->input->get('default');
        $export = $this->input->get('export');
        $tmp_str = array();

        foreach ($vID_str as $key => $value)
            {$tmp_str[] = 'vID['.$key.']='.$value;}
        
        $vID_str = implode('&',$tmp_str);
        $vID_str .= '&not='.$not_show;
        $vID_arr = $this->input->get('vID')?$this->input->get('vID'):array();

        $tmp_v_list = $this->db->where('show',1)->get('volunteer_category')->result();
        $v_list = array();
        $all_checked = true;
        foreach ($tmp_v_list as $each)
        {
            $v_list[$each->id]['name'] = $each->name;
            $v_list[$each->id]['checked'] = isset($vID_arr[$each->id]);
            $all_checked = $all_checked && $v_list[$each->id]['checked'];
        }



        $ONLY_ME=false;
        // $default_month_date = null;

        if($default){
            $default_month_date = isset($default_month_date)?date('Y-m-d',$default_month_date):date('Y-m-d',strtotime(date('Y-m-01').'+1 month'));
            $week_list = $this->volunteer_manage_model->get_week_list($default_month_date);
        } else {
            $start_date = $default_month_date;
            $end_date = strtotime(date('Y-m-d',$default_month_date).'+1 month -1 day');

            $week_list = array();
            $now_date = $start_date;
            while ($now_date <= $end_date) {
                $w = date('w',$now_date);
                
                    $week_list[] = date('Y-m-d',$now_date);
                // } else {
                //     if($w != '0' && $w != '6'){
                //         $week_list[] = date('Y-m-d',$now_date);
                //     }
                // }
                
                $now_date+=60*60*24;
            }

            
        }
        
        
            $week_list_count = count($week_list);
            for($i=0;$i<$week_list_count;$i++){
                $weekday = date('w',strtotime($week_list[$i]));
    
                if($weekday == '0' || $weekday == '6'){
                    $exist = $this->volunteer_manage_model->checkExist($week_list[$i]);

                    if(!$exist){
                        unset($week_list[$i]);
                    }
                }
            }
            // seeData($week_list,1);
        // }

        // seeData($week_list,1);

        $vc_list = $this->volunteer_manage_model->get_vc_list();


        $start = current($week_list);
        $end = end($week_list);
        $calendar_list = $this->volunteer_manage_model->get_calendar_list($start,$end,$this->user->long_range,$not_show);
        $apply_data = $this->volunteer_manage_model->get_all_apply_data($start,$end);

        reset($week_list);
        // seeData($apply_data,1);
        // seeData($calendar_list);

        $note = $this->db->where('id',1)->get('system_value')->row();
        $note =$note?$note->value:'';

        $use_classroom = array();
        $outside = array();
        foreach ($calendar_list as $key => $value) {
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    if(!in_array($value3->classroomID, $use_classroom)){
                        array_push($use_classroom, $value3->classroomID);
                    }
                    if($value3->belongto == '68001'){
                        $outside_key = $value3->date.'-'.$value3->courseID;
                        array_push($outside, $outside_key);
                    }
                }
            }
        }

        foreach ($vc_list[1] as $key => $value) {
            if(!in_array($key, $use_classroom)){
                unset($vc_list[1][$key]);
            }
        }

        $data['outside'] = $outside;
        $data['userID'] = $this->user->id;
        $data['note'] = $note;
        $data['vID_str'] = $vID_str;
        $data['vID_arr'] = $vID_arr;
        $data['week_list'] = $week_list;
        $data['vc_list'] = $vc_list;
        $data['v_list'] = $v_list;
        $data['all_checked'] = $all_checked;
        $data['calendar_list'] = $calendar_list;
        $data['apply_data'] = $apply_data;
        $data['ONLY_ME'] = $ONLY_ME;
        $data['not_show'] = $not_show;
        $data['default'] = $default;

        if(preg_match("/^61.216.24.9[5,6]$/", $_SERVER["REMOTE_ADDR"])) {
            // echo '<pre>';
            // print_r($calendar_list);
            // // print_r($vc_list);
            // die();
        }
        

        // 輸出教室
        $sql = "SELECT 
                    volunteer_classroom.volunteerID ,
                    classroom.*
                FROM `volunteer_classroom` as volunteer_classroom
                LEFT JOIN classroom as classroom  on classroom.id = volunteer_classroom.classroomID" ;
        $data['classRoomList'] = $this->db->query($sql)->result() ;

        if($export){
            $data['userID'] = $this->user->id;  
            $this->load->view('volunteer_manage/publish_detail_export',$data);
            // exit;
        } else {
            $this->load->view('volunteer_manage/publish_detail_new',$data);
        }
       
        $this->load->view('volunteer_manage/footer');
    }



    /**
     * [get_apply_user description]
     * @return [type] [description]
     */
    public function get_apply_user()
    {
        // get
        $cid = $this->input->post('cid');
        // 讀取
        $select = array(
            'calendar_apply.id',
            'calendar_apply.calendarID cid',
            'calendar_apply.got_it got_it',
            'users.id userId',
            'users.name userName',
        );
        $apply_data = $this->db->select(implode(',',$select))
                               ->from('volunteer_calendar_apply calendar_apply')
                               ->join('users','calendar_apply.userID = users.id')
                               ->where('calendar_apply.calendarID =',$cid);

        $apply_data = $this->db->get() ;
        if ( $apply_data ) {
            $apply_data = $apply_data->result('array') ;
        } else {
            $apply_data = array() ;
        }


        $result = array(
            'code'          => '100' ,
            'apply_data'    =>  $apply_data
        ) ;
        echo json_encode($result) ;
        exit ;
    }


    public function cancel_course()
    {
        // get
        $cid = $this->input->post('cid');
        // status = 0
        $this->db->where('id',$cid)
                 ->update('volunteer_calendar',array('status'=>'0'));

        // 找出 courseID
        $tmp = $this->db->where('id',$cid)->get('volunteer_calendar') ;
        if ( $tmp ) {
            $tmp = $tmp->first_row('array') ;
            // 刪除
            // $this->db->where('id',$tmp['courseID'])
            //      ->update('course',array('need'=>'0'));
            
            // 是否通知
            $send   = $this->input->post('send');
            // 空的
            $msg    = "原定 ".$tmp['date']." ，".$this->input->post('courseName')."，您報名的班務志工，班期已取消，請勿到班，謝謝!" ;
            // 是否通知
            if ( $send=='1' && $msg!='' ) {
                // 寫入
                $UserList = $this->db->where('calendarID',$cid)->where('got_it','1')->get('volunteer_calendar_apply')->result();
                // 處理時間
                $end_time   = $this->input->post('end_time'); 
                $tmp        = explode('T',$end_time ) ;
                $date       = $tmp[0] ;
                $end_time   = $tmp[1] ;
                // 個別通知
                foreach ($UserList as $User) {
                    $insert_data = array(
                        'user_id'   => $User->userID        ,
                        'msg'       => $msg                 ,
                        'exptime'   => $date." ".$end_time  ,
                    );
                    $this->db->insert('user_msg',$insert_data);
                }
            }
            
            // 最後刪除
            // volunteer_calendar_apply
            // 中 cid 資料
            // 2021/09/17 修正
            $this->db->where('calendarID',$cid)->delete('volunteer_calendar_apply');

        }
        $result = array(
            'code'          => '100' ,
        ) ;
        echo json_encode($result) ;
        exit ;
    }



    public function edit_course()
    {
        // get
        $cid    = $this->input->post('cid');
        $calr   = $this->db->where('id',$cid)->get('volunteer_calendar') ;
        if ( $calr ) {
            // 轉換
            $calr   = $calr->first_row('array') ;
            // 是否通知
            $send   = $this->input->post('send');
            // 空的
            $msg    = "" ;

            // 更新名稱 ?
            $courseName = $this->input->post('courseName'); 
            // 讀取更新名稱
            $course     = $this->db->where('id',$calr['courseID'])->get('course')->first_row('array') ;

            if ( isset($course['name']) && $courseName != $course['name'] ) {
                // 更新 時間
                $updataDate = array(
                    'name' => $courseName ,
                ) ;
                $this->db->where('id',$calr['courseID'])->update('course',$updataDate);
                // 語言
                $msg = "原定".$calr['date']." ，".$course['name']."，您報名的班務志工，班期已修改 課程名稱 為 ".$this->input->post('courseName')."，謝謝! " ;
            }

            // 是否通知
            if ( $send=='1' && $msg!='' ) {
                // 寫入
                $UserList = $this->db->where('calendarID',$cid)->where('got_it','1')->get('volunteer_calendar_apply')->result();
                // 處理時間
                $end_time   = $this->input->post('end_time'); 
                $tmp        = explode('T',$end_time ) ;
                $date       = $tmp[0] ;
                $end_time   = $tmp[1] ;
                // 個別通知
                foreach ($UserList as $User) {
                    $insert_data = array(
                        'user_id'   => $User->userID        ,
                        'msg'       => $msg                 ,
                        'exptime'   => $date." ".$end_time  ,
                    );
                    $this->db->insert('user_msg',$insert_data);
                }
                // 空的
                $msg    = "" ;
            }


            // 更新教室 ?
            // 教室還是無法修改
            // ---------------------------------------
            // 關聯太多
            // 非單一課程
            $classroomID    = $this->input->post('classroomID'); 
            $Room           = $this->db->where('id',$classroomID)->get('classroom')->first_row('array') ;

            // 找出原本的 vcID 組合 $calr->vcID
            $tmp            = $this->db->where('id',$calr['vcID'])->get('volunteer_classroom')->first_row('array') ;

            if ( isset($tmp['volunteerID']) ) {
                // 原本的分類：警衛？課程 ... 這類
                $volunteerID = $tmp['volunteerID'] ;

                // 找到要更改的 vcID
                $tmp            = $this->db->where('volunteerID',$volunteerID )
                                        ->where('classroomID',$classroomID)
                                        ->get('volunteer_classroom')->first_row('array') ;
                // 找到 vcID
                if ( isset($tmp['id']) ) {
                    $vcID = $tmp['id'] ;
                } else {
                    // 沒有就新增
                    $insert_data = array(
                        'volunteerID'       => $volunteerID                 ,
                        'classroomID'   => $classroomID ,
                    );
                    $this->db->insert('volunteer_classroom',$insert_data);

                    // vcID
                    $vcID = $this->db->insert_id();
                }

                // 有換 vcID 才需要通知
                if ( $calr['vcID'] != $vcID ) {
                    // 更新 $vcID
                    $updataDate = array(
                        'vcID' => $vcID ,
                    ) ;
                    $this->db->where('id',$cid)->update('volunteer_calendar',$updataDate);

                    // 語言
                    $msg = "原定".$calr['date']." ，".$course['name']."，您報名的志工，教室修改成為 ".$Room['name']."，謝謝! " ;
                }
            }
            // 是否通知
            if ( $send=='1' && $msg!='' ) {
                // 寫入
                $UserList = $this->db->where('calendarID',$cid)->where('got_it','1')->get('volunteer_calendar_apply')->result();
                // 處理時間
                $end_time   = $this->input->post('end_time'); 
                $tmp        = explode('T',$end_time ) ;
                $date       = $tmp[0] ;
                $end_time   = $tmp[1] ;
                // 個別通知
                foreach ($UserList as $User) {
                    $insert_data = array(
                        'user_id'   => $User->userID        ,
                        'msg'       => $msg                 ,
                        'exptime'   => $date." ".$end_time  ,
                    );
                    $this->db->insert('user_msg',$insert_data);
                }
                // 空的
                $msg    = "" ;
            }



            // 時間
            $start_time = $this->input->post('start_time'); 
            $end_time   = $this->input->post('end_time'); 
            if ( strtotime($start_time) >= strtotime($end_time) ) {
                $end_time = $start_time ;
            }
            // 處理時間
            $tmp = explode('T',$start_time ) ;
            $date       = $tmp[0] ;
            $start_time = $tmp[1] ;
            // 處理時間
            $tmp = explode('T',$end_time ) ;
            $date       = $tmp[0] ;
            $end_time = $tmp[1] ;

            // 只要有一個時間不對
            if ( $calr['date']!=$date || $calr['start_time']!=$start_time || $calr['end_time']!=$end_time ) {
                // 更新 時間
                $updataDate = array(
                    'date'          => $date        ,
                    'start_time'    => $start_time  ,
                    'end_time'      => $end_time    ,
                ) ;
                // status = 0
                $this->db->where('id',$cid)->update('volunteer_calendar',$updataDate);
                $msg = "原定".$calr['date']." ，".$this->input->post('courseName')."，您報名的班務志工，班期已修改日期為 ".$date."，時間為".$start_time."開始，".$end_time."結束 請留意服務日期，謝謝! " ;
                // 是否通知
                if ( $send=='1' && $msg!='' ) {
                    // 寫入
                    $UserList = $this->db->where('calendarID',$cid)->where('got_it','1')->get('volunteer_calendar_apply')->result();
                    // 個別通知
                    foreach ($UserList as $User) {
                        $insert_data = array(
                            'user_id'   => $User->userID        ,
                            'msg'       => $msg                 ,
                            'exptime'   => $date." ".$end_time  ,
                        );
                        $this->db->insert('user_msg',$insert_data);
                    }
                }
            }
            

            // 正確
            $result = array(
                'code'          => '100' ,
            ) ;

        } else {
            $result = array(
                'code'          => '200' ,
            ) ;
        }

        // echo
        echo json_encode($result) ;
        exit ;
    }

    public function subsidy_list()
    {
        $data['category'] = $this->volunteer_manage_model->get_volunteer_category_detail2();

        $this->load->view('volunteer_manage/subsidy_list', $data);
        $this->load->view('volunteer_manage/footer');
    }

    public function checkout_to_user()
    {
        // init
        $data               = array() ;
        // 設定
        //$data['change_url'] = "https://elearning.taipei/eda/apply/manage/checkout_to_user/" ;
        $applyUrl = $this->config->item('eda_apply_url');
        $data['change_url'] = "{$applyUrl}/manage/checkout_to_user/" ;
        // 輸出志工
        $data['userList']   = $this->db->where('role_id','20')->get('users')->result();

        // view
        $this->load->view('volunteer_manage/checkout_to_user' , $data );
        $this->load->view('volunteer_manage/footer');
    }
    
    public function user_list()
    {
        // 輸出志工
        $data['userList']   = $this->db->where('role_id','20')->get('users')->result();
        // view
        $this->load->view('volunteer_manage/user_list' , $data );
        $this->load->view('volunteer_manage/footer');
    }

    /**
     * 類別承辦人
     */
    public function manage_admin()
    {
        $this->load->model('Manager_model');
        $users = $this->Manager_model->getManagers(true);
        $nonAdmins = $this->Manager_model->getManagers(false);
        $data['users'] = $this->Manager_model->wrapManagers($users);
        $data['user_list'] = $nonAdmins;
        $data['category'] = $this->volunteer_manage_model->get_volunteer_category_detail2();
        $data['query_category'] = ! empty($data['category']) ? $data['category'] : array();
        $data['managerList'] = $this->db->where('role_id','20')->get('users')->result();
        $this->load->view('volunteer_manage/admin_list' , $data );
        $this->load->view('volunteer_manage/footer');
    }

    public function manage_admin_add()
    {
        $url = '"'.base_url('volunteer_manage/manage_admin').'"';
        $userID = intval($this->input->post('userID'));
        $category = $this->input->post('category');
        if(!$userID) {
            echo"<script>
                    alert('操作錯誤！請選擇會員！');
                    location.href = $url;
                </script>
            ";
        } else {
            if(!empty($category)) {
                $category_list = array();
                for($i=0;$i<count($category);$i++){
                    if($category[$i] != 'all'){
                        $category_list[] = intval($category[$i]);
                    } 
                }
                $evaluation_category = implode('|', $category_list);
                $this->db->where('id', $userID)->update('users',array('category_admin' => $evaluation_category));
                echo "<script>
                    alert('更新完成');
                    location.href = $url;
                </script>";
            } else {
                echo"<script>
                        alert('操作錯誤！請選擇類別！');
                        location.href = $url;
                    </script>
                ";
            }
        }
    }

    public function manage_admin_remove(){
        $userID = intval($this->input->post('userID'));
        //$url = '"'.base_url('volunteer_manage/manage_admin').'"';
        if(!$userID) {
            json_response(array('status'=>false,'msg'=>'操作錯誤，請返回上一步'));
        } else {
            $this->db->where('id',$userID)->update('users',array('category_admin' => null));
            json_response(array('status'=>true,'msg'=>'更新完成'));
        }
    }

    public function set_report_stage()
    {
        // init
        $data               = array() ;
        // 輸出
        $stageList          = $this->db->order_by('no','desc')->get('volunteer_stage') ;  //20210902 Roger 將排序最新的放最上面
        if ( $stageList ) {
            $data['stageList'] = $stageList->result();
        } else {
            $data['stageList'] = array() ;
        }

        $seq_number = intval($this->input->post('seq_number'));
        if($seq_number > 0){
            $data['detail'] = $this->volunteer_manage_model->get_volunteer_stage_detail($seq_number);
        }

        $data['save_url'] = base_url().'Volunteer_manage/set_report_stage';
             
        // view
        $this->load->view('volunteer_manage/set_report_stage',$data);
        $this->load->view('volunteer_manage/footer');
    }



    public function ajax_insert_report_stage()
    {
        // insert_data
        $insert_data = array(
            'category'      => intval($this->input->post('category'))      ,
            'startTime'     => addslashes($this->input->post('startTime'))      ,
            'endTime'       => addslashes($this->input->post('endTime'))        ,
            'reg_startTime' => addslashes($this->input->post('reg_startTime'))      ,
            'reg_endTime'   => addslashes($this->input->post('reg_endTime'))      ,
            'sum'           => intval($this->input->post('sum'))            ,
            'first'         => intval($this->input->post('first'))            ,
        );
        
        // check exists?
        if(isAjax() || isPost()) {
            $_post = array_map("htmlspecialchars", $this->input->post());
            $insert_data = array(
                'category'      => intval($_post['category']),
                'startTime'     => addslashes($_post['startTime']),
                'endTime'       => addslashes($_post['endTime']),
                'reg_startTime' => addslashes($_post['reg_startTime']),
                'reg_endTime'   => addslashes($_post['reg_endTime']),
                'sum'           => intval($_post['sum']),
                'first'         => intval($_post['first']),
            );
            // Difference between htmlspecialchars and addslashes.
            $exists = false;
            if (!isset($_post['confirmed'])) {
                $exists = $this->volunteer_manage_model->checkExistStage($_post['startTime'], $_post['endTime'], $_post['first']);
            }
            if ( !$exists) {
                $this->db->insert('volunteer_stage',$insert_data);
                // 正確
                $result = array(
                    'code'          => '100' ,
                    'message'       => '新增完成!'
                );
            } else {
                $result = array(
                    'code'          => '101' ,
                    'message'       => '"課程_開始時間"、"課程_結束時間"、"第一階段"等條件，資料已重覆，仍要繼續新增資料?'
                ) ;
            }
            //echo json_encode($result);
            //exit ;
            json_response($result);
        } else {
            die('error');
        }
    }


    public function ajax_delete_report_stage()
    {
        // 刪除
        $this->db->where('no',$this->input->post('no'))->delete('volunteer_stage');
        // 正確
        $result = array(
            'code'          => '100' ,
        ) ;
        // echo
        echo json_encode($result) ;
        exit ;
    }

    public function importdatabase()
    {
        $this->load->library('migration');
		if ($this->migration->latest() === FALSE) {
			echo $this->migration->error_string();
		}
		//$this->session->set_flashdata('success_msg', 'Database migrated successfully!');
		return redirect('/');
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
        $data['link_save'] = base_url('volunteer_manage/ban_users');
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
