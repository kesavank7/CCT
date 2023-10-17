<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Consultant_task_detail extends CI_Model
{

	private $db1_after = "Cgvak_Synergy_System.dbo.";
	public $num_rec_per_page = 10;
	private $active = 1;
	private $in_active = 0;
	private $project_type_code = 8; /* - 8 - Retainership -([CGVak_ProjectType_Master]) - */

	public function __construct()
	{
		$this->load->database(_DB_NAME_SYNERGY, TRUE);
	}

	public function def_in_active()
	{
		return $this->in_active;
	}

	public function def_active()
	{
		return $this->active;
	}

	public function db1_after()
	{
		return $this->db1_after;
	}

	public function get_project()
	{
		$query = $this->db->get(_DB_SYNERGY . TBL_PROJECT_MASTER);
		return $query->result_array();
	}

	/** -----------------------------------------------------------------------------------------------------------------------
	 * ------------------------------------------------- Entry Listing -----------------------------------------------------------
	 * ----------------------------------------------------------------------------------------------------------------------- * */
	public function get_entry_list($emp_id, $project_id = null)
	{
		$this->db->select('main.TaskICode,main.ProjectICode,main.TaskDescription,main.EstimatedHours,main.TaskStartDate,main.TaskEndDate,sub2.ConsultantFirstName,sub2.ConsultantLastName');
		// $this->db->select('*');
		$this->db->from(_DB_SYNERGY . TBL_CONSULTANT_TASK_ENTRY . ' as main');
		$this->db->join(_DB_SYNERGY . TBL_CONSULTANT_MASTER . ' as sub2', 'main.ConsultantICode = sub2.ConsultantICode', 'left');
		$this->db->join(_DB_SYNERGY . TBL_PROJECT_MASTER . ' as sub3', 'main.ProjectICode = sub3.ProjectICode', 'inner');
		$this->db->where(" main.CreatedBy = $emp_id");
		$this->db->where(" main.IsActive", $this->active);
		$this->db->where("sub3.project_status_icode in (3,6,9)");
		if ($project_id)
			$this->db->where(" main.ProjectICode = $project_id");
		$this->db->order_by('main.TaskICode', 'Desc');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_project_phase($phaseTypeICode, $projectICode)
	{
		$this->db->select('*');
		$this->db->from(_DB_SYNERGY . TBL_PROJECT_PHASE);
		$this->db->where(array('PhaseTypeICode' => $phaseTypeICode, 'ProjectICode' => $projectICode));
		$query = $this->db->get();
		return $query->result_array();
	}

	public function project_phase($ProjectICode)
	{
		$this->db->select('master.PhaseTypeICode,master.PhaseName');
		$this->db->from(_DB_SYNERGY . TBL_PROJECT_PHASE . ' as phase');
		$this->db->join(_DB_SYNERGY . TBL_PROJECT_PHASE_MASTER . ' as master', 'phase.PhaseTypeICode = master.PhaseTypeICode', 'inner');
		$this->db->where("phase.ProjectICode", $ProjectICode);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function taskType($PhaseTypeICode)
	{
		$this->db->select('TaskTypeICode,TaskTypeName');
		$this->db->from(_DB_SYNERGY . TBL_TASK_TYPE);
		$this->db->where("PhaseTypeICode", $PhaseTypeICode);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function insert_entry($table, $data)
	{
		$this->db->trans_start();
		$this->db->insert($table, $data);
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
		} else {
			$this->db->trans_commit();
		}
		return $insert_id;
	}

	public function get_task_history_entry($table, $data, $fromDate  = null, $toDate  = null)
	{
		if($fromDate == null && $toDate == null) {
			return $this->db->order_by('TaskProgressDate', 'DESC')->get_where($table, $data)->result_array();
		} else {
			return $this->db->order_by('TaskProgressDate', 'DESC')->where('TaskProgressDate >=', $fromDate)->where('TaskProgressDate <=', $toDate)->get_where($table, $data)->result_array();
		}
	}

	public function get_consultant_technologies($table)
	{
		$DB2 = $this->load->database('Cgvak_Synergy_System', TRUE);
		$query = $DB2->get_where($this->db1_after.$table, array('isactive' => 1));
		return $query->result_array();
	}

	public function getEnterDetails($user_id, $manual_date)
	{

		$serverName = _SERVER_IP; //serverName\instanceName
		$connectionInfo = array("Database" => _DB_NAME, "UID" => _DB_USER, "PWD" => _DB_PASSWORD);
		$conn = sqlsrv_connect($serverName, $connectionInfo);

		if ($conn) {
			$sql = "{call Cgvak_Synergy_System.dbo.sp_check_emp_work_log(?,?)}";
			$params = array($user_id, $manual_date);
			if ($stmt = sqlsrv_prepare($conn, $sql, $params)) {
				//echo "Statement prepared.<br><br>\n";
			} else {
//                echo "Statement could not be prepared.\n";
//                die(print_r(sqlsrv_errors(), true));
			}

			if (sqlsrv_execute($stmt) === false) {

//                die(print_r(sqlsrv_errors(), true));
			} else {

				return sqlsrv_fetch_array($stmt);
			}
		} else {
//            echo "Connection could not be established.<br />";
//            die(print_r(sqlsrv_errors(), true));
		}
	}

	public function get_scenarios($task_details)
	{
		$return_arr = array();
		foreach ($task_details as $tasks) {
			$task_code = $tasks['TaskICode'];
			$query = $this->db->query("select TestScenarioDescription from " . _DB_SYNERGY . TBL_TASK_SCENARIO . " where taskicode =  $task_code and isactive = 1");
			$return_arr[$task_code] = $query->result_array();
		}
		return $return_arr;
	}

	public function get_single_list($TaskICode)
	{
		$this->db->select('main.*, CONVERT(varchar(10),ActualEndDate, 105) as ActualEndDate, CONVERT(varchar(10),TaskEndDate, 105) as  TaskEndDate, CONVERT(varchar(10),ActualStartDate, 105) as ActualStartDate, CONVERT(varchar(10),TaskStartDate, 105) as TaskStartDate '); //, sub.*
		$this->db->from(_DB_SYNERGY . TBL_PROJECT_TASK . ' as main');
		$this->db->where(" main.TaskICode = $TaskICode");
		$query = $this->db->get();
		$ret[] = $query->result_array();
//        $ret[] = $this->get_scenarios($query->result_array());
		return $ret;
	}

	public function update_entry($table, $data, $id_name, $id_val)
	{
		$this->db->trans_start();
		$this->db->where($id_name, $id_val);
		$this->db->update($table, $data);
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
		} else {
			$this->db->trans_commit();
		}
		return $id_val;
	}

	public function delete_entry($table, $id_name, $id_val){
		$this->db->trans_start();
		$this -> db -> where($id_name, $id_val);
		$this -> db -> delete($table);
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
		} else {
			$this->db->trans_commit();
		}
		return $id_val;
	}

	public function exist_scenario_id($taskicode, $tbl_sceno)
	{
		$this->db->select('taskTestIcode');
		$this->db->from($tbl_sceno);
		$this->db->where("taskicode = $taskicode");
		$query = $this->db->get();
		return $query->result_array();
	}


	/*     * ** Get Task Consultant list ** */

	public function consultantTypeList()
	{
		$this->db->select('*');
		$this->db->from(_DB_SYNERGY . TBL_CONSULTANT_MASTER);
		$this->db->where(array('isselfregisterApproved' => true, 'IsActive' => true));
		$this->db->order_by('ConsultantFirstName ASC');
		$query = $this->db->get();
		return $query->result_array();
	}

	/** -----------------------------------------------------------------------------------------------------------------------
	 * ------------------------------------------------- Progress Listing --------------------------------------------------------
	 * ----------------------------------------------------------------------------------------------------------------------- *
	 * @param $emp_id
	 * @param $project_id
	 * @return array
	 */
	public function progress_list($emp_id, $project_id = null)
	{
		$this->db->select(" main.ProjectICode, main.TaskICode, main.TaskDescription, main.EstimatedHours, main.ActualEndDate, main.TaskEndDate, ISNULL( CAST(sum(sub2.ManHours)  AS char(10)), '00:00')  as ManHours ");
		$this->db->from(_DB_SYNERGY . TBL_CONSULTANT_TASK_ENTRY . ' as main');
		$this->db->join(_DB_SYNERGY . TBL_CONSULTANT_PROJECT_PROGRESS . ' as sub2', 'main.TaskICode = sub2.TaskIcode', 'left');
		$this->db->join(_DB_SYNERGY . TBL_PROJECT_MASTER . ' as sub3', 'main.ProjectICode = sub3.ProjectICode', 'inner');
		$this->db->where("(main.CreatedBy = $emp_id OR main.ConsultantICode = $emp_id)");
		$this->db->where("main.IsActive", $this->active);
		$this->db->where("sub3.project_status_icode in (3,6,9)");
		if ($project_id)
			$this->db->where("main.ProjectICode", $project_id);
		$this->db->group_by(array("main.TaskICode","main.TaskDescription", "main.EstimatedHours", "main.ActualEndDate"," main.TaskEndDate","  main.ProjectICode"));
		$this->db->order_by('main.TaskICode Desc');
		$query = $this->db->get();
		$data = $query->result_array();
		$result = [];
		$workedHours = 0;
		foreach ($data as $key => $d) {
			$this->db->select('EmployeeICode');
			$this->db->from(_DB_SYNERGY . TBL_PROJECT_MEMBERS);
			$this->db->where("ProjectICode", $d['ProjectICode']);
			$this->db->where("RoleICode", 6);
			$querys = $this->db->get();
			$datas = $querys->result_array();
			$d['lead_id'] = $datas[0]['EmployeeICode'];
			$workedHours = $this->get_worked_hours($emp_id, $project_id, $d['TaskICode']);
			$d['workedHours'] = $workedHours;
			$result[] = $d;
		}		
		return $result;
	}
	
	public function get_worked_hours($emp_id, $project_id, $task_id)
	{
		$this->db->select("main.TaskICode,sub2.ManHours as workedHours");
		$this->db->from(_DB_SYNERGY . TBL_CONSULTANT_TASK_ENTRY . ' as main');
		$this->db->join(_DB_SYNERGY . TBL_CONSULTANT_PROJECT_PROGRESS . ' as sub2', 'main.TaskICode = sub2.TaskIcode', 'left');
		$this->db->join(_DB_SYNERGY . TBL_PROJECT_MASTER . ' as sub3', 'main.ProjectICode = sub3.ProjectICode', 'inner');
		$this->db->where("(main.CreatedBy = $emp_id OR main.ConsultantICode = $emp_id)");
		$this->db->where("main.IsActive", $this->active);
		$this->db->where("sub2.TaskICode", $task_id);
		$this->db->where("sub3.project_status_icode in (3,6,9)");
		if ($project_id) {
			$this->db->where("main.ProjectICode", $project_id);
		}		
		$this->db->order_by('main.TaskICode Desc');
		$query = $this->db->get();
		$data = $query->result_array();
		$init_hours = 0.00;
		//$res = [];
		foreach ($data as $key => $d) {
			$init_hours = $this->sumHoursAndMinutes($init_hours,$d['workedHours']);
			//$res[] = $d['workedHours'];
		}	
		
		return $init_hours;
	}
	
	public function sumHoursAndMinutes($time_one,$time_two) {
		$hoursAndMinutesArray = [];
		$time_one = ($time_one == '' || $time_one == '.00') ? '00.00' : $time_one;
		$time_two = ($time_two == '' || $time_two == '.00') ? '00.00' : $time_two;
		array_push($hoursAndMinutesArray, $time_one, $time_two);		
		return $this->OverallTime($hoursAndMinutesArray);
	}
	
	public function OverallTime($allTimes) {
		$minutes = 0;
		$hours = 0;
		
		foreach ($allTimes as $time) {
			list($hh, $mm) = explode('.', strval($time));
			$h = ($hh == '00' || $hh == '.00') ? 0 : (int)$hh;
			$m = ($mm == '00' || $mm == '.00') ? 0 : (int)$mm;
			
			$minutes += $h * 60;
			$minutes += $m;		
		}

		$hours = floor($minutes / 60);
		$min = $minutes - ($hours * 60);
		$mins_formatted = (strlen((string)$min) == 1) ? '0'.$min : $min;
		return $hours.".".$mins_formatted;
	}
	
	public function recent_progress_list($emp_id, $project_id = null, $recent = 30)
	{
		$this->db->select(" main.ProjectICode, main.TaskICode, main.TaskDescription, main.EstimatedHours, main.ActualEndDate, main.TaskEndDate, ISNULL( CAST(sum(sub2.ManHours)  AS char(10)), '00:00')  as ManHours ");
		$this->db->from(_DB_SYNERGY . TBL_CONSULTANT_TASK_ENTRY . ' as main');
		$this->db->join(_DB_SYNERGY . TBL_CONSULTANT_PROJECT_PROGRESS . ' as sub2', 'main.TaskICode = sub2.TaskIcode', 'left');
		$this->db->join(_DB_SYNERGY . TBL_PROJECT_MASTER . ' as sub3', 'main.ProjectICode = sub3.ProjectICode', 'inner');
		$this->db->where("(main.CreatedBy = $emp_id OR main.ConsultantICode = $emp_id)");
		$this->db->where("main.IsActive", $this->active);
		$this->db->where("sub3.project_status_icode in (3,6,9)");

		if($recent == 30) {
			$this->db->where('sub2.TaskProgressDate >= DATEADD(day,-30, GETDATE())');
		}

		if ($project_id)
			$this->db->where("main.ProjectICode", $project_id);
		$this->db->group_by(array("main.TaskICode","main.TaskDescription", "main.EstimatedHours", "main.ActualEndDate"," main.TaskEndDate","  main.ProjectICode"));
		$this->db->order_by('main.TaskICode Desc');
		$query = $this->db->get();
		$data = $query->result_array();
		$result = [];
		foreach ($data as $key => $d) {
			$this->db->select('EmployeeICode');
			$this->db->from(_DB_SYNERGY . TBL_PROJECT_MEMBERS);
			$this->db->where("ProjectICode", $d['ProjectICode']);
			$this->db->where("RoleICode", 6);
			$querys = $this->db->get();
			$datas = $querys->result_array();
			$d['lead_id'] = $datas[0]['EmployeeICode'];
			$result[] = $d;
		}
		return $result;
	}

	public function recent_progress_list_count($emp_id, $project_id = null, $recent = 30)
	{
		$this->db->select("count(TaskProgressICode) as task_progress_count");
		$this->db->from(_DB_SYNERGY . TBL_CONSULTANT_PROJECT_PROGRESS);
		$this->db->where("EmployeeICode", $emp_id);
		if($recent != null) {
			$this->db->where('TaskProgressDate < DATEADD(day,-'.$recent.', GETDATE())');
		}
		$row = $this->db->get()->row_array();
		return ($row) ? $row['task_progress_count'] : 0;
	}

	public function get_actual_start_date($taskicode)
	{
		$this->db->select('main.ActualStartDate');
		$this->db->from($this->db1_after . 'CGVak_Project_Tasks as main');
		$this->db->where("main.TaskICode", $taskicode);
		$query = $this->db->get();
		$value = $query->result_array();
		return $value[0]['ActualStartDate'];
	}

	public function total_progress_count($emp_id, $project_id = null)
	{
		$this->db->select(" COUNT(*) as  totalrows ");
		$this->db->from($this->db1_after . 'CGVak_Project_Tasks as main');
		$this->db->join($this->db1_after . 'CGVak_Project_Tasks_Progress as sub2', 'main.TaskICode = sub2.TaskIcode', 'left');
		$this->db->where("main.CreatedBy", $emp_id);
		$this->db->where("main.IsActive", $this->active);
		if ($project_id)
			$this->db->where("main.ProjectICode", $project_id);
		$this->db->group_by(array("main.TaskICode","main.TaskDescription"," main.EstimatedHours"," main.ActualEndDate"," main.TaskEndDate"," main.ProjectICode"));
		$this->db->order_by('main.TaskICode Desc');

		$query = $this->db->get();
		return $query->result_array();
	}

	/** -----------------------------------------------------------------------------------------------------------------------
	 * ------------------------------------------------- Listing All Tasks --------------------------------------------------------
	 * ----------------------------------------------------------------------------------------------------------------------- * */
	public function get_all_task_listing($emp_id, $project_id = null)
	{//, $last_id = 100)
		$this->db->select("main.TaskICode , main.TaskDescription, main.EstimatedHours, sub3.ProjectName, ISNULL( CAST(sum(sub2.ManHours)  AS char(10)), '00:00')  as ManHours ");
		$this->db->distinct();
		$this->db->from($this->db1_after . 'CGVak_Project_Tasks as main');
		$this->db->join($this->db1_after . 'CGVak_Project_Tasks_Progress as sub2', 'main.TaskICode = sub2.TaskIcode', 'left');
		$this->db->join($this->db1_after . 'CGVak_Project_Master as sub3', 'main.ProjectICode = sub3.ProjectICode', 'inner');
		$this->db->where(" main.CreatedBy", $emp_id);
		$this->db->where(" main.IsActive", $this->active);
		$this->db->where("sub3.project_status_icode in (3,6,9)");
		if ($project_id)
			$this->db->where(" main.ProjectICode = $project_id");
		$this->db->group_by(array("main.TaskICode","main.TaskDescription", "main.EstimatedHours", "sub3.ProjectName"));
		$this->db->order_by('main.TaskICode', 'desc');
		$query = $this->db->get();

		return $query->result_array();
	}

	public function get_task_listing($emp_id, $project_id = null)
	{
		$this->db->select('main.*, sub1.*, sub2.ManHours, sub3.ProjectName');
		$this->db->from($this->db1_after . 'CGVak_Project_Tasks as main');
		$this->db->join($this->db1_after . 'CGvak_Project_tasks_TestScenario as sub1', 'main.TaskICode = sub1.taskicode', 'inner');
		$this->db->join($this->db1_after . 'CGVak_Project_Tasks_Progress as sub2', 'main.TaskICode = sub2.TaskIcode', 'inner');
		$this->db->join($this->db1_after . 'CGVak_Project_Master as sub3', 'main.ProjectICode = sub3.ProjectICode', 'inner');
		$this->db->where("main.CreatedBy = $emp_id ");
		if ($project_id)
			$this->db->where("main.ProjectICode", $project_id);
		$this->db->order_by('main.TaskICode', 'ASC');
		$query = $this->db->get();

		return $query->result_array();
	}

	/*
	  $active = 1
	  $in_active = 0
	 */

	function inactive_record($id, $active)
	{
		/* - Task - */
		$data = array('IsActive' => $active, 'ClosedDate' => date('Y-m-d H:i:s'), 'ActualEndDate' => date('Y-m-d H:i:s'));
		$this->db->where('TaskICode', $id);
		$this->db->update($this->db1_after . 'CGVak_Project_Tasks', $data);

		/* - Test Scenario - */
		$data_sceno = array("isactive" => $active);
		$this->db->where('TaskICode', $id);
		$this->db->update($this->db1_after . 'CGvak_Project_tasks_TestScenario', $data_sceno);

		/* - Test Progress - */
		$data_progress = array("IsActive" => $active);
		$this->db->where('TaskICode', $id);
		$this->db->update($this->db1_after . 'CGVak_Project_Tasks_Progress', $data_progress);

		return 1;
	}

	/** -----------------------------------------------------------------------------------------------------------------------
	 * ------------------------------------------------- Current User Project Listing --------------------------------------------
	 * ----------------------------------------------------------------------------------------------------------------------- * */
	public function emp_project($emp_id)
	{
		$query = $this->db->query("select ProjectICode, ProjectName from " . $this->db1_after . "CGVak_Project_Master where ProjectICode in ( select DISTINCT ProjectICode from " . $this->db1_after . "CGVak_Project_Members where EmployeeICode = $emp_id and project_status_icode in (3,6,9))");
		return $query->result_array();
	}

	public function consultant_project($emp_id)
	{

		$query = $this->db->query("select ProjectICode, ProjectName from " . $this->db1_after . "CGVak_Project_Master where ProjectICode in ( select DISTINCT ProjectICode from " . $this->db1_after . "CGvak_Consultant_Project_Tasks where ConsultantICode = $emp_id and project_status_icode in (3,6,9))");

		return $query->result_array();
	}

}
