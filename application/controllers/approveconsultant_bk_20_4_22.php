<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class ApproveConsultant extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		/*-Session LogOut Catch Clear -*/
		$this->output->set_header('cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header("cache-Control: post-check=0, pre-check=0", false);
		$this->output->set_header("Pragma: no-cache");
		$this->output->set_header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		// $this->load->library('excel');
		$this->load->helper('url');
		$this->load->model('consultant_timesheet_mod');
	}

	/** -----------------------------------------------------------------------------------------------------------------------
	 * ------------------------------------------------- Consultant TimeSheet --------------------------------------------------------------
	 * ----------------------------------------------------------------------------------------------------------------------- **/

	/* 
		Timesheet export view
	*/
	public function index()
	{
		if ($this->session->userdata('id')) {
			$data = array();
			$data['entry_list'] = array();
			$data['emp_project'] = $this->consultant_timesheet_mod->emp_project($this->session->userdata('id'));
			//$data['emp_project'] = $this->consultant_timesheet_mod->emp_project(173);
			switch(USERROLE) {
				case 'acc':
					$this->load->view('approve_consultant_hours_acc', $data);
				break;
				case 'hr':
					$this->load->view('approve_consultant_hours_hr', $data);
				break;
				default:
					$this->load->view('approve_consultant_hours', $data);
			}
			
		} else {
			$this->load->view('login');
		}
	}

	/*
	 * Display employee details
	 */
	public function get_emp()
	{
		$response = array();
		$TimeSheetType = $this->input->post('timesheet_type');
		if ($TimeSheetType == 'monthly') {
			$month = $this->input->post('month');
			$year = $this->input->post('year');
			$timestamp = strtotime($month . ' ' . $year);
			$startDate = date('m-01-Y', $timestamp);
			$toDate = date('m-t-Y', $timestamp);
		} elseif ($TimeSheetType == 'date_range') {
			$startDate = date('m-d-Y', strtotime($this->input->post('fromDate')));
			$toDate = date('m-d-Y', strtotime($this->input->post('toDate')));
		}
		$response['emp_list'] = $this->consultant_timesheet_mod->get_timesheet_employees($this->session->userdata('id'), $_POST['ProjectICode'], $startDate);
		//$response['emp_list'] = $this->consultant_timesheet_mod->get_timesheet_employees(173,$_POST['ProjectICode'],$toDate);
//		 echo'<pre>';
//		 print_r($response);
		echo json_encode($response);
		die();
	}

	/*
	 * Time sheet export function  
	 */
	public function preview()
	{
		$response = array();
		$TimeSheetType = $this->input->post('timesheet_type');
		if ($TimeSheetType == 'monthly') {
			$month = $this->input->post('month');
			$year = $this->input->post('year');
			$timestamp = strtotime($month . ' ' . $year);
			$startDate = date('m-01-Y', $timestamp);
			$toDate = date('m-t-Y', $timestamp);
		} elseif ($TimeSheetType == 'date_range') {
			$startDate = date('m-d-Y', strtotime($this->input->post('fromDate')));
			$toDate = date('m-d-Y', strtotime($this->input->post('toDate')));
		}
		$response['ProjectICode'] = $this->input->post('ProjectICode');
		$response['startDate'] = $startDate;
		$response['toDate'] = $toDate;
		$response['emp_ids'] = $this->input->post('emp_ids');
		$response['timesheet_list'] = $this->consultant_timesheet_mod->get_timesheet_details($response['emp_ids'], $response['ProjectICode'], $startDate, $toDate);
		$emp_list_hours = array();
		foreach ($response['timesheet_list'] as $key => $value) {
			$response['timesheet_list'][$key]['date'] = $startDate = date('d-M-Y', strtotime($value['taskprogressdate']));
			$response['timesheet_list'][$key]['day'] = $startDate = date('l', strtotime($value['taskprogressdate']));
			if (!isset($emp_list_hours[$value['employee_name']])) {
				$emp_list_hours[$value['employee_name']] = $value['Hours_Worked'];
			} else {
				$emp_list_hours[$value['employee_name']] = $emp_list_hours[$value['employee_name']] + $value['Hours_Worked'];
			}
		}
		$response['total_hours'] = array_sum($emp_list_hours);
		echo json_encode($response);
		die();
	}

	public function hrSubmit() {
		$ids = $this->input->post('task_progress_id');
		$perRates = $this->input->post('rate_per_hr');
		$accApproves = $this->input->post('accApprove');
		$approved_hours = $this->input->post('approved_hours');
		$amount_per_days = $this->input->post('amount_per_day');
		$deletetasks = explode(',', $this->input->post('deletetasks'));
		foreach ($ids as $key => $id) {
			$update = [
				'IsHrApproved' 			=> 	$accApproves[$key],
				'HrApprovedHours' 		=> 	$approved_hours[$key],
				'HrApprovedRateHour' 	=> 	$perRates[$key],
				'HrApprovedBillAmount' 	=> 	$amount_per_days[$key],
				'HrApprovedBy'			=>	$this->session->userdata('id'),
				'HrApprovedOn'			=>	date("Y-m-d H:i:s")
			];
			$this->consultant_timesheet_mod->updateMonthlyHours($id, $update);
		}
		foreach ($deletetasks as $key => $id) {
			if(intval($id) > 0) {
				$this->consultant_timesheet_mod->deleteMonthlyHours($id);
			}
		}
		redirect('/ApproveConsultant', 'refresh');
	}

	public function accSubmit() {
		$ids = $this->input->post('task_progress_id');
		$perRates = $this->input->post('rate_per_hr');
		$accApproves = $this->input->post('accApprove');
		$approved_hours = $this->input->post('approved_hours');
		$amount_per_days = $this->input->post('amount_per_day');
		$deletetasks = explode(',', $this->input->post('deletetasks'));
		foreach ($ids as $key => $id) {
			$update = [
				'IsAccountsApproved' 	=> 	$accApproves[$key],
				'AccountsApprovedHours' => 	$approved_hours[$key],
				'AccountsRateHour' 		=> 	$perRates[$key],
				'AccountsBillAmount' 	=> 	floatval($amount_per_days[$key]),
				'AccountsApprovedBy'	=>	$this->session->userdata('id'),
				'AccountsApprovedOn'	=>	date("Y-m-d H:i:s")
			];
			$this->consultant_timesheet_mod->updateMonthlyHours($id, $update);
		}
		foreach ($deletetasks as $key => $id) {
			if(intval($id) > 0) {
				$this->consultant_timesheet_mod->deleteMonthlyHours($id);
			}
		}
		redirect('/ApproveConsultant', 'refresh');
	}

	public function addLeadApprovedHours()
	{
		$taskProjectICode = $this->input->post('task_ProjectICode');
		$taskEmployeeICode = $this->input->post('task_EmployeeICode');

		$update['Billable'] = 'true';
		$update['IsApproved'] = 'true';
		$update['ApprovedHours'] = $this->input->post('approved_hours');
		$update['ModifiedByLead'] = $this->session->userdata('id');
		$update['LeadModifiedDate'] = date("Y-m-d H:i:s");
		$id = $this->input->post('task_progress_id');
		$year = date('Y');
		$month = date('m');
		$taskProgressData = date('Y/m/d H:i:s');

		$updatedStatus = $this->consultant_timesheet_mod->updateBillableHours($id, $update);
		
		$taskRecords = $this->consultant_timesheet_mod->getConsultantTaskProgressById($id);
		if(!empty($taskRecords)) {
			$taskProgressData = $taskRecords[0]['TaskProgressDate'];
			$year = date('Y', strtotime($taskProgressData));
			$month = date('m', strtotime($taskProgressData));
		}
		$startDate = date('m-01-Y', strtotime($taskProgressData));
		$toDate = date('m-t-Y', strtotime($taskProgressData));
		
		// $billingRecords = $this->consultant_timesheet_mod->getBillableHoursByEmp($taskProjectICode, $taskEmployeeICode, $startDate, $toDate);

		// if(!empty($billingRecords)) {
			$approvedHours = $taskRecords[0]['ManHours'];
			$loggedHours = $taskRecords[0]['ApprovedHours'];
			$this->consultant_timesheet_mod->toggleStoreMonthlyData($taskProjectICode, $taskEmployeeICode, $year, $month, $approvedHours, $loggedHours, $update['ModifiedByLead']);
		// }		

		$this->session->set_userdata(array(
			'msg' => "upt",
		));
	}

	public function hrApprovedHours()
	{
		$update['HrApprovedHours'] = $this->input->post('hr_approved_hours');
		$update['RatePerHour'] = $this->input->post('rate_per_hour');
		$update['AmountPerDay'] = $this->input->post('amount_per_day');
		$update['HrApprovedBy'] = $this->session->userdata('id');
		$update['HrApprovedOn'] = date("Y-m-d H:i:s");
		$id = $this->input->post('task_progress_id');
		$this->consultant_timesheet_mod->updateBillableHours($id, $update);
		$this->session->set_userdata(array(
			'msg' => "upt",
		));
	}
}
