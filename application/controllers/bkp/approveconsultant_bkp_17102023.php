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
		$this->load->library('excel');
		$this->load->helper('url');
		$this->load->model('consultant_timesheet_mod');
		//$this->load->library('pdf');
	}

	/** -----------------------------------------------------------------------------------------------------------------------
	 * ------------------------------------------------- Consultant TimeSheet --------------------------------------------------------------
	 * ----------------------------------------------------------------------------------------------------------------------- **/

	/* 
		Timesheet export view
	*/
	public function index()
	{
		if ($this->session->userdata('id') && ($this->session->userdata('role') == 'Lead' || $this->session->userdata('role') == 'HR' || $this->session->userdata('role') == 'Account')) {
			$data = array();
			$data['entry_list'] = array();
			$data['emp_project'] = $this->consultant_timesheet_mod->emp_project($this->session->userdata('id'));
			$data['emp_list'] = $this->get_employee_list();
			//$data['emp_project'] = $this->consultant_timesheet_mod->emp_project(173);
						
			switch ($this->session->userdata('role')) {
				case 'Account':
					$this->load->view('approve_consultant_hours_acc', $data);
					break;
				case 'HR':
					$data['users'] = $this->consultant_timesheet_mod->listAllUsers();					
					$this->load->view('approve_consultant_hours_hr', $data);
					break;
				default:
					$this->load->view('approve_consultant_hours', $data);
			}

		} elseif (($this->session->userdata('id')) && ($this->session->userdata('role') !== 'Lead' || $this->session->userdata('role') !== 'HR' || $this->session->userdata('role') !== 'Account')) {
			$this->load->view('no_access');
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
		$TimeSheetType = $this->input->post('timesheet_type') ?? 'monthly';
		$projectICode = $this->input->post('ProjectICode') ?? '';
		if ($TimeSheetType == 'monthly') {
			$month = $this->input->post('month') ?? date('m');
			$year = $this->input->post('year') ?? date('Y');
			$timestamp = strtotime($month . ' ' . $year);
			$startDate = date('m-01-Y', $timestamp);
			$toDate = date('m-t-Y', $timestamp);
		} elseif ($TimeSheetType == 'date_range') {
			$startDate = date('m-d-Y', strtotime($this->input->post('fromDate')));
			$toDate = date('m-d-Y', strtotime($this->input->post('toDate')));
		}
		$response['emp_list'] = $this->consultant_timesheet_mod->get_timesheet_employees($this->session->userdata('id'),$projectICode, $startDate);
		//$response['emp_list'] = $this->consultant_timesheet_mod->get_timesheet_employees(173,$_POST['ProjectICode'],$toDate);
		echo json_encode($response);
		die();
	}

	public function get_employee_list()
	{
		$response = array();
		$TimeSheetType = $this->input->post('timesheet_type') ?? 'monthly';
		$projectICode = $this->input->post('ProjectICode') ?? '';
		if ($TimeSheetType == 'monthly') {
			$month = $this->input->post('month') ?? date('m');
			$year = $this->input->post('year') ?? date('Y');
			$timestamp = strtotime($month . ' ' . $year);
			$startDate = date('m-01-Y', $timestamp);
			$toDate = date('m-t-Y', $timestamp);
		} elseif ($TimeSheetType == 'date_range') {
			$startDate = date('m-d-Y', strtotime($this->input->post('fromDate')));
			$toDate = date('m-d-Y', strtotime($this->input->post('toDate')));
		}
		$response['emp_list'] = $this->consultant_timesheet_mod->get_timesheet_employees($this->session->userdata('id'),$projectICode, $startDate);
		return $response['emp_list'];
	}

	/*
	 * Time sheet export function  
	 */
	public function preview()
	{
		$response = array();
		$lastAccApprovedDate = '';
		$TimeSheetType = $this->input->post('timesheet_type');
		if ($TimeSheetType == 'monthly') {
			$month = $this->input->post('month');
			$year = $this->input->post('year');
			$timestamp = strtotime($month . ' ' . $year);
			$startDate = date('m-01-Y', $timestamp);
			$toDate = date('m-t-Y', $timestamp);
			$lastAccApprovedDate = $toDate;
			if ($this->session->userdata('role') == 'Lead') {
				$accApprovedEnteries = $this->consultant_timesheet_mod->toGetAccApprovedDate($this->input->post('ProjectICode'), $this->input->post('emp_ids'), $year, date('m', strtotime($month)));
				if (!empty($accApprovedEnteries)) {
					$lastEntered = end($accApprovedEnteries);
					if (!empty($lastEntered)) {
						$lastAccApprovedDate = $lastEntered['AccountsApprovedOn'];
					}
				}
			}
		} elseif ($TimeSheetType == 'date_range') {
			$startDate = date('m-d-Y', strtotime($this->input->post('fromDate')));
			$toDate = date('m-d-Y', strtotime($this->input->post('toDate')));
			$lastAccApprovedDate = $toDate;
		}
		$response['ProjectICode'] = $this->input->post('ProjectICode') ?? '';
		$response['startDate'] = $startDate;
		$response['toDate'] = $toDate;
		$response['emp_ids'] = $this->input->post('emp_ids');
		$response['timesheet_list'] = $this->consultant_timesheet_mod->get_timesheet_details($response['emp_ids'], $response['ProjectICode'], $startDate, $toDate, $this->input->post('approvedStatus'));
		// echo "<pre>"; print_r($response['timesheet_list']); exit;
		$emp_list_hours = array();
		$emp_man_hours = array();
		$lead_approved_hours = array();
		// print_r($response['timesheet_list']);exit;
		foreach ($response['timesheet_list'] as $key => $value) {
			$response['timesheet_list'][$key]['date'] = $startDate = date('d-M-Y', strtotime($value['taskprogressdate']));
			$response['timesheet_list'][$key]['day'] = $startDate = date('l', strtotime($value['taskprogressdate']));
			if(isset($value['Hours_Worked'])) {
				if (!isset($emp_man_hours[$value['ConsultantICode']])) {
					$emp_list_hours[$value['ConsultantICode']] = $value['Hours_Worked'];
				} else {
					// $emp_list_hours[$value['ConsultantICode']] = $emp_list_hours[$value['ConsultantICode']] + $value['Hours_Worked'];	
					$emp_list_hours[$value['ConsultantICode']] = $this->sumHoursAndMinutes($emp_list_hours[$value['ConsultantICode']] , $value['Hours_Worked']);
				}
			}	
			
			if(isset($value['actual_wrked'])) {
				
				if (!isset($emp_man_hours[$value['ConsultantICode']])) {
					$emp_man_hours[$value['ConsultantICode']] = $value['actual_wrked'];
				} else {					
					// $emp_man_hours[$value['ConsultantICode']] = $emp_man_hours[$value['ConsultantICode']] + $value['actual_wrked'];	
					$emp_man_hours[$value['ConsultantICode']] = $this->sumHoursAndMinutes($emp_man_hours[$value['ConsultantICode']] , $value['actual_wrked']);					
				}
			}	

			if(isset($value['approved_hrs'])) {
				if (!isset($lead_approved_hours[$value['ConsultantICode']])) {
					$lead_approved_hours[$value['ConsultantICode']] = $value['approved_hrs'];
				} else {
					// $lead_approved_hours[$value['ConsultantICode']] = $lead_approved_hours[$value['ConsultantICode']] + $value['approved_hrs'];	
					$lead_approved_hours[$value['ConsultantICode']] = $this->sumHoursAndMinutes($lead_approved_hours[$value['ConsultantICode']] , $value['approved_hrs']);					
				}
			}	

			if (isset($value['IsApproved'])) {
				$lastModifiedDate = (isset($value['LeadModifiedDate'])) ? $value['LeadModifiedDate'] : $lastAccApprovedDate;
				if (isset($lastAccApprovedDate) && (strtotime($lastAccApprovedDate) > strtotime($lastModifiedDate))) {
					$response['timesheet_list'][$key]['accApproved'] = true;
				} else {
					$response['timesheet_list'][$key]['accApproved'] = false;
				}
			}
		}
		// print_r($emp_list_hours);exit;
		// print_r($emp_man_hours);exit;
		// print_r($lead_approved_hours);exit;
		$response['total_hours'] = (!$emp_list_hours) ? 0 : $this->OverallTime($emp_list_hours);
		$response['actual_worked_hours'] = (!$emp_man_hours) ? 0 : $this->OverallTime($emp_man_hours);
		$response['lead_approved_hours'] = (!$lead_approved_hours) ? 0 : $this->OverallTime($lead_approved_hours);
		// $response['total_hours'] = array_sum($emp_list_hours);
		// $response['actual_worked_hours'] = array_sum($emp_man_hours);
		// $response['lead_approved_hours'] = array_sum($lead_approved_hours);
		// print_r($response);exit;
		echo json_encode($response);
		die();
	}

	private function OverallTime($allTimes) {
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

		return $hours.".".$min;
	}

	private function sumHoursAndMinutes($time_one,$time_two) {
		$hoursAndMinutesArray = [];
		$time_one = ($time_one == '' || $time_one == '.00') ? '00.00' : $time_one;
		$time_two = ($time_two == '' || $time_two == '.00') ? '00.00' : $time_two;
		array_push($hoursAndMinutesArray, $time_one, $time_two);
		// print_r($hoursAndMinutesArray);exit;
		return $this->OverallTime($hoursAndMinutesArray);
	}

	public function hrSubmit()
	{
		$ids = $this->input->post('task_progress_id');
		$perRates = $this->input->post('rate_per_hr');
		$accApproves = $this->input->post('accApprove');
		$approved_hours = $this->input->post('approved_hours');
		$amount_per_days = $this->input->post('amount_per_day');
		$deletetasks = explode(',', $this->input->post('deletetasks'));
		foreach ($ids as $key => $id) {
			if(isset($perRates[$key]) && $perRates[$key] !== 0 && $perRates[$key] !== 0.00 && $perRates[$key] !== '' && isset($amount_per_days[$key]) && $amount_per_days[$key] !== '' ){
				$update = [
					'IsHrApproved' => $accApproves[$key],
					'HrApprovedHours' => $approved_hours[$key],
					'HrApprovedRateHour' => $perRates[$key],
					'HrApprovedBillAmount' => $amount_per_days[$key],
					'HrApprovedBy' => $this->session->userdata('id'),
					'HrApprovedOn' => date("Y-m-d H:i:s")
				];
				$this->consultant_timesheet_mod->updateMonthlyHours($id, $update);
			}
		}
		if(isset($deletetasks)){
			foreach ($deletetasks as $key => $id) {
				if (intval($id) > 0) {
					$this->consultant_timesheet_mod->deleteMonthlyHours($id);
				}
			}
		}
		redirect('/ApproveConsultant', 'refresh');
	}

	public function accSubmit()
	{
		$ids = $this->input->post('task_progress_id');
		$perRates = $this->input->post('rate_per_hr');
		$accApproves = $this->input->post('accApprove');
		$approved_hours = $this->input->post('approved_hours');
		$amount_per_days = $this->input->post('amount_per_day');
		$deletetasks = explode(',', $this->input->post('deletetasks'));
		foreach ($ids as $key => $id) {
			$update = [
				'IsAccountsApproved' => $accApproves[$key],
				'AccountsApprovedHours' => $approved_hours[$key],
				'AccountsRateHour' => $perRates[$key],
				'AccountsBillAmount' => floatval($amount_per_days[$key]),
				'AccountsApprovedBy' => $this->session->userdata('id'),
				'AccountsApprovedOn' => date("Y-m-d H:i:s")
			];
			$this->consultant_timesheet_mod->updateMonthlyHours($id, $update);
		}
		foreach ($deletetasks as $key => $id) {
			if (intval($id) > 0) {
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

		$updateStatus = $this->consultant_timesheet_mod->updateBillableHours($id, $update);

		$taskRecords = $this->consultant_timesheet_mod->getConsultantTaskProgressById($id);
		$taskProgressData = $taskRecords[0]['TaskProgressDate'];
		$year = date('Y', strtotime($taskProgressData));
		$month = date('m', strtotime($taskProgressData));
		$approvedHours = $taskRecords[0]['ManHours'];
		$loggedHours = $taskRecords[0]['ApprovedHours'];
		$updateMonthlyTimesheetStatus = $this->consultant_timesheet_mod->toggleStoreMonthlyData($taskProjectICode, $taskEmployeeICode, $year, $month, $approvedHours, $loggedHours, $update['ModifiedByLead']);

		if ($updateStatus && $updateMonthlyTimesheetStatus) {
			$this->session->set_userdata(array(
				'msg' => "upt",
			));
		}
	}

	public function editLeadApproval()
	{

		$update['Billable'] = 'false';
		$update['IsApproved'] = 'false';
		$update['ApprovedHours'] = 0;
		// $update['ApprovedHours'] = $this->input->post('approved_hours');
		// $update['ModifiedByLead'] = $this->session->userdata('id');
		$update['ModifiedByLead'] = $this->session->userdata('id');
		$id = $this->input->post('task_progress_id');
		$taskRecords = $this->consultant_timesheet_mod->getConsultantTaskProgressById($id);
		$taskProjectICode = $this->input->post('task_ProjectICode');
		$taskEmployeeICode = $this->input->post('task_EmployeeICode');
		$taskProgressDate = $taskRecords[0]['TaskProgressDate'];
		$year = date('Y', strtotime($taskProgressDate));
		$month = date('m', strtotime($taskProgressDate));
		$approvedHours = $taskRecords[0]['ManHours'];
		$loggedHours = $taskRecords[0]['ApprovedHours'];

		$updateStatus = $this->consultant_timesheet_mod->updateBillableHours($id, $update);
		$this->consultant_timesheet_mod->toEditMonthlyData($taskProjectICode, $taskEmployeeICode, $year, $month, $approvedHours, $loggedHours, $update['ModifiedByLead']);


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

	public function deleteHrApprovalEnteries($id)
	{
		$dataParams = array(
			'IsAccountsApproved' => 0,
			'IsHrApproved' => 0,
		);
		$this->consultant_timesheet_mod->updateConsultantMonthlyData($id, $dataParams);
	}

	public function updatePaymentApproval()
	{

		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$month = $this->input->post('month');
		$year = $this->input->post('year');
		$timestamp = strtotime($month . ' ' . $year);
		$startDate = date('m-01-Y', $timestamp);
		$toDate = date('m-t-Y', $timestamp);

		$id = $this->input->post('id');

		$consultantMonthlyEntry = $this->consultant_timesheet_mod->getConsultantMonthlyDataById($id)[0];

		$paymentApproval = [];
		$paymentApproval['invoice_no'] = $consultantMonthlyEntry['employee_name'] . '_' . $consultantMonthlyEntry['BillMonth'] . '_' . $consultantMonthlyEntry['BillYear'];
		$paymentApproval['department_id'] = 2;
		$paymentApproval['invoice_category_id'] = 9;
		$paymentApproval['invoice_status'] = 0;
		$paymentApproval['status'] = 1;
		$paymentApproval['date'] = $consultantMonthlyEntry['HrApprovedOn'];
		$paymentApproval['invoice_amount'] = $consultantMonthlyEntry['HrApprovedBillAmount'];
		$paymentApproval['balance_amount'] = $consultantMonthlyEntry['HrApprovedBillAmount'];
		$paymentApproval['payment_type'] = 1;
		$paymentApproval['supplier_id'] = 31;
		$paymentApproval['payment_paid'] = 0;
		$paymentApproval['paid'] = 0;
		$paymentApproval['payment_paid'] = 0;

		$response = array();
		$response['ProjectICode'] = $consultantMonthlyEntry['ProjectIcode'];
		$response['project_name'] = $this->consultant_timesheet_mod->get_project_name($response['ProjectICode']);
		$response['emp_ids'] = [$consultantMonthlyEntry['EmployeeIcode']];
		$response['employee_name'] = $consultantMonthlyEntry['employee_name'];
		$response['hr_approved_hours'] = $consultantMonthlyEntry['HrApprovedHours'];
		$response['hr_approved_rate_hour'] = $consultantMonthlyEntry['HrApprovedRateHour'];
		$response['hr_approved_bill_amount'] = $consultantMonthlyEntry['HrApprovedBillAmount'];
		$response['startDate'] = $startDate;
		$response['toDate'] = $toDate;
		$response['timesheet_list'] = $this->consultant_timesheet_mod->get_timesheet_details_to_list($response['emp_ids'], $response['ProjectICode'], $startDate, $toDate);
		
		$filename = $response['project_name'] . "_" . $consultantMonthlyEntry['employee_name'] . '_' . $consultantMonthlyEntry['BillMonth'] . '_' . $consultantMonthlyEntry['BillYear'];
		$filename = str_replace(' ', '_', $filename);

		$this->htmlPdf($response,$filename);

		$invoice_id = $this->getInvoice_id($paymentApproval);
		$consultantTimesheetDetails = $this->consultant_timesheet_mod->getConsultantTimesheetDetailsByInvoiceId($invoice_id);
		// print_r($consultantTimesheetDetails);exit;
		$details = array(
			'BillSystemPushedBy' => $this->session->userdata('id'),
			'BillSystemPushedOn' => date("Y-m-d H:i:s"),
			'FinalApprovalStatus' => 0,
			'FinalApprovedDate' => date("Y-m-d H:i:s"),
			'ConsultApprovedStatus' => 0,
			'ConsultApprovedDate' => date("Y-m-d H:i:s"),
			'InvoiceId' => $invoice_id
		);
		$this->consultant_timesheet_mod->updateTimesheetDetails($id, $details);

		$updates = [
			'InvoiceId' => $invoice_id
		];
		$this->consultant_timesheet_mod->updateMonthlyHours($id, $updates);
		
		$result = array();
		try {
			$local_file =  base_url().'pdf/'.$filename.'.pdf';
			$ftp_uploaded = false;

			$connection = ftp_connect(FTP_SERVER, FTP_PORT);

			$login_result = ftp_login($connection, FTP_USERNAME, FTP_PASSWORD);
			ftp_pasv($connection, true);

			if (ftp_put($connection, "/public/images/invoices/human resource/". $filename . ".pdf", $local_file, FTP_BINARY)) {
			 	$ftp_uploaded = true;
			}

			ftp_close($connection);


			// if (copy($local_file, INVOICE_DOC_TO . $filename . ".pdf")) {
			// 	$imageStoreArray = array(
			// 		'name' => $filename . ".pdf",
			// 		'file_type' => "pdf",
			// 		'path' => INVOICE_DOC_PATH,
			// 		'department_id' => $paymentApproval['department_id'],
			// 		'status' => 1,
			// 		'created_at'=>date('Y-m-d h:i:s a', time())
			// 	);
			// 	$result = $this->getResult($imageStoreArray, $invoice_id, $result);
			// } else {
			// 	$result['bol'] = FALSE;
			// 	$result['status'] = 'Failed';
			// 	$result['msg'] = 'Something Went wrong';

			// 	echo json_encode($result);
			// };

			if ($ftp_uploaded) {
				$imageStoreArray = array(
					'name' => $filename . ".pdf",
					'file_type' => "pdf",
					'path' => INVOICE_DOC_PATH,
					'department_id' => $paymentApproval['department_id'],
					'status' => 1,
					'created_at'=>date('Y-m-d h:i:s a', time())
				);
				$result = $this->getResult($imageStoreArray, $invoice_id, $result);
			} else {
				$result['bol'] = FALSE;
				$result['status'] = 'Failed';
				$result['msg'] = 'Something Went wrong';

				echo json_encode($result);
				exit();
			};
		} catch (\Exception $e) {
			$result['bol'] = FALSE;
			$result['status'] = 'Failed';
			$result['msg'] = $e->getMessage();
			echo json_encode($result);
			exit();
		}

	}

	/**
	 * @param array $paymentApproval
	 * @return mixed
	 */
	public function getInvoice_id($paymentApproval)
	{
		$paymentApproval['created_by'] = $this->session->id;
		$paymentApproval['created_at'] = date('Y-m-d H:i:s');
		$invoice_id = $this->consultant_timesheet_mod->insertPaymentApproval($paymentApproval);

		$paymentApprovalComment['invoice_id'] = $invoice_id;
		// $paymentApprovalComment['comments'] = 'consultant invoice entered';
		$paymentApprovalComment['comments'] = $this->input->post('comments');
		$paymentApprovalComment['assigned_by'] = $this->session->id;
		$paymentApprovalComment['send_to'] = $this->input->post('sendTo');
		$paymentApprovalComment['status'] = 1;
		$paymentApprovalComment['created_at'] = date('Y-m-d H:i:s');

		$this->consultant_timesheet_mod->insertPaymentApprovalComments($paymentApprovalComment);
		return $invoice_id;
	}

	/**
	 * @param $timesheetData
	 * @param $filename
	 * @throws \Mpdf\MpdfException
	 */
	public function htmlPdf($timesheetData,$filename)
	{

		$data = [];

		$data['employee_name'] = $timesheetData['employee_name'];
		$data['project_name'] = $timesheetData['project_name'];
		$data['hr_approved_hours'] = $timesheetData['hr_approved_hours'];
		$data['hr_approved_rate_hour'] = $timesheetData['hr_approved_rate_hour'];
		$data['hr_approved_bill_amount'] = $timesheetData['hr_approved_bill_amount'];
		$data['from'] = $timesheetData['startDate'];
		$data['to'] = $timesheetData['toDate'];
		$data['timesheet'] = $timesheetData['timesheet_list'];


		$live_mpdf = new \Mpdf\Mpdf();
		$all_html = $this->load->view('mypdf', $data, true); //CodeIgniter view file name
		$live_mpdf->WriteHTML($all_html);
		$filename = $filename. ".pdf";
		$live_mpdf->Output('pdf/' . $filename, 'F');
	}

	/**
	 * @param array $imageStoreArray
	 * @param $invoice_id
	 * @param array $result
	 * @return array
	 */
	public function getResult(array $imageStoreArray, $invoice_id, array $result)
	{
		$result['bol'] = $this->consultant_timesheet_mod->insertPaymentApprovalDoc($imageStoreArray, $invoice_id);
		if ($result['bol']) {
			$result['status'] = 'Success';
			$result['msg'] = 'Updated for Payment to get Approved';
			echo json_encode($result);
			// $this->invoiceApproved($invoice_id);
		} else {
			$result['status'] = 'Failed';
			$result['msg'] = 'Something Went wrong';
			echo json_encode($result);
		}
		return $result;
	}

	public function testHtmlPdfBk()
	{

		$live_mpdf = new \Mpdf\Mpdf();
		$all_html = $this->load->view('mypdf', [], true); //CodeIgniter view file name
		$live_mpdf->WriteHTML($all_html);
		//$live_mpdf->Output(); // simple run and opens in browser
		//$live_mpdf->Output('pakainfo_details.pdf','D'); // it CodeIgniter downloads the file into the main dynamic system, with give your file name

		$filename = time() . ".pdf";

		$live_mpdf->Output('pdf/' . $filename, 'F');
	}

	public function htmlToPf()
	{
//		return '<section>
//        <h3>CG-VAK Software & Exports Ltd., India</h3>
//        <h3>Log Sheet for Devops tasks - Prince Praveen</h3>
//    </section>
//    <section>
//        <table>
//            <thead>
//                <tr>
//                    <th>#</th>
//                    <th>Name</th>
//                    <th>Total Hours</th>
//                </tr>
//            </thead>
//            <tbody>
//                <tr>
//                    <td>1</td>
//                    <td>Prince Praveen</td>
//                    <td>14.50</td>
//                </tr>
//            </tbody>
//        </table>
//    </section>
//
//    <section>
//        <table>
//            <thead>
//                <tr>
//                    <th>Date</th>
//                    <th>Day</th>
//                    <th>Name</th>
//                    <th>Work description</th>
//                    <th>Hours Worked</th>
//                </tr>
//            </thead>
//            <tbody>
//                <tr>
//                    <td>March 24, 2021</td>
//                    <td>Wednesday</td>
//                    <td>Prince</td>
//                    <td>Some description</td>
//                    <td>8.00</td>
//                </tr>
//                <tr>
//                    <td>March 25, 2021</td>
//                    <td>Thursday</td>
//                    <td>Prince</td>
//                    <td>Some description</td>
//                    <td>8.00</td>
//                </tr>
//                <tr>
//                    <td>Total Hours Spent</td>
//                    <td>14.50</td>
//                </tr>
//                <tr>
//                    <td>Rate Per Hour</td>
//                    <td>1000.00</td>
//                </tr>
//                <tr>
//                    <td>Amount to be Paid</td>
//                    <td>14500.00</td>
//                </tr>
//            </tbody>
//        </table>
//    </section>';
	}

	public function updatePaymentApprovalbp()
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$month = $this->input->post('month');
		$year = $this->input->post('year');
		$timestamp = strtotime($month . ' ' . $year);
		$startDate = date('m-01-Y', $timestamp);
		$toDate = date('m-t-Y', $timestamp);

		$id = $this->input->post('id');

		// echo $id;
		$consultantMonthlyEntry = $this->consultant_timesheet_mod->getConsultantMonthlyDataById($id)[0];

		$paymentApproval = [];

		$paymentApproval['invoice_no'] = $consultantMonthlyEntry['employee_name'] . '_' . $consultantMonthlyEntry['BillMonth'] . '_' . $consultantMonthlyEntry['BillYear'];
		$paymentApproval['department_id'] = 2;
		$paymentApproval['invoice_category_id'] = 9;
		$paymentApproval['invoice_status'] = 0;
		$paymentApproval['status'] = 1;
		$paymentApproval['date'] = $consultantMonthlyEntry['HrApprovedOn'];
		$paymentApproval['invoice_amount'] = $consultantMonthlyEntry['HrApprovedBillAmount'];
		$paymentApproval['balance_amount'] = $consultantMonthlyEntry['HrApprovedBillAmount'];
		$paymentApproval['payment_type'] = 1;
		$paymentApproval['supplier_id'] = 31;
		$paymentApproval['payment_paid'] = 0;
		//$paymentApproval['paid'] = 0;


		$invoice_id = $this->getInvoice_id($paymentApproval);


		$response = array();
		$response['ProjectICode'] = $consultantMonthlyEntry['ProjectIcode'];
		$response['project_name'] = $this->consultant_timesheet_mod->get_project_name($response['ProjectICode']);
		$response['emp_ids'] = [$consultantMonthlyEntry['EmployeeIcode']];
		$response['excelstartDate'] = date('M 01,Y', $timestamp);
		$response['exceltoDate'] = date('M t,Y', $timestamp);
		$monthInDate = date('m', strtotime($month));
		$response['startDate'] = $startDate;
		$response['toDate'] = $toDate;

		$response['timesheet_list'] = $this->consultant_timesheet_mod->get_timesheet_details_to_list($response['emp_ids'], $response['ProjectICode'], $startDate, $toDate);

		$filename = $response['project_name'] . "_" . $consultantMonthlyEntry['employee_name'] . '_' . $consultantMonthlyEntry['BillMonth'] . '_' . $consultantMonthlyEntry['BillYear'];
		$filename = str_replace(' ', '_', $filename);

		ini_set('display_errors', 1);
		error_reporting(E_ALL);
		$result = array();

		try {
			if (copy(INVOICE_DOC_FROM, INVOICE_DOC_TO . $filename . ".xls")) {

				$imageStoreArray = array(
					'name' => $filename . ".xls",
					'file_type' => "xls",
					'path' => INVOICE_DOC_PATH,
					'department_id' => $paymentApproval['department_id'],
					'status' => 1
				);
				
				$result = $this->getResult($imageStoreArray, $invoice_id, $result);
				// Update Invoice details to timesheet table after pushing to payment approval system
				//$this->consultant_timesheet_mod->updateInvoiceDetails($response['emp_ids'], $response['ProjectICode'], $invoice_id, $toDate)

			} else {
				$result['bol'] = FALSE;
				$result['status'] = 'Failed';
				$result['msg'] = 'Something Went wrong';

				echo json_encode($result);
			};
		} catch (\Exception $e) {
			$result['bol'] = FALSE;
			$result['status'] = 'Failed';
			$result['msg'] = $e->getMessage();
			echo json_encode($result);
		}

	}

	public function testHtmlPdfbkp1()
	{
		/*
			{
				"id":20,
				"EmployeeIcode":264,
				"ProjectIcode":3680,
				"BillYear":2022,
				"BillMonth":6,
				"ProjectLeadIcode":572,
				"WorkedHours":"120.000",
				"LeadApprovedHours":"120.000",
				"CreatedOn":"2022-06-17 13:23:40.000",
				"IsAccountsApproved":1,
				"AccountsApprovedHours":"120.000",
				"AccountsRateHour":"5.000",
				"AccountsApprovedBy":572,
				"AccountsBillAmount":"600.000",
				"AccountsApprovedOn":"2022-06-17 13:24:34.000",
				"IsHrApproved":1,
				"HrApprovedHours":"120.000",
				"HrApprovedBy":572,
				"HrApprovedOn":"2022-06-17 13:24:59.000",
				"HrApprovedRateHour":"5.000",
				"HrApprovedBillAmount":"600.000",
				"IsPushedtoBillSystem":0,
				"BillSystemPushedBy":null,
				"BillSystemPushedOn":null,
				"taskprogressdate":"2022-06-17 13:23:40.000",
				"Hours_Worked":"120.000",
				"employee_name":"abhinav",
				"ProjectName":"Internal - Synergy Automation",
				"approved_by":"Kesavan R",
				"lead_approved_by":"Kesavan R",
				"date":"17-Jun-2022",
				"day":"Friday"
			}
		*/

		$data = [];
		$data['employee_name'] = 'employee_name';
		$data['project_name'] = 'Internal - Synergy Automation';
		$data['HrApprovedHours'] = "30.000";
		$data['from'] = '01/05/2022';
		$data['to'] = '03/05/2022';

		$data['timesheet'][0]['date'] = '01/05/2022';
		$data['timesheet'][0]['task_description'] = 'Worked on project setup';
		$data['timesheet'][0]['hours_spent'] = '10.00';

		$data['timesheet'][1]['date'] = '02/05/2022';
		$data['timesheet'][1]['task_description'] = 'Worked on frontend';
		$data['timesheet'][1]['hours_spent'] = '10.00';

		$data['timesheet'][2]['date'] = '03/05/2022';
		$data['timesheet'][2]['task_description'] = 'Worked on backend';
		$data['timesheet'][2]['hours_spent'] = '10.00';


		$live_mpdf = new \Mpdf\Mpdf();
		$all_html = $this->load->view('mypdf', $data, true); //CodeIgniter view file name
		$live_mpdf->WriteHTML($all_html);
		$filename = time() . ".pdf";
		$live_mpdf->Output('pdf/' . $filename, 'F');
	}
	/*
	Handle the process after final approval from payment approval system
	*/
	// function invoiceApproved($invNo) {
	// 	echo 'Invoice - '.$invNo.' approved successfully';
	// 	//Update the final approval status to database
	// 	$result['bol'] = $this->consultant_timesheet_mod->updateFinalApproval($imageStoreArray, $invoice_id);
	// 	// trigger the email to consultant 
	// }


	/*
	Handle the process after final approval from payment approval system
	*/
	function invoiceApproved($invNo) {
		// print_r($invNo);exit;
		echo 'Invoice - '.$invNo.' approved successfully';
		$updates = [
			'FinalApprovalStatus' => 1,
			'FinalApprovedDate' => date("Y-m-d H:i:s")
		];
		$consultantTimesheetDetails = $this->consultant_timesheet_mod->getConsultantTimesheetDetailsByInvoiceId($invNo);
		// print_r($consultantTimesheetDetails);exit;
		$this->consultant_timesheet_mod->updateMonthlyHours($consultantTimesheetDetails['id'], $updates);
		// print_r($invNo);exit;
		$consultantBasicDetails = $this->consultant_timesheet_mod->getConsultantBasicDetailsByEmployeeIcode($consultantTimesheetDetails['EmployeeIcode']);
		$this->mail_to_consultant($consultantTimesheetDetails,$consultantBasicDetails);
		//Generate Invoice Pdf
		$data['Title'] = "Approve Invoice By Consultant";
		$data['consultant'] = $consultantBasicDetails;
		$data['consultant_timesheet'] = $consultantTimesheetDetails;

		$live_mpdf = new \Mpdf\Mpdf();
		$all_html = $this->load->view('invoice_pdf_template', $data, true); //CodeIgniter view file name
		$live_mpdf->WriteHTML($all_html);
		// $filename = "Invoice_".$id. ".pdf";
		$filename = $invNo. ".pdf";
		$live_mpdf->Output('pdf/' . $filename, 'F');

		// $result['bol'] = $this->consultant_timesheet_mod->updateFinalApproval($imageStoreArray, $invNo);
	}

	// Invoice Mail to Consultant for Approval
	public function mail_to_consultant($consultantTimesheetDetails,$consultantBasicDetails)
	{
		$data['mailHead'] = "Hi " . $consultantBasicDetails['ConsultantFirstName'] . " " . $consultantBasicDetails['ConsultantLastName'];
		$data['mailBody'] = "Your Invoice has been approved by HR. you can check and confirm";
		$subject = 'Reg: Invoice Approved';
		$data['id'] = base64_encode($consultantTimesheetDetails['InvoiceId']);
		$message = $this->load->view('email/invoice_approval_mailto_consultant.php', $data, TRUE);
		$this->send_email($message, $consultantBasicDetails['ConsultantEmailId'], $subject);
		//$this->send_email($message, $consultantBasicDetails['ConsultantEmailId'], $subject);
	}

	// Send Mail
	public function send_email($message, $email, $subject, $file = '')
	{
		$config = array(
			'protocol' => 'smtp', // 'mail', 'sendmail', or 'smtp'
			'smtp_host' => 'mail.cgvakindia.com',
			'smtp_port' => 587,
			'smtp_crypto' => 'tls',
			'smtp_user' => 'ctt@cgvakindia.com',
			'smtp_pass' => 'CGwelcome@123',
			'mailtype' => 'html', //plaintext 'text' mails or 'html'
			'smtp_timeout' => '30',
			'charset' => 'iso-8859-1',
			'wordwrap' => TRUE
		);

		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		$this->email->from('ctt@cgvakindia.com', 'CG-VAK software and exports');
		$this->email->to($email);
		$this->email->cc('nagalingam@cgvakindia.com');
		$this->email->subject($subject);
		$this->email->message($message);
		if ($file)
			$this->email->attach($file);

		if ($this->email->send()) {
			// echo "Success mail";
		} else {
			show_error($this->email->print_debugger());
			// echo "Failure";
		}
	}

	// Approve consultant page display
	public function approveInvoiceByConsultant($id)
	{
		// if ($this->session->userdata('id')) {
			$id = base64_decode($id);
			$consultantTimesheetDetails = $this->consultant_timesheet_mod->getConsultantTimesheetDetailsByInvoiceId($id);
			$consultantBasicDetails = $this->consultant_timesheet_mod->getConsultantBasicDetailsByEmployeeIcode($consultantTimesheetDetails['EmployeeIcode']);
			$data['Title'] = "Approve Invoice By Consultant";
			$data['consultant'] = $consultantBasicDetails;
			$data['consultant_timesheet'] = $consultantTimesheetDetails;

			// $live_mpdf = new \Mpdf\Mpdf();
			// $all_html = $this->load->view('invoice_pdf_template', $data, true); //CodeIgniter view file name
			// $live_mpdf->WriteHTML($all_html);
			// // $filename = "Invoice_".$id. ".pdf";
			// $filename = $id. ".pdf";
			// $live_mpdf->Output('pdf/' . $filename, 'F');

			$this->load->view('invoice_approval_by_consultant', $data);
		// } elseif (($this->session->userdata('id')) && ($this->session->userdata('role') != 'consultant')) {
		// 	$this->load->view('no_access');
		// } else {
		// 	$this->session->set_flashdata('currentUrlExists', TRUE);
		// 	$this->session->set_flashdata('currentUrl', current_url());
		// 	$this->load->view('login');
		// }
	}

	// Invoice Accepted by consultant
	public function invoiceAcceptedByConsultant() {
		// print_r($this->input->post('id'));exit;
		$msg = $msg_array = [];
		$invNo = $this->input->post('id');
		$consultantTimesheetDetails = $this->consultant_timesheet_mod->getConsultantTimesheetDetailsByInvoiceId($invNo);
		$consultantBasicDetails = $this->consultant_timesheet_mod->getConsultantBasicDetailsByEmployeeIcode($consultantTimesheetDetails['EmployeeIcode']);
		$updates = [
			'ConsultApprovedStatus' => 1,
			'ConsultApprovedDate' => date("Y-m-d H:i:s")
		];
		$this->consultant_timesheet_mod->updateMonthlyHours($consultantTimesheetDetails['id'], $updates);
		$consultantTimesheetDetails['invoiceStatus'] = 'Accepted';
		$this->mail_to_hr($consultantTimesheetDetails,$consultantBasicDetails);
		$msg['status'] = 1;
		$msg['msg'] = "Success";
		$msg_array['msg'] = $msg;
		echo json_encode($msg_array);
		exit();
	}

	// Acknowledgement Mail to HR Reg Consultant response for Invoice
	public function mail_to_hr($consultantTimesheetDetails, $consultantBasicDetails)
	{
		$data['mailHead'] = "Dear HR";
		if(isset($consultantTimesheetDetails['reason'])) {
			$data['mailBody'] = "MR." . $consultantBasicDetails['ConsultantFirstName'] . " " . $consultantBasicDetails['ConsultantLastName'] . " has ".$consultantTimesheetDetails['invoiceStatus']." the Invoice." ."<br><br> Reason: " . $consultantTimesheetDetails['reason'].".";
		} else {
			$data['mailBody'] = "MR." . $consultantBasicDetails['ConsultantFirstName'] . " " . $consultantBasicDetails['ConsultantLastName'] . " has ".$consultantTimesheetDetails['invoiceStatus']." the Invoice.";
		}
		// $data['mailBody'] = "MR." . $consultantBasicDetails['ConsultantFirstName'] . " " . $consultantBasicDetails['ConsultantLastName'] . " has ".$consultantTimesheetDetails['invoiceStatus']." the Invoice.";
		$data['toHr'] = 1;
		$subject = 'Invoice Approval';
		$message = $this->load->view('email/invoice_approval_mailto_consultant.php', $data, TRUE);
		$this->send_email($message, HR_MAIL, $subject);
		// $this->send_email($message, 'testconsultant@mailinator.com', $subject);
	}

	// Invoice Denied by consultant
	public function invoiceDeniedByConsultant() {
		// print_r($this->input->post('reason'));exit;
		$msg = $msg_array = [];
		$invNo = $this->input->post('id');
		$reason = $this->input->post('reason');
		$consultantTimesheetDetails = $this->consultant_timesheet_mod->getConsultantTimesheetDetailsByInvoiceId($invNo);
		$consultantBasicDetails = $this->consultant_timesheet_mod->getConsultantBasicDetailsByEmployeeIcode($consultantTimesheetDetails['EmployeeIcode']);
		$updates = [
			'ConsultApprovedStatus' => 2,
			'ConsultApprovedDate' => date("Y-m-d H:i:s")
		];
		$this->consultant_timesheet_mod->updateMonthlyHours($consultantTimesheetDetails['id'], $updates);
		$consultantTimesheetDetails['invoiceStatus'] = 'Denied';
		$consultantTimesheetDetails['reason'] = $reason;
		$this->mail_to_hr($consultantTimesheetDetails,$consultantBasicDetails);
		$msg['status'] = 1;
		$msg['msg'] = "Success";
		$msg_array['msg'] = $msg;
		echo json_encode($msg_array);
		exit();
	}

	//goto Consultant Invoices
	public function consultantInvoice() {
		
		if ($this->session->userdata('id') && ($this->session->userdata('role') == 'Account' || $this->session->userdata('role') == 'HR' || $this->session->userdata('role') == 'consultant')) {
			$data = array();
			$data['entry_list'] = array();
			$data['emp_project'] = $this->consultant_timesheet_mod->emp_project($this->session->userdata('id'));
			$this->load->view('consultant_invoice', $data);
		} elseif (($this->session->userdata('id')) && ($this->session->userdata('role') !== 'Account')) {
			$this->load->view('no_access');
		} else {
			$this->load->view('login');
		}
	}

	//get Consultant Invoices
	public function getConsultantInvoices() {
		
		$response = array();
		$lastAccApprovedDate = '';
		$TimeSheetType = $this->input->post('timesheet_type');
		$user_role = $this->input->post('user_role');
		if ($TimeSheetType == 'monthly') {
			$month = $this->input->post('month');
			$year = $this->input->post('year');
			
			$timestamp = strtotime($month . ' ' . $year);
			$startDate = date('m-01-Y', $timestamp);
			$toDate = date('m-t-Y', $timestamp);
			$lastAccApprovedDate = $toDate;
		} elseif ($TimeSheetType == 'date_range') {
			$startDate = date('m-d-Y', strtotime($this->input->post('fromDate')));
			$toDate = date('m-d-Y', strtotime($this->input->post('toDate')));
			$lastAccApprovedDate = $toDate;
		}
		$response['startDate'] = $startDate;
		$response['toDate'] = $toDate;
		
		$response['timesheet_list'] = $this->consultant_timesheet_mod->getConsultantTimesheetDetails($startDate, $toDate, $user_role);

		// echo "<pre>"; print_r($response);exit;
		echo json_encode($response);
		die();
	}

	public function export(){
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$TimeSheetType = $this->input->post('timesheet_type');
		// $startDate_timestamp = strtotime($this->input->post('fromDate'));
		// $posttoDate_timestamp = strtotime($this->input->post('toDate'));
		$response = array();
		$response['ProjectICode'] = $this->input->post('ProjectICode');
		$response['project_name'] = $this->consultant_timesheet_mod->get_project_name($response['ProjectICode']);
		$response['emp_ids'] = $this->input->post('emp_ids');
		if($TimeSheetType == 'monthly'){
			$month = $this->input->post('month');
			$year = $this->input->post('year');
			$timestamp = strtotime($month. ' '.$year);
			$startDate = date('m-01-Y', $timestamp);
			$toDate  = date('m-t-Y', $timestamp);
			$filename = $response['project_name']."_Timesheet_".date('M_01_Y', $timestamp)."_to_".date('M_t_Y', $timestamp);	
			$response['excelstartDate']	= 	date('M 01,Y', $timestamp);
			$response['exceltoDate']	= 	date('M t,Y', $timestamp);
		}elseif($TimeSheetType == 'date_range'){
			$fromDatetimestamp = strtotime($this->input->post('fromDate'));
			$toDatetimestamp = strtotime($this->input->post('toDate'));
			$startDate = date('m-d-Y', $fromDatetimestamp);
			$toDate  = date('m-d-Y', $toDatetimestamp);
			$filename = $response['project_name']."_Timesheet_".date('M_d_Y',$fromDatetimestamp)."_to_".date('M_d_Y', $toDatetimestamp);
			$response['excelstartDate']	= 	date('M d,Y', $fromDatetimestamp);
			$response['exceltoDate']	= 	date('M d,Y', $toDatetimestamp);
		}
		
		$response['startDate'] = $startDate;
		$response['toDate'] = $toDate;
		$response['timesheet_list'] = $this->consultant_timesheet_mod->get_timesheet_details($response['emp_ids'],$response['ProjectICode'],$startDate,$toDate);
		// $response['timesheet_list'] = $this->consultant_timesheet_mod->get_timesheet_details_to_list($response['emp_ids'],$response['ProjectICode'],$startDate,$toDate);

		// echo "<pre>";print_r($response['timesheet_list']);die();


		$emp_list_hours = array();
		foreach($response['timesheet_list'] as $key => $value){
			$response['timesheet_list'][$key]['date'] = date('d-M-Y', strtotime($value['taskprogressdate']));
			$response['timesheet_list'][$key]['day'] = date('l', strtotime($value['taskprogressdate']));
			if(!isset($emp_list_hours[$value['employee_name']])){
				$emp_list_hours[$value['employee_name']] = $value['approved_hrs'];
			}else{
				$emp_list_hours[$value['employee_name']] = $this->sumHoursAndMinutes($emp_list_hours[$value['employee_name']] , $value['approved_hrs']);
			}
		}
		$emp_list = array_unique(array_column($response['timesheet_list'], 'employee_name'));
		asort($emp_list);
		
		// echo "<pre>";print_r($response);		
		// print_r($_POST);die();
		$this->excel->setActiveSheetIndex(0);
		
		$this->excel->getActiveSheet()->setTitle('TimeSheet');
		$Dstyle = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			),
			'font'  => array(						
				'size'  => 10,				
			)
		);
	
		$this->excel->getDefaultStyle()->applyFromArray($Dstyle);
		$this->excel->getActiveSheet()->setShowGridlines(false);
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
		$this->excel->getActiveSheet()->mergeCells('B2:M2');
		$this->excel->getActiveSheet()->SetCellValue('B2', 'CG-VAK Software & Exports Ltd.,');
		$this->excel->getActiveSheet()->mergeCells('B3:M3');
		$this->excel->getActiveSheet()->SetCellValue('B3', 'Log sheet for '.$response['project_name']);
			
		
		$titleFontSize = array(
			'font'  => array(
				'bold'  => true,				
				'size'  => 12,				
			));
		$this->excel->getActiveSheet()->getStyle('B2')->applyFromArray($titleFontSize);
		$this->excel->getActiveSheet()->getStyle('B3')->applyFromArray($titleFontSize);
		$this->excel->getActiveSheet()->getRowDimension('2')->setRowHeight(30);
		$this->excel->getActiveSheet()->getRowDimension('3')->setRowHeight(30);
		
		$formDate_row = 5;
		$toDate_row = 6;
		
		$this->excel->getActiveSheet()->mergeCells('C'.$formDate_row.':E'.$formDate_row);		
		$this->excel->getActiveSheet()->mergeCells('C'.$toDate_row.':E'.$toDate_row);
		$this->excel->getActiveSheet()->getStyle('C'.$toDate_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$this->excel->getActiveSheet()->getStyle('C'.$formDate_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$this->excel->getActiveSheet()->SetCellValue('B'.$formDate_row, 'From');		
		$this->excel->getActiveSheet()->SetCellValue('B'.$toDate_row, 'To');
		$this->excel->getActiveSheet()->SetCellValue('C'.$formDate_row,$response['excelstartDate']);		
		$this->excel->getActiveSheet()->SetCellValue('C'.$toDate_row, $response['exceltoDate']);
		$this->excel->getActiveSheet()->getRowDimension($formDate_row)->setRowHeight(20);
		$this->excel->getActiveSheet()->getRowDimension($toDate_row )->setRowHeight(20);
		$styleArray = array(
			'borders' => array(
			  'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			  )
			)
		  );		
		  
		$this->excel->getActiveSheet()->getStyle('B2:M3')->applyFromArray($styleArray);
		$this->excel->getActiveSheet()->getStyle('B5:E6')->applyFromArray($styleArray);
		//unset($styleArray);

		$summarized_row = 8;
		$this->excel->getActiveSheet()->mergeCells('B'.$summarized_row.':F'.$summarized_row);
		$this->excel->getActiveSheet()->SetCellValue('B'.$summarized_row, 'Summarized Timesheet');
		$summarizedFont = array(
			'font'  => array(
				'bold'  => true,				
				'size'  => 10,				
			));
		$this->excel->getActiveSheet()->getStyle('B'.$summarized_row)->applyFromArray($summarizedFont);
		$this->excel->getActiveSheet()->getRowDimension($summarized_row)->setRowHeight(20);
		$this->excel->getActiveSheet()->getStyle('B'.$summarized_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$summarizedHead_row = 9;
		$this->excel->getActiveSheet()->SetCellValue('B'.$summarizedHead_row, '#');
		$this->excel->getActiveSheet()->mergeCells('C'.$summarizedHead_row.':E'.$summarizedHead_row);
		$this->excel->getActiveSheet()->SetCellValue('C'.$summarizedHead_row, 'Name');
		$this->excel->getActiveSheet()->SetCellValue('F'.$summarizedHead_row, 'Total hours');
		$summarizedHeadFont = array(
			'font'  => array(
				'bold'  => true,				
				'size'  => 10,				
			));
		$this->excel->getActiveSheet()->getStyle('B'.$summarizedHead_row.':F'.$summarizedHead_row)->applyFromArray($summarizedHeadFont);
		$this->excel->getActiveSheet()->getRowDimension($summarizedHead_row)->setRowHeight(20);

		$summarizedDetail_row = $summarizedHead_row+1;
		$s = 1;
		foreach($emp_list as $emp_name){			
			$this->excel->getActiveSheet()->SetCellValue('B'.$summarizedDetail_row, $s);
			$this->excel->getActiveSheet()->mergeCells('C'.$summarizedDetail_row.':E'.$summarizedDetail_row);
			$this->excel->getActiveSheet()->SetCellValue('C'.$summarizedDetail_row, $emp_name);
			$this->excel->getActiveSheet()->SetCellValue('F'.$summarizedDetail_row, $emp_list_hours[$emp_name]);
			$summarizedDetail_row++;
			$s++;
		}
		
		$this->excel->getActiveSheet()->mergeCells('B'.$summarizedDetail_row.':E'.$summarizedDetail_row);
		$this->excel->getActiveSheet()->SetCellValue('B'.$summarizedDetail_row, 'Total Hours');
		$this->excel->getActiveSheet()->SetCellValue('F'.$summarizedDetail_row, array_sum($emp_list_hours));
		$this->excel->getActiveSheet()->getStyle('B'.$summarizedDetail_row)->applyFromArray($summarizedHeadFont);
		$this->excel->getActiveSheet()->getStyle('B'.$summarized_row.':F'.$summarizedDetail_row)->applyFromArray($styleArray);

		$timesheetDetails_row = $summarizedDetail_row+2;
		$this->excel->getActiveSheet()->mergeCells('B'.$timesheetDetails_row.':M'.$timesheetDetails_row);
		$this->excel->getActiveSheet()->SetCellValue('B'.$timesheetDetails_row, 'Detailed Timesheet');
		$timesheetDetailsFont = array(
			'font'  => array(
				'bold'  => true,				
				'size'  => 10,				
			));
		$i = 1;
				
		$this->excel->getActiveSheet()->getStyle('B'.$timesheetDetails_row)->applyFromArray($timesheetDetailsFont);		
		$this->excel->getActiveSheet()->getStyle('B'.$timesheetDetails_row.':M'.$timesheetDetails_row)->applyFromArray($styleArray);
		$this->excel->getActiveSheet()->getRowDimension($timesheetDetails_row)->setRowHeight(20);

		$timesheetDetailsHead_row = $timesheetDetails_row+1;
		for($i = 1;$i <= ($timesheetDetailsHead_row+1);$i++){
			$this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(30);
		}

		$this->excel->getActiveSheet()->getRowDimension($timesheetDetailsHead_row+1)->setRowHeight(30);
		$this->excel->getActiveSheet()->SetCellValue('B'.$timesheetDetailsHead_row, 'Date');
		$this->excel->getActiveSheet()->SetCellValue('C'.$timesheetDetailsHead_row, 'Day');
		$this->excel->getActiveSheet()->mergeCells('D'.$timesheetDetailsHead_row.':F'.$timesheetDetailsHead_row);
		$this->excel->getActiveSheet()->SetCellValue('D'.$timesheetDetailsHead_row, 'Name');
		$this->excel->getActiveSheet()->mergeCells('G'.$timesheetDetailsHead_row.':L'.$timesheetDetailsHead_row);
		$this->excel->getActiveSheet()->SetCellValue('G'.$timesheetDetailsHead_row, 'Work description');
		$this->excel->getActiveSheet()->SetCellValue('M'.$timesheetDetailsHead_row, 'Hours');
		$timesheetDetailsFont = array(
			'font'  => array(
				'bold'  => true,				
				'size'  => 10,				
			));
		$this->excel->getActiveSheet()->getStyle('B'.$timesheetDetailsHead_row.':M'.$timesheetDetailsHead_row)->applyFromArray($timesheetDetailsFont);

		$timesheetDetails_row = $timesheetDetailsHead_row;
		foreach($response['timesheet_list'] as $key => $value){
			++$timesheetDetails_row;
			$this->excel->getActiveSheet()->SetCellValue('B'.$timesheetDetails_row, $value['date']);
			$this->excel->getActiveSheet()->SetCellValue('C'.$timesheetDetails_row, $value['day']);
			$this->excel->getActiveSheet()->mergeCells('D'.$timesheetDetails_row.':F'.$timesheetDetails_row);
			$this->excel->getActiveSheet()->SetCellValue('D'.$timesheetDetails_row, $value['employee_name']);
			$this->excel->getActiveSheet()->mergeCells('G'.$timesheetDetails_row.':L'.$timesheetDetails_row);
			$this->excel->getActiveSheet()->SetCellValue('G'.$timesheetDetails_row, $value['workdescription']);
			$this->excel->getActiveSheet()->getStyle('G'.$timesheetDetails_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$this->excel->getActiveSheet()->getStyle('G'.$timesheetDetails_row)->getAlignment()->setWrapText(true);
			$this->excel->getActiveSheet()->SetCellValue('M'.$timesheetDetails_row, $value['approved_hrs']);
			$this->excel->getActiveSheet()->getRowDimension($timesheetDetails_row )->setRowHeight(40);			
		}
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
		
		$timesheetDetailsTotal_row = $timesheetDetails_row+1;
		$this->excel->getActiveSheet()->mergeCells('B'.$timesheetDetailsTotal_row.':L'.$timesheetDetailsTotal_row);
		$this->excel->getActiveSheet()->SetCellValue('B'.$timesheetDetailsTotal_row, 'Total Hours');
		$this->excel->getActiveSheet()->getStyle('B'.$timesheetDetailsTotal_row)->applyFromArray($titleFontSize);
		$this->excel->getActiveSheet()->SetCellValue('M'.$timesheetDetailsTotal_row, array_sum($emp_list_hours));
		$this->excel->getActiveSheet()->getRowDimension($timesheetDetailsTotal_row )->setRowHeight(25);
		$this->excel->getActiveSheet()->getStyle('B'.$timesheetDetailsHead_row.':M'.$timesheetDetailsTotal_row)->applyFromArray($styleArray);
		$this->excel->getActiveSheet()->getRowDimension($timesheetDetailsTotal_row)->setRowHeight(30);
		$filename = str_replace(' ', '_', $filename);		
		$filename = $filename.'.xls'; //save our workbook as this file name
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache

		
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
		
		$objWriter->save('php://output');
		
	}
}
