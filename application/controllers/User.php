<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		/*-Session LogOut Catch Clear -*/
		$this->output->set_header('cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header("cache-Control: post-check=0, pre-check=0", false);
		$this->output->set_header("Pragma: no-cache");
		$this->output->set_header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		/*- Loading Model -*/
		$this->load->model('user_detail');
	}

	/*
		This Function Used to Valid Username and Password
		Store the Login user to session
	*/
	public function valid()
	{
		$this->form_validation->set_rules('username', 'Username', 'required|trim');
		$this->form_validation->set_rules('password', 'Password', 'required|trim');
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('login');
		} else {
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$data = $this->user_detail->user_valid($username, $password);
			
			/*- Set the value in Session -*/
			if (count($data) == 1) {

				$userRole = $this->getUserRole($data[0]['DesignationICode']);

				if ($userRole != 'Lead' && $userRole != 'HR' && $userRole != 'Account') {
					$this->session->set_userdata(array(
						'msg' => "access",
					));
					$this->load->view('login');
					return;
				}

				$this->session->set_userdata(array(
					'username' => $data[0]['LoginUserName'],
					'id' => $data[0]['EmployeeICode'],
					'role' => $this->getUserRole($data[0]['DesignationICode']),

					
					// 'role' => 'HR',


					'display_name' => $data[0]['EmployeeDisplayName'],
					'first_name' => $data[0]['EmployeeFirstName'],
					'last_name' => $data[0]['EmployeeLastName'],
					'emp_photo' => $data[0]['emp_photo'],
					'EmployeeNumber' => $data[0]['EmployeeNumber'],
					'msg' => "succ",
				));

				/*- Remember Me -*/
				if ($this->input->post('remember')) {
					$cookie = array(
						'name' => 'rem_username',
						'value' => $username,
						'expire' => "86500",
					);
					$this->input->set_cookie($cookie);

					$cookie = array(
						'name' => 'rem_password',
						'value' => $password,
						'expire' => "86500",
					);
					$this->input->set_cookie($cookie);
				} else {
					delete_cookie("rem_username");
					delete_cookie("rem_password");
				}

				if ($this->session->flashdata('currentUrlExists')) {
					redirect($this->session->flashdata('currentUrl'), 'refresh');
				} else {
					if ($userRole == 'Lead') {
						redirect('/consultanttask/entry/', 'refresh');
					}
					redirect('/ApproveConsultant', 'refresh');
				}
			} else {
				$this->session->set_userdata(array(
					'msg' => "error",
				));
				$this->load->view('login');
				//redirect('/user/login', 'refresh');
			}
		}
	}

	/**
	 * @param $designationICode
	 * @return string
	 */
	private function getUserRole($designationICode)
	{
		$designationDetails = $this->user_detail->user_designation($designationICode);
		$leadArray = ['Project Leader', 'Project Manager', 'Associate PM', 'Associate PL'];
		$accountantArray = ['Sr.Accounts Executive', 'Senior Finance Manager', 'Finance Manager', 'Assistant Manager- Finance', 'Assistant Mananger-Accounts'];
		$designation = $designationDetails[0]['Designation'];
		$userRole = '';
		if (strpos($designation, 'HR') !== false) {
			$userRole = 'HR';
		} elseif (in_array($designation, $leadArray)) {
			$userRole = "Lead";
		} elseif (in_array($designation, $accountantArray)) {
			$userRole = "Account";
		}
		return $userRole;
	}

	/*
		Default Page - User Login Page
	*/
	public function login()
	{
		if ($this->session->userdata('id'))
			redirect('/consultanttask/entry', 'refresh');
		else {
			$this->load->view('login');
		}
	}

	/*
		Logout the User Session Values.
	*/
	public function logout()
	{
		// $this->session->unset_userdata(array(
		// 	'username' 		=> '',
		// 	'id'			=> '',
		// 	'display_name'	=> '',
		// 	'first_name' 	=> '',
		// 	'last_name' 	=> '',

		// ));
		$this->session->sess_destroy();
		redirect('/user/login', 'refresh');
	}

	public function passupt()
	{
		if ($this->session->userdata('id'))
			$this->load->view('password_upt');
		else
			$this->load->view('login');
	}

	public function pwdreset()
	{
		if ($this->session->userdata('id')) {
			$this->form_validation->set_rules('new_password', 'New Password', 'required|trim|min_length[5]|max_length[15]');
			/*|callback_alpha_dash_space*/
			$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|trim|matches[new_password]');

			if ($this->form_validation->run() == FALSE) {
				$this->load->view('password_upt');
			} else {
				$emp_id = $this->session->userdata('id');
				$n_password = $this->input->post('new_password');
				$c_password = $this->input->post('confirm_password');
				$data = $this->user_detail->user_passupt($n_password, $emp_id);
				$this->session->set_userdata(array(
					'msg' => "succ",
				));
				$this->load->view('password_upt');
			}
		} else {
			$this->load->view('login');
		}
	}

	/*
	function alpha_dash_space($str_in)
	{
		if (! preg_match("/^[@!#\$\^%&*()+=\-\[\]\\\';,\.\/\{\}\|\":<>\? ]+$/i", $str_in))
		{
			$this->form_validation->set_message('alpha_dash_space', 'The %s field may only contain alpha-numeric characters, and special characters.');
			return FALSE;
		} else {
			return TRUE;
		}
	}
	*/
}
