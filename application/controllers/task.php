<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Task extends CI_Controller {

    public function __construct() {
        parent::__construct();
        /* -Session LogOut Catch Clear - */
        $this->output->set_header('cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header("cache-Control: post-check=0, pre-check=0", false);
        $this->output->set_header("Pragma: no-cache");
        $this->output->set_header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        $this->load->helper('url');
        $this->load->model('task_detail');
        $this->load->model('mgeneral');
        $this->load->model('Swipe_detail');
    }

    /** -----------------------------------------------------------------------------------------------------------------------
      ------------------------------------------------- Task Entry --------------------------------------------------------------
      ----------------------------------------------------------------------------------------------------------------------- * */
    /*
      Listing New Task Entrys
     */
    public function entry() {
        if ($this->session->userdata('id') && !$this->session->userdata('role')) {
            /* - Get all Project Name - */
            $data['emp_project'] = $this->task_detail->emp_project($this->session->userdata('id'));

            if (isset($_POST['entry_search_filter']) || get_cookie('project_filter')) {
                if ($this->input->post("entry_search_filter")) {
                    delete_cookie("project_filter");
                    $cookie = array(
                        'name' => "project_filter",
                        'value' => $search_project_id,
                        'expire' => "86500"
                    );
                    $this->input->set_cookie($cookie);
                } else if (get_cookie('project_filter')) {
                    $search_project_id = get_cookie('project_filter');
                    /* - Calling Helper function to check Prj-Id Exist in that - */
                    $check = in_multiarray($search_project_id, $data['emp_project'], "ProjectICode");
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
            $data['entry_list'] = $this->task_detail->get_entry_list($this->session->userdata('id'), $search_project_id);
            /* Getting Scenario List */
            $data['scenario_list'] = $this->task_detail->get_scenarios($data['entry_list']);
            $this->load->view('entry', $data);
        } elseif (($this->session->userdata('id')) && ($this->session->userdata('role') == 'consultant')) {
            $this->load->view('no_access');
        } else {
            $this->load->view('login');
        }
    }

    /*
      List the Task Entrys in Project Filter

     */

    public function entry_search() {
        if ($this->session->userdata('id')) {
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
            $data['emp_project'] = $this->task_detail->emp_project($this->session->userdata('id'));
            $data['entry_list'] = $this->task_detail->get_entry_list($this->session->userdata('id'), $search_project_id);
            /* Getting Scenario List */
            $data['scenario_list'] = $this->task_detail->get_scenarios($data['entry_list']);

            $this->load->view('entry', $data);
        } else {
            $this->load->view('login');
        }
    }

    /*
      Insert New Task Entrys
     */

    public function entry_insert() {
        if ($this->session->userdata('id')) {
            $data = $this->input->post();
            /* Updating Cookie Value */
            delete_cookie("project_filter");
            $search_project_id = $data['ProjectICode'];
            $cookie = array(
                'name' => "project_filter",
                'value' => $search_project_id,
                'expire' => "86500"
            );
            $this->input->set_cookie($cookie);

            $this->db->select('*');
            $this->db->from('Cgvak_Synergy_System.dbo.CGVak_Project_Phase');
            $this->db->where(array('PhaseTypeICode' => $data['PhaseTypeICode'], 'ProjectICode' => $data['ProjectICode']));
            $query = $this->db->get();
            $result_phase = $query->result_array();

            $data['ProjectPhaseICode'] = $result_phase[0]['ProjectPhaseICode'];
            unset($data['PhaseTypeICode']);
            $TaskTypeICode = $data['TaskTypeICode'];
            unset($data['TaskTypeICode']);

            /* Convert Time to Decimal */
            $data['EstimatedHours'] = str_replace(":", ".", $data['EstimatedHours']);
            /* Convert Date in Y-m-d */
            // $data['ActualEndDate'] = date('Y-m-d', strtotime($data['ActualEndDate']));
            // $data['ActualStartDate'] = date('Y-m-d', strtotime($data['ActualStartDate']));
            $data['TaskStartDate'] = date('Y-m-d', strtotime($data['TaskStartDate']));
            $data['TaskEndDate'] = date('Y-m-d', strtotime($data['TaskEndDate']));

            $test_scenario = $data['TestScenarioDescription'];
            unset($data['TestScenarioDescription']);

            $tbl_task = $this->task_detail->db1_after() . 'CGVak_Project_Tasks';
            $tbl_sceno = $this->task_detail->db1_after() . 'CGvak_Project_tasks_TestScenario';
            /* Insert in Task Table */
            $task_insert_id = $this->task_detail->insert_entry($tbl_task, $data);

            /*             * * Insert Project Task memebers -> Updated at 20/11/18** */
            $tbl_task_member = $this->task_detail->db1_after() . 'CGVak_Project_Task_Members';

            $task_mem_array = array('CompanyIcode' => $data['CompanyICode'], 'ProjectICode' => $data['ProjectICode'], 'TaskICode' => $task_insert_id, 'EmployeeICode' => $data['CreatedBy'], 'TaskStartDate' => $data['TaskStartDate'], 'TaskEndDate' => $data['TaskEndDate'], 'EmployeeAssignedDate' => $data['CreatedDate'], 'IsActive' => $data['IsActive'], 'CreatedBy' => $data['CreatedBy'], 'CreatedDate' => $data['CreatedDate'], 'TaskTypeICode' => $TaskTypeICode);

            $task_memebers_insert_id = $this->task_detail->insert_entry($tbl_task_member, $task_mem_array);

            /*             * * End ** */

            $task_sceno_data = explode("\n", $test_scenario);
            foreach ($task_sceno_data as $val) {
                if (trim($val)) {
                    $data_sceno = array("isactive" => $this->task_detail->def_active(), "taskicode" => $task_insert_id, "TestScenarioDescription" => $val, "createdby" => $data['CreatedBy'], "createddate" => $data['CreatedDate'], "testscenariodate" => $data['CreatedDate']);
                    /* Insert in Sceno Table */
                    $sceno_insert_id = $this->task_detail->insert_entry($tbl_sceno, $data_sceno);
                }
            }

            /* - Set Seesion Notification Message - */
            $this->session->set_userdata(array(
                'msg' => "new",
            ));
            redirect(base_url() . 'task/entry/', 'refresh');
        } else {
            $this->load->view('login');
        }
    }

    /*
      Update Row, Task values (AJAX)
     */

    public function entry_edit() {
        if ($this->session->userdata('id')) {
            $data = $this->task_detail->get_single_list($this->input->post('TaskICode'));
            echo json_encode($data);
        } else {
            $this->load->view('login');
        }
    }

    /*
      Update The Task entrys
     */

    public function entry_update() {
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
            // $data['ActualEndDate'] = date('Y-m-d', strtotime($data['ActualEndDate']));
            // $data['ActualStartDate'] = date('Y-m-d', strtotime($data['ActualStartDate']));
            $data['TaskStartDate'] = date('Y-m-d', strtotime($data['TaskStartDate']));
            $data['TaskEndDate'] = date('Y-m-d', strtotime($data['TaskEndDate']));

            $test_scenario = $data['TestScenarioDescription'];
            unset($data['TestScenarioDescription']);

            $tbl_task = $this->task_detail->db1_after() . 'CGVak_Project_Tasks';
            $tbl_sceno = $this->task_detail->db1_after() . 'CGvak_Project_tasks_TestScenario';

            /* Insert in Task Table */
            $task_insert_id = $this->task_detail->upadate_entry($tbl_task, $data, 'TaskICode', $id);

            $exist_scenario_id = $this->task_detail->exist_scenario_id($id, $tbl_sceno);

            $task_sceno_datas = explode("\n", $test_scenario);
            /* Trim the Array */
            foreach ($task_sceno_datas as $val) {
                if (trim($val))
                    $task_sceno_data[] = $val;
            }

            if (count($task_sceno_data) >= count($exist_scenario_id)) {
                $i = 0;
                foreach ($task_sceno_data as $val) {
                    if (trim($val) && isset($exist_scenario_id[$i]['taskTestIcode'])) {
                        $taskTestIcode = $exist_scenario_id[$i]['taskTestIcode'];
                        $data_sceno = array("TestScenarioDescription" => $val);
                        /* Update in Sceno Table */
                        $sceno_insert_id = $this->task_detail->upadate_entry($tbl_sceno, $data_sceno, 'taskTestIcode', $taskTestIcode);
                    } else if (trim($val)) {
                        $data_sceno = array("isactive" => $this->task_detail->def_active(), "taskicode" => $task_insert_id, "TestScenarioDescription" => $val, "createdby" => $this->session->userdata('id'), "createddate" => date('Y-m-d H:i:s'), "testscenariodate" => date('Y-m-d H:i:s'));
                        /* Insert in Sceno Table */
                        $sceno_insert_id = $this->task_detail->insert_entry($tbl_sceno, $data_sceno);
                    }
                    $i++;
                }
            } else {
                $i = 0;
                foreach ($exist_scenario_id as $val) {
                    if (isset($task_sceno_data[$i])) {
                        $taskTestIcode = $val['taskTestIcode'];
                        $data_sceno = array("TestScenarioDescription" => $task_sceno_data[$i]);
                        /* Update in Sceno Table */
                        $sceno_insert_id = $this->task_detail->upadate_entry($tbl_sceno, $data_sceno, 'taskTestIcode', $taskTestIcode);
                    } else {

                        $taskTestIcode = $val['taskTestIcode'];
                        $data_sceno = array("isactive" => $this->task_detail->def_in_active());
                        /* Update in Sceno Table - IN-ACTIVE */
                        $sceno_inactive = $this->task_detail->upadate_entry($tbl_sceno, $data_sceno, 'taskTestIcode', $taskTestIcode);
                    }
                    $i++;
                }
            }

            /* - Set Seesion Notification Message - */
            $this->session->set_userdata(array(
                'msg' => "upt",
            ));
            redirect(base_url() . "task/entry?page=" . $page_no, 'refresh');
        } else {
            $this->load->view('login');
        }
    }

    /*
      In Active the Entry Task (Delete)
     */

    public function entry_inactive() {
        if ($this->session->userdata('id')) {
            $task_code = $this->input->get('request_id');
            $page_no = $this->input->get('page');

            $data_task = array("IsActive" => $this->task_detail->def_in_active());
            //,'ClosedDate' => date('Y-m-d H:i:s'));
            //, 'ActualEndDate'=>date('Y-m-d'));
            $data_sceno = array("isactive" => $this->task_detail->def_in_active());
            $data_progress = array("IsActive" => $this->task_detail->def_in_active());

            $tbl_task = $this->task_detail->db1_after() . 'CGVak_Project_Tasks';
            $tbl_sceno = $this->task_detail->db1_after() . 'CGvak_Project_tasks_TestScenario';
            $tbl_progress = $this->task_detail->db1_after() . 'CGVak_Project_Tasks_Progress';

            /* Update in Sceno Table - IN-ACTIVE */
            $tasks_inactive = $this->task_detail->upadate_entry($tbl_task, $data_task, 'TaskICode', $task_code);
            $sceno_inactive = $this->task_detail->upadate_entry($tbl_sceno, $data_sceno, 'TaskICode', $task_code);
            $progre_inactive = $this->task_detail->upadate_entry($tbl_progress, $data_progress, 'TaskICode', $task_code);

            /* - Set Seesion Notification Message - */
            $this->session->set_userdata(array(
                'msg' => "act",
            ));
            redirect(base_url() . "task/entry?page=" . $page_no, 'refresh');
        } else {
            $this->load->view('login');
        }
    }

    /** -----------------------------------------------------------------------------------------------------------------------
      ------------------------------------------------- Progress All Tasks ------------------------------------------------------
      ----------------------------------------------------------------------------------------------------------------------- * */

    /**
      Listing the Progress entrys
     * */
    public function progress() {

        if ($this->session->userdata('id')) {
            /* - Get all Project Name - */
            $data['emp_project'] = $this->task_detail->emp_project($this->session->userdata('id'));


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
                $check = in_multiarray($search_project_id, $data['emp_project'], "ProjectICode");
                $search_project_id = ($check) ? $search_project_id : null;
            } else {
                $search_project_id = null;
            }
            $data['search_project_id'] = $search_project_id;
            $data['progress_list'] = $this->task_detail->progress_list($this->session->userdata('id'), $search_project_id);
//			echo "<pre>";
//                        print_r($data);
//                        exit();
            $this->load->view('progress', $data);
        } else {
            $this->load->view('login');
        }
    }

    /**
      Listing the Progress Entry Search By Project
     * */
    public function progress_search() {
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
            $data['emp_project'] = $this->task_detail->emp_project($this->session->userdata('id'));
            $data['progress_list'] = $this->task_detail->progress_list($this->session->userdata('id'), $search_project_id);

            $this->load->view('progress', $data);
        } else {
            $this->load->view('login');
        }
    }

    /**
      Insert the New Progress Entrys
     * */
    public function progress_insert_ajax() {
        if ($this->session->userdata('id')) {
            $page_no = $this->input->post('page');
            $task_code_lists = $this->input->post('task_code');
            $task_proj_name = $this->input->post('projectICode');
            $task_work_desc = $this->input->post('work_desc');
            $task_mans_hour = $this->input->post('mans_hour');
            $task_prog_date = $this->input->post('prog_date');
            $late_entry_reson = $this->input->post('late_entry_reson');
            $lead_id_report = $this->input->post('lead_id_report');
            $hr_mail = $this->input->post('hr_mail');
            $latereasoncheck = $this->input->post('latereasoncheck');
            $msg = $msg_array = [];
            
            if (trim($task_work_desc) == "") {
                $msg['status'] = 0;
                $msg['msg'] = "Please Enter Work Description";
                $msg_array['msg'] = $msg;
                echo json_encode($msg_array);
                exit();
            }
            if ($this->getEntryByDate(date('Y-m-d', strtotime($task_prog_date))) == FALSE) {
                $msg['status'] = 0;
                $msg['msg'] = "Swipe Entry is missing for " . date('Y-m-d', strtotime($task_prog_date));
                $msg_array['msg'] = $msg;
                echo json_encode($msg_array);
                exit();
            }
            $Callproducer = $this->getEntryByDateCallproducer(date('Y-m-d', strtotime($task_prog_date)));
            if ($Callproducer != FALSE) {
                $msg['status'] = 0;
                $msg['msg'] = "Task Entry Failed to save.. Please enter task entry for following dates : " . $Callproducer;
                $msg_array['msg'] = $msg;
                echo json_encode($msg_array);
                exit();
            }

            if (trim($task_work_desc) != "" && trim($task_mans_hour) != "" && trim($task_prog_date) != "") {
                //echo $task_work_desc[$value][$i]."--".$task_mans_hour[$value][$i]."--".$task_prog_date[$value][$i];
                $tbl_work_late_entry = $this->task_detail->db1_after() . 'cgvak_employee_lateentry_reasons';
                $tbl_work_desc = $this->task_detail->db1_after() . 'CGVak_Project_Tasks_Progress';
                $tbl_task = $this->task_detail->db1_after() . 'CGVak_Project_Tasks';
                /* Insert Actual Start */
                $get_actual_start_date = $this->task_detail->get_actual_start_date($value);
                if (!$get_actual_start_date) {
                    $data_actual_start_date = array("ActualStartDate" => date('Y-m-d H:i:s'));
                    $upt_astart_date = $this->task_detail->upadate_entry($tbl_task, $data_actual_start_date, 'TaskICode', $value);
                }
                $data = array(
                    "CompanyICode" => 1,
                    "ProjectICode" => $task_proj_name,
                    "TaskICode" => $task_code_lists,
                    "EmployeeICode" => $this->session->userdata('id'),
                    "WorkDescription" => $task_work_desc,
                    "ManHours" => str_replace(":", ".", $task_mans_hour),
                    "TaskProgressDate" => date('Y-m-d', strtotime($task_prog_date)),
                    "CreatedDate" => date('Y-m-d H:i:s'),
                    "IsActive" => $this->task_detail->def_active(),
                    "TaskProgress" => 1,
                    "CreatedBy" => $this->session->userdata('id'),
                );

                $insert_work_desc_id = $this->task_detail->insert_entry($tbl_work_desc, $data);

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
                $insert_work_desc_id = $this->task_detail->insert_entry($tbl_work_late_entry, $late_reason);
            }
//			echo "<pre>";
//                        print_r($insert_work_desc_id);
//                        exit();

            /* - Set Seesion Notification Message - */
            $this->session->set_userdata(array(
                'msg' => "progress",
            ));
            $msg['status'] = 1;
            $msg['msg'] = "Success";
            $msg_array['msg'] = $msg;
            echo json_encode($msg_array);
            exit();
            //redirect("/synergy/task/progress/?page=".$page_no, 'refresh');
        } else {
            $msg['status'] = 1;
            $msg['msg'] = "Success";
            $msg_array['msg'] = $msg;
            echo json_encode($msg_array);
            exit();
        }
    }

    public function progress_insert() {
        if ($this->session->userdata('id')) {
            $page_no = $this->input->post('page');
            $task_code_lists = $this->input->post('TaskICode');
            $task_proj_name = $this->input->post('ProjectICode');
            $task_work_desc = $this->input->post('WorkDescription');
            $task_mans_hour = $this->input->post('ManHours');
            $task_prog_date = $this->input->post('TaskProgressDate');
            $i = 0;

            echo "<pre>";
            print_r($task_mans_hour);
            exit();

            foreach ($task_code_lists as $value) {
                if (isset($task_work_desc[$value][0]) && isset($task_mans_hour[$value][0]) && isset($task_prog_date[$value][0])) {
                    if (trim($task_work_desc[$value][0]) != "" && trim($task_mans_hour[$value][0]) != "" && trim($task_prog_date[$value][0]) != "") {
                        //echo $task_work_desc[$value][$i]."--".$task_mans_hour[$value][$i]."--".$task_prog_date[$value][$i];
                        $tbl_work_desc = $this->task_detail->db1_after() . 'CGVak_Project_Tasks_Progress';
                        $tbl_task = $this->task_detail->db1_after() . 'CGVak_Project_Tasks';
                        /* Insert Actual Start */
                        $get_actual_start_date = $this->task_detail->get_actual_start_date($value);
                        if (!$get_actual_start_date) {
                            $data_actual_start_date = array("ActualStartDate" => date('Y-m-d H:i:s'));
                            $upt_astart_date = $this->task_detail->upadate_entry($tbl_task, $data_actual_start_date, 'TaskICode', $value);
                        }
                        $data = array(
                            "CompanyICode" => 1,
                            "ProjectICode" => $task_proj_name[$i],
                            "TaskICode" => $value,
                            "EmployeeICode" => $this->session->userdata('id'),
                            "WorkDescription" => $task_work_desc[$value][0],
                            "ManHours" => str_replace(":", ".", $task_mans_hour[$value][0]),
                            "TaskProgressDate" => date('Y-m-d', strtotime($task_prog_date[$value][0])),
                            "CreatedDate" => date('Y-m-d H:i:s'),
                            "IsActive" => $this->task_detail->def_active(),
                            "TaskProgress" => 1,
                            "CreatedBy" => $this->session->userdata('id'),
                        );
                        $this->getEntryByDate(date('Y-m-d', strtotime($task_prog_date[$value][0])));
                        echo "=======>";
                        echo "<pre>";
                        print_r($data);
                        echo "</pre>";

//						//$insert_work_desc_id = $this->task_detail->insert_entry($tbl_work_desc, $data);
                    }
                }
                $i++;
            }

            /* - Set Seesion Notification Message - */
            $this->session->set_userdata(array(
                'msg' => "progress",
            ));
            exit();
            redirect("/synergy/task/progress/?page=" . $page_no, 'refresh');
        } else {
            $this->load->view('login');
        }
    }

    public function getEntryByDate($manual_date) {
        $reporting_to = $this->mgeneral->get_leaders_list();
        //$manual_date = $this->input->post('manual_date');
        $manual_date = date("Y-m-d", strtotime($manual_date));
        $data['swipe_details'] = $this->Swipe_detail->getSwipeDetails($this->session->userdata('id'), $manual_date, $manual_date, $reporting_to);
        if (count($data['swipe_details']) > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getEntryByDateCallproducer($manual_date) {
        //$manual_date = $this->input->post('manual_date');
        //$manual_date = date("Y-m-d", strtotime($manual_date));
        //$manual_date = "2020-07-06";
        //$data = $this->task_detail->getEnterDetails('4940', $manual_date);

        $data = $this->task_detail->getEnterDetails($this->session->userdata('id'), $manual_date);
        if (isset($data[0])) {
            if (count($data[0]) > 0) {
                return $data[0];
            } else {
                 return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    /*     * * Get Task History * */

    public function gettaskhistory() {
        $tbl_work_desc = $this->task_detail->db1_after() . 'CGVak_Project_Tasks_Progress';
        $data['ProjectICode'] = $this->input->post('PorjectIcode');
        $data['TaskICode'] = $this->input->post('TaskIcode');
        $data['EmployeeICode'] = $this->session->userdata('id');
        $task_history = $this->task_detail->get_task_history_entry($tbl_work_desc, $data);

        $result = '';
        if (!empty($task_history)) {
            $i = 1;
            foreach ($task_history as $res) {
                $result .= "<tr>
							<td>" . $i . "</td>
							<td>" . date('d-m-Y', strtotime($res['TaskProgressDate'])) . "</td>
							<td>" . $res['WorkDescription'] . "</td>
							<td>" . $res['ManHours'] . "</td>
							<td>" . date('d-m-Y H:i:s', strtotime($res['CreatedDate'])) . "</td>
				           </tr>";
                $i++;
            }
        } else {
            $result .= "<tr><td colspan='5' >No Records Found</td></tr>";
        }
        echo $result;
    }

    /** -----------------------------------------------------------------------------------------------------------------------
      ------------------------------------------------- Entry Listing -----------------------------------------------------------
      ----------------------------------------------------------------------------------------------------------------------- * */

    /**
      Listing the Task Entrys, Search By Project
     * */
    public function list_search() {
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
            $data['emp_project'] = $this->task_detail->emp_project($this->session->userdata('id'));
            /* $data['progress_list'] = $this->task_detail->progress_list($this->session->userdata('id'), $search_project_id); */
            $data['task_list'] = $this->task_detail->get_all_task_listing($this->session->userdata('id'), $search_project_id);
            $this->load->view('listing', $data);
        } else {
            $this->load->view('login');
        }
    }

    /**
      Listing the Progress entrys
     * */
    public function listing() {
        if ($this->session->userdata('id')) {
            /* - Get all Project Name - */
            $data['emp_project'] = $this->task_detail->emp_project($this->session->userdata('id'));

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
                $check = in_multiarray($search_project_id, $data['emp_project'], "ProjectICode");
                $search_project_id = ($check) ? $search_project_id : null;
            } else {
                $search_project_id = null;
            }

            $data['search_project_id'] = $search_project_id;
            /* $data['progress_list'] = $this->task_detail->progress_list($this->session->userdata('id'), $search_project_id); */
            $data['task_list'] = $this->task_detail->get_all_task_listing($this->session->userdata('id'), $search_project_id);
            $this->load->view('listing', $data);
        } else {
            $this->load->view('login');
        }
    }

    /**
      In-activate the Task Entrys
     * */
    public function list_delete() {
        if ($this->session->userdata('id')) {
            $inactive_id = $this->input->get('id');
            $page_no = $this->input->get('page');
            $this->input->get('active');
            $active_status = 0;
            if ($this->input->get('active')) {
                $active_status = $this->input->get('active');
            }
            $return = $this->task_detail->inactive_record($inactive_id, $active_status);
            if ($return) {
                /* - Set Seesion Notification Message - */
                $this->session->set_userdata(array('msg' => "act"));
                redirect(base_url() . "/task/listing?page=" . $page_no, 'refresh');
            } else {
                /* - Set Seesion Notification Message - */
                $this->session->set_userdata(array('msg' => "lError"));
                redirect(base_url() . "task/listing?page=" . $page_no, 'refresh');
            }
        } else {
            $this->load->view('login');
        }
    }

    /*     * ** Get Phase list ** */

    public function phaselist() {
        $ProjectICode = $this->input->post('projectID');
        $this->db->select('master.PhaseTypeICode,master.PhaseName');
        $this->db->from('Cgvak_Synergy_System.dbo.CGVak_Project_Phase as phase');
        $this->db->join('Cgvak_Synergy_System.dbo.CGVak_PhaseType_Master as master', 'phase.PhaseTypeICode = master.PhaseTypeICode', 'inner');
        $this->db->where("phase.ProjectICode", $ProjectICode);
        $query = $this->db->get();
        $result = $query->result_array();
        $select = "<option value=''> Select Phase </option>";
        if ($result) {
            foreach ($result as $res) {
                $select .= "<option value='" . $res['PhaseTypeICode'] . "'>" . $res['PhaseName'] . "</option>";
            }
        }
        echo $select;
    }

    /*     * ** Get Task type list ** */

    public function tasktypelist() {
        $PhaseTypeICode = $this->input->post('phaseID');
        $this->db->select('*');
        $this->db->from('Cgvak_Synergy_System.dbo.CGVak_TaskType_Master');
        $this->db->where("PhaseTypeICode", $PhaseTypeICode);
        $query = $this->db->get();
        $result = $query->result_array();
        $select = "<option value=''> Select Task Type </option>";
        if ($result) {
            foreach ($result as $res) {
                $select .= "<option value='" . $res['TaskTypeICode'] . "'>" . $res['TaskTypeName'] . "</option>";
            }
        }
        echo $select;
    }

}
