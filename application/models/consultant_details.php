<?php

class Consultant_details extends CI_Model
{

	private $employee_db;

	public function __construct()
	{
		parent::__construct();
		$this->employee_db = $this->load->database(_DB_NAME_EMPLOYEE, TRUE);
	}

	function add_data($tablename = NULL, $data = NULL)
	{

		if (isset($tablename) && isset($data)) {
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

	public function isExistConsultantUsername($id, $data) {
		$result = $this->db->where('ConsultantLoginUserName', $data['ConsultantLoginUserName'])->get(TBL_CONSULTANT_MASTER)->row_array();
		return ($result && count($result) > 0) ? true : false;
	}

	public function updateConsultant($id, $data)
	{
		$result = $this->db->where(array('ConsultantICode' => $id))->update(TBL_CONSULTANT_MASTER, $data);
		return $result;
	}

	//Get all the consultant details
	public function getConsultantDetails($status=null)
	{
		if($status == 'active') {
			$where_status = 'IsActive = 1';
		} else if($status == 'inactive') {
			$where_status = 'IsActive = 0';
		} else if($status == 'pending') {
			$where_status = 'isselfregisterApproved = 0 OR isselfregisterApproved IS NULL';
		} else if($status == 'approved') {
			$where_status = 'isselfregisterApproved = 1';
		} else {
			$where_status = [1=>1];
		}
		try {
			return $this->db->select("*, DATEDIFF(day, GETDATE(), selfregisterapprovedon) as appdays")->where($where_status)->order_by('CreatedDate desc')->get(TBL_CONSULTANT_MASTER)->result_array();

		} catch (Exception $e) {
			echo 'Received exception : ', $e->getMessage(), "\n";
		}
	}

	public function getNotLatestConsultantDetails($status=null)
	{
		if($status == 'active') {
			$where_status = 'IsActive = 1';
		} else if($status == 'inactive') {
			$where_status = 'IsActive = 0';
		} else if($status == 'pending') {
			$where_status = 'isselfregisterApproved = 0 OR isselfregisterApproved IS NULL';
		} else if($status == 'approved') {
			$where_status = 'isselfregisterApproved = 1';
		} else {
			$where_status = [1=>1];
		}
		try {
			return $this->db->select()->where($where_status)->where('CreatedDate <= DATEADD(day,-30, GETDATE())')->get(TBL_CONSULTANT_MASTER)->result_array();

		} catch (Exception $e) {
			echo 'Received exception : ', $e->getMessage(), "\n";
		}
	}

	//Get individual consultant detail
	public function getConsultant($consultantId, $select)
	{
		$this->db->select($select);
		$this->db->from(TBL_CONSULTANT_MASTER);
		$this->db->where("ConsultantICode = $consultantId");
		$query = $this->db->get();
		return $query->result_array();
	}


}
