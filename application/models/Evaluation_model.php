<?php

class Evaluation_model extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database('dcsdphy'); // NOT phy
	}

	function getSelfEvaluation($startDate, $endDate, $category, $name, $year, $helf, $status)
	{
		$this->db->select('volunteer_category.id as category_id, volunteer_category.name as category_name, users.id as uid, users.name as user_name, self_evaluation.id seid, self_evaluation.top_grade, self_evaluation.bottom_grade, self_evaluation.undertaker_top_grade, self_evaluation.undertaker_bottom_grade, self_evaluation.leader_top_grade, self_evaluation.leader_bottom_grade, self_evaluation.status, self_evaluation.undertaker_status, self_evaluation.again, user_signature.signature');

		$this->db->from('volunteer_calendar_apply');
		$this->db->join('volunteer_calendar', 'volunteer_calendar_apply.calendarID = volunteer_calendar.id');
		$this->db->join('volunteer_classroom', 'volunteer_calendar.vcID = volunteer_classroom.id');
		$this->db->join('volunteer_category', 'volunteer_classroom.volunteerID = volunteer_category.id');
		$this->db->join('users', 'volunteer_calendar_apply.userID = users.id');
		$this->db->join('sign_log', "DATE_FORMAT( sign_log.sign_time, '%Y-%m-%d' ) = volunteer_calendar.date and users.idNo = sign_log.idno");
		$this->db->join('user_signature', 'volunteer_calendar_apply.userID = user_signature.user_id');
		$this->db->join("self_evaluation", "self_evaluation.status = 1 and self_evaluation.year = $year and self_evaluation.helf = $helf and volunteer_calendar_apply.userID = self_evaluation.uid and volunteer_category.id = self_evaluation.category");
		$this->db->where('volunteer_calendar.date >=', $startDate);
		$this->db->where('volunteer_calendar.date <=', $endDate);
		$this->db->where('undertaker_status =', $status);

		if (!empty($user_name)) {
			$this->db->where('users.name', $user_name);
		}

		if (!empty($category)) {
			$all = false;
			$category_list = array();

			for ($i = 0; $i < count($category); $i++) {
				if ($category[$i] == 'all') {
					$all = true;
					break;
				} else {
					$category_list[] = intval($category[$i]);
				}
			}

			if (!$all) {
				$this->db->where_in('volunteer_category.id', $category_list);
			}
		}

		$this->db->group_by('users.id,volunteer_category.id');
		$this->db->group_by('volunteer_category.id,users.name');

		$query = $this->db->get();
		$result = $query->result_array();

		return $result;
	}

	public function getEvaluationSetup()
	{
		$sql = sprintf("SELECT
                            evaluation_setup.category,
                            evaluation_setup.year,
                            evaluation_setup.helf
                        FROM
                            evaluation_setup
                        JOIN (
                            SELECT
                                max(id) id
                            FROM
                                evaluation_setup
                            GROUP BY
                                year,
		                        helf,
                                category) md
                        on
                            evaluation_setup.id = md.id
                        where
                            '%s' BETWEEN evaluation_setup.start_time and evaluation_setup.end_time
                        order by
                            evaluation_setup.year,
                            evaluation_setup.helf,
                            evaluation_setup.category", date('Y-m-d H:i:s'));

		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}

	/**
	 * Parameter: Year, Helf 
	 * Turn to: start_date, end_date via evaluation_setup
	 */
	//public function getUserApplyVolunteerCategory($start_date, $end_date, $category, $year, $helf, $uid = NULL) 
	public function getEval($year, $helf, $category, $userId = NULL) 
	{
		$leftJoinStr = "eval.year = {$year} And eval.helf = {$helf} And eval.category = cat.id";
		$whereStr = "cal.date between '2022-07-01' and '2022-12-31'";
		if (isset($userId) && !empty($userId)) {
			$leftJoinStr .= " And eval.uid = {$userId}";
			$whereStr .= " And apply.userID = {$userId}";
		}
		if (!empty($category)) {
			$all = false;
			$category_list = array();
			for ($i = 0; $i < count($category); $i++) {
				if ($category[$i] == 'all') {
					$all = true;
					break;
				} else {
					$category_list[] = intval($category[$i]);
				}
			}
			if (!$all) {
				$_tmp = implode(',', $category_list);
				$whereStr .= " And cat.id in ({$_tmp})";
			}
		}

		
		$_select = 'apply.userID uid, calendarID, u.name user_name, cal.date, classroomId, cat.id category_id, cat.name category_name';
		$_select .= ', eval.id seid, top_grade, bottom_grade, selfcomment, undertaker_top_grade, undertaker_bottom_grade, undertaker_status, leader_top_grade, leader_bottom_grade, again';
		$this->db->select($_select)
			->from('volunteer_calendar_apply apply')
			->join('users u', 'apply.userID = u.id')
			->join('volunteer_calendar cal', 'apply.calendarID = cal.id')
			->join('volunteer_classroom vc', 'cal.vcID = vc.id')
			->join('volunteer_category cat', 'cat.id = vc.volunteerID')
			->join('self_evaluation eval', $leftJoinStr, 'left')
			->where($whereStr)
			->group_by('uid, category_id')
			->order_by('uid, cat.sort');
        $query = $this->db->get();
        //$result = $query->result_array();
        //return $result;
		$resultArr= array();
		if($query !== FALSE && $query->num_rows() > 0){
			$resultArr = $query->result_array();
		}
		return $resultArr;
    }

	public function getUserID($userName) 
	{
		$this->db->select('id')->from('users')->where('name', $userName);
		$query = $this->db->get();
		$ret = $query->row();
		return $ret->id;
	}
}
