<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Consultant_timesheet_mod extends CI_Model
{

	private $db1_after = "Cgvak_Synergy_System.dbo.";
	private $db2_after = "Cgvak_Synergy_System.dbo.";
	public $num_rec_per_page = 10;
	private $active = 1;
	private $in_active = 0;
	private $project_type_code = 8; /*- 8 - Retainership -([CGVak_ProjectType_Master]) -*/

	public function __construct()
	{
		$this->load->database('Cgvak_Synergy_System', TRUE);
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
		$query = $this->db->get($this->db1_after . 'CGVak_Project_Master');
		return $query->result_array();
	}

	/** -----------------------------------------------------------------------------------------------------------------------
	 *------------------------------------------------- Current User Project Listing --------------------------------------------
	 *----------------------------------------------------------------------------------------------------------------------- **/
	public function emp_project($emp_id)
	{
//		$sql = "SELECT Distinct ". $this->db1_after ."CGVak_Project_Master.projecticode AS ProjectICode,
//		". $this->db1_after ."CGVak_Project_Master.projectname AS ProjectName,
//		( SELECT Top 1 ". $this->db1_after ."CGVak_Consultant_Master.ConsultantICode
//		FROM ". $this->db1_after ."CGVak_Consultant_Master,
//		". $this->db1_after ."CGvak_Consultant_Project_Tasks
//		WHERE ( ". $this->db1_after ."CGVak_Consultant_Master.ConsultantICode = ". $this->db1_after ."CGvak_Consultant_Project_Tasks.ConsultantICode ) and
//		( ". $this->db1_after ."CGvak_Consultant_Project_Tasks.projecticode = ". $this->db1_after ."CGVak_Project_Master.projecticode )) 'employeeicode',
//		". $this->db1_after ."CGVak_Project_Master.companyicode
//		FROM ". $this->db1_after ."CGVak_Project_Master,
//		". $this->db1_after ."CGvak_Consultant_Project_Tasks
//		WHERE ( ". $this->db1_after ."CGVak_Project_Master.projecticode = ". $this->db1_after ."CGvak_Consultant_Project_Tasks.projecticode ) and
//		( ( IsNull(". $emp_id .",0) = 0 OR ". $this->db1_after ."CGvak_Consultant_Project_Tasks.ConsultantICode = ". $emp_id .") AND
//		( ". $this->db1_after ."CGVak_Project_Master.isactive = 1 ) )
//		ORDER BY ". $this->db1_after ."CGVak_Project_Master.projectname ASC";
//		$query = $this->db->query( $sql );
//		return $query->result_array();

		$query = $this->db->query("select ProjectICode, ProjectName from " . $this->db1_after . "CGVak_Project_Master where ProjectICode in ( select DISTINCT ProjectICode from " . $this->db1_after . "CGVak_Project_Members where EmployeeICode = $emp_id and project_status_icode in (3,6,9))");
		return $query->result_array();
	}

	public function get_timesheet_employees($emp_id, $project_id, $toDate)
	{
		$sql = "SELECT distinct " . $this->db1_after . "CGVak_Consultant_Master.ConsultantICode, 
		" . $this->db1_after . "CGVak_Consultant_Master.ConsultantLoginUserName,
		0 'check',
		" . $this->db1_after . "CGVak_Consultant_Master.isactive
		FROM " . $this->db1_after . "CGVak_Consultant_Master, 
		" . $this->db1_after . "CGvak_Consultant_Project_Tasks
		WHERE ( " . $this->db1_after . "CGVak_Consultant_Master.ConsultantICode = " . $this->db1_after . "CGvak_Consultant_Project_Tasks.ConsultantICode ) and 
		( (" . $this->db1_after . "CGvak_Consultant_Project_Tasks.projecticode in ( select a.projecticode 
		from " . $this->db1_after . "CGVak_Project_Master a," . $this->db1_after . "CGvak_Consultant_Project_Tasks b
		where a.projecticode = b.projecticode
		and a.isactive = 1
		and a.projecticode = $project_id))) 
		and " . $this->db1_after . "CGVak_Consultant_Master.isactive = 1
		
		Union
		
		SELECT " . $this->db1_after . "CGVak_Consultant_Master.ConsultantICode, 
		" . $this->db1_after . "CGVak_Consultant_Master.ConsultantLoginUserName,
		0 'check',
		" . $this->db1_after . "CGVak_Consultant_Master.isactive
		FROM " . $this->db1_after . "CGVak_Consultant_Master
		WHERE " . $this->db1_after . "CGVak_Consultant_Master.ConsultantICode = $emp_id
		and " . $this->db1_after . "CGVak_Consultant_Master.isactive = 1";

//		 echo $sql;
		// echo $emp_id;
		// echo $project_id;
		// echo $toDate;
//		 die;
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function updateMonthlyHours($id, $update)
	{
		$this->db->where('id', $id);
		$result = $this->db->update(TBL_CONSULTANT_MONTHLY_APPROVAL, $update);
		if ($result) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public function deleteMonthlyHours($id){

        if($this->db->delete(TBL_CONSULTANT_MONTHLY_APPROVAL, array('id'=>$id))){
            return TRUE;
        }
        else{
            return FALSE;
        }

    } 
	private function getACCPendingAproveSQL() {
		
		$month = $this->input->post('month');
		$year = $this->input->post('year');

		$timestamp = strtotime($month . ' ' . $year);
		$year = date('Y', $timestamp);
		$month = date('m', $timestamp);
		
		$sql = "SELECT cmp.*, cmp.CreatedOn as taskprogressdate, cmp.WorkedHours as Hours_Worked, cm.ConsultantLoginUserName as employee_name, pm.ProjectName  from ".TBL_CONSULTANT_MONTHLY_APPROVAL." cmp LEFT JOIN " . $this->db1_after . "CGVak_Consultant_Master cm on cmp.EmployeeICode = cm.ConsultantICode LEFT JOIN " . $this->db1_after . "CGVak_Project_Master pm on pm.ProjectICode = cmp.ProjectICode WHERE cmp.BillYear = '".$year."' AND cmp.BillMonth = '".$month."' AND cmp.IsAccountsApproved = 'false' ";
		// echo $sql; exit;
		
		return $sql;
	}

	private function getHRApproveSQL() {
		$month = $this->input->post('month');
		$year = $this->input->post('year');

		$timestamp = strtotime($month . ' ' . $year);
		$year = date('Y', $timestamp);
		$month = date('m', $timestamp);
		
		$sql = "SELECT cmp.*, cmp.CreatedOn as taskprogressdate, cmp.WorkedHours as Hours_Worked, cm.ConsultantLoginUserName as employee_name, pm.ProjectName  from ".TBL_CONSULTANT_MONTHLY_APPROVAL." cmp LEFT JOIN " . $this->db1_after . "CGVak_Consultant_Master cm on cmp.EmployeeICode = cm.ConsultantICode LEFT JOIN " . $this->db1_after . "CGVak_Project_Master pm on pm.ProjectICode = cmp.ProjectICode WHERE cmp.BillYear = '".$year."' AND cmp.BillMonth = '".$month."' AND cmp.IsAccountsApproved = 'true' and cmp.IsHrApproved = 'false' ";
		return $sql;
	}

	private function getLeadApproveSQL($emp_ids, $projectId, $fromDate, $toDate) {

		$leadApprovedCondition = "and(". $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.IsApproved = 'false')";
		$sql = "SELECT " . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.taskprogressdate, 
		" . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.EmployeeICode,
		" . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.workdescription, 
		" . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.TaskProgressICode, 
		(SELECT " . $this->db1_after . "CGVak_Consultant_Master.ConsultantLoginUserName 
		FROM " . $this->db1_after . "CGVak_Consultant_Master 
		WHERE " . $this->db1_after . "CGVak_Consultant_Master.ConsultantICode = " . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.EmployeeICode)'employee_name',
		" . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.leadmanhours Hours_Worked,
		" . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.manhours actual_wrked,
		" . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.approvedhours approved_hrs
		
		FROM " . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress 
		WHERE (" . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.taskprogressdate BETWEEN '$fromDate' And '$toDate')
		and (" . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.EmployeeICode in (" . implode(',', $emp_ids) . "))
		and (" . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.projecticode = $projectId)".$leadApprovedCondition."
		ORDER BY " . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.taskprogressdate ASC";

		return $sql;

	}

	public function get_timesheet_details($emp_ids, $projectId, $fromDate, $toDate)
	{
		$sql = '';
		switch(USERROLE) {
			case 'acc':
				$sql = $this->getACCPendingAproveSQL();
			break;
			case 'hr':
				$sql = $this->getHRApproveSQL();
			break;
			default:
				$sql = $this->getLeadApproveSQL($emp_ids, $projectId, $fromDate, $toDate);
		}

		// echo $sql;
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function get_project_name($projectId)
	{
		$query = $this->db->query("select ProjectICode, ProjectName from " . $this->db1_after . "CGVak_Project_Master where ProjectICode = " . $projectId);
		$result = $query->result_array();
		if (count($result) > 0) {
			return $result[0]['ProjectName'];
		} else {
			return false;
		}
	}

	public function getConsultantTaskProgressById($id)
	{
		$query = $this->db->query("select * from " . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress cpp where cpp.TaskProgressICode = '" . $id."'");
		$result = $query->result_array();
		if (count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	public function getBillableHoursByEmp($projectId, $empId, $fromDate, $toDate)
	{
		$sql = "select sum(cpp.ManHours) totalLoggedHours, sum(cpp.ApprovedHours) totalApprovedHours from " . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress cpp where cpp.ProjectICode = '" . $projectId."' AND cpp.EmployeeICode = '".$empId."' AND cpp.Billable = 'true' AND cpp.IsApproved = 'true' AND (cpp.taskprogressdate BETWEEN '".$fromDate."' And '".$toDate."')";
		$query = $this->db->query($sql);
		$result = $query->result_array();
		if (count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	public function updateBillableHours($id, $update)
	{
		$this->db->where('TaskProgressICode', $id);
		$result = $this->db->update(TBL_CONSULTANT_PROJECT_PROGRESS, $update);
		if ($result) {
			echo "updated";
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function getConsultantMonthlyData($projectId, $empId, $year, $month) {
		$sql = "select * from " . $this->db1_after . "CGvak_Consultant_Monthly_Timesheet_Approvals cmp where cmp.ProjectIcode = '" . $projectId."' AND cmp.EmployeeIcode = '".$empId."' AND cmp.BillYear = '".$year."' AND cmp.BillMonth = '".$month."' AND cmp.IsAccountsApproved = 'false'";
		// AND cmp.IsAccountsApproved = 'false'
		$query = $this->db->query($sql);
		$result = $query->result_array();
		if (count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	public function insertConsultantMonthlyData($data) {
		$this->db->insert(TBL_CONSULTANT_MONTHLY_APPROVAL, $data);
		$id = $this->db->insert_id();
		if($id){ 
            return TRUE;
        }
        else{
            return FALSE;
        }
	}

	public function updateConsultantMonthlyData($id, $update)
	{
		$this->db->where('id', $id);
		$result = $this->db->update(TBL_CONSULTANT_MONTHLY_APPROVAL, $update);
		if ($result) {
			echo "updated";
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function toggleStoreMonthlyData($taskProjectICode, $taskEmployeeICode, $year, $month, $totalHours, $workedHours, $leadId) {
		$data = $this->getConsultantMonthlyData($taskProjectICode, $taskEmployeeICode, $year, $month);
		$dataParams = array(
			'EmployeeIcode' 	=> $taskEmployeeICode,
			'ProjectIcode' 		=> $taskProjectICode,
			'BillYear' 			=> $year,
			'BillMonth' 		=> $month,
			'ProjectLeadIcode' 	=> $leadId,
			'WorkedHours' 		=> $workedHours,
			'LeadApprovedHours' => $totalHours,
			'CreatedOn' 		=> date('Y-m-d H:i:s')
		);
		if(!empty($data)) {
			$this->updateConsultantMonthlyData($data[0]['id'] ,$dataParams);
		} else {			
			$this->insertConsultantMonthlyData($dataParams);
		}		
	}

}
