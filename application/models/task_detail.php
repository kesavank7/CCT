<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Task_detail extends CI_Model {

    private $db1_after = "Cgvak_Synergy_System.dbo.";
    private $db2_after = "Cgvak_Synergy_System.dbo.";
    public $num_rec_per_page = 10;
    private $active = 1;
    private $in_active = 0;
    private $project_type_code = 8; /* - 8 - Retainership -([CGVak_ProjectType_Master]) - */

    public function __construct() {
        $this->load->database('Cgvak_Synergy_System', TRUE);
    }

    public function def_in_active() {
        return $this->in_active;
    }

    public function def_active() {
        return $this->active;
    }

    public function db1_after() {
        return $this->db1_after;
    }

    public function get_project() {
        $query = $this->db->get($this->db1_after . 'CGVak_Project_Master');
        return $query->result_array();
    }

    /** -----------------------------------------------------------------------------------------------------------------------
      ------------------------------------------------- Entry Listing -----------------------------------------------------------
      ----------------------------------------------------------------------------------------------------------------------- * */
    public function get_entry_list($emp_id, $project_id = null) {
        $this->db->select('main.*');
        $this->db->from($this->db1_after . 'CGVak_Project_Tasks as main');
        $this->db->join($this->db1_after . 'CGVak_Project_Master as sub3', 'main.ProjectICode = sub3.ProjectICode', 'inner');
        $this->db->where(" main.CreatedBy = $emp_id");
        $this->db->where(" main.IsActive", $this->active);
        //$this->db->where("sub3.ProjectTypeICode" , $this->project_type_code);
        $this->db->where("sub3.project_status_icode in (3,6,9)");
        if ($project_id)
            $this->db->where(" main.ProjectICode = $project_id");
        $this->db->order_by('main.TaskICode', 'Desc');
        $query = $this->db->get();
        // echo $this->db->last_query();

        return $query->result_array();
    }

    public function insert_entry($table, $data) {
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

    public function get_task_history_entry($table, $data) {
        return $this->db->order_by('TaskProgressDate', 'DESC')->get_where($table, $data)->result_array();
    }

    public function getEnterDetails($user_id, $manual_date) {

        $serverName = _SERVER_IP; //serverName\instanceName
        $connectionInfo = array("Database" => _DB_NAME, "UID" => _DB_USER, "PWD" => _DB_PASSWORD);
        $conn = sqlsrv_connect($serverName, $connectionInfo);

        if ($conn) {
            $sql = "{call Cgvak_Synergy_System.dbo.sp_check_emp_work_log(?,?)}";
            $params = array($user_id, $manual_date);
            if ($stmt = sqlsrv_prepare($conn, $sql, $params)) {
//                echo "Statement prepared.<br><br>\n";
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

    public function get_scenarios($task_details) {
        $return_arr = array();
        foreach ($task_details as $tasks) {
            $task_code = $tasks['TaskICode'];
            $query = $this->db->query("select TestScenarioDescription from " . $this->db1_after . "CGvak_Project_tasks_TestScenario where taskicode =  $task_code and isactive = 1");
            $return_arr[$task_code] = $query->result_array();
        }
        return $return_arr;
    }

    public function get_single_list($TaskICode) {
        $this->db->select('main.*, CONVERT(varchar(10),ActualEndDate, 105) as ActualEndDate, CONVERT(varchar(10),TaskEndDate, 105) as  TaskEndDate, CONVERT(varchar(10),ActualStartDate, 105) as ActualStartDate, CONVERT(varchar(10),TaskStartDate, 105) as TaskStartDate '); //, sub.*
        $this->db->from($this->db1_after . 'CGVak_Project_Tasks as main');
        $this->db->where(" main.TaskICode = $TaskICode");
        $query = $this->db->get();
        $ret[] = $query->result_array();
        $ret[] = $this->get_scenarios($query->result_array());
        return $ret;
    }

    public function upadate_entry($table, $data, $id_name, $id_val) {
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

    public function exist_scenario_id($taskicode, $tbl_sceno) {
        $this->db->select('taskTestIcode');
        $this->db->from($tbl_sceno);
        $this->db->where("taskicode = $taskicode");
        $query = $this->db->get();
        return $query->result_array();
    }

    /** -----------------------------------------------------------------------------------------------------------------------
      ------------------------------------------------- Progress Listing --------------------------------------------------------
      ----------------------------------------------------------------------------------------------------------------------- * */
    public function progress_list($emp_id, $project_id = null) {
        $this->db->select(" main.ProjectICode, main.TaskICode, main.TaskDescription, main.EstimatedHours, main.ActualEndDate, main.TaskEndDate, ISNULL( CAST(sum(sub2.ManHours)  AS char(10)), '00:00')  as ManHours ");
        $this->db->from($this->db1_after . 'CGVak_Project_Tasks as main');
        $this->db->join($this->db1_after . 'CGVak_Project_Tasks_Progress as sub2', 'main.TaskICode = sub2.TaskIcode', 'left');
        $this->db->join($this->db1_after . 'CGVak_Project_Task_Members as task_mem', 'main.TaskICode = task_mem.TaskIcode', 'left');
        $this->db->join($this->db1_after . 'CGVak_Project_Master as sub3', 'main.ProjectICode = sub3.ProjectICode', 'inner');
        $this->db->where("(main.CreatedBy = $emp_id OR task_mem.EmployeeICode = $emp_id)");
        $this->db->where("main.IsActive", $this->active);
        //$this->db->where("sub3.ProjectTypeICode" , $this->project_type_code);
        $this->db->where("sub3.project_status_icode in (3,6,9)");
        if ($project_id)
            $this->db->where("main.ProjectICode", $project_id);
        $this->db->group_by(array("main.TaskICode,main.TaskDescription, main.EstimatedHours, main.ActualEndDate, main.TaskEndDate,  main.ProjectICode"));
        $this->db->order_by('main.TaskICode Desc');

        $query = $this->db->get();
//		 echo $this->db->last_query();
//                 die;
//                echo '<pre>';
//                print_r($query->result_array());
        $data = $query->result_array();
        $result = [];
        foreach ($data AS $key => $d) {
            $this->db->select('EmployeeICode');
            $this->db->from($this->db1_after . 'CGVak_Project_Members');
            $this->db->where("ProjectICode", $d['ProjectICode']);
            $this->db->where("RoleICode", 6);
            $querys = $this->db->get();
            $datas = $querys->result_array();
            $d['lead_id'] = $datas[0]['EmployeeICode'];
            $result[] = $d;
        }
        return $result;
    }

    public function get_actual_start_date($taskicode) {
        $this->db->select('main.ActualStartDate');
        $this->db->from($this->db1_after . 'CGVak_Project_Tasks as main');
        $this->db->where("main.TaskICode", $taskicode);
        $query = $this->db->get();
        //echo $this->db->last_query();
        $value = $query->result_array();
        return $value[0]['ActualStartDate'];
    }

    public function total_progress_count($emp_id, $project_id = null) {
        $this->db->select(" COUNT(*) as  totalrows ");
        $this->db->from($this->db1_after . 'CGVak_Project_Tasks as main');
        $this->db->join($this->db1_after . 'CGVak_Project_Tasks_Progress as sub2', 'main.TaskICode = sub2.TaskIcode', 'left');
        $this->db->where("main.CreatedBy", $emp_id);
        $this->db->where("main.IsActive", $this->active);
        if ($project_id)
            $this->db->where("main.ProjectICode", $project_id);
        $this->db->group_by(array("main.TaskICode,main.TaskDescription, main.EstimatedHours, main.ActualEndDate, main.TaskEndDate, main.ProjectICode"));
        $this->db->order_by('main.TaskICode Desc');

        $query = $this->db->get();
        // echo $this->db->last_query();
        return $query->result_array();
    }

    /** -----------------------------------------------------------------------------------------------------------------------
      ------------------------------------------------- Listing All Tasks --------------------------------------------------------
      ----------------------------------------------------------------------------------------------------------------------- * */
    public function get_all_task_listing($emp_id, $project_id = null) {//, $last_id = 100)
        $this->db->select("main.TaskICode , main.TaskDescription, main.EstimatedHours, sub3.ProjectName, ISNULL( CAST(sum(sub2.ManHours)  AS char(10)), '00:00')  as ManHours ");
        $this->db->distinct();
        $this->db->from($this->db1_after . 'CGVak_Project_Tasks as main');
        $this->db->join($this->db1_after . 'CGVak_Project_Tasks_Progress as sub2', 'main.TaskICode = sub2.TaskIcode', 'left');
        $this->db->join($this->db1_after . 'CGVak_Project_Master as sub3', 'main.ProjectICode = sub3.ProjectICode', 'inner');
        $this->db->where(" main.CreatedBy", $emp_id);
        $this->db->where(" main.IsActive", $this->active);
        //$this->db->where("sub3.ProjectTypeICode" , $this->project_type_code);
        $this->db->where("sub3.project_status_icode in (3,6,9)");
        if ($project_id)
            $this->db->where(" main.ProjectICode = $project_id");
        $this->db->group_by(array("main.TaskICode,main.TaskDescription, main.EstimatedHours, sub3.ProjectName"));
        $this->db->order_by('main.TaskICode', 'desc');
        $query = $this->db->get();
        // echo $this->db->last_query();

        return $query->result_array();
    }

    public function get_task_listing($emp_id, $project_id = null) {
        $this->db->select('main.*, sub1.*, sub2.ManHours, sub3.ProjectName');
        $this->db->from($this->db1_after . 'CGVak_Project_Tasks as main');
        $this->db->join($this->db1_after . 'CGvak_Project_tasks_TestScenario as sub1', 'main.TaskICode = sub1.taskicode', 'inner');
        $this->db->join($this->db1_after . 'CGVak_Project_Tasks_Progress as sub2', 'main.TaskICode = sub2.TaskIcode', 'inner');
        $this->db->join($this->db1_after . 'CGVak_Project_Master as sub3', 'main.ProjectICode = sub3.ProjectICode', 'inner');
        $this->db->where("main.CreatedBy = $emp_id ");
        //$this->db->where("sub3.ProjectTypeICode" , $this->project_type_code);
        if ($project_id)
            $this->db->where("main.ProjectICode", $project_id);
        $this->db->order_by('main.TaskICode', 'ASC');
        $query = $this->db->get();
        // echo $this->db->last_query();	

        return $query->result_array();
    }

    /*
      $active = 1
      $in_active = 0
     */

    function inactive_record($id, $active) {
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
      ------------------------------------------------- Current User Project Listing --------------------------------------------
      ----------------------------------------------------------------------------------------------------------------------- * */
    public function emp_project($emp_id) {
        /*

         */
        //$query = $this->db->query( "select ProjectICode, ProjectName from ". $this->db1_after ."CGVak_Project_Master where ProjectICode in ( select DISTINCT ProjectICode from ". $this->db1_after ."CGVak_Project_Members where EmployeeICode = $emp_id and project_status_icode in (3,6,9)) and ProjectTypeICode = ". $this->project_type_code ." ");
        $query = $this->db->query("select ProjectICode, ProjectName from " . $this->db1_after . "CGVak_Project_Master where ProjectICode in ( select DISTINCT ProjectICode from " . $this->db1_after . "CGVak_Project_Members where EmployeeICode = $emp_id and project_status_icode in (3,6,9))");
        return $query->result_array();
    }

}
