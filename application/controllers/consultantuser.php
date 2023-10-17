<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ConsultantUser extends CI_Controller {

	public function __construct(){
		parent::__construct();		
		/*-Session LogOut Catch Clear -*/
        $this->output->set_header('cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header("cache-Control: post-check=0, pre-check=0", false);
        $this->output->set_header("Pragma: no-cache");
        $this->output->set_header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		/*- Loading Model -*/
		$this->load->model('consultant_user_detail');
		/*- Loading session -*/
		// $this->load->library('session');

	}
	
	/*
		This Function Used to Valid Username and Password
		Store the Login user to session
	*/
	public function index(){
		if(($this->session->userdata('id'))  && ($this->session->userdata('role')=='consultant')){
			redirect('/cosultantprogress/index', 'refresh');
		}elseif(($this->session->userdata('id'))  && ($this->session->userdata('role') !== 'consultant')){
			$this->load->view('no_access');
		}else {
			$this->load->view('consultant_login');
		}
	}


	public function valid()
	{
		$this->form_validation->set_rules('username', 'Username', 'required|trim');
		$this->form_validation->set_rules('password', 'Password', 'required|trim');
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('consultant_login');
		}
		else
		{
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$data =  $this->consultant_user_detail->user_valid($username, $password);

			/*- Set the value in Session -*/
			if(count($data) == 1) {

				$this->session->set_userdata(array(
					'role' 			=> 'consultant',
					'username' 		=> $data[0]['ConsultantLoginUserName'],
					'id' 			=> $data[0]['ConsultantICode'],
					'display_name' 	=> $data[0]['ConsultantLoginUserName'],
					'first_name' 	=> $data[0]['ConsultantFirstName'],
					'last_name' 	=> $data[0]['ConsultantLastName'],
					'emp_photo' 	=> $data[0]['pictureupload'],
					'EmployeeNumber' 	=> $data[0]['ConsultantICode'],
					'msg' 			=> "succ",
				));

				/*- Remember Me -*/
				if($this->input->post('remember'))
				{
					$cookie = array(
						'name'   => 'rem_username',
						'value'  => $username,
						'expire' => "86500",
					);
					$this->input->set_cookie($cookie);
					
					$cookie = array(
						'name'   => 'rem_password',
						'value'  => $password,
						'expire' => "86500",
					);
					$this->input->set_cookie($cookie);
				}
				else {
					delete_cookie("rem_username");
					delete_cookie("rem_password");
				}				
				redirect('/cosultantprogress/progress', 'refresh');
			}
			else {
				$this->session->set_userdata(array(
					'msg'	=> "error",
				));
				$this->load->view('consultant_login');
			}
		}
	}
	
	/*
		Default Page - User Login Page
	*/
	public function login()
	{
		if($this->session->userdata('id'))
			redirect('/cosultantprogress', 'refresh');
		else
			$this->load->view('consultant_login');
	}
	
	/*
		Logout the User Session Values.
	*/	
	public function logout()
	{
		$array_items = array('username', 'role','id','display_name','first_name','last_name');
		$this->session->unset_userdata($array_items);
		$this->load->view('consultant_login');
			
	}
	
	public function passupt()
	{
		if($this->session->userdata('id'))
			$this->load->view('password_upt');
		else
			$this->load->view('consultant_login');
	}
	
	public function pwdreset(){
		if($this->session->userdata('id'))
		{
			$this->form_validation->set_rules('new_password','New Password','required|trim|min_length[5]|max_length[15]');
			/*|callback_alpha_dash_space*/
			$this->form_validation->set_rules('confirm_password','Confirm Password', 'required|trim|matches[new_password]');

			if ($this->form_validation->run() == FALSE)
			{
				$this->load->view('password_upt');
			}
			else
			{
				$emp_id = $this->session->userdata('id');
				$n_password = $this->input->post('new_password');
				$c_password = $this->input->post('confirm_password');
				$data =  $this->consultant_user_detail->user_passupt($n_password, $emp_id);
				$this->session->set_userdata(array(
					'msg' 			=> "succ",
				));
				$this->load->view('password_upt');
			}
		}
		else {
			$this->load->view('consultant_login');
		}
	}
}
