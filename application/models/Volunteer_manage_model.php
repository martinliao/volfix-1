<?php

class Volunteer_manage_model extends MY_Model{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database('phy');
	}
	
	function get_volunteer_category_detail($id=0){
		if($id > 0){
			$this->db->where('id',$id);
		}
		
		$this->db->where('others',1);
        $this->db->where('show',1);
		$query = $this->db->get('volunteer_category');

		return $query->result();
	}

    function get_volunteer_category_detail2($id=0){  //20210830 Roger E F 選項用
		if($id > 0){
			$this->db->where('id',$id);
		}
		
		$this->db->where('others',1);
        //$this->db->where('show',1);
		$query = $this->db->get('volunteer_category');

		return $query->result();
	}

	function add_volunteer_category_detail($data){
		unset($data['id']);
		$data['create_time'] = time();
		$data['update_time'] = null;

        $data['morning_use'] = 1;
        $data['afternoon_use'] = 1;

        if($data['morning_start'] || $data['morning_end'])
            $data['morning_status'] = 1;

        if($data['afternoon_start'] || $data['afternoon_end'])
            $data['afternoon_status'] = 1;


        if($this->db->insert('volunteer_category',$data))
        {
            $vID = $this->db->insert_id();


            $this->db->insert('classroom',array('name'=>$data['name'].'志工工作區域'));

            $cID = $this->db->insert_id();

            $this->db->insert('volunteer_classroom',array(
                'volunteerID'=>$vID,
                'classroomID'=>$cID,
            ));

			return true;
		} 

		return false;
	}

	function upd_volunteer_category_detail($data){
		$id = $data['id'];
		unset($data['id']);
		$data['update_time'] = time();

        $data['morning_use'] = 1;
        $data['afternoon_use'] = 1;

        if($data['morning_start'] || $data['morning_end'])
            $data['morning_status'] = 1;

        if($data['afternoon_start'] || $data['afternoon_end'])
            $data['afternoon_status'] = 1;
        
		$this->db->where('id', $id);

		if($this->db->update('volunteer_category',$data)){
			return true;
		} 

		return false;
	}

	function del_volunteer_category_detail($id){
		if($this->db->delete('volunteer_category', array('id' => $id))){
            $this->db->delete('volunteer_classroom', array('volunteerid' => $id));

			return true;
		} 

		return false;
	}


	function get_course_detail($start_date,$end_date,$long_range=null){
		$this->db->where('start_date >= ',$start_date);
		$this->db->where('start_date <= ',$end_date);
        
        if(isset($long_range))
            $this->db->where('long_range',$long_range);

		$query = $this->db->get('course course');

		return $query->result();
	}

	function get_week_list($date){
        $week_index = array(
            'Sunday' =>0,
            'Monday' =>1,
            'Tuesday' =>2,
            'Wednesday' =>3,
            'Thursday' =>4,
            'Friday' =>5,
            'Saturday' =>6,
        );
        $date_index = $week_index[date('l',strtotime($date))];       

        
            $less = $date_index;
            $plus = (6-$date_index);
        // } else {
        //     $less = $date_index-1;
        //     $plus = (6-$date_index)-1;
        // }
        

        $week_list = array();
        for ($unix_time=strtotime($date.' -'.$less.' day'); $unix_time <= strtotime($date.' +'.$plus.' day'); $unix_time+=60*60*24)
        { 
            $key = date('w',$unix_time);
            $week_list[$key] = date('Y-m-d',$unix_time);
        }

        return $week_list;
    }

   function get_vc_list(){
        $volunteer_list = array();

        $select = array(
            'vc.id vcID',
            'vc.volunteerID vID',
            'vc.classroomID cID',
            'v.name volunteerName',
            'v.others',
            '(CASE WHEN v.others>0 THEN v.morning_start ELSE NULL END ) morning_start',
            '(CASE WHEN v.others>0 THEN v.morning_end ELSE NULL END ) morning_end',
            '(CASE WHEN v.others>0 THEN v.afternoon_start ELSE NULL END ) afternoon_start',
            '(CASE WHEN v.others>0 THEN v.afternoon_end ELSE NULL END ) afternoon_end',
            '(NULL) night_start',
            '(NULL) night_end',
            'v.sign_month',
            'classroom.name classroomName',
        );
        $volunteer_list = $this->db->select(implode(',',$select))
                                   ->from('volunteer_category v')
                                   ->join('volunteer_classroom vc','v.id = vc.volunteerID')
                                   ->join('classroom classroom','classroom.id = vc.classroomID')
                                   ->where('show',1)
                                   ->order_by('(CASE WHEN v.sort > 0 THEN 1 ELSE 0 END) DESC,v.sort asc,v.others ASC ,v.id,classroom.sort,classroom.name')
                                   ->get()
                                   ->result();
        $volunteer_list = $volunteer_list?$volunteer_list:array();

        $return= array();
        foreach($volunteer_list as $key => $each)
        {
            $return[$each->vID][$each->cID] = $each;            
        }

        return $return;
    }

    function checkExist($date){
        $this->db->select('count(1) cnt');
        $this->db->where('date',$date);
        $this->db->where('status',1);
        $query = $this->db->get('volunteer_calendar');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0){
            return true;
        }

        return false;
    }

    function get_calendar_list($start,$end,$long_range,$not_show=0){
        $calendar_list = array();
        // $long_range = $long_range?$long_range:null;

        $select = array(
            'calendar.id',
            'calendar.vcID',
            'vc.volunteerID',
            'vc.classroomID',
            'c.sname',
            'c.belongto',
            'calendar.date',
            'calendar.type',
            'calendar.start_time',
            'calendar.end_time',
            'calendar.status',
            'calendar.hours',
            'calendar.num_got_it',
            'calendar.num_waiting',
            'calendar.courseID',            
            'course.name courseName',
            'course.long_range',
            'course.worker',
            'course.term',
        );
        $this->db->select(implode(',',$select))
            	 ->from('volunteer_category v')
				 ->join('volunteer_classroom vc','v.id = vc.volunteerID')
                 ->join('classroom c','c.id = vc.classroomID')
				 ->join('volunteer_calendar calendar','calendar.vcID = vc.id')

				 // 僅班務志工要撈班期資訊  //2021-04-19加入join volunteer_apply_setting
				 ->join('course course','course.id = calendar.courseID AND v.others = 0 AND course.need != 0 ','left')
                 ->join('volunteer_apply_setting apply_setting','apply_setting.year = YEAR(calendar.date) AND apply_setting.month = MONTH(calendar.date) AND v.id = apply_setting.volunteerID','left')
				 ->where('calendar.date >=',$start)
				 ->where('(v.others = 1  OR ( v.others = 0 AND course.id IS NOT NULL))')
				 ->where('calendar.date <=',$end);

        if($not_show == '1'){
            $this->db->where('c.belongto != 68001 or c.belongto is null',null);
        }


		// if($long_range)
		// 	$this->db->where('course.long_range',1);
		// else
		// 	$this->db->where('(course.long_range = 0 OR course.long_range IS NULL)');
        $this->db->order_by('start_time'); 

                                  
		$calendar_list = $this->db->get()
								  ->result();

        $calendar_list = $calendar_list?$calendar_list:array();

        $return= array();
        foreach($calendar_list as $key => $each)
        {
            $return[$each->vcID][$each->date][$each->type] = $each;

        }

        return $return;

    }

    function get_all_apply_data($start,$end,$userID=null){

        $select = array(
            'calendar_apply.id',
            'calendar_apply.userID',
            'calendar_apply.calendarID',
            'calendar.vcID',
            'vc.volunteerID vID',
            'vc.classroomID cID',
            'v.name vName',
            'v.others',
            'v.content',
            'c.name cName',
            'calendar.date',
            'calendar.type',
            'calendar_apply.start_time',
            'calendar_apply.end_time',
            'calendar_apply.hours',
            'calendar_apply.got_it',
            'users.name userName',
            '(
              CASE
              WHEN v.id IN (\'3\',\'4\') AND calendar.person > 0
                   THEN calendar.person
              WHEN v.id = 1
                   THEN (SELECT person FROM course WHERE course.id = calendar.id)
              WHEN v.id IN (\'2\',\'5\')
                   THEN ROUND( ((SELECT sum_person FROM v_course_person_per_date_type WHERE v_course_person_per_date_type.date = calendar.date AND v_course_person_per_date_type.type = calendar.type) / v.person_division_by),\'0\') 
              ELSE 0 END
            ) person'
        );
        $order_by = array(
            'calendar.vcID asc',
            'calendar.date asc',
            'calendar.type asc',
            'calendar_apply.got_it desc',
            'calendar_apply.id asc',
        );
        $apply_data = $this->db->select(implode(',',$select))
                               ->from('volunteer_calendar_apply calendar_apply')
                               ->join('users','calendar_apply.userID = users.id')
                               ->join('volunteer_calendar calendar','calendar.id = calendar_apply.calendarID')
                               ->join('volunteer_classroom vc','calendar.vcID = vc.id')
                               ->join('volunteer_category v','vc.volunteerID = v.id')
                               ->join('classroom c','vc.classroomID = c.id')
                               ->where('calendar.date >=',$start)
                               ->where('calendar.date <=',$end)
                               ->order_by(implode(',',$order_by));
        if(isset($userID))
          $this->db->where('users.id',$userID);

        $apply_data = $this->db->get()
                               ->result();
        $return = array();
        foreach ($apply_data as $each_apply)
        {
            $each_apply->userName_enc = $each_apply->userName;
            // if(mb_strlen($each_apply->userName_enc,'UTF-8') > 2)
            // {
            //     // 除了前後兩字外都屏蔽
            //     $each_apply->userName_enc = mb_substr(($each_apply->userName),0,1,'UTF-8').str_pad('',mb_strlen($each_apply->userName_enc,'UTF-8')-2,'O').mb_substr(($each_apply->userName),-1,1,'UTF-8');
            // }
            // elseif (mb_strlen($each_apply->userName_enc,'UTF-8') == 2)
            // {
            //     $each_apply->userName_enc = mb_substr(($each_apply->userName),0,1,'UTF-8').'O';
            // }

            // $return[$each_apply->vcID][$each_apply->date][$each_apply->type][$each_apply->userID] = $each_apply;
            $return[$each_apply->calendarID][$each_apply->userID] = $each_apply;
        }

        return $return;
    }

    public function get_users_name()
    {
        $this->db->select("name");
        $this->db->from("users");  
        $query= $this->db->get();              
        return $query->result_array() ; 
    } 

    public function get_volunteer_category($id)
    {
        $this->db->select("name,id,special_note");
        $this->db->from("volunteer_category");  
        $this->db->where('id',$id);
        //$this->db->where('show',1);
        $query= $this->db->get();              
        return $query->row_array() ; 
    } 

    public function get_volunteer_stage_detail($seq_number){
        $this->db->select('*');
        $this->db->where('no', $seq_number);

        $query = $this->db->get('volunteer_stage');
        $data = $query->result_array();

        return $data;
    }

    public function get_volunteer_sign_report($name,$year,$month_start,$month_end,$category_id)
    {
        $start_date = ($year+1911).'-'.$month_start.'-01';
        $end_date = ($year+1911).'-'.$month_end.'-'.date('t',strtotime((($year+1911).'-'.$month_end.'-01')));

        $this->db->select("a.id,a.idno");
        $this->db->from("users a");
        $this->db->join("card_log b","a.idno = b.idno");
        $this->db->where('b.use_date >=',$start_date);
        $this->db->where('b.use_date <=',$end_date);    
        $this->db->where('a.name',$name);  
        $query = $this->db->get();              
        $userid = $query->result_array(); 

        if(empty($userid)){
            $nodata = array();
            return $nodata;
        }

        $this->db->select("b.date,b.start_time,b.end_time,b.hours,b.type,b.person person_other,c.volunteerID,d.year,d.class_no,d.term,d.name,d.worker,d.person");
        $this->db->from("volunteer_calendar_apply a");
        $this->db->join("volunteer_calendar b","a.calendarID = b.id");
        $this->db->join("volunteer_classroom c","b.vcID = c.id");
        $this->db->join("course d","b.courseID = d.id","left");
        $this->db->where('a.userID',$userid[0]['id']);
        $this->db->where('a.got_it',"1");
        $this->db->where_in('c.volunteerID',explode(',', $category_id));
        $this->db->where('b.date >=',$start_date);
        $this->db->where('b.date <=',$end_date);    
        $query = $this->db->get();
        $data = $query->result_array();

        if(!empty($data)){
            for($i=0;$i<count($data);$i++){
                $this->db->select("max(pass_time) max_time");
                $this->db->from("card_log");
                $this->db->where('idno',$userid[0]['idno']);    
                $this->db->where('use_date',$data[$i]['date']); 
                $this->db->where('category',$data[$i]['volunteerID']); 
                $this->db->where('type',$data[$i]['type']);
                $query = $this->db->get();              
                $max = $query->result_array();

                $data[$i]['max_time'] = $max[0]['max_time'];

                $this->db->select("min(pass_time) min_time");
                $this->db->from("card_log");
                $this->db->where('idno',$userid[0]['idno']);    
                $this->db->where('use_date',$data[$i]['date']);
                $this->db->where('category',$data[$i]['volunteerID']);
                $this->db->where('type',$data[$i]['type']);   
                $query = $this->db->get();              
                $min = $query->result_array();     

                $data[$i]['min_time'] = $min[0]['min_time'];

                if($data[$i]['volunteerID'] == '2'){
                    $this->db->select('person_division_by');
                    $this->db->from('volunteer_category');
                    $this->db->where('id',$data[$i]['volunteerID']);    

                    $query = $this->db->get();              
                    $result = $query->result_array();

                    $data[$i]['person_division_by'] = $result[0]['person_division_by'];
                }
            }
        }

        return $data; 
    } 

    function get_volunteer_traffic_report($year,$month,$category_id){
        $month_array = explode('-', $month);

        $start_date = date('Y-m-d',strtotime(($year+1911).'-'.$month_array[0].'-01'));
        $end_date = date('Y-m-d',strtotime(($year+1911).'-'.$month_array[1].'-'.date('t',strtotime((($year+1911).'-'.$month_array[1].'-01')))));

        $this->db->select("b.id,a.idno,b.name,b.email,b.address");
        $this->db->from("card_log a");
        $this->db->join("users b","a.idno = b.idno");
        $this->db->where("a.use_date >=",$start_date);    
        $this->db->where("a.use_date <=",$end_date);
        $this->db->where_in("a.category",explode(',', $category_id));
        $this->db->group_by("a.idno");  
        $query = $this->db->get();              
        $users = $query->result_array(); 

        for($i=0;$i<count($users);$i++){
            $z = 0;
            for ($j=$month_array[0];$j<=$month_array[1];$j++) { 
                $start_date = date('Y-m-d',strtotime(($year+1911).'-'.$j.'-01'));
                $end_date = date('Y-m-t',strtotime($start_date));
                
                $this->db->select("b.date,b.start_time,b.end_time,b.hours,b.type,c.volunteerID");
                $this->db->from("volunteer_calendar_apply a");
                $this->db->join("volunteer_calendar b","a.calendarID = b.id");
                $this->db->join("volunteer_classroom c","b.vcID = c.id");
                $this->db->where('a.userID',$users[$i]['id']);
                $this->db->where('a.got_it',"1");
                $this->db->where_in('c.volunteerID',explode(',', $category_id));
                $this->db->where('b.date >=',$start_date);
                $this->db->where('b.date <=',$end_date);    
                $query = $this->db->get();
                $data = $query->result_array();

               
                if(!empty($data)){
                    for($k=0;$k<count($data);$k++){
                        $this->db->select("max(pass_time) max_time");
                        $this->db->from("card_log");
                        $this->db->where('idno',$users[$i]['idno']);    
                        $this->db->where('use_date',$data[$k]['date']);
                        $this->db->where('category',$data[$k]['volunteerID']);
                        $this->db->where('type',$data[$k]['type']);     
                        $query = $this->db->get();              
                        $max = $query->result_array();

                        $data[$k]['max_time'] = $max[0]['max_time'];

                        $this->db->select("min(pass_time) min_time");
                        $this->db->from("card_log");
                        $this->db->where('idno',$users[$i]['idno']);    
                        $this->db->where('use_date',$data[$k]['date']); 
                        $this->db->where('category',$data[$k]['volunteerID']);
                        $this->db->where('type',$data[$k]['type']);    
                        $query = $this->db->get();              
                        $min = $query->result_array();     

                        $data[$k]['min_time'] = $min[0]['min_time'];
                    }
                }

                $z++;

                if($z == '1'){
                    $users[$i]['first'] = $data;
                 } else if($z == '2'){
                    $users[$i]['second'] = $data;
                 } else if($z == '3'){
                    $users[$i]['third'] = $data;
                 }
            }
        }    

        return $users;
    }
    
    function checkExistStage($startTime, $endTime, $first){
        $_row = $this->db->where('startTime', $startTime)
                    ->where('endTime', $endTime)
                    ->where('first', $first)
                    ->get('volunteer_stage')
                    ->row();
        return (isset($_row));
    }
}
