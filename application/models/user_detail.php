<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_detail extends CI_Model 
{	
	public $db2_after = "Cgvak_Synergy_System.dbo.";
	
	/* public function __construct() {$this->load->database('Cgvak_Synergy_System', TRUE);} */
	
	/*
		Get Login User Information
	*/
	public function user_valid($username, $password, $status = 1)
	{
		/*- Get The Value from second Database -*/
		$DB2 = $this->load->database('Cgvak_Synergy_System', TRUE);
		$query = $DB2->get_where($this->db2_after.'CGVak_EmployeeMaster', array('LoginUserName' => $username, 'LoginPassword'=> $password ,'IsActive' => 1));		
		return $query->result_array();
	}
	
	/*
		Update Password
	*/
	public function user_passupt($newpassword, $emp_id)
	{
		$DB2 = $this->load->database('Cgvak_Synergy_System', TRUE);
		$data = array(
			'LoginPassword' => $newpassword,
			'ModifiedBy' 	=> $emp_id,
			'ModifiedDate' 	=> date('Y-m-d H:i:s'),
		);
		$DB2->where('EmployeeICode', $emp_id);
		$DB2->update($this->db2_after.'CGVak_EmployeeMaster', $data);
		
		return $emp_id;
	}

	public function user_designation($designation_id)
	{
		/*- Get The Value from second Database -*/
		$DB2 = $this->load->database('Cgvak_Synergy_System', TRUE);
		$query = $DB2->get_where($this->db2_after.'CGVak_DesignationMaster', array('DesignationICode' => $designation_id));
		return $query->result_array();
	}
}
