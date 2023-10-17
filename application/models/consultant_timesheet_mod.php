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
		// $invoice_sys = $this->load->database('invoice_system',TRUE);
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
		$where_project_id = ($project_id == '') ? '' : ' and a.projecticode = '. $project_id;
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
		". $where_project_id ."))) 
		and " . $this->db1_after . "CGVak_Consultant_Master.isactive = 1
		and " . $this->db1_after . "CGvak_Consultant_Project_Tasks.CreatedBy = $emp_id
		
		Union
		
		SELECT " . $this->db1_after . "CGVak_Consultant_Master.ConsultantICode,
		" . $this->db1_after . "CGVak_Consultant_Master.ConsultantLoginUserName,
		0 'check',
		" . $this->db1_after . "CGVak_Consultant_Master.isactive
		FROM " . $this->db1_after . "CGVak_Consultant_Master";
		
		if ($this->session->userdata('role') === 'Lead') {
			$sql .= " WHERE " . $this->db1_after . "CGVak_Consultant_Master.ConsultantICode = '" . $emp_id . "' 
							AND " . $this->db1_after . "CGVak_Consultant_Master.isactive = 1";
		} else {
			$sql .= " WHERE " . $this->db1_after . "CGVak_Consultant_Master.isactive = 1";
		}
		
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
	
	public function updateTimesheetDetails($id, $details)
	{
		$result = $this->db->query("UPDATE CGvak_Consultant_Monthly_Timesheet_Approvals SET BillSystemPushedBy = ".$details['BillSystemPushedBy'].", BillSystemPushedOn = '".$details['BillSystemPushedOn']."', FinalApprovalStatus = ".$details['FinalApprovalStatus'].", FinalApprovedDate = '".$details['FinalApprovedDate']."', ConsultApprovedStatus = ".$details['ConsultApprovedStatus'].", ConsultApprovedDate = '".$details['ConsultApprovedDate']."', InvoiceId = ".$details['InvoiceId']." WHERE id = ".$id."");
		if ($result) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function getConsultantTimesheetDetails($startDate, $toDate, $role='consultant')
	{
		$month = $this->input->post('month');
		$year = $this->input->post('year');

		$timestamp = strtotime($month . ' ' . $year);
		$year = date('Y', $timestamp);
		$month = date('m', $timestamp);
		$approvalStatus = '';
		
		if($role == 'consultant') {
			$approvalStatus = 'cmp.FinalApprovalStatus = 1 AND ';
		}
		
		$sql = "SELECT cmp.*, cmp.CreatedOn as taskprogressdate, cmp.WorkedHours as Hours_Worked, CONCAT(cm.ConsultantFirstName, ' ',cm.ConsultantLastName) as employee_name, pm.ProjectName, em.EmployeeDisplayName as approved_by  from ".TBL_CONSULTANT_MONTHLY_APPROVAL." cmp 
		LEFT JOIN " . $this->db1_after . "CGVak_Consultant_Master cm on cmp.EmployeeICode = cm.ConsultantICode 
		LEFT JOIN " . $this->db1_after . "CGVak_Project_Master pm on pm.ProjectICode = cmp.ProjectICode 
		LEFT JOIN ".$this->db1_after."CGVak_EmployeeMaster em on cmp.ProjectLeadIcode = em.EmployeeICode
		WHERE ".$approvalStatus."cmp.BillYear = '".$year."' AND cmp.BillMonth = '".$month."' AND cmp.InvoiceId>0";
		
		if($this->session->userdata('role') === 'consultant') {
			$sql .= " AND cmp.EmployeeICode = '".$this->session->userdata('id')."'";
		}
		$query = $this->db->query($sql);
		return $query->result_array();
		// $query = $this->db->where('BillYear', $year)->where('BillMonth', $month)->get($this->db1_after . 'CGvak_Consultant_Monthly_Timesheet_Approvals')->row_array();
		// return $query;
	}

	public function getConsultantTimesheetDetailsByInvoiceId($invoiceId)
	{
		$query = $this->db->where('InvoiceId', $invoiceId)->get($this->db1_after . 'CGvak_Consultant_Monthly_Timesheet_Approvals')->row_array();
		return $query;
	}

	public function getConsultantBasicDetailsByEmployeeIcode($consultantIcode)
	{
		$query = $this->db->where('ConsultantICode', $consultantIcode)->get($this->db1_after . 'CGVak_Consultant_Master')->row_array();
		return $query;
	}
	
	public function deleteMonthlyHours($id){

        if($this->db->delete(TBL_CONSULTANT_MONTHLY_APPROVAL, array('id'=>$id))){
            return TRUE;
        }
        else{
            return FALSE;
        }

    } 
	private function getACCPendingAproveSQL($emp_ids, $approved_status = '') {
		
		$month = $this->input->post('month');
		$year = $this->input->post('year');

		$timestamp = strtotime($month . ' ' . $year);
		$year = date('Y', $timestamp);
		$month = date('m', $timestamp);

		if($approved_status == '') {
			$approvedStatus = '';
		} elseif($approved_status == 'approved') {
			$approvedStatus = 'true';
		} else {
			$approvedStatus = 'false';
		}
		$approved_status_query = ($approvedStatus == '') ? '' : " AND cmp.IsAccountsApproved = '".$approvedStatus."'";
		
		$sql = "SELECT cmp.*, cm.ConsultantICode, cmp.CreatedOn as taskprogressdate, cmp.WorkedHours as Hours_Worked, CONCAT(cm.ConsultantFirstName, ' ',cm.ConsultantLastName) as employee_name, pm.ProjectName, em.EmployeeDisplayName as approved_by  from ".TBL_CONSULTANT_MONTHLY_APPROVAL." cmp 
		LEFT JOIN " . $this->db1_after . "CGVak_Consultant_Master cm on cmp.EmployeeICode = cm.ConsultantICode 
		LEFT JOIN " . $this->db1_after . "CGVak_Project_Master pm on pm.ProjectICode = cmp.ProjectICode 
		LEFT JOIN ".$this->db1_after."CGVak_EmployeeMaster em on cmp.ProjectLeadIcode = em.EmployeeICode
		WHERE cmp.BillYear = '".$year."' AND cmp.BillMonth = '".$month."' and (cmp.EmployeeICode in (" . implode(',', $emp_ids) . "))";

		$sql .= $approved_status_query;

		// $sql .= "LEFT JOIN ".$this->db1_after."CGVak_EmployeeMaster em on cmp.ProjectLeadIcode = em.EmployeeICode";
		// AND cmp.IsAccountsApproved = 'false' 
		// echo $sql; exit;
		
		return $sql;
	}

	private function getHRApproveSQL($emp_ids, $approved_status = '') {
		$month = $this->input->post('month');
		$year = $this->input->post('year');

		$timestamp = strtotime($month . ' ' . $year);
		$year = date('Y', $timestamp);
		$month = date('m', $timestamp);

		if($approved_status == '') {
			$approvedStatus = '';
		} elseif($approved_status == 'approved') {
			$approvedStatus = 'true';
		} else {
			$approvedStatus = 'false';
		}
		$approved_status_query = ($approvedStatus == '') ? '' : " AND cmp.IsHrApproved = '".$approvedStatus."'";
		
		$sql = "SELECT cmp.*, cm.ConsultantICode, cmp.CreatedOn as taskprogressdate, cmp.WorkedHours as Hours_Worked, 
		CONCAT(cm.ConsultantFirstName, ' ',cm.ConsultantLastName) as employee_name,cm.HourlyRate, pm.ProjectName , 
		em.EmployeeDisplayName as approved_by ,
		epm.EmployeeDisplayName as lead_approved_by 
		from ".TBL_CONSULTANT_MONTHLY_APPROVAL." cmp 
		LEFT JOIN " . $this->db1_after . "CGVak_Consultant_Master cm on cmp.EmployeeICode = cm.ConsultantICode 
		LEFT JOIN " . $this->db1_after . "CGVak_Project_Master pm on pm.ProjectICode = cmp.ProjectICode 
		LEFT JOIN ".$this->db1_after."CGVak_EmployeeMaster epm on cmp.ProjectLeadIcode = epm.EmployeeICode 
		LEFT JOIN ".$this->db1_after."CGVak_EmployeeMaster em on cmp.AccountsApprovedBy = em.EmployeeICode 
		WHERE cmp.BillYear = '".$year."' AND cmp.BillMonth = '".$month."' AND cmp.IsAccountsApproved = 'true' and ( cmp.EmployeeICode in (" . implode(',', $emp_ids) . "))";
		// and cmp.IsHrApproved = 'false' 

		$sql .= $approved_status_query;
		return $sql;
	}

	private function getLeadApproveSQL($emp_ids, $projectId, $fromDate, $toDate) {
		$where_project_id = ($projectId == '') ? '' : 'and ('. $this->db1_after . 'CGVak_Consultant_Project_Tasks_Progress.projecticode = '.$projectId.')';
		$leadApprovedCondition = "and(". $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.IsApproved = 'false')";
		$sql = "SELECT " 
		. $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.taskprogressdate, ". $this->db1_after ."CGVak_Consultant_Project_Tasks_Progress.IsApproved,
		" . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.EmployeeICode,
		" . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.ProjectICode,
		" . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.workdescription, 
		" . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.LeadModifiedDate, 
		" . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.TaskProgressICode, 
		(SELECT " . $this->db1_after . "CGVak_Consultant_Master.ConsultantLoginUserName 
		FROM " . $this->db1_after . "CGVak_Consultant_Master 
		WHERE " . $this->db1_after . "CGVak_Consultant_Master.ConsultantICode = " . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.EmployeeICode)'ConsultantICode',
		(SELECT " . $this->db1_after . "CGVak_Consultant_Master.ConsultantLoginUserName 
		FROM " . $this->db1_after . "CGVak_Consultant_Master 
		WHERE " . $this->db1_after . "CGVak_Consultant_Master.ConsultantICode = " . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.EmployeeICode)'employee_name',
		(SELECT " . $this->db1_after . "CGVak_Project_Master.ProjectName 
		FROM " . $this->db1_after . "CGVak_Project_Master 
		WHERE " . $this->db1_after . "CGVak_Project_Master.ProjectICode = " . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.ProjectICode)'project_name',
		" . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.leadmanhours Hours_Worked,
		" . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.manhours actual_wrked,
		" . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.approvedhours approved_hrs
		
		FROM " . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress 
		WHERE (" . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.taskprogressdate BETWEEN '$fromDate' And '$toDate')
		and (" . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.EmployeeICode in (" . implode(',', $emp_ids) . "))"
		. $where_project_id ." 
		ORDER BY " . $this->db1_after . "CGVak_Consultant_Project_Tasks_Progress.taskprogressdate ASC";
		// ".$leadApprovedCondition."

		return $sql;

	}

	public function get_timesheet_details_to_list($emp_ids, $projectId, $fromDate, $toDate){
		$sql = $this->getLeadApproveSQL($emp_ids, $projectId, $fromDate, $toDate);
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function get_timesheet_details($emp_ids, $projectId, $fromDate, $toDate, $approved_status = '')
	{
		$sql = '';
		switch($this->session->userdata('role')) {
			case 'Account':
				$sql = $this->getACCPendingAproveSQL($emp_ids, $approved_status);
			break;
			case 'HR':
				$sql = $this->getHRApproveSQL($emp_ids, $approved_status);
			break;
			default:
				$sql = $this->getLeadApproveSQL($emp_ids, $projectId, $fromDate, $toDate, $approved_status);
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

	public function toggleStoreMonthlyData($taskProjectICode, $taskEmployeeICode, $year, $month, $workedHours, $totalHours, $leadId) {
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
			// $dataParams['WorkedHours'] += $data[0]['WorkedHours'];
			// $dataParams['LeadApprovedHours'] += $data[0]['LeadApprovedHours'];
			$dataParams['WorkedHours'] = $this->sumHoursAndMinutes($dataParams['WorkedHours'] , $data[0]['WorkedHours']);
			$dataParams['LeadApprovedHours'] = $this->sumHoursAndMinutes($dataParams['LeadApprovedHours'] , $data[0]['LeadApprovedHours']);
			//echo $dataParams['LeadApprovedHours'];
			//exit;
			return	$this->updateConsultantMonthlyData($data[0]['id'] ,$dataParams);
		} else {			
			return $this->insertConsultantMonthlyData($dataParams);
		}		
	}

	public function toEditMonthlyData($taskProjectICode, $taskEmployeeICode, $year, $month, $workedHours, $totalHours, $leadId){
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
			$dataParams['WorkedHours'] = $this->subtractHoursAndMinutes($data[0]['WorkedHours'] , $dataParams['WorkedHours']);
			$dataParams['LeadApprovedHours'] = $this->subtractHoursAndMinutes($data[0]['LeadApprovedHours'] , $dataParams['LeadApprovedHours']);
			if($dataParams['LeadApprovedHours'] && $dataParams['LeadApprovedHours'] > 0 ){
				return $this->updateConsultantMonthlyData($data[0]['id'] ,$dataParams);
			}
			else{
				return	$this->deleteMonthlyHours($data[0]['id']);
			}
		}
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
			/*$hh = floor($time);
			$mm = ($time-$hh)*100;		
			
			
			$minutes += $hh * 60;
			$minutes += $mm;*/
			
		}

		$hours = floor($minutes / 60);
		$min = $minutes - ($hours * 60);
		$mins_formatted = (strlen((string)$min) == 1) ? '0'.$min : $min;
		return $hours.".".$mins_formatted;
	}

	public function sumHoursAndMinutes($time_one,$time_two) {
		$hoursAndMinutesArray = [];
		$time_one = ($time_one == '' || $time_one == '.00') ? '00.00' : $time_one;
		$time_two = ($time_two == '' || $time_two == '.00') ? '00.00' : $time_two;
		array_push($hoursAndMinutesArray, $time_one, $time_two);
		// print_r($hoursAndMinutesArray);exit;
		return $this->OverallTime($hoursAndMinutesArray);
	}

	public function subtractHoursAndMinutes($startTime,$endTime){

			// Create DateTime objects from the time strings
			$startDateTime = new DateTime($startTime);
			$endDateTime = new DateTime($endTime);

			// Calculate the time difference
			$timeInterval = $startDateTime->diff($endDateTime);

			// Extract the difference in hours, minutes, and seconds
			$hours = $timeInterval->h;
			$minutes = $timeInterval->i;
			$seconds = $timeInterval->s;

			return $hours.'.'.$minutes;
	}
	public function toGetAccApprovedDate($projectId, $emp_ids, $year, $month){
		$sql = "select * from " . $this->db1_after . "CGvak_Consultant_Monthly_Timesheet_Approvals cmp where cmp.ProjectIcode = '" . $projectId."' AND cmp.EmployeeIcode in (". implode(',', $emp_ids) .") AND cmp.BillYear = '".$year."' AND cmp.BillMonth = '".$month."' AND cmp.IsAccountsApproved = 'true'";
		//  AND cmp.IsAccountsApproved = 'true'
		// in (" . implode(',', $emp_ids) . ")
		$query = $this->db->query($sql);
		$result = $query->result_array();
		if (count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

	public function getConsultantMonthlyDataById($id){
		$sql = "SELECT cmp.*, cmp.CreatedOn as taskprogressdate, cmp.WorkedHours as Hours_Worked, 
		CONCAT(cm.ConsultantFirstName, ' ',cm.ConsultantLastName) as employee_name, pm.ProjectName , 
		em.EmployeeDisplayName as approved_by ,
		epm.EmployeeDisplayName as lead_approved_by 
		from ".TBL_CONSULTANT_MONTHLY_APPROVAL." cmp 
		LEFT JOIN " . $this->db1_after . "CGVak_Consultant_Master cm on cmp.EmployeeICode = cm.ConsultantICode 
		LEFT JOIN " . $this->db1_after . "CGVak_Project_Master pm on pm.ProjectICode = cmp.ProjectICode 
		LEFT JOIN ".$this->db1_after."CGVak_EmployeeMaster epm on cmp.ProjectLeadIcode = epm.EmployeeICode 
		LEFT JOIN ".$this->db1_after."CGVak_EmployeeMaster em on cmp.AccountsApprovedBy = em.EmployeeICode 
		WHERE cmp.id='$id' ";
		$query = $this->db->query($sql);
		$result = $query->result_array();
		if (count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}


	public function insertPaymentApproval($payment){
		$DB2 = $this->load->database('invoice_system',TRUE);
		$id = $DB2->insert('invoices', $payment);
		$insert_id = $DB2->insert_id();
		if($id){ 
            return $insert_id;
        }
        else{
            return FALSE;
        }
	}
	public function insertPaymentApprovalComments($paymentComment){
		$DB2 = $this->load->database('invoice_system',TRUE);
		$id = $DB2->insert('invoice_comments', $paymentComment);
		if($id){ 
            return TRUE;
        }
        else{
            return FALSE;
        }
	}

	public function insertPaymentApprovalDoc($imageStoreArray,$invoice_id){
		$DB2 = $this->load->database('invoice_system',TRUE);
		$DB2->insert('images', $imageStoreArray);
		$insert_id = $DB2->insert_id();

		if($insert_id){

			$imageInvoiceArr= array(
				'invoice_id'=>$invoice_id,
				'image_id'=>$insert_id,
				'status'=>1
			);
			$DB2->insert('invoice_images', $imageInvoiceArr);
			$imageInvoiceId = $DB2->insert_id();

			if($imageInvoiceId){
				return TRUE;
			}else{
				return FALSE;
			}
		}

	}

	public function listAllUsers(){
		$this->db->select('EmployeeICode, LoginUserName,DepartmentICode,DesignationICode');
		$this->db->from($this->db1_after . 'CGVak_EmployeeMaster');
		$this->db->where('IsActive', true);
		$query = $this->db->get();
		return $query->result_array();
	}
	
}
