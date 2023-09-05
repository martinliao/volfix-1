<?php

class Volunteer_select_model extends MY_Model{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database('phy');
	}
	
	function get_course_detail($start_date,$end_date,$class_no,$class_name){
		$this->db->where('start_date >= ',$start_date);
		$this->db->where('start_date <= ',$end_date);

        if(!empty($class_no)){
            $this->db->where('class_no',$class_no);
        }

        if(!empty($class_name)){
            $this->db->like('name', $class_name); 
        }

		$query = $this->db->get('course');

		return $query->result();
	}

	function get_course_detail_by_id($id){
		$this->db->where('id',$id);

		$query = $this->db->get('course');

		return $query->result();
	}

	function get_course_date_list($cid){
		$this->db->select('date');
		$this->db->where('courseid',$cid);
		$this->db->where('status','1');
		$this->db->group_by("date"); 
		$query = $this->db->get('volunteer_calendar');

		return $query->result();
	}	

	function get_volunteer_calendar_detail_by_id($cid,$date,$type,$aid=''){
		$this->db->select('a.id,a.num_got_it,a.num_waiting,b.id as aid,b.got_it,b.start_time,b.end_time,c.name,c.email');
		$this->db->from('volunteer_calendar a');
		$this->db->join('volunteer_calendar_apply b','a.id = b.calendarid');
		$this->db->join('users c','c.id = b.userid');
		$this->db->where('a.courseid',$cid);
		$this->db->where('a.status','1');
		$this->db->where('a.type',$type);
		$this->db->where('a.date',$date);
		if(!empty($aid)){
			$this->db->where('b.id',$aid);
		}
		
		$query = $this->db->get();

		return $query->result();
	}

	function get_person_limit($cid,$date,$type){
		$this->db->select('id,num_got_it,num_waiting');
		$this->db->from('volunteer_calendar');
		$this->db->where('courseid',$cid);
		$this->db->where('status','1');
		$this->db->where('type',$type);
		$this->db->where('date',$date);

		$query = $this->db->get();

		return $query->result();
	}

	function upd_volunteer_calendar($vid,$num_got_it){
		$this->db->set('num_got_it', $num_got_it);
		$this->db->set('num_waiting', ($num_got_it*0));
		$this->db->where('id',$vid);
		$this->db->update('volunteer_calendar'); 

		return true;
	}

	function upd_volunteer_calendar_apply($aid,$cid,$date,$type){
		$this->db->select('got_it');
		$this->db->from('volunteer_calendar_apply');
		$this->db->where('id',$aid); 
		$query = $this->db->get();
		$check = $query->result();

		$mail = array();

		if($check[0]->got_it == '1'){
			return $mail;
		}

		$course = $this->get_course_detail_by_id($cid);
		$detail = $this->get_volunteer_calendar_detail_by_id($cid,$date,$type,$aid);

		$this->db->set('got_it', '1');
		$this->db->where('id',$aid); 

		if($this->db->update('volunteer_calendar_apply')){
			$this->db->set('category', '班務');
			$this->db->set('year', $course[0]->year);
			$this->db->set('class_no', $course[0]->class_no);
			$this->db->set('term', $course[0]->term);
			$this->db->set('course_name', $course[0]->name);
			$this->db->set('firstname', $detail[0]->name);
			$this->db->set('course_date', $date);

			if($type == '1'){
				$type_name = '早上';
			} else if($type == '2'){
				$type_name = '下午';
			} else if($type == '3') {
				$type_name = '晚上';
			}

			$this->db->set('type', $type_name);
			$this->db->set('action', '錄取');
			$this->db->set('modifytime',date('Y-m-d H:i:s'));

			$this->db->insert('log');

			$date = (date('Y',strtotime($date))-1911).'年'.date('m',strtotime($date)).'月'.date('d',strtotime($date)).'日';
			$title = '提醒晉升公訓處志工正取-'.$date.'(班務志工)'; 
			$body = 'Dear '.$detail[0]->name.' 先生/小姐您好:<br>
					感謝您支持:臺北市政府公務人員訓練處志工隊之志願服務，<br>
					有關您選填:'.$date.' '.$detail[0]->start_time.'~'.$detail[0]->end_time.'<br>
					班期名稱:'.$course[0]->name.'<br>
					原為候補第一順位，因正取人員取消，<br>
					<font color="red">已晉升為正取!</font>屆時請您如期支援該班期，特來信通知，萬分感謝!!';

			$mail = array(
						'title' => $title,
						'body' => $body,
						'email' => $detail[0]->email,
				);
		}

		return $mail;
	}

	function upd_volunteer_calendar_apply_others($aid,$vid,$date,$type){
		$this->db->select('got_it,userid,start_time,end_time');
		$this->db->from('volunteer_calendar_apply');
		$this->db->where('id',$aid); 
		$query = $this->db->get();
		$check = $query->result();

		$mail = array();
		if($check[0]->got_it == '1'){
			return $mail;
		}

		$this->db->set('got_it', '1');
		$this->db->where('id',$aid);

		if($this->db->update('volunteer_calendar_apply')){
			$this->db->select('name');
			$this->db->from('volunteer_category');
			$this->db->where('id',$vid);
			$result = $this->db->get();
			$category_name = $result->row();

			$this->db->select('name,email');
			$this->db->from('users');
			$this->db->where('id',$check[0]->userid);
			$result = $this->db->get();
			$firstname = $result->result();

			if($type == '1'){
				$type_name = '早上';
			} else if($type == '2'){
				$type_name = '下午';
			}

			$this->db->set('category', $category_name->name);
			$this->db->set('firstname', $firstname[0]->name);
			$this->db->set('course_date', date('Y-m-d',$date));
			$this->db->set('type', $type_name);
			$this->db->set('action', '錄取');
			$this->db->set('modifytime',date('Y-m-d H:i:s'));
			$this->db->insert('log');

			$date = (date('Y',$date)-1911).'年'.date('m',$date).'月'.date('d',$date).'日';
			$title = '提醒晉升公訓處志工正取-'.$date.'('.$category_name->name.'志工)'; 
			$body = 'Dear '.$firstname[0]->name.' 先生/小姐您好:<br>
					感謝您支持:臺北市政府公務人員訓練處志工隊之志願服務，<br>
					有關您選填:'.$date.' '.$check[0]->start_time.'~'.$check[0]->end_time.'<br>
					原為候補第一順位，因正取人員取消，<br>
					<font color="red">已晉升為正取!</font>屆時請您如期支援該班期，特來信通知，萬分感謝!!';

			$mail = array(
						'title' => $title,
						'body' => $body,
						'email' => $firstname[0]->email,
				);

		}

		return $mail;
	}

	function del_volunteer_calendar_apply($aid,$cid,$date,$type){
		$course = $this->get_course_detail_by_id($cid);
		$detail = $this->get_volunteer_calendar_detail_by_id($cid,$date,$type,$aid);

		$mail = array();
		if($this->db->delete('volunteer_calendar_apply', array('id' => $aid))){
			$this->db->set('category', '班務');
			$this->db->set('year', $course[0]->year);
			$this->db->set('class_no', $course[0]->class_no);
			$this->db->set('term', $course[0]->term);
			$this->db->set('course_name', $course[0]->name);
			$this->db->set('firstname', $detail[0]->name);
			$this->db->set('course_date', $date);

			if($type == '1'){
				$type_name = '早上';
			} else if($type == '2'){
				$type_name = '下午';
			} else if($type == '3') {
				$type_name = '晚上';
			}

			$this->db->set('type', $type_name);
			$this->db->set('action', '取消');
			$this->db->set('modifytime',date('Y-m-d H:i:s'));

			$this->db->insert('log');

			$date = (date('Y',strtotime($date))-1911).'年'.date('m',strtotime($date)).'月'.date('d',strtotime($date)).'日';
			$title = '提醒取消公訓處志工正取-'.$date.'(班務志工)'; 
			$body = 'Dear '.$detail[0]->name.' 先生/小姐您好:<br>
					感謝您支持:臺北市政府公務人員訓練處志工隊之志願服務，<br>
					有關您選填:'.$date.' '.$detail[0]->start_time.'~'.$detail[0]->end_time.'<br>
					班期名稱:'.$course[0]->name.'<br>
					原為正取人員，因上述志工管理者已為您取消報名，<br>
					故原定報名服務班次已取消!，特來信通知，萬分感謝!!';

			$mail = array(
						'title' => $title,
						'body' => $body,
						'email' => $detail[0]->email,
				);
		}

		return $mail;
	}

	function del_volunteer_calendar_apply_others($aid,$vid,$date,$type){
		$this->db->select('userid,start_time,end_time');
		$this->db->from('volunteer_calendar_apply');
		$this->db->where('id',$aid); 
		$query = $this->db->get();
		$check = $query->result();

		$mail = array();
		if($this->db->delete('volunteer_calendar_apply', array('id' => $aid))){
			$this->db->select('name');
			$this->db->from('volunteer_category');
			$this->db->where('id',$vid);
			$result = $this->db->get();
			$category_name = $result->row();

			$this->db->select('name,email');
			$this->db->from('users');
			$this->db->where('id',$check[0]->userid);
			$result = $this->db->get();
			$firstname = $result->result();

			if($type == '1'){
				$type_name = '早上';
			} else if($type == '2'){
				$type_name = '下午';
			}

			$this->db->set('category', $category_name->name);
			$this->db->set('firstname', $firstname[0]->name);
			$this->db->set('course_date', date('Y-m-d',$date));
			$this->db->set('type', $type_name);
			$this->db->set('action', '取消');
			$this->db->set('modifytime',date('Y-m-d H:i:s'));
			$this->db->insert('log');

			$date = (date('Y',$date)-1911).'年'.date('m',$date).'月'.date('d',$date).'日';
			$title = '提醒取消公訓處志工正取-'.$date.'('.$category_name->name.'志工)'; 
			$body = 'Dear '.$firstname[0]->name.' 先生/小姐您好:<br>
					感謝您支持:臺北市政府公務人員訓練處志工隊之志願服務，<br>
					有關您選填:'.$date.' '.$check[0]->start_time.'~'.$check[0]->end_time.'<br>
					原為正取人員，因上述志工管理者已為您取消報名，<br>
					故原定報名服務班次已取消!，特來信通知，萬分感謝!!';

			$mail = array(
						'title' => $title,
						'body' => $body,
						'email' => $firstname[0]->email,
				);
		}

		return $mail;
	}

	function makeup_volunteer_calendar_apply($vid){
		$this->db->select('num_got_it,courseid,date');
		$this->db->from('volunteer_calendar');
		$this->db->where('id',$vid);
		$query = $this->db->get();
		$data1 = $query->result();

		$this->db->select('count(1) cnt');
		$this->db->from('volunteer_calendar_apply');
		$this->db->where('calendarid',$vid);
		$this->db->where('got_it','1');
		$query2 = $this->db->get();
		$data2 = $query2->result();
		$mail = array();
		if($data1[0]->num_got_it == $data2[0]->cnt){
			return $mail;
		} else {
			$this->db->select('id,userid,start_time,end_time');
			$this->db->from('volunteer_calendar_apply');
			$this->db->where('calendarid',$vid);
			$this->db->where('got_it','0');
			$this->db->order_by('id','asc');
			$query3 = $this->db->get();
			$data3 = $query3->result();

			$k = $data1[0]->num_got_it - $data2[0]->cnt;

			for($i=0;$i<$k;$i++){
				if(isset($data3[$i]->id) && $data3[$i]->id > 0){
					$this->db->set('got_it', '1');
					$this->db->where('id',$data3[$i]->id);
					
					if($this->db->update('volunteer_calendar_apply')){
						$this->db->select('name,email');
						$this->db->from('users');
						$this->db->where('id',$data3[0]->userid);
						$result = $this->db->get();
						$firstname = $result->result();

						$course = $this->get_course_detail_by_id($data1[0]->courseid);

						$date = (date('Y',strtotime($data1[0]->date))-1911).'年'.date('m',strtotime($data1[0]->date)).'月'.date('d',strtotime($data1[0]->date)).'日';
						$title = '提醒晉升公訓處志工正取-'.$date.'(班務志工)'; 
						$body = 'Dear '.$firstname[0]->name.' 先生/小姐您好:<br>
								感謝您支持:臺北市政府公務人員訓練處志工隊之志願服務，<br>
								有關您選填:'.$date.' '.$data3[0]->start_time.'~'.$data3[0]->end_time.'<br>
								班期名稱:'.$course[0]->name.'<br>
								原為候補第一順位，因正取人員取消，<br>
								<font color="red">已晉升為正取!</font>屆時請您如期支援該班期，特來信通知，萬分感謝!!';

						$mail_tmp = array(
									'title' => $title,
									'body' => $body,
									'email' => $firstname[0]->email,
							);

						array_push($mail, $mail_tmp);
						unset($mail_tmp);
					}
				}
			}

			return $mail;
		}

	}

	function makeup_volunteer_calendar_apply_other($vid,$category_id){
		$this->db->select('num_got_it,date');
		$this->db->from('volunteer_calendar');
		$this->db->where('id',$vid);
		$query = $this->db->get();
		$data1 = $query->result();

		$this->db->select('count(1) cnt');
		$this->db->from('volunteer_calendar_apply');
		$this->db->where('calendarid',$vid);
		$this->db->where('got_it','1');
		$query2 = $this->db->get();
		$data2 = $query2->result();
		$mail = array();
		if($data1[0]->num_got_it == $data2[0]->cnt){
			return $mail;
		} else {
			$this->db->select('id,userid,start_time,end_time');
			$this->db->from('volunteer_calendar_apply');
			$this->db->where('calendarid',$vid);
			$this->db->where('got_it','0');
			$this->db->order_by('id','asc');
			$query3 = $this->db->get();
			$data3 = $query3->result();

			$k = $data1[0]->num_got_it - $data2[0]->cnt;

			for($i=0;$i<$k;$i++){
				if(isset($data3[$i]->id) && $data3[$i]->id > 0){
					$this->db->set('got_it', '1');
					$this->db->where('id',$data3[$i]->id);
					if($this->db->update('volunteer_calendar_apply')){
						$this->db->select('name');
						$this->db->from('volunteer_category');
						$this->db->where('id',$category_id);
						$result = $this->db->get();
						$category_name = $result->row();

						$this->db->select('name,email');
						$this->db->from('users');
						$this->db->where('id',$data3[0]->userid);
						$result = $this->db->get();
						$firstname = $result->result();

						$date = (date('Y',strtotime($data1[0]->date))-1911).'年'.date('m',strtotime($data1[0]->date)).'月'.date('d',strtotime($data1[0]->date)).'日';
						$title = '提醒晉升公訓處志工正取-'.$date.'('.$category_name->name.'志工)'; 
						$body = 'Dear '.$firstname[0]->name.' 先生/小姐您好:<br>
								感謝您支持:臺北市政府公務人員訓練處志工隊之志願服務，<br>
								有關您選填:'.$date.' '.$data3[0]->start_time.'~'.$data3[0]->end_time.'<br>
								原為候補第一順位，因正取人員取消，<br>
								<font color="red">已晉升為正取!</font>屆時請您如期支援該班期，特來信通知，萬分感謝!!';

						$mail_tmp = array(
									'title' => $title,
									'body' => $body,
									'email' => $firstname[0]->email,
							);

						array_push($mail, $mail_tmp);
						unset($mail_tmp);
					}
				}
			}

			return $mail;
		}

	}

	function get_log($start_date, $end_date, $timesup = false) {
		$this->db->where('course_date >= ',$start_date);
		$this->db->where('course_date < ',$end_date);
		if ($timesup) {
			$this->db->where('timesup', 1);
		}
		$query = $this->db->get('log');	
		return $query->result();
	}

	function get_volunteer_category($vcid){
		$volunteer_data = $this->db->where('id',$vcid)
                                   ->where('others',1)
                                   ->get('volunteer_category')
                                   ->row();

        return $volunteer_data;              
	}

	function get_card_log_detail($start_date,$end_date,$name,$category){
		$this->db->select('a.*,b.name');
		$this->db->from('card_log a');
		$this->db->join('volunteer_category b','a.category = b.id');
		$this->db->where('use_date >= ',$start_date);
		$this->db->where('use_date <= ',$end_date);

		if(!empty($name)){
			$this->db->where('firstname',$name);
		}

		if(!empty($category)){
			$all = false;
			$category_list = '';

			for($i=0;$i<count($category);$i++){
				if($category[$i] == 'all'){
					$all = true;
					break;
				} else {
					$category_list .= $category[$i].',';
				}
			}

			if(!$all){
				$category_list = substr($category_list, 0,-1);
				$this->db->where_in('a.category',$category_list);
			} 
		} 

		$this->db->order_by('a.machine_id','asc');
		$this->db->order_by('a.use_date','asc');
		$this->db->order_by('a.idno','asc');
		$this->db->order_by('a.pass_time','asc');
		$query = $this->db->get();

		return $query->result_array();
	}

	function get_sign_log_list($start_date,$end_date,$name,$category){
		$where = '';
		if(!empty($start_date) && !empty($end_date)){
			// $start_date = $start_date.' 00:00:00';
			// $end_date = $end_date.' 23:59:59';
			// $where .= sprintf(" AND sign_log.sign_time BETWEEN '%s' AND '%s' ",addslashes($start_date),addslashes($end_date));
			$where .= sprintf(" AND volunteer_calendar.date BETWEEN '%s' AND '%s' ",addslashes($start_date),addslashes($end_date));
		}

		if(!empty($name)){
			$where .= sprintf(" AND users.name = '%s' ",addslashes(trim($name))); 
		}

		if(!empty($category)){
			$all = false;
			$category_list = '';

			for($i=0;$i<count($category);$i++){
				if($category[$i] == 'all'){
					$all = true;
					break;
				} else {
					$category_list .= addslashes($category[$i]).',';
				}
			}

			if(!$all){
				$category_list = substr($category_list, 0,-1);
				$where .= sprintf(" AND volunteer_classroom.volunteerID in (%s) ",$category_list); 
			} 
		} 

		$sql = sprintf("SELECT 
							users.idNo as userIdNo, volunteer_calendar.date,
							sign_log.idno,
							sign_log.status,
							DATE_FORMAT( sign_log.sign_time, '%%Y-%%m-%%d' ) AS sign_date,
							DATE_FORMAT( sign_log.sign_time, '%%H:%%i:%%s' ) AS sign_time,
							users.`name`,
							users.`id` as uid,
							volunteer_classroom.volunteerID,
							volunteer_category.`name` AS category_name,
							volunteer_calendar.type,
							CASE
								volunteer_classroom.volunteerID 
							WHEN '1' THEN
								volunteer_calendar.hours 
							ELSE
								CASE
									volunteer_calendar.type 
								WHEN '1' THEN
									(
										round(TIME_TO_SEC(TIMEDIFF(volunteer_category.morning_end,volunteer_category.morning_start))/3600)
									)
								WHEN '2' THEN
									(
										round(TIME_TO_SEC(TIMEDIFF(volunteer_category.afternoon_end,volunteer_category.afternoon_start))/3600)
									)
								END 
							END hours
						FROM
							volunteer_calendar_apply
							JOIN volunteer_calendar ON volunteer_calendar_apply.calendarID = volunteer_calendar.id 
							JOIN users ON users.id = volunteer_calendar_apply.userID
							JOIN volunteer_classroom ON volunteer_calendar.vcID = volunteer_classroom.id
							JOIN volunteer_category ON volunteer_classroom.volunteerID = volunteer_category.id 
							LEFT Join sign_log ON users.idNo = sign_log.idno AND DATE_FORMAT( sign_log.sign_time, '%%Y-%%m-%%d' ) = volunteer_calendar.date
						WHERE
							users.role_id = 20 
							AND volunteer_calendar_apply.got_it = 1 
							%s
						GROUP BY
							sign_log.idno,
							sign_log.sign_time,
							volunteer_calendar.date,
							volunteer_calendar.type 
						ORDER BY
							sign_log.sign_time,
							sign_log.idno",$where);
		$query = $this->db->query($sql);

		return $query->result_array();
	}

	function add_card_log($id,$hour,$minute,$second){
		$this->db->where('id',$id);
		$query = $this->db->get('card_log');
		$result = $query->result();

		if(!empty($result)){
			if(intval($hour) < 10){
				$hour = '0'.$hour;
			}

			if(intval($minute) < 10){
				$minute = '0'.$minute;
			}

			if(intval($second) < 10){
				$second = '0'.$second;
			}

			$this->db->set('category',$result[0]->category);
			$this->db->set('type',$result[0]->type);
			$this->db->set('idno',$result[0]->idno);
			$this->db->set('use_date',$result[0]->use_date);
			$this->db->set('firstname',$result[0]->firstname);
			$this->db->set('machine_id',$result[0]->machine_id);
			$this->db->set('pass_time',$hour.$minute.$second);

			if($this->db->insert('card_log')){
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function add_card_log_new($idno,$hour,$minute,$second,$sign_date){
		if(intval($hour) < 10){
			$hour = '0'.$hour;
		}

		if(intval($minute) < 10){
			$minute = '0'.$minute;
		}

		if(intval($second) < 10){
			$second = '0'.$second;
		}

		$sign_time = $sign_date.' '.$hour.':'.$minute.':'.$second;
		
		$this->db->set('idno',$idno);
		$this->db->set('sign_time',$sign_time);
		$this->db->set('status','Y');

		if($this->db->insert('sign_log')){
			return true;
		} else {
			return false;
		}
	}

	function get_person_limit_others($vid,$type,$date){
		$this->db->select('id');
		$this->db->from('volunteer_classroom');
		$this->db->where('volunteerID',$vid);
		$result = $this->db->get();
		$category_id = $result->row();

		$this->db->select('id,num_got_it,num_waiting');
		$this->db->from('volunteer_calendar');
		$this->db->where('vcID',$category_id->id);
		$this->db->where('status','1');
		$this->db->where('type',$type);
		$this->db->where('date',date('Y-m-d',$date));
		$query = $this->db->get();
	
		return $query->result();
	}

	function get_volunteer_calendar_others_detail($vid,$type,$date){
		$date = date('Y-m-d',$date);
		$sql = sprintf("SELECT
							a.id,
							a.num_got_it,
							a.num_waiting,
							d.`name`,
							c.got_it,
							c.id as aid
						FROM
							volunteer_calendar a
						JOIN volunteer_classroom b ON a.vcID = b.id
						JOIN volunteer_calendar_apply c ON c.calendarID = a.id
						JOIN users d ON c.userid = d.id
						WHERE
							a.type = '%s'
						AND a.date = '%s'
						AND b.volunteerID = '%s'",addslashes($type),addslashes($date),addslashes($vid));
		
		$query = $this->db->query($sql);
		
		return $query->result();
	}

	function upd_volunteer_calendar_batch($vcid,$first_day,$last_day,$day,$type,$num_got_it){
		$this->db->select('id');
		$this->db->from('volunteer_classroom');
		$this->db->where('volunteerID',$vcid);
		$result = $this->db->get();
		$category_id = $result->row();

		if($category_id->id > 0 && $category_id->id != '1'){
			$this->db->where('vcid',$category_id->id);
			$this->db->where('day',$day);
			$this->db->where('type',$type);
			$this->db->where('date >=',$first_day);
			$this->db->where('date <=',$last_day);
			$this->db->set('num_got_it',$num_got_it);
			$this->db->set('num_waiting',($num_got_it*0));
			$this->db->update('volunteer_calendar');

			return true;
		}
		
		return false;
	}

	public function getVolunteerList($key){
		$this->db->select('name');
		$this->db->like('name', $key, 'after');
		$query = $this->db->get('users');
		$data = $query->result_array();

		return $data;
	}

	public function getVolunteerIdno($uid){
		$this->db->select('idNo');
		$this->db->where('id',$uid);
		$query = $this->db->get('users');
		$data = $query->result_array();

		if(!empty($data)){
			return $data[0]['idNo'];
		}

		return '';
	}

	public function getSignDetail($course_id, $sign_date, $sign_time){
		$this->db->select('id, hours, start_time, end_time');
		$this->db->where('courseID', $course_id);
		$this->db->where('date', $sign_date);
		$this->db->where('type', $sign_time);
		$query = $this->db->get('volunteer_calendar');

		$data = $query->result_array();

		return $data;
	}

	public function sign($user_id, $id, $hours, $start_time, $end_time){
		$this->db->set('userid', $user_id);
		$this->db->set('calendarId', $id);
		$this->db->set('start_time', $start_time);
		$this->db->set('end_time', $end_time);
		$this->db->set('hours', $hours);
		$this->db->set('got_it', 1);
		$this->db->set('buildTime', date('Y-m-d H:i:s'));

		if($this->db->insert('volunteer_calendar_apply')){
			return true;
		}

		return false;
	}

	public function get_num_got_it($vcid,$first_day,$last_day){
		$this->db->select('id');
		$this->db->from('volunteer_classroom');
		$this->db->where('volunteerID',$vcid);
		$result = $this->db->get();
		$category_id = $result->row();

		$data = array();
		if($category_id->id > 0 && $category_id->id != '1'){
			$this->db->select('date, type, num_got_it');
			$this->db->where('vcid', $category_id->id);
			$this->db->where('date >=', $first_day);
			$this->db->where('date <=', $last_day);
			$this->db->where('status', 1);
			$this->db->order_by('date, type');

			$query = $this->db->get('volunteer_calendar');
			$data = $query->result_array();
		}

		return $data;
	}

	public function batchUndertakerStatus($year, $helf, $category){
		$this->db->set('undertaker_status', 1);
		$this->db->where('year', $year);
		$this->db->where('helf', $helf);
		$this->db->where('undertaker_top_grade is not null', null);
		$this->db->where('undertaker_bottom_grade is not null', null);
		$this->db->where('undertaker_status', 0);

		if(!empty($category)){
			$all = false;
			$category_list = array();

			for($i=0;$i<count($category);$i++){
				if($category[$i] == 'all'){
					$all = true;
					break;
				} else {
					$category_list[] = intval($category[$i]);
				}
			}

			if(!$all){
				$this->db->where_in('category', $category_list);
			} 
		} 

		if( $this->db->update('self_evaluation')){
            return true;
        }

        return false;
	}

	public function batchUndertakerStatusSpecial($year, $helf, $category){
		$this->db->set('undertaker_status', 1);
		$this->db->set('undertaker_top_grade', 'top_grade', false);
		$this->db->set('undertaker_bottom_grade', 'bottom_grade', false);
		$this->db->where('year', $year);
		$this->db->where('helf', $helf);
		$this->db->where('undertaker_status', 0);

		if(!empty($category)){
			$all = false;
			$category_list = array();

			for($i=0;$i<count($category);$i++){
				if($category[$i] == 'all'){
					$all = true;
					break;
				} else {
					$category_list[] = intval($category[$i]);
				}
			}

			if(!$all){
				$this->db->where_in('category', $category_list);
			} 
		} 

		if( $this->db->update('self_evaluation')){
            return true;
        }

        return false;
	}

	public function getUserApplyVolunteerCategory($start_date, $end_date, $category, $user_name, $year, $helf){
        $this->db->select('volunteer_category.id as category_id, volunteer_category.name as category_name, users.id as uid, users.name as user_name, self_evaluation.id seid, self_evaluation.top_grade, self_evaluation.bottom_grade, self_evaluation.undertaker_top_grade, self_evaluation.undertaker_bottom_grade, self_evaluation.leader_top_grade, self_evaluation.leader_bottom_grade, self_evaluation.status, self_evaluation.undertaker_status, self_evaluation.again, user_signature.signature');
        $this->db->from('volunteer_calendar_apply');
        $this->db->join('volunteer_calendar', 'volunteer_calendar_apply.calendarID = volunteer_calendar.id');
        $this->db->join('volunteer_classroom', 'volunteer_calendar.vcID = volunteer_classroom.id');
        $this->db->join('volunteer_category', 'volunteer_classroom.volunteerID = volunteer_category.id');
		$this->db->join('users', 'volunteer_calendar_apply.userID = users.id');
		$this->db->join('sign_log', "DATE_FORMAT( sign_log.sign_time, '%Y-%m-%d' ) = volunteer_calendar.date and users.idNo = sign_log.idno");
		$this->db->join('user_signature', 'volunteer_calendar_apply.userID = user_signature.user_id');
		$this->db->join("self_evaluation", "self_evaluation.status = 1 and self_evaluation.year = $year and self_evaluation.helf = $helf and volunteer_calendar_apply.userID = self_evaluation.uid and volunteer_category.id = self_evaluation.category");
        $this->db->where('volunteer_calendar.date >=', $start_date);
        $this->db->where('volunteer_calendar.date <=', $end_date);

		if(!empty($user_name)){
			$this->db->where('users.name', $user_name);
		}

		if(!empty($category)){
			$all = false;
			$category_list = array();

			for($i=0;$i<count($category);$i++){
				if($category[$i] == 'all'){
					$all = true;
					break;
				} else {
					$category_list[] = intval($category[$i]);
				}
			}

			if(!$all){
				$this->db->where_in('volunteer_category.id', $category_list);
			} 
		} 

        $this->db->group_by('users.id,volunteer_category.id');
		$this->db->group_by('volunteer_category.id,users.name');

        $query = $this->db->get();
        $result = $query->result_array();
		
        return $result;
    }

	public function check_self_evaluation($year, $helf, $category, $uid){
        $this->db->select('count(1) cnt');
        $this->db->where('year', $year);
        $this->db->where('helf', $helf);
        $this->db->where('category', $category);
        $this->db->where('uid', $uid);
        
        $query = $this->db->get('self_evaluation');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0){
            return true;
        }
        
        return false;
    }

	public function get_self_evaluation($year, $helf, $category, $uid){
        $this->db->select('*');
        $this->db->where('year', $year);
        $this->db->where('helf', $helf);
        $this->db->where('category', $category);
        $this->db->where('uid', $uid);
        
        $query = $this->db->get('self_evaluation');
        $result = $query->result_array();

        return $result;
    }

	public function insert_self_evaluation($type, $year, $helf, $category, $grade, $uid){
		if($type == '1'){
			$this->db->set('undertaker_top_grade', intval($grade));
		} else if($type == '2'){
			$this->db->set('undertaker_bottom_grade', intval($grade));
		} else if($type == '3'){
			$this->db->set('leader_top_grade', intval($grade));
		} else if($type == '4'){
			$this->db->set('leader_bottom_grade', intval($grade));
		} else if($type == '5'){
			$this->db->set('undertaker_status', 1);
		} else if($type == '6'){
			$this->db->set('status', '0');
		} else {
			return false;
		}

        $this->db->set('year', $year);
        $this->db->set('helf', $helf);
        $this->db->set('category', $category);
        $this->db->set('uid', $uid);
        $this->db->set('create_time', date('Y-m-d H:i:s'));
        $this->db->set('modify_time', date('Y-m-d H:i:s'));

		if( $this->db->insert('self_evaluation')){
            return true;
        }

        return false;
	}

	public function update_self_evaluation($type, $year, $helf, $category, $grade, $uid){
		if($type == '1'){
			$this->db->set('undertaker_top_grade', intval($grade));
		} else if($type == '2'){
			$this->db->set('undertaker_bottom_grade', intval($grade));
		} else if($type == '3'){
			$this->db->set('leader_top_grade', intval($grade));
		} else if($type == '4'){
			$this->db->set('leader_bottom_grade', intval($grade));
		} else if($type == '5'){
			$this->db->set('undertaker_status', 1);
		} else if($type == '6'){
			$this->db->set('status', '0');
		} else if($type == '7'){
			$this->db->set('undertaker_status', '0');
		} else {
			return false;
		}

        $this->db->set('modify_time', date('Y-m-d H:i:s'));

		$this->db->where('uid', $uid);
        $this->db->where('year', $year);
        $this->db->where('helf', $helf);
        $this->db->where('category', $category);
       
        if($this->db->update('self_evaluation')){
            return true;
        }

        return false;
	}

	public function getEvaluationDetail($seid){
		$this->db->select('self_evaluation.*, users.name user_name, volunteer_category.name category_name, user_signature.signature');
		$this->db->from('self_evaluation');
		$this->db->join('users', 'self_evaluation.uid = users.id');
		$this->db->join('volunteer_category', 'self_evaluation.category = volunteer_category.id');
		$this->db->join('user_signature', 'self_evaluation.uid = user_signature.user_id');
		$this->db->where('self_evaluation.id', intval($seid));

		$query = $this->db->get();
		$result = $query->result_array();
		
		return $result;
	}

	public function getEvaluation($seid){
		$this->db->select('self_evaluation.*');
		$this->db->from('self_evaluation');
		$this->db->where('self_evaluation.id', intval($seid));

		$query = $this->db->get();
		$result = $query->result_array();
		
		return $result;
	}

	public function getEvaluationAllYearDetail($info){
		$this->db->select('self_evaluation.*, users.name user_name, volunteer_category.name category_name, user_signature.signature');
		$this->db->from('self_evaluation');
		$this->db->join('users', 'self_evaluation.uid = users.id');
		$this->db->join('volunteer_category', 'self_evaluation.category = volunteer_category.id');
		$this->db->join('user_signature', 'self_evaluation.uid = user_signature.user_id');
		// $this->db->where('self_evaluation.year', intval($info[0]['year']));
		$this->db->where('self_evaluation.uid', intval($info[0]['uid']));
		// $this->db->where('self_evaluation.category', intval($info[0]['category']));

		$query = $this->db->get();
		$result = $query->result_array();
		
		return $result;
	}

	public function getTotalHours($uid, $start_date, $end_date, $category){
        $sql = sprintf("select
                            sum(hours) total
                        from
                            (
                            select
                                CASE
                                                        volunteer_classroom.volunteerID 
                                                    WHEN '1' THEN
                                                        volunteer_calendar.hours
                                    ELSE
                                                        CASE
                                                            volunteer_calendar.type 
                                                        WHEN '1' THEN
                                                            (
                                                                round(TIME_TO_SEC(TIMEDIFF(volunteer_category.morning_end, volunteer_category.morning_start))/ 3600)
                                                            )
                                        WHEN '2' THEN
                                                            (
                                                                round(TIME_TO_SEC(TIMEDIFF(volunteer_category.afternoon_end, volunteer_category.afternoon_start))/ 3600)
                                                            )
                                    END
                                END hours
                            from
                                volunteer_calendar_apply
                            join volunteer_calendar on
                                volunteer_calendar_apply.calendarID = volunteer_calendar.id
                            join volunteer_classroom on
                                volunteer_calendar.vcID = volunteer_classroom.id
                            join volunteer_category on
                                volunteer_classroom.volunteerID = volunteer_category.id
                            join users on
                                volunteer_calendar_apply.userID = users.id
                            join sign_log on
                                DATE_FORMAT( sign_log.sign_time, '%%Y-%%m-%%d' ) = volunteer_calendar.date
                                and users.idNo = sign_log.idno
                            where
                                volunteer_calendar_apply.userID = '%s'
                                AND volunteer_calendar_apply.got_it = 1 
                                and volunteer_category.id = '%s'
                                and volunteer_calendar.date >= '%s'
                                and volunteer_calendar.date <= '%s'
                            GROUP BY
                                sign_log.idno,
                                volunteer_calendar.date,
                                volunteer_calendar.type ) a", intval($uid), intval($category), addslashes($start_date), addslashes($end_date));

        $query = $this->db->query($sql);
        $result =$query->result_array();

        if($result[0]['total'] > 0){
            return $result[0]['total'];
        }

        return 0;
    }

	public function againSave($type, $list){
		if($type == '1' || $type == '2'){
			$this->db->set('again', $type);
		} else {
			return false;
		}

		$this->db->where_in('id', $list);

		if($this->db->update('self_evaluation')){
			return true;
		}

		return false;
	}

	public function evaluationAgainSave($year, $helf, $category, $uid, $again){
		if($again == '1' || $again == '2'){
			$this->db->set('again', intval($again));
		} else {
			return false;
		}

		$this->db->set('modify_time', date('Y-m-d H:i:s'));

		$this->db->where('uid', intval($uid));
        $this->db->where('year', intval($year));
        $this->db->where('helf', intval($helf));
        $this->db->where('category', intval($category));
       
        if($this->db->update('self_evaluation')){
            return true;
        }

        return false;
	}

	public function getSignOthersDetail($id){
		$this->db->select('volunteer_calendar.*,volunteer_classroom.volunteerID');
		$this->db->from('volunteer_calendar');
		$this->db->join('volunteer_classroom','volunteer_classroom.id = volunteer_calendar.vcID');
		$this->db->where('volunteer_calendar.id',intval($id));
		$this->db->where('volunteer_calendar.status','1');

		$query = $this->db->get();

		return $query->result_array();
	}

	public function signOthers($user_id, $id, $start_time, $end_time){
		$this->db->set('userid', intval($user_id));
		$this->db->set('calendarId', intval($id));
		$this->db->set('start_time', addslashes($start_time));
		$this->db->set('end_time', addslashes($end_time));
		$this->db->set('got_it', 1);
		$this->db->set('buildTime', date('Y-m-d H:i:s'));

		if($this->db->insert('volunteer_calendar_apply')){
			return true;
		}

		return false;
	}

	/**
     * For 停權設定
     */
	function getUserLogs($startDate, $endDate, $userName) {
		$this->db->where('course_date >= ', $startDate);
		$this->db->where('course_date < ', $endDate);
		$this->db->where('firstname', $userName);
		$query = $this->db->get('log');
		return $query->result();
	}

	function getLog4BanUser($start_date, $end_date) {
		$this->db->select("firstname, category, count(*) as cancels");
		$this->db->where('course_date >= ',$start_date);
		$this->db->where('course_date < ',$end_date);
		//$this->db->where('action', '取消');
		$this->db->group_by("firstname, category"); 
		$this->db->order_by('firstname','asc');
		$query = $this->db->get('log');
	
		return $query->result_array();
	}

	function getUserByName($username){
		$this->db->select("idNo, name, role_id, email");
		$this->db->where('name', $username);
		$query = $this->db->get('users');
		return $query->row();
	}

	function getBansByUserID($idNo) {
		$this->db->select("category_id, c.name, start_date, end_date");
		$this->db->from('ban_users b');
		$this->db->where('idNo', $idNo);
		$this->db->join('volunteer_category c', 'c.id = b.category_id');
		$query = $this->db->get();
		//return $query->result();
		$resultArr= new stdClass();
		if($query !== FALSE && $query->num_rows() > 0){
			$resultArr = $query->result();
		}
		return $resultArr;
	}

	function getCategoryByName($name) {
		$this->db->where('name', $name);
		$query = $this->db->get('volunteer_category');
		return $query->row();
	}

	function getCategoryMaps($others = false) {
		$this->db->select("id, name"); // others?
		if ($others) {
			$this->db->where('others', 1);
		}
		$query = $this->db->get('volunteer_category');
		//return $query->result_array();
		$mysqlResult = $query->result_array();
		$map = array();
		foreach ($mysqlResult as $row) {
			$map[$row['id']] = $row['name'];
		}
		return $map;
	}

	function setBanUser($banData) {
		$this->db->set('idNo', $banData['idNo']);
		$this->db->set('category_id', $banData['category_id']);
		$this->db->set('start_date', $banData['start_date']);
		$this->db->set('end_date', $banData['end_date']);
		$this->db->set('create_time', date('Y-m-d H:i:s'));
		$this->db->set('status', '1');

		if($this->db->insert('ban_users')){
			return true;
		}
		return false;		
	}

	function getUserById($idNo){
		$this->db->select("name, role_id, email");
		$this->db->where('idNo', $idNo);
		$query = $this->db->get('users');
		return $query->row();
	}

	/**
	 * 重新累計(resume banned)
	 */
	function update_log($id, $description) {
		$this->db->set('description', $description);
		$this->db->set('action', '管理者重新累計');
		$this->db->where('id', $id);
		return $this->db->update('log');
	}
}
