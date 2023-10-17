<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class CosultantProgress extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		/* -Session LogOut Catch Clear  - */
		$this->output->set_header('cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header("cache-Control: post-check=0, pre-check=0", false);
		$this->output->set_header("Pragma: no-cache");
		$this->output->set_header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		$this->load->helper('url');
		$this->load->model('consultant_task_detail');
		$this->load->model('mgeneral');
		$this->load->model('Swipe_detail');
	}

	/**
	 * Listing the Progress entries
	 * */
	public function index()
	{
		$this->progress();
	}

	public function progress()
	{

		if (($this->session->userdata('id')) && ($this->session->userdata('role') == 'consultant')) {
			/* - Get all Project Name - */
			$data['emp_project'] = $this->consultant_task_detail->consultant_project($this->session->userdata('id'));
			if ($this->input->post("entry_progress_filter", TRUE)) {
				delete_cookie("project_filter");
				$search_project_id = $this->input->post("entry_progress_filter", TRUE);
				$cookie = array(
					'name' => "project_filter",
					'value' => $search_project_id,
					'expire' => "86500"
				);
				$this->input->set_cookie($cookie);
			} else if (get_cookie('project_filter')) {
				$search_project_id = get_cookie('project_filter');
				/* - Calling Helper function to check Prj-Id Exist in that - */
				$check = in_array($search_project_id, $data['emp_project'], "ProjectICode");
				$search_project_id = ($check) ? $search_project_id : null;
			} else {
				$search_project_id = null;
			}
			$data['search_project_id'] = $search_project_id;
			$data['progress_list'] = $this->consultant_task_detail->progress_list($this->session->userdata('id'), $search_project_id);
			/* echo '<pre>';
			print_r($data);
			echo '</pre>';
			die; */
			$this->load->view('consultant_progress', $data);
		} elseif (($this->session->userdata('id')) && ($this->session->userdata('role') !== 'consultant')) {
			$this->load->view('no_access');
		} else {
			$this->load->view('consultant_login');
		}
	}

	/**
	 * Listing the Progress Entry Search By Project
	 * */
	public function progress_search()
	{
		if ($this->session->userdata('id')) {
			if ($this->input->post("entry_progress_filter")) {
				delete_cookie("project_filter");
				$search_project_id = $this->input->post("entry_progress_filter");
				$cookie = array(
					'name' => "project_filter",
					'value' => $search_project_id,
					'expire' => "86500"
				);
				$this->input->set_cookie($cookie);
			} else if ($this->input->post("entry_progress_filter") == "") {
				delete_cookie("project_filter");
				$search_project_id = null;
				$cookie = array(
					'name' => "project_filter",
					'value' => $search_project_id,
					'expire' => "86500"
				);
				$this->input->set_cookie($cookie);
			} else if (get_cookie('project_filter')) {
				$search_project_id = get_cookie('project_filter');
			} else {
				$search_project_id = null;
			}

			$data['search_project_id'] = $search_project_id;
			$data['emp_project'] = $this->consultant_task_detail->consultant_project($this->session->userdata('id'));
			$data['progress_list'] = $this->consultant_task_detail->progress_list($this->session->userdata('id'), $search_project_id);

			$this->load->view('consultant_progress', $data);
		} else {
			$this->load->view('consultant_login');
		}
	}

	public function returnEnteredDate($enteredDate)
	{
		return  date('Y-m-d h:i:s', strtotime($enteredDate));
	}
	/**
	 * Insert the New Progress Entrys
	 * */
	public function progress_insert_ajax()
	{
		
		if ($this->session->userdata('id')) {

			$tbl_work_desc = _DB_SYNERGY . TBL_CONSULTANT_PROJECT_PROGRESS;
			$data['ProjectICode'] = $this->input->post('projectICode');
			$data['TaskICode'] = $this->input->post('task_code');
			$data['EmployeeICode'] = $this->session->userdata('id');
			$data['TaskProgressDate'] = date('Y-m-d 00:00:00', strtotime($this->input->post('prog_date')));
			$task_history = $this->consultant_task_detail->get_task_history_entry($tbl_work_desc, $data);
			$postedDate = date('Y-m-d 00:00:00', strtotime($this->input->post('prog_date')));
			$enteredDates = array_column($task_history, 'TaskProgressDate');
			
			// if(empty($task_history)){
			if(empty($task_history) || !empty($task_history)){
				$page_no = $this->input->post('page');
				$task_code_lists = $this->input->post('task_code');
				$task_proj_name = $this->input->post('projectICode');
				$task_work_desc = $this->input->post('work_desc');
				$task_mans_hour = $this->input->post('mans_hour');
				$task_prog_date = $this->input->post('prog_date');
				$late_entry_reson = $this->input->post('late_entry_reson');
				$lead_id_report = $this->input->post('lead_id_report');
				$hr_mail = $this->input->post('hr_mail');
				$msg = $msg_array = [];

				if (trim($task_work_desc) != "" && trim($task_mans_hour) != "" && trim($task_prog_date) != "") {
					$tbl_work_late_entry = _DB_SYNERGY . TBL_LATEENTRY_REASON;
					$tbl_work_desc = _DB_SYNERGY . TBL_CONSULTANT_PROJECT_PROGRESS;

					$data = array(
						"CompanyICode" => 1,
						"ProjectICode" => $task_proj_name,
						"TaskICode" => $task_code_lists,
						"EmployeeICode" => $this->session->userdata('id'),
						"WorkDescription" => $task_work_desc,
						"ManHours" => str_replace(":", ".", $task_mans_hour),
						"TaskProgressDate" => date('Y-m-d', strtotime($task_prog_date)),
						"CreatedDate" => date('Y-m-d H:i:s'),
						"IsActive" => $this->consultant_task_detail->def_active(),
						"TaskProgress" => 1,
						"CreatedBy" => $this->session->userdata('id'),
					);
					$this->consultant_task_detail->insert_entry($tbl_work_desc, $data);
					$late_reason = array(
						"employeeicode" => $this->session->userdata('id'),
						"taskdate" => date('Y-m-d', strtotime($task_prog_date)),
						"entereddate" => date('Y-m-d H:i:s'),
						"createdon" => date('Y-m-d H:i:s'),
						"lateentryreason" => $late_entry_reson,
						"isactive" => 1,
						"isemailsenthr" => $hr_mail,
						"timesheet_swipe" => 'T',
						"proj_non_proj" => 'P',
						"createdby" => $this->session->userdata('id'),
						"reportingleadicode" => $lead_id_report
					);
					$this->consultant_task_detail->insert_entry($tbl_work_late_entry, $late_reason);
				}
				// - Set Seesion Notification Message -
				$this->session->set_userdata(array(
					'msg' => "progress",
				));
				$msg['status'] = 1;
				$msg['msg'] = "Success";
				$msg_array['msg'] = $msg;
				echo json_encode($msg_array);
				exit();
			
			}else{
				$data = $task_history[0];
				if($data['IsApproved']){
					$msg['status'] = 0;
					$msg['msg'] = "Lead has already approved this task progress. Check with him.";
					$msg_array['msg'] = $msg;
					echo json_encode($msg_array);
					exit();
				}else{
					$tbl_work_desc = _DB_SYNERGY . TBL_CONSULTANT_PROJECT_PROGRESS;
					$updateData['WorkDescription'] = $this->input->post('work_desc');
					$this->consultant_task_detail->update_entry($tbl_work_desc, $updateData,'TaskProgressICode',$data['TaskProgressICode']);
					// - Set Seesion Notification Message -
					$this->session->set_userdata(array(
						'msg' => "progress",
					));
					$msg['status'] = 1;
					$msg['msg'] = "Success";
					$msg_array['msg'] = $msg;
					echo json_encode($msg_array);
					exit();
				}
			}
		} else {
			$msg['status'] = 0;
			$msg['msg'] = "Failed";
			$msg_array['msg'] = $msg;
			echo json_encode($msg_array);
			exit();
		}
	} 

//    public function progress_insert()
//    {
//        if ($this->session->userdata('id')) {
//            $this->input->post('page');
//            $task_code_lists = $this->input->post('TaskICode');
//            $task_proj_name = $this->input->post('ProjectICode');
//            $task_work_desc = $this->input->post('WorkDescription');
//            $task_mans_hour = $this->input->post('ManHours');
//            $task_prog_date = $this->input->post('TaskProgressDate');
//            $i = 0;
//
//            echo "<pre>";
//            print_r($task_mans_hour);
//            exit();
//
//            foreach ($task_code_lists as $value) {
//                if (isset($task_work_desc[$value][0]) && isset($task_mans_hour[$value][0]) && isset($task_prog_date[$value][0])) {
//                    if (trim($task_work_desc[$value][0]) != "" && trim($task_mans_hour[$value][0]) != "" && trim($task_prog_date[$value][0]) != "") {
//                        $tbl_work_desc = $this->consultant_task_detail->db1_after() . 'CGVak_Consultant_Project_Tasks_Progress';
//                        $tbl_task = $this->consultant_task_detail->db1_after() . 'CGvak_Consultant_Project_Tasks';
//                        /* Insert Actual Start */
//                        $get_actual_start_date = $this->consultant_task_detail->get_actual_start_date($value);
//                        if (!$get_actual_start_date) {
//                            $data_actual_start_date = array("ActualStartDate" => date('Y-m-d H:i:s'));
//                            $upt_astart_date = $this->consultant_task_detail->upadate_entry($tbl_task, $data_actual_start_date, 'TaskICode', $value);
//                        }
//                        $data = array(
//                            "CompanyICode" => 1,
//                            "ProjectICode" => $task_proj_name[$i],
//                            "TaskICode" => $value,
//                            "EmployeeICode" => $this->session->userdata('id'),
//                            "WorkDescription" => $task_work_desc[$value][0],
//                            "ManHours" => str_replace(":", ".", $task_mans_hour[$value][0]),
//                            "TaskProgressDate" => date('Y-m-d', strtotime($task_prog_date[$value][0])),
//                            "CreatedDate" => date('Y-m-d H:i:s'),
//                            "IsActive" => $this->consultant_task_detail->def_active(),
//                            "TaskProgress" => 1,
//                            "CreatedBy" => $this->session->userdata('id'),
//                        );
//                        $this->getEntryByDate(date('Y-m-d', strtotime($task_prog_date[$value][0])));
//                        echo "=======>";
//                        echo "<pre>";
//                        print_r($data);
//                        echo "</pre>";
//                    }
//                }
//                $i++;
//            }
//
//            /* - Set Seesion Notification Message - */
//            $this->session->set_userdata(array(
//                'msg' => "progress",
//            ));
//            exit();
//            redirect("/synergy/consultant_task/progress/?page=" . $page_no, 'refresh');
//        } else {
//            $this->load->view('consultant_login');
//        }
//    }

	public function getEntryByDate($manual_date)
	{
		$reporting_to = $this->mgeneral->get_leaders_list();
		$manual_date = date("Y-m-d", strtotime($manual_date));
		$data['swipe_details'] = $this->Swipe_detail->getSwipeDetails($this->session->userdata('id'), $manual_date, $manual_date, $reporting_to);
		if (count($data['swipe_details']) > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

//    public function getEntryByDateCallproducer($manual_date)
//    {
//        $data = $this->consultant_task_detail->getEnterDetails($this->session->userdata('id'), $manual_date);
//        if (isset($data[0])) {
//            if (count($data[0]) > 0) {
//                return $data[0];
//            } else {
//                return FALSE;
//            }
//        } else {
//            return FALSE;
//        }
//    }

	/*     * * Get Task History * */

	public function gettaskhistory()
	{
		// $TimeSheetType = $this->input->post('timesheet_type');
		if($this->input->post('month')) {
			$TimeSheetType = 'monthly';
			if ($TimeSheetType == 'monthly') {
				$month = $this->input->post('month');
				$year = $this->input->post('year');
				// $month = 'December';
				// $year = '2022';
				$timestamp = strtotime($month . ' ' . $year);
				$startDate = date('m-01-Y', $timestamp);
				$toDate = date('m-t-Y', $timestamp);
			} elseif ($TimeSheetType == 'date_range') {
				$startDate = date('m-d-Y', strtotime($this->input->post('fromDate')));
				$toDate = date('m-d-Y', strtotime($this->input->post('toDate')));
			}
		} else {
			$startDate = null;
			$toDate = null;
		}
		$tbl_work_desc = _DB_SYNERGY . TBL_CONSULTANT_PROJECT_PROGRESS;
		$data['ProjectICode'] = $this->input->post('PorjectIcode');
		$data['TaskICode'] = $this->input->post('TaskIcode');
		$data['EmployeeICode'] = $this->session->userdata('id');
		
		$task_history = $this->consultant_task_detail->get_task_history_entry($tbl_work_desc, $data,$startDate, $toDate);
		$result = '';
		$initialHour = 0.00;
		if (!empty($task_history)) {
			$i = 1;
			foreach ($task_history as $res) {
				$action = $res["IsApproved"] ? 
				"<div onclick=\"alert('Lead has approved this task entry. Consult lead to unapprove task on this date')\">Approved</div>
				<input type='hidden' id='Time_TaskICode' name='TaskICode' value=".$res['TaskICode'].">
				<input type='hidden' id='Time_ProjectICode' name='ProjectICode' value=".$res['ProjectICode'].">" : 
				"<button type='button' class='btn btn-red' onclick=\"deleteTaskProgressEntery('".$res['TaskProgressICode']."','".$res['TaskProgressDate']."','".$res['ProjectICode']."','".$res['TaskICode']."')\">Delete</button>
				<input type='hidden' id='Time_TaskICode' name='TaskICode' value=".$res['TaskICode'].">
				<input type='hidden' id='Time_ProjectICode' name='ProjectICode' value=".$res['ProjectICode'].">";
				$result .= "<tr>
                                <td>" . $i . "</td>
                                <td>" . date('d-m-Y', strtotime($res['TaskProgressDate'])) . "</td>
                                <td>" . $res['WorkDescription'] . "</td>
                                <td>" . str_replace(".", ":", $res['ManHours']) . "</td>
                                <td>" . date('d-m-Y H:i:s', strtotime($res['CreatedDate'])) . "</td>
                                <td>
									". $action  ."
								</td>
                            </tr>";
				$initialHour = $this->sumHoursAndMinutes($initialHour , $res['ManHours']);
				$i++;
			}
			
			$result .= "<tr>
                                <td></td>
                                <td></td>
                                <td> Total</td>
                                <td>" . str_replace(".", ":", $initialHour) . "</td>
                                <td></td>
                                <td></td>
                            </tr>";
		} else {
			$result .= "<tr><td colspan='5' >No Records Found</td></tr>
			<input type='hidden' id='Time_TaskICode' name='TaskICode' value=".$data['TaskICode'].">
			<input type='hidden' id='Time_ProjectICode' name='ProjectICode' value=".$data['ProjectICode'].">";
		}
		echo $result;
	}

	public function OverallTime($allTimes) {
		$minutes = 0;
		$hours = 0;
		
		foreach ($allTimes as $time) {
			
			$time_one = number_format((float)$time,2);
			list($hh, $mm) = explode('.', strval($time_one));
			
			
			$h = ($hh == '00' || $hh == '.00') ? 0 : (int)$hh;
			$m = ($mm == '00' || $mm == '.00') ? 0 : (int)$mm;
			
			$minutes += $h * 60;
			$minutes += $m;
			
			
			
			
		}

		$hours = floor($minutes / 60);
		$min = $minutes - ($hours * 60);
		$mins_formatted = (strlen((string)$min) == 1) ? '0'.$min : $min;
		print('*****hoursAndMin******');print($hours.".".$mins_formatted);
		return $hours.".".$mins_formatted;
	}

	public function sumHoursAndMinutes($time_one,$time_two) {
		$hoursAndMinutesArray = [];
		$time_one = ($time_one == '' || $time_one == '.00') ? '00.00' : $time_one;
		$time_two = ($time_two == '' || $time_two == '.00') ? '00.00' : $time_two;
		array_push($hoursAndMinutesArray, $time_one, $time_two);
		print('*****hoursAndMinutesArray******');print_r($hoursAndMinutesArray);
		// print_r($hoursAndMinutesArray);exit;
		return $this->OverallTime($hoursAndMinutesArray);
	}

	/* Get whether there was any entry on that date */
	public function taskProgressEntryOnThatdate(){
		$tbl_work_desc = _DB_SYNERGY . TBL_CONSULTANT_PROJECT_PROGRESS;
		$data['ProjectICode'] = $this->input->post('projectICode');
		$data['TaskICode'] = $this->input->post('task_code');
		$data['EmployeeICode'] = $this->session->userdata('id');
		$data['TaskProgressDate'] = date('Y-m-d 00:00:00', strtotime($this->input->post('prog_date')));
		$task_history = $this->consultant_task_detail->get_task_history_entry($tbl_work_desc, $data);

		if(!empty($task_history)){
			echo 1;
		}else{
			echo 0;
		}
		die;
	}

	public function deleteTaskProgressEntery(){
		$tbl_work_desc = _DB_SYNERGY . TBL_CONSULTANT_PROJECT_PROGRESS;
		$taskProgressICode =  $this->input->post('task_progress_id');
		$taskProgressDate =  $this->input->post('task_progress_entry_date');
		$taskProgressDate =  date('d-m-Y',strtotime($taskProgressDate));
		$this->consultant_task_detail->delete_entry($tbl_work_desc, "TaskProgressICode",$taskProgressICode);
		$msg['status'] = 1;
		$msg['msg'] = "Task entry on the date $taskProgressDate was deleted";
		echo json_encode($msg);
		exit();
	}


}
