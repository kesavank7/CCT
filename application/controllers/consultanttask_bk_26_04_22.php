<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class ConsultantTask extends CI_Controller
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


	/** -----------------------------------------------------------------------------------------------------------------------
	 * ------------------------------------------------- Task Entry --------------------------------------------------------------
	 * ----------------------------------------------------------------------------------------------------------------------- * */
	/*
	  Listing New Task Entrys
	 */

	public function index()
	{
		$this->entry();
	}

	public function entry()
	{
		if (($this->session->userdata('id')) && (!$this->session->userdata('role'))) {
			/* - Get all Project Name - */
			$data['emp_project'] = $this->consultant_task_detail->emp_project($this->session->userdata('id'));
			$data['consultantList'] = $this->consultant_task_detail->consultantTypeList();
			if (isset($_POST['entry_search_filter']) || get_cookie('project_filter')) {
				if ($this->input->post("entry_search_filter")) {
					delete_cookie("project_filter");
					$search_project_id = $this->input->post("entry_search_filter");
					$cookie = array(
						'name' => "project_filter",
						'value' => $search_project_id,
						'expire' => "86500"
					);
					$this->input->set_cookie($cookie);
				} else if (get_cookie('project_filter')) {
					$search_project_id = get_cookie('project_filter');
					/* - Calling Helper function to check Prj-Id Exist in that - */
					// $check = in_multiarray($search_project_id, $data['emp_project'], "ProjectICode");
					$check = in_array($search_project_id, $data['emp_project'], "ProjectICode");
					$search_project_id = ($check) ? $search_project_id : null;
				} else if ($this->input->post("entry_search_filter") == "") {
					$search_project_id = null;
				} else {
					$search_project_id = null;
				}
			} else {
				$search_project_id = null;
			}

			$data['search_project_id'] = $search_project_id;
			$data['entry_list'] = $this->consultant_task_detail->get_entry_list($this->session->userdata('id'), $search_project_id);
//            $data['scenario_list'] = $this->consultant_task_detail->get_scenarios($data['entry_list']);
			$this->load->view('consultant_entry', $data);
		} elseif (($this->session->userdata('id')) && ($this->session->userdata('role') == 'consultant')) {
			$this->load->view('no_access');
		} else {
			$this->load->view('login');
		}
	}

	/*
	  List the Task Entries in Project Filter
	 */

	public function entry_search()
	{
		if ($this->session->userdata('id')) {
			$data['consultantList'] = $this->consultant_task_detail->consultantTypeList();
			if (isset($_POST['entry_search_filter']) || get_cookie('project_filter')) {
				if ($this->input->post("entry_search_filter")) {
					delete_cookie("project_filter");
					$search_project_id = $this->input->post("entry_search_filter");
					$cookie = array(
						'name' => "project_filter",
						'value' => $search_project_id,
						'expire' => "86500"
					);
					$this->input->set_cookie($cookie);
				} else if ($this->input->post("entry_search_filter") == "") {
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
			} else {
				$search_project_id = null;
			}

			$data['search_project_id'] = $search_project_id;
            $data['emp_project'] = $this->consultant_task_detail->emp_project($this->session->userdata('id'));
            $data['entry_list'] = $this->consultant_task_detail->get_entry_list($this->session->userdata('id'), $search_project_id);
			/* Getting Scenario List */
//            $data['scenario_list'] = $this->consultant_task_detail->get_scenarios($data['entry_list']);

			$this->load->view('consultant_entry', $data);
		} else {
			$this->load->view('login');
		}
	}

	/*
	  Insert New Task Entrys
	 */

	public function entry_insert()
	{
		if ($this->session->userdata('id')) {
			$data = $this->input->post();
			delete_cookie("project_filter");
			$search_project_id = $data['ProjectICode'];
			$cookie = array(
				'name' => "project_filter",
				'value' => $search_project_id,
				'expire' => "86500"
			);
			$this->input->set_cookie($cookie);
			$result_phase = $this->consultant_task_detail->get_project_phase($data['PhaseTypeICode'], $data['ProjectICode']);

			$data['ProjectPhaseICode'] = $result_phase[0]['ProjectPhaseICode'];
			unset($data['PhaseTypeICode']);
			$TaskTypeICode = $data['TaskTypeICode'];

			$data['EstimatedHours'] = str_replace(":", ".", $data['EstimatedHours']);
			/* Convert Date in Y-m-d */
			$data['TaskStartDate'] = date('Y-m-d', strtotime($data['TaskStartDate']));
			$data['TaskEndDate'] = date('Y-m-d', strtotime($data['TaskEndDate']));

			$tbl_task = _DB_SYNERGY . TBL_CONSULTANT_TASK_ENTRY;
			$ConsultantICode = $data['ConsultantICode'];
			unset($data['ConsultantICode']);

			foreach ($ConsultantICode as $ConsICode) {
				$data['ConsultantICode'] = $ConsICode;
				$this->consultant_task_detail->insert_entry($tbl_task, $data);
			}

			/* - Set Seesion Notification Message - */
			$this->session->set_userdata(array(
				'msg' => "new",
			));
			// redirect(base_url() . 'consultanttask/', 'refresh');
			$this->entry();
		} else {
			$this->load->view('login');
		}
	}

	/*
	  Update Row, Task values (AJAX)
	 */

	public function entry_edit()
	{
		if ($this->session->userdata('id')) {
			$data = $this->consultant_task_detail->get_single_list($this->input->post('TaskICode'));
			echo json_encode($data);
		} else {
			$this->load->view('login');
		}
	}

	/*
	  Update The Task entries
	 */

	public function entry_update()
	{
		if ($this->session->userdata('id')) {
			$data = $this->input->post();
			$page_no = $data['page'];
			unset($data['page']);
			$id = $data['TaskICode'];
			unset($data['TaskICode']);
			/* Updating Cookie Value */
			delete_cookie("project_filter");
			$search_project_id = $data['ProjectICode'];
			$cookie = array(
				'name' => "project_filter",
				'value' => $search_project_id,
				'expire' => "86500"
			);
			$this->input->set_cookie($cookie);

			/* Convert Time to Decimal */
			$data['EstimatedHours'] = str_replace(":", ".", $data['EstimatedHours']);
			/* Convert Date in Y-m-d */
			$data['TaskStartDate'] = date('Y-m-d', strtotime($data['TaskStartDate']));
			$data['TaskEndDate'] = date('Y-m-d', strtotime($data['TaskEndDate']));

			$tbl_task = _DB_SYNERGY . TBL_CONSULTANT_TASK_ENTRY;;
			/* Update in Task Table */
			$task_insert_id = $this->consultant_task_detail->update_entry($tbl_task, $data, 'TaskICode', $id);

			/* - Set Seesion Notification Message - */
			$this->session->set_userdata(array(
				'msg' => "upt",
			));
			redirect(base_url() . "consultanttask/entry?page=" . $page_no, 'refresh');
		} else {
			$this->load->view('login');
		}
	}

	/*
	  In Active the Entry Task (Delete)
	 */

	public function entry_inactive()
	{
		if ($this->session->userdata('id')) {
			$task_code = $this->input->get('request_id');
			$page_no = $this->input->get('page');

			$data_task = array("IsActive" => $this->consultant_task_detail->def_in_active());

//            $data_sceno = array("isactive" => $this->consultant_task_detail->def_in_active());
			$data_progress = array("IsActive" => $this->consultant_task_detail->def_in_active());

			$tbl_task = $this->consultant_task_detail->db1_after() . 'CGvak_Consultant_Project_Tasks';
//            $tbl_sceno = $this->consultant_task_detail->db1_after() . 'CGvak_Project_tasks_TestScenario';
			$tbl_progress = $this->consultant_task_detail->db1_after() . 'CGVak_Project_Tasks_Progress';

			/* Update in Sceno Table - IN-ACTIVE */
			$tasks_inactive = $this->consultant_task_detail->update_entry($tbl_task, $data_task, 'TaskICode', $task_code);
//            $sceno_inactive = $this->consultant_task_detail->upadate_entry($tbl_sceno, $data_sceno, 'TaskICode', $task_code);
			$progre_inactive = $this->consultant_task_detail->update_entry($tbl_progress, $data_progress, 'TaskICode', $task_code);

			/* - Set Seesion Notification Message - */
			$this->session->set_userdata(array(
				'msg' => "act",
			));
			redirect(base_url() . "consultanttask/entry?page=" . $page_no, 'refresh');
		} else {
			$this->load->view('login');
		}
	}

	/** -----------------------------------------------------------------------------------------------------------------------
	 * ------------------------------------------------- Entry Listing -----------------------------------------------------------
	 * ----------------------------------------------------------------------------------------------------------------------- * */

	/**
	 * Listing the Task Entries, Search By Project
	 * */
//    public function list_search()
//    {
//        if ($this->session->userdata('id')) {
//            if ($this->input->post("entry_progress_filter")) {
//                delete_cookie("project_filter");
//                $search_project_id = $this->input->post("entry_progress_filter");
//                $cookie = array(
//                    'name' => "project_filter",
//                    'value' => $search_project_id,
//                    'expire' => "86500"
//                );
//                $this->input->set_cookie($cookie);
//            } else if ($this->input->post("entry_progress_filter") == "") {
//                delete_cookie("project_filter");
//                $search_project_id = null;
//                $cookie = array(
//                    'name' => "project_filter",
//                    'value' => $search_project_id,
//                    'expire' => "86500"
//                );
//                $this->input->set_cookie($cookie);
//            } else if (get_cookie('project_filter')) {
//                $search_project_id = get_cookie('project_filter');
//            } else {
//                $search_project_id = null;
//            }
//
//            $data['search_project_id'] = $search_project_id;
//            $data['emp_project'] = $this->consultant_task_detail->emp_project($this->session->userdata('id'));
//            /* $data['progress_list'] = $this->consultant_task_detail->progress_list($this->session->userdata('id'), $search_project_id); */
//            $data['task_list'] = $this->consultant_task_detail->get_all_task_listing($this->session->userdata('id'), $search_project_id);
//            $this->load->view('listing', $data);
//        } else {
//            $this->load->view('consultant_login');
//        }
//    }

	/**
	 * Listing the Progress entries
	 * */
//    public function listing()
//    {
//        if ($this->session->userdata('id')) {
//            /* - Get all Project Name - */
//            $data['emp_project'] = $this->consultant_task_detail->emp_project($this->session->userdata('id'));
//
//            if ($this->input->post("entry_progress_filter", TRUE)) {
//                delete_cookie("project_filter");
//                $search_project_id = $this->input->post("entry_progress_filter", TRUE);
//                $cookie = array(
//                    'name' => "project_filter",
//                    'value' => $search_project_id,
//                    'expire' => "86500"
//                );
//                $this->input->set_cookie($cookie);
//            } else if (get_cookie('project_filter')) {
//                $search_project_id = get_cookie('project_filter');
//                /* - Calling Helper function to check Prj-Id Exist in that - */
//                $check = in_multiarray($search_project_id, $data['emp_project'], "ProjectICode");
//                $search_project_id = ($check) ? $search_project_id : null;
//            } else {
//                $search_project_id = null;
//            }
//
//            $data['search_project_id'] = $search_project_id;
//            /* $data['progress_list'] = $this->consultant_task_detail->progress_list($this->session->userdata('id'), $search_project_id); */
//            $data['task_list'] = $this->consultant_task_detail->get_all_task_listing($this->session->userdata('id'), $search_project_id);
//            $this->load->view('listing', $data);
//        } else {
//            $this->load->view('consultant_login');
//        }
//    }

	/**
	 * In-activate the Task Entries
	 * */
//    public function list_delete()
//    {
//        if ($this->session->userdata('id')) {
//            $inactive_id = $this->input->get('id');
//            $page_no = $this->input->get('page');
//            $this->input->get('active');
//            $active_status = 0;
//            if ($this->input->get('active')) {
//                $active_status = $this->input->get('active');
//            }
//            $return = $this->consultant_task_detail->inactive_record($inactive_id, $active_status);
//            if ($return) {
//                /* - Set Seesion Notification Message - */
//                $this->session->set_userdata(array('msg' => "act"));
//                redirect(base_url() . "/consultant_task/listing?page=" . $page_no, 'refresh');
//            } else {
//                /* - Set Seesion Notification Message - */
//                $this->session->set_userdata(array('msg' => "lError"));
//                redirect(base_url() . "consultant_task/listing?page=" . $page_no, 'refresh');
//            }
//        } else {
//            $this->load->view('consultant_login');
//        }
//    }

	/*     * ** Get Phase list ** */

	public function phaseList()
	{
		$result = $this->consultant_task_detail->project_phase($this->input->post('projectID'));
		$select = "<option value=''> Select Phase </option>";
		if ($result) {
			foreach ($result as $res) {
				$select .= "<option value='" . $res['PhaseTypeICode'] . "'>" . $res['PhaseName'] . "</option>";
			}
		}
		echo $select;
	}

	/*     * ** Get Task type list ** */

	public function taskTypeList()
	{
		$result = $this->consultant_task_detail->taskType($this->input->post('phaseID'));
		$select = "<option value=''> Select Task Type </option>";
		if ($result) {
			foreach ($result as $res) {
				$select .= "<option value='" . $res['TaskTypeICode'] . "'>" . $res['TaskTypeName'] . "</option>";
			}
		}
		echo $select;
	}


}
