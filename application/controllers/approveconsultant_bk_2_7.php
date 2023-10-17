<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
		$this->load->library('excel');
		$this->load->library('pdf');
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
		if ($this->session->userdata('id')  &&  ($this->session->userdata('role') == 'lead' || $this->session->userdata('role') == 'hr' || $this->session->userdata('role') == 'acc' )) {
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
			
		} elseif(($this->session->userdata('id'))  && ($this->session->userdata('role') !== 'lead' || $this->session->userdata('role') !== 'hr' || $this->session->userdata('role') !== 'acc' )){
			$this->load->view('no_access');
		}else {
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
		$lastAccApprovedDate = '';
		$TimeSheetType = $this->input->post('timesheet_type');
		if ($TimeSheetType == 'monthly') {
			$month = $this->input->post('month');
			$year = $this->input->post('year');
			$timestamp = strtotime($month . ' ' . $year);
			$startDate = date('m-01-Y', $timestamp);
			$toDate = date('m-t-Y', $timestamp);
			if(USERROLE == 'lead')
			{
				$accApprovedEnteries = $this->consultant_timesheet_mod->toGetAccApprovedDate($this->input->post('ProjectICode'),$this->input->post('emp_ids'),$year, date('m',strtotime($month)));
				if(!empty($accApprovedEnteries)){
					$lastEntered = end($accApprovedEnteries);
					// echo '<pre>';
					// print_r($lastEntered);
					// echo '</pre>';
					// die;
					if(!empty($lastEntered)){
						$lastAccApprovedDate = $lastEntered['AccountsApprovedOn'];
					}
				}
			}
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

			if(isset($value['IsApproved'])){
				if(isset($lastAccApprovedDate) && (strtotime($lastAccApprovedDate) >  strtotime($value['LeadModifiedDate']))){
					$response['timesheet_list'][$key]['accApproved'] = true;
				} else{
					$response['timesheet_list'][$key]['accApproved'] = false;
				}
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

		$updateStatus = $this->consultant_timesheet_mod->updateBillableHours($id, $update);
		
		$taskRecords = $this->consultant_timesheet_mod->getConsultantTaskProgressById($id);
		$taskProgressData = $taskRecords[0]['TaskProgressDate'];
		$year = date('Y', strtotime($taskProgressData));
		$month = date('m', strtotime($taskProgressData));
		$approvedHours = $taskRecords[0]['ManHours'];
		$loggedHours = $taskRecords[0]['ApprovedHours'];
		$updateMonthlyTimesheetStatus  = $this->consultant_timesheet_mod->toggleStoreMonthlyData($taskProjectICode, $taskEmployeeICode, $year, $month, $approvedHours, $loggedHours, $update['ModifiedByLead']);	

		if($updateStatus && $updateMonthlyTimesheetStatus){
			$this->session->set_userdata(array(
				'msg' => "upt",
			));
		}
	}


	public function editLeadApproval(){
		
		$update['Billable'] = 'false';
		$update['IsApproved'] = 'false';
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

	public function deleteHrApprovalEnteries($id){
		$dataParams=array(
			'IsAccountsApproved' => 0,
			'IsHrApproved' => 0,
		);
		$this->consultant_timesheet_mod->updateConsultantMonthlyData($id ,$dataParams);
	}


	public function updatePaymentApproval(){
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$month = $this->input->post('month');
		$year = $this->input->post('year');
		$timestamp = strtotime($month . ' ' . $year);
		$startDate = date('m-01-Y', $timestamp);
		$toDate = date('m-t-Y', $timestamp);

		$id =  $this->input->post('id');

		// echo $id;
		$consultantMonthlyEntry = $this->consultant_timesheet_mod->getConsultantMonthlyDataById($id)[0];

		$paymentApproval = [];

		$paymentApproval['invoice_no'] = $consultantMonthlyEntry['employee_name'].'_'.$consultantMonthlyEntry['BillMonth'].'_'.$consultantMonthlyEntry['BillYear'];
		$paymentApproval['department_id'] = 2;
		$paymentApproval['invoice_category_id'] = 9;
		$paymentApproval['invoice_status'] = 1;
		$paymentApproval['date'] = $consultantMonthlyEntry['HrApprovedOn'];
		$paymentApproval['invoice_amount'] = $consultantMonthlyEntry['HrApprovedBillAmount'];
		$paymentApproval['payment_type'] = 1;
		$paymentApproval['supplier_id'] = 31;
		$paymentApproval['payment_paid'] = 0;
		$paymentApproval['paid'] = 0;


		// $invoice_id = $this->consultant_timesheet_mod->insertPaymentApproval($paymentApproval);
		// $paymentApprovalComment['invoice_id'] = $invoice_id;
		$paymentApprovalComment['invoice_id'] = 0;
		$paymentApprovalComment['comments'] = 'consultant invoice entered';
		$paymentApprovalComment['assigned_by'] = $this->session->id;
		$paymentApprovalComment['send_to'] = 3;
		$paymentApprovalComment['status'] = 1;

		// $this->consultant_timesheet_mod->insertPaymentApprovalComments($paymentApprovalComment);


		$timestamp = strtotime($month. ' '.$year);
		$response= array();
		$response['ProjectICode'] = $consultantMonthlyEntry['ProjectIcode'];
		$response['project_name'] = $this->consultant_timesheet_mod->get_project_name($response['ProjectICode']);
		$response['emp_ids'] = [$consultantMonthlyEntry['EmployeeIcode']];
		$response['excelstartDate']	= 	date('M 01,Y', $timestamp);
		$response['exceltoDate']	= 	date('M t,Y', $timestamp);
		$monthInDate = date('m',strtotime($month));
		$response['startDate'] = $startDate;
		$response['toDate'] = $toDate;

		$response['timesheet_list'] = $this->consultant_timesheet_mod->get_timesheet_details_to_list($response['emp_ids'],$response['ProjectICode'],$startDate,$toDate);

		$emp_list_hours = array();
		foreach($response['timesheet_list'] as $key => $value){
			$response['timesheet_list'][$key]['date'] = date('d-M-Y', strtotime($value['taskprogressdate']));
			$response['timesheet_list'][$key]['day'] = date('l', strtotime($value['taskprogressdate']));
			if(!isset($emp_list_hours[$value['employee_name']])){
				$emp_list_hours[$value['employee_name']] = $value['Hours_Worked'];
			}else{
				$emp_list_hours[$value['employee_name']] = $emp_list_hours[$value['employee_name']] + $value['Hours_Worked'];
			}
		}
		$emp_list = array_unique(array_column($response['timesheet_list'], 'employee_name'));
		asort($emp_list);
		

		echo '<pre>';
		print_r($emp_list);
		print_r($response);
		echo '</pre>';
		die;
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
			$this->excel->getActiveSheet()->SetCellValue('M'.$timesheetDetails_row, $value['Hours_Worked']);
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
		$filename = "Internal_Consultant_Timesheet_System_".$consultantMonthlyEntry['employee_name'].'_'.$consultantMonthlyEntry['BillMonth'].'_'.$consultantMonthlyEntry['BillYear'];
		$filename = str_replace(' ', '_', $filename);		
		$filename = $filename.'.xls'; //save our workbook as this file name
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache

		
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
		
		$objWriter->save('php://output');
	}


}
