<?php

class Mgeneral extends CI_Model {

    private $employee_db;

    public function __construct() {
        parent::__construct();
        $this->employee_db = $this->load->database(_DB_NAME_EMPLOYEE, TRUE);
    }

    /* This will insert the the @data into @tabla
     * @data 
     * @tableName
     */

    function add_data($tablename = NULL, $data = NULL) { 
        if ((isset($tablename) && isset($data))) {
            $this->db->trans_start();
            $this->db->insert($tablename, $data);
            $id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
            return $id;
        }
    }

    /**
     * This will update the table ,accourding to @condtions ,, @tableName ,$condition,$data are required @conditions are Array
     */
    function update_data($tablename, $condition = NULL, $data) {
        if (!isset($condition) && count($condition) > 0) {
            return FALSE;
        } else {
            if (isset($tablename) && trim($tablename) != '' && isset($data) && count($data) > 0) {
                $this->db->trans_start();
                $this->db->where($condition);
                $this->db->update($tablename, $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();
                }
            }
        }
    }

    /**
     * This will return interger like 25,50,etc ,number of rows in table @tableName is required
     */
    function count_rows($tablename, $conditions = "", $fields = "*") {
        if (isset($tablename) && trim($tablename) != '') { // table Contains value Or Not
            $this->db->select($fields);
            if (isset($conditions) && $conditions) {
                $this->db->where($conditions, null, false);
            }
            $recordSet = $this->db->get($tablename);
            return $recordSet->num_rows();
        }
    }

    /**
     * This will update the Multiple recored from the table @tableName @condtions Are required
     */
    function update_multiple_data($tablename, $ids, $fieldname, $val, $condfld) {
        if (!isset($ids)) {
            return FALSE;
        } else {
            if (isset($tablename) && trim($tablename) != '' && trim($fieldname) != '' && trim($condfld) != '' && trim($ids) != '') {
                $this->db->trans_start(); // start database transction
                $this->db->query("UPDATE " . $tablename . " SET $fieldname=" . trim($val) . " WHERE " . trim($condfld) . " IN(" . trim($ids) . ")");
                $this->db->trans_complete(); // complete database transction

                if ($this->db->trans_status() === FALSE) { // it returns false if transction falied
                    $this->db->trans_rollback(); // Rollback to previous state
                } else {
                    $this->db->trans_commit(); // either Commit data
                }
            }
        }
    }

    /**
     * This will delete the records from the table @tableName @conditions Are required @conditions are Array
     */
    function delete_data($tablename, $conditions) {
        if (empty($conditions) || empty($tablename)) {
            return FALSE;
        }
        $this->db->where($conditions);
        if ($this->db->delete($tablename)) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Function to get data
     * @param string $table_name
     * @param string $condition
     * @param string $fields
     * @return boolean
     */
    public function get_data($table_name, $conditions, $fields = false) {
        if (empty($conditions) || empty($table_name)) {
            return FALSE;
        }
        if ($fields) {
            $this->db->select($fields);
        }
        $this->db->where($conditions);
        $query = $this->db->get($table_name);
        return $query->row_array();
    }

    /**
     * Function to get the reporting persons
     * @return type
     */
    public function get_leaders_list() {
        $DB2 = $this->employee_db;
        $DB2->select('DesignationICode');
        $DB2->where('DepartmentICode', _DEPARTMENT_SOFTWARE_DEVELOPMENT);
        $DB2->where('Isapprove', _APPROVED);
        $all_designations = $DB2->get(_DB_EMPLOYEE . 'CGVak_DesignationMaster')->result_array();
        $designations = array();
        if (!empty($all_designations)) {
            $designations = array_map(function($des) {
                return $des['DesignationICode'];
            }, $all_designations);
        }
        $DB2->select('EmployeeICode,EmployeeDisplayName');
        $DB2->where('IsActive', _ACTIVE);
        $DB2->where_in('DesignationIcode', $designations);
        $DB2->order_by('EmployeeDisplayName', 'ASC');
        $query = $DB2->get(_DB_EMPLOYEE . 'CGVak_EmployeeMaster');
        return $query->result_array();
    }

    /**
     * Function to get All the Non Project task entries / Self Learning Entries
     */
    public function get_task_entry_list($parameters = array(), $count_rows = false, $self_learning = false) {

        $year = $parameters['year'];
        $month = $parameters['month'];
        if ($month && $month != 'all') {
            $start_date = date("$year-$month-01");
            $end_date = date('Y-m-t', strtotime($start_date));
        } else {
            $start_date = date("$year-01-01");
            $end_date = date("$year-12-31");
        }

//        $condition = "task_entry.employeeicode = '" . $this->session->userdata('id') . "' AND task_entry.progressdate >= '" . $start_date . "' AND task_entry.progressdate <= '" . $end_date . "' AND task_entry.taskcategory != '" . _SELF_LEARNING_ID . "'";
        $condition = "task_entry.employeeicode = '" . $this->session->userdata('id') . "' AND task_entry.progressdate >= '" . $start_date . "' AND task_entry.progressdate <= '" . $end_date . "'";
        if ($self_learning) {
            $condition .= " AND task_entry.taskcategory = '" . _SELF_LEARNING_ID . "'";
        } else {
            $condition .= " AND task_entry.taskcategory != '" . _SELF_LEARNING_ID . "'";
        }
        if (!$count_rows) {
            $param = array(
                'base_url' => $parameters['base_url'] . $year . '/' . $month,
                'recordCountQuery' => $parameters['total_rows'],
                'pagination_uri_segment' => $parameters['uri_segment']
            );
            $pagination = pagination($param);

            $db_synergy = _DB_NAME_SYNERGY;
            $db_employee = _DB_NAME_EMPLOYEE;
            $start = $pagination['start'];
            $offset = $pagination['offset'];

            $sql = "  ;WITH Results_CTE AS
                    (
                        SELECT
                            task_entry.*,employee.EmployeeDisplayName as emp_name, approved.EmployeeDisplayName as approved_name,
                            ROW_NUMBER() OVER (ORDER BY dailyprogressid desc)AS RowNum
                        FROM $db_synergy.dbo.CGvak_non_project_task_entry as task_entry
                        JOIN $db_employee.dbo.CGVak_EmployeeMaster as employee ON employee.EmployeeICode = task_entry.employeeicode 
                        LEFT JOIN $db_employee.dbo.CGVak_EmployeeMaster as approved ON approved.EmployeeICode = task_entry.approvedby 
                        WHERE $condition
                    )
                    SELECT *
                    FROM Results_CTE
                    WHERE RowNum > $start
                    AND RowNum <= $start + $offset";

            $query = $this->db->query($sql);
            return $query->result_array();
        } else {
            $this->db->select('task_entry.dailyprogressid');
            $this->db->where($condition);
            $this->db->from(_DB_SYNERGY . 'CGvak_non_project_task_entry as task_entry');
            $query = $this->db->get();
            return $query->num_rows();
        }
    }

}
