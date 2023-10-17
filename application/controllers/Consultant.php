<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Consultant extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		/*-Session LogOut Catch Clear -*/
		$this->output->set_header('cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header("cache-Control: post-check=0, pre-check=0", false);
		$this->output->set_header("Pragma: no-cache");
		$this->output->set_header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		$this->load->helper('url');
		$this->load->helper('email');
		$this->load->model('mgeneral');
		$this->load->model('consultant_details');
		$this->load->model('consultant_task_detail');
		// $this->load->library('upload');
	}

	/** -----------------------------------------------------------------------------------------------------------------------
	 * ------------------------------------------------- Add Consultant Deatails --------------------------------------------------------------
	 * ----------------------------------------------------------------------------------------------------------------------- **/
	public function index()
	{
		$data['technologies'] =  $this->consultant_task_detail->get_consultant_technologies('CGvak_Technology_platform');
		$this->load->view('consultant',$data);
	}

	public function register_consultant()
	{
		if ($_FILES) {
			$first_name = $this->input->post('consultant_first_name');
			$last_name = $this->input->post('consultant_last_name');
			$file_name = $first_name . '_' . $last_name;

			if ($this->add_consultant_form_rules("add") == True) {
				if ($this->email_exists()) {
					
					$config['upload_path'] = './public/images/consultant/';
					$config['allowed_types'] = 'gif|jpg|png|jpeg';
					$config['file_name'] = $file_name;

					$this->load->library('upload', $config);
					if (!$this->upload->do_upload('pictureupload')) {
						$error = array('imgTypeError' => $this->upload->display_errors());
					} else {
						$image_data = $this->upload->data();
						$file_config['upload_path'] = './public/resumes/';
						$file_config['allowed_types'] = 'doc|docx|pdf';
						$file_config['file_name'] = $file_name;
						$this->upload->initialize($file_config);

						if (!$this->upload->do_upload('resumeupload')) {
							$error = array('fileTypeError' => $this->upload->display_errors());
						} else {
							$file_data = $this->upload->data();
							$data['image_name'] = $image_data['file_name'];
							$data['resume_name'] = $file_data['file_name'];
							$data['resume_path'] = $file_data['full_path'];

							$file_config['upload_path'] = './public/consultant/docs/';
							$file_config['allowed_types'] = 'jpg|jpeg|png|pdf';
							$file_config['file_name'] = $file_name.'_pan_'.time();
							$this->upload->initialize($file_config);

							if (!$this->upload->do_upload('pancard')) {
								$error = array('pancardTypeError' => $this->upload->display_errors());
							} else {
								$pan_data = $this->upload->data();
								$data['pancard_name'] = $pan_data['file_name'];

								$file_config['file_name'] = $file_name.'_aadhar_'.time();
								$this->upload->initialize($file_config);

								if (!$this->upload->do_upload('aadharcard')) {
									$error = array('aadharcardTypeError' => $this->upload->display_errors());
								} else {
									$aadhar_data = $this->upload->data();
									$data['aadharcard_name'] = $aadhar_data['file_name'];

									$file_config['file_name'] = $file_name.'_bank_statement_'.time();
									$this->upload->initialize($file_config);

									if (!$this->upload->do_upload('bank_statement')) {
										$error = array('bank_statementTypeError' => $this->upload->display_errors());
									} else {
										$bank_statement_data = $this->upload->data();
										$data['bank_statement_name'] = $bank_statement_data['file_name'];

										$file_config['file_name'] = $file_name.'_nda_document_'.time();
										$this->upload->initialize($file_config);

										if (!$this->upload->do_upload('nda_document')) {
											$error = array('nda_documentTypeError' => $this->upload->display_errors());
										} else {
											$nda_document_data = $this->upload->data();
											$data['nda_document_name'] = $nda_document_data['file_name'];

											$this->add_consultant($data);

										}
									}
								}
							}

						}

					}


				} else {
					$error['email_already_exists'] = "Given Email Id is already registered";
				}

			}
		}
		if (isset($error)) {
			$data = $error;
		} else {
			$data = '';
		}
		$this->load->view('consultant', $data);
	}

	public function email_exists()
	{
		$consultant_email = $this->input->post('consultant_email');
		$this->db->select('*');
		$this->db->from(TBL_CONSULTANT_MASTER);
		$this->db->where('ConsultantEmailId', $consultant_email);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return false;
		} else {
			return true;
		}
	}


	public function email_exists_through_ajax()
	{
		$consultant_email = $this->input->post('consultant_email');
		$this->db->select('*');
		$this->db->from(TBL_CONSULTANT_MASTER);
		$this->db->where('ConsultantEmailId', $consultant_email);
		$query = $this->db->get();

		echo json_encode($query->num_rows() > 0);
	}

	public function add_consultant_form_rules($type)
	{
		$this->form_validation->set_rules('consultant_first_name', 'Consultant FirstName', 'trim|required');
		$this->form_validation->set_rules('consultant_last_name', 'Consultant LastName', 'trim|required');
		$this->form_validation->set_rules('consultant_name', 'Consultant Name', 'trim|required');
		$this->form_validation->set_rules('consultant_email', 'Consultant Email', 'trim|required|valid_email');
		$this->form_validation->set_rules('consultant_mobile_number', 'Consultant MobileNo', 'trim|required|regex_match[/^[0-9]{10}$/]');
		$this->form_validation->set_rules('consultant_current_address1', 'Consultant CurrentAddress1', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('consultant_current_address2', 'Consultant CurrentAddress2', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('consultant_current_address3', 'Consultant CurrentAddress3', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('consultant_current_city', 'Consultant CurrentCity', 'trim|required');
		$this->form_validation->set_rules('consultant_current_state', 'Consultant CurrentState', 'trim|required');
		$this->form_validation->set_rules('consultant_current_country', 'Consultant CurrentCountry', 'trim|required');
		$this->form_validation->set_rules('consultant_pincode', 'Consultant PinCode', 'trim|required|regex_match[/^[1-9]{1}[0-9]{2}\\s{0,1}[0-9]{3}$/]');
		$this->form_validation->set_rules('consultant_alternative_address', 'Consultant AlternativeAddress', 'trim|max_length[200]');
		$this->form_validation->set_rules('consultant_phone_number', 'Consultant PhoneNo', 'trim|regex_match[/^[0-9]{10}$/]');
		$this->form_validation->set_rules('consultant_pan_number', 'Consultant PanNo', 'trim|required');
		$this->form_validation->set_rules('consultant_technology', 'Consultant Technology', 'required');
		$this->form_validation->set_rules('consultant_other_skills', 'Other Skills', 'trim');
		$this->form_validation->set_rules('consultant_reference', 'Consultant Reference', 'trim');
		$this->form_validation->set_rules('consultant_bank_name', 'Consultant BankName', 'trim|required');
		$this->form_validation->set_rules('consultant_bank_account_number', 'Consultant BankAccountNo', 'trim|required|regex_match[/^[0-9]{9,18}$/]', array('regex_match' => "Bank account number only accepts the numbers between 9 and 18 digits"));
		$this->form_validation->set_rules('consultant_bank_ifsc_code', 'Consultant BankIFSCCode', 'trim|required');
		$this->form_validation->set_rules('consultant_alternative_bank_name', 'Consultant Alternative BankName', 'trim');
		$this->form_validation->set_rules('consultant_alternative_bank_account_number', 'Consultant Alternative BankAccountNo', 'trim|regex_match[/^[0-9]{9,18}$/]');
		$this->form_validation->set_rules('consultant_alternative_bank_ifsc_code', 'Consultant Alternative BankIFSCCode', 'trim');

		if ($type == "add") {
			if (empty($_FILES['pictureupload']['name'])) {
				$this->form_validation->set_rules('pictureupload', 'Image', 'required');
			}
			if (empty($_FILES['pancard']['name'])) {
				$this->form_validation->set_rules('pancard', 'PAN Card', 'required');
			}
			if (empty($_FILES['aadharcard']['name'])) {
				$this->form_validation->set_rules('aadharcard', 'Aadhar Card', 'required');
			}
			if (empty($_FILES['bank_statement']['name'])) {
				$this->form_validation->set_rules('bank_statement', 'Bank Statement / Cheque', 'required');
			}
			if (empty($_FILES['nda_document']['name'])) {
				$this->form_validation->set_rules('nda_document', 'NDA Document', 'required');
			}
		}

		return $this->form_validation->run();
	}

	private function consultant_details($data)
	{
		if(isset($data['image_name'])){
			$file_url = "public/images/consultant/" . $data['image_name'];
			$InputArray['pictureupload'] = $file_url;
		}
		if(isset($data['resume_name'])){
			$InputArray['resume_path'] = "public/resumes/" . $data['resume_name'];
		}
		$docs_path = "public/consultant/docs/";

		$InputArray['ConsultantFirstName'] = $this->input->post('consultant_first_name');
		$InputArray['ConsultantLastName'] = $this->input->post('consultant_last_name');

		$InputArray['ConsultantName'] = $this->input->post('consultant_name');
		if(isset($data['pancard_name']))
			$InputArray['PanDocument'] = $docs_path.$data['pancard_name'];
		if(isset($data['aadharcard_name']))
			$InputArray['AadharDocument'] = $docs_path.$data['aadharcard_name'];
		if(isset($data['bank_statement_name']))
			$InputArray['BankDocument'] = $docs_path.$data['bank_statement_name'];
		if(isset($data['nda_document_name']))
			$InputArray['NDADocument'] = $docs_path.$data['nda_document_name'];

		$InputArray['ConsultantCurrentAddress1'] = $this->input->post('consultant_current_address1');
		$InputArray['ConsultantCurrentAddress2'] = $this->input->post('consultant_current_address2');
		$InputArray['ConsultantCurrentAddress3'] = $this->input->post('consultant_current_address3');
		$InputArray['ConsultantCurrentCity'] = $this->input->post('consultant_current_city');
		$InputArray['ConsultantCurrentState'] = $this->input->post('consultant_current_state');
		$InputArray['ConsultantCurrentCountry'] = $this->input->post('consultant_current_country');
		$InputArray['ConsultantPinCode'] = $this->input->post('consultant_pincode');
		$InputArray['ConsultantPhoneNo'] = $this->input->post('consultant_phone_number');
		$InputArray['ConsultantMobileNo'] = $this->input->post('consultant_mobile_number');
		$InputArray['ConsultantEmailId'] = $this->input->post('consultant_email');
		$InputArray['ConsultantAlternativeAddress'] = $this->input->post('consultant_alternative_address');
		$InputArray['ConsultantPANNo'] = $this->input->post('consultant_pan_number');
		$InputArray['ConsultantBankName'] = $this->input->post('consultant_bank_name');
		$InputArray['ConsultantBankAccountNo'] = $this->input->post('consultant_bank_account_number');
		$InputArray['ConsultantBankIFSCCode'] = $this->input->post('consultant_bank_ifsc_code');
		$InputArray['ConsultantalrternativeBankName'] = $this->input->post('consultant_alternative_bank_name');
		$InputArray['ConsultantalternativeBankAccountNo'] = $this->input->post('consultant_alternative_bank_account_number');
		$InputArray['ConsultantalternativeBankIFSCCode'] = $this->input->post('consultant_alternative_bank_ifsc_code');
		$InputArray['ConsultantTechnology'] = $this->input->post('consultant_technology');
		$InputArray['OtherSkills'] = $this->input->post('consultant_other_skills');
		$InputArray['ConsultantReference'] = $this->input->post('consultant_reference');
		$InputArray['HourlyRate'] = $this->input->post('HourlyRate');
		$InputArray['CompanyICode'] = 1;
		$InputArray['DepartmentICode'] = 5;
		$InputArray['isselfregistered'] = 1;
		$InputArray['selftregistersubmittedon'] = date('Y-m-d H:i:s');
		$InputArray['CreatedDate'] = date('Y-m-d H:i:s');

		return $InputArray;
	}

	public function add_consultant($data)
	{
		$InputArray = $this->consultant_details($data);
		$insert_id = $this->consultant_details->add_data(TBL_CONSULTANT_MASTER, $InputArray);
		$this->mail_to_consultant($InputArray);
		$this->mail_to_hr($InputArray, $insert_id,$data['resume_path']);
		if ($insert_id) {
			$message = 'Registered Successfully';
			$class = 'success';
		} else {
			$message = 'Failed please try again!';
			$class = 'warning';
		}
		$this->session->set_flashdata($class, $message);
		redirect(base_url('consultant'));
	}

	private function mail_to_consultant($InputArray)
	{
		$data['mailHead'] = "Hi " . $InputArray['ConsultantFirstName'] . " " . $InputArray['ConsultantLastName'];
		$data['mailBody'] = "Welcome to CG-VAK Software and Exports Ltd. Your profile has been registered successfully. Our HR will reach you out to your registered email with your credentials for login";
		$subject = 'Welcome to CG-VAK';
		$message = $this->load->view('email/consultant_register_email.php', $data, TRUE);
		$this->send_email($message, $InputArray['ConsultantEmailId'], $subject);
	}

	private function mail_to_hr($InputArray, $insert_id,$file_path)
	{
		$data['mailHead'] = "Dear HR";
		$data['mailBody'] = "MR." . $InputArray['ConsultantFirstName'] . " " . $InputArray['ConsultantLastName'] . " has successfully registered as a consultant. Kindly review and approve his registration using below link.";
		$data['toHr'] = 1;
		$data['id'] = base64_encode($insert_id);
		$subject = 'Approve Consultant';
		$message = $this->load->view('email/consultant_register_email.php', $data, TRUE);
		$this->send_email($message, HR_MAIL, $subject,$file_path);
	}

	// To check mail functionality - for testing purpose
	// public function test_mail() {
	// 	$message = 'Test Message';
	// 	$email = 'saravanakumarss@cgvakindia.com';
	// 	$subject = 'Test Subject';
	// 	return $this->send_email($message, $email, $subject);
	// }

	public function send_email($message, $email, $subject, $file = '')
	{
		// $config = array(
		// 	'protocol' => 'smtp', // 'mail', 'sendmail', or 'smtp'
		// 	'smtp_host' => 'ssl://smtp.googlemail.com',
		// 	'smtp_port' => 465,
		// 	'smtp_user' => 'cgvakconsulant@gmail.com',
		// 	'smtp_pass' => 'yuqhkumzxflfxpti',
		// 	'mailtype' => 'html', //plaintext 'text' mails or 'html'
		// 	'smtp_timeout' => '30',
		// 	'charset' => 'iso-8859-1',
		// 	'wordwrap' => TRUE
		// );

		$config = array(
			'protocol' => 'smtp', // 'mail', 'sendmail', or 'smtp'
			'smtp_host' => 'mail.cgvakindia.com',
			'smtp_port' => 587,
			'smtp_crypto' => 'tls',
			'smtp_user' => 'ctt@cgvakindia.com',
			'smtp_pass' => 'CGwelcome@123',
			'mailtype' => 'html', //plaintext 'text' mails or 'html'
			'smtp_timeout' => '30',
			'charset' => 'iso-8859-1',
			'wordwrap' => TRUE
		);


		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		$this->email->from('ctt@cgvakindia.com', 'CG-VAK software and exports');
		$this->email->to($email);
		$this->email->subject($subject);
		$this->email->message($message);
		if ($file)
			$this->email->attach($file);

		if ($this->email->send()) {
			// echo "Success mail";
		} else {
			show_error($this->email->print_debugger());
			// echo "Failure";
		}
	}

	//Get all the consultant and display in view
	public function get_consultant_list()
	{
		if ($this->session->userdata('id')) {
			
			if($this->input->get('refresh') == '1') {
				// $consultant_users = $this->consultant_details->getNotLatestConsultantDetails('active');
				$consultant_users = $this->consultant_details->getConsultantDetails('active');
				foreach($consultant_users as $cons) {
					
					$data['progress_list'] = $this->consultant_task_detail->recent_progress_list($cons['ConsultantICode'], null);
					if(empty($data['progress_list']) && $cons['appdays'] > 30) {
						$this->consultant_details->updateConsultant($cons['ConsultantICode'], ['IsActive' => 0]);
					}
				}
				if($this->input->get('status') != 'all') {
					$url = 'consultant/get_consultant_list?status='.$this->input->get('status');
				} else {
					$url = 'consultant/get_consultant_list';
				}
				redirect(base_url($url));
			}
			$status_filter = ($this->input->get('status') != 'all') ? $this->input->get('status') : null;
			$data['consultant'] = $this->consultant_details->getConsultantDetails($status_filter);
			if($this->input->get('status') == 'not_utilized') {
				$data['consultant'] = array_map(function($value) {
					// $data['progress_list'] = $this->consultant_task_detail->recent_progress_list($value['ConsultantICode'], null);
					// if(empty($data['progress_list'])) {
					// 	return $value;
					// }
					$progress_list_count = $this->consultant_task_detail->recent_progress_list_count($value['ConsultantICode'], null, null);
					if($progress_list_count == 0) {
						return $value;
					}
				}, $data['consultant']);

				$data['consultant'] = array_values(array_filter($data['consultant']));
			} else if($this->input->get('status') == 'not_utilized_30_days') {
				$data['consultant'] = array_map(function($value) {
					// $data['progress_list'] = $this->consultant_task_detail->recent_progress_list($value['ConsultantICode'], null, 30);
					$progress_list_count = $this->consultant_task_detail->recent_progress_list_count($value['ConsultantICode'], null, 30);
					if($progress_list_count > 0) {
						return $value;
					}
				}, $data['consultant']);

				$data['consultant'] = array_values(array_filter($data['consultant']));
			}
			$data['technologies'] =  $this->consultant_task_detail->get_consultant_technologies('CGvak_Technology_platform'); 
			$this->load->view('consultant_listing', $data);
		} else {
			$this->load->view('login');
		}
	}

	// Approve consultant page display
	public function approveConsultant($id)
	{
		// if ($this->session->userdata('id') && !$this->session->userdata('role')) {
		if ($this->session->userdata('id')) {
			$id = base64_decode($id);
			$data['Title'] = "Approve consultant";
			$columns = 'ConsultantFirstName, ConsultantLastName, ConsultantMobileNo, ConsultantPhoneNo, ConsultantEmailId, ConsultantICode, isselfregisterApproved';
			$data['consultant'] = $this->consultant_details->getConsultant($id, $columns);
			if($data['consultant'][0]['isselfregisterApproved']){
				redirect(base_url('consultant/get_consultant_list'));
			}else{
				$this->load->view('approve_consultant', $data);
			}
		} elseif (($this->session->userdata('id')) && ($this->session->userdata('role') == 'consultant')) {
			$this->load->view('no_access');
		} else {
			$this->session->set_flashdata('currentUrlExists', TRUE);
			$this->session->set_flashdata('currentUrl', current_url());
			$this->load->view('login');
		}
	}

	//Update consultant button click function
	public function updateConsultantEntry()
	{
		$id = base64_decode($this->input->post('id'));
		$userName = $this->input->post('userName');
		$password = $this->input->post('password');
		$HourlyRate = $this->input->post('HourlyRate');
		$result = array();
		if ($id) {
			$data['isselfregisterApproved'] = 1;
			$data['IsActive'] = 1;
			$data['selfregisterapprovedby'] = $this->session->userdata('id');
			$data['selfregisterapprovedon'] = date('Y-m-d H:i:s');
			$data['ConsultantLoginUserName'] = $userName;
			$data['ConsultantLoginPassword'] = $password;
			$data['HourlyRate'] = $HourlyRate;

			$check = $this->consultant_details->isExistConsultantUsername($id, $data);
			if($check) {
				$result['status'] = _FAILED;
				$result['message'] = 'Username already exist';
				echo json_encode($result);
				exit;
			}			
			$this->consultant_details->updateConsultant($id, $data);

			$columns = 'ConsultantFirstName, ConsultantLastName, ConsultantMobileNo, ConsultantPhoneNo, ConsultantEmailId, ConsultantICode';
			$consultantDetail = $this->consultant_details->getConsultant($id, $columns);
			$this->send_approve_email($consultantDetail, $userName, $password);
			$result['status'] = _SUCCESS;
			$result['message'] = "Consultant is Approved";
		} else {
			$result['status'] = _FAILED;
			$result['message'] = 'Failed try after sometimes.';
		}
		echo json_encode($result);
		exit;
	}

	public function reApproveConsultant() {
		$id = base64_decode($this->input->post('id'));
		if($id) {
			$data = [];
			$data['isselfregisterApproved'] = 1;
			$data['IsActive'] = 1;
			$data['selfregisterapprovedby'] = $this->session->userdata('id');
			$data['selfregisterapprovedon'] = date('Y-m-d H:i:s');

			$this->consultant_details->updateConsultant($id, $data);

			$columns = 'ConsultantFirstName, ConsultantLastName, ConsultantMobileNo, ConsultantPhoneNo, ConsultantEmailId, ConsultantICode, ConsultantLoginUserName, ConsultantLoginPassword';
			$consultantDetail = $this->consultant_details->getConsultant($id, $columns);
			$this->send_approve_email($consultantDetail, $consultantDetail[0]['ConsultantLoginUserName'], $consultantDetail[0]['ConsultantLoginPassword']);
			$result['status'] = _SUCCESS;
			$result['message'] = "Consultant is Approved";
		} else {
			$result['status'] = _FAILED;
			$result['message'] = 'Failed try after sometimes.';
		}
		echo json_encode($result);
		exit;
	}

	private function send_approve_email($consultantDetail, $userName, $password)
	{
		$data['userName'] = $userName;
		$data['password'] = $password;
		$data['toHr'] = 1;
		$data['mailHead'] = "Hi " . $consultantDetail[0]['ConsultantFirstName'] . " " . $consultantDetail[0]['ConsultantLastName'];
		$data['mailBody'] = 'You have successfully registered into our system. Here is your credentials for login.<br><br>Username: ' . $userName . ' <br>Password: ' . $password . ' <br>';
		$subject = 'Your Request is Approved!';
		$message = $this->load->view('email/consultant_register_email.php', $data, TRUE);
		$this->send_email($message, $consultantDetail[0]['ConsultantEmailId'], $subject);
	}

	//Edit consultant detail view
	public function editConsultantDetails($id)
	{
		$this->session->set_userdata('consultantId', $id);
		$columns = 'ConsultantFirstName, ConsultantLastName, ConsultantCurrentAddress1, 
      ConsultantCurrentAddress2, ConsultantCurrentAddress3, ConsultantCurrentCity, ConsultantCurrentState, 
      ConsultantCurrentCountry, ConsultantPinCode, ConsultantPhoneNo, ConsultantMobileNo, ConsultantEmailId,
      ConsultantAlternativeAddress, ConsultantPANNo, ConsultantBankName, ConsultantBankAccountNo, ConsultantBankIFSCCode, 
      ConsultantalrternativeBankName, ConsultantalternativeBankAccountNo, ConsultantalternativeBankIFSCCode, 
      ConsultantTechnology, OtherSkills, ConsultantReference, HourlyRate, ConsultantICode, pictureupload';
		$data['consultant'] = $this->consultant_details->getConsultant($id, "*");
		$data['technologies'] =  $this->consultant_task_detail->get_consultant_technologies('CGvak_Technology_platform');
		if ($this->session->userdata('role') != 'Lead') {
			$this->load->view('edit_consultant', $data);
		} else {
			$this->load->view('view_consultant', $data);
		}
	}

	//Update consultant functionality
	public function update_consultant(){

		$valid = true;
		$resume_valid = true;
		$picture_valid = true;
		$pancard_valid = true;
		$aadharcard_valid = true;
		$bank_statement_valid = true;
		$nda_document_valid = true;
		$id = $this->input->post('consultant_i_code');
		$data['consultant'] = $this->consultant_details->getConsultant($id, "*");
		$error = [];

		if($_POST){
			$first_name = $this->input->post('consultant_first_name');
			$last_name = $this->input->post('consultant_last_name');
			$file_name = $first_name . '_' . $last_name;
			$valid = ($this->add_consultant_form_rules("update")) ? true : false;
		}

		if($_FILES){

			$config['upload_path'] = './public/images/consultant/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['file_name'] = $file_name;


			// $this->upload->initialize($config);
			$this->load->library('upload', $config);
			

			if($_FILES['pictureupload']['name']){

				$config['upload_path'] = './public/images/consultant/';
				$config['allowed_types'] = 'gif|jpg|png|jpeg';
				$config['file_name'] = $file_name;


				// $this->upload->initialize($config);
				$this->load->library('upload', $config);

				if (!$this->upload->do_upload('pictureupload')) {
					$error = array_merge($error,array('imgTypeError' => $this->upload->display_errors()));
					$picture_valid = false;
				} else {
					$image_data = $this->upload->data();
					$data['image_name'] = $image_data['file_name'];
					$picture_valid = true;
				}
			}


			if($_FILES['resumeupload']['name']){
				$file_config['upload_path'] = './public/resumes/';
				$file_config['allowed_types'] = 'doc|docx|pdf';
				$file_config['file_name'] = $file_name;
				// $this->upload->initialize($file_config);
				$this->load->library('upload', $file_config);

				if (!$this->upload->do_upload('resumeupload')) {
					$error = array_merge($error,array('fileTypeError' => $this->upload->display_errors()));
					$resume_valid = false;
				} else {
					$file_data = $this->upload->data();
					$data['resume_name'] = $file_data['file_name'];
					$resume_valid = true;
				}
			}

			if($_FILES['pancard']['name']){
				$file_config['upload_path'] = './public/consultant/docs';
				$file_config['allowed_types'] = 'pdf|jpg|jpeg|png';
				$file_config['file_name'] = $file_name.'_pan_'.time();
				$this->upload->initialize($file_config);
				// $this->load->library('upload', $file_config);

				if (!$this->upload->do_upload('pancard')) {
					$error = array_merge($error,array('pancardTypeError'=>$this->upload->display_errors()));
					$pancard_valid = false;
				} else {
					$file_data = $this->upload->data();
					$data['pancard_name'] = $file_data['file_name'];
					$pancard_valid = true;
				}
			}

			if($_FILES['aadharcard']['name']){
				$file_config['upload_path'] = './public/consultant/docs';
				$file_config['allowed_types'] = 'pdf|jpg|jpeg|png';
				$file_config['file_name'] = $file_name.'_aadhar_'.time();
				$this->upload->initialize($file_config);
				// $this->load->library('upload', $file_config);

				if (!$this->upload->do_upload('aadharcard')) {
					$error = array_merge($error,array('aadharcardTypeError'=>$this->upload->display_errors()));
					$aadharcard_valid = false;
				} else {
					$file_data = $this->upload->data();
					$data['aadharcard_name'] = $file_data['file_name'];
					$aadharcard_valid = true;
				}
			}

			if($_FILES['bank_statement']['name']){
				$file_config['upload_path'] = './public/consultant/docs';
				$file_config['allowed_types'] = 'pdf|jpg|jpeg|png';
				$file_config['file_name'] = $file_name.'_bank_statement_'.time();
				$this->upload->initialize($file_config);
				// $this->load->library('upload', $file_config);

				if (!$this->upload->do_upload('bank_statement')) {
					$error = array_merge($error,array('bank_statementTypeError'=>$this->upload->display_errors()));
					$bank_statement_valid = false;
				} else {
					$file_data = $this->upload->data();
					$data['bank_statement_name'] = $file_data['file_name'];
					$bank_statement_valid = true;
				}
			}

			if($_FILES['nda_document']['name']){
				$file_config['upload_path'] = './public/consultant/docs';
				$file_config['allowed_types'] = 'pdf|jpg|jpeg|png';
				$file_config['file_name'] = $file_name.'_nda_document_'.time();
				$this->upload->initialize($file_config);
				// $this->load->library('upload', $file_config);

				if (!$this->upload->do_upload('nda_document')) {
					$error = array_merge($error,array('nda_documentTypeError'=>$this->upload->display_errors()));
					$nda_document_valid = false;
				} else {
					$file_data = $this->upload->data();
					$data['nda_document_name'] = $file_data['file_name'];
					$nda_document_valid = true;
				}
			}

		}

		if($valid && $resume_valid && $picture_valid && $pancard_valid && $aadharcard_valid && $bank_statement_valid && $nda_document_valid){
		
				$InputArray = $this->consultant_details($data);
				$id = $this->input->post('consultant_i_code');
					
				$update_id = $this->consultant_details->updateConsultant($id, $InputArray);

				if ($update_id) {
					$message = 'Consultant updated Successfully';
					$class = 'success';
				} else {
					$message = 'Failed please try again!';
					$class = 'warning';
				}

				$this->session->set_flashdata($class, $message);
				redirect(base_url('consultant/get_consultant_list'));
		}else{
			if(isset($error)){
				$data = array_merge($data,$error);
				$this->load->view('edit_consultant', $data);
			}
		}
	}

	//Unapprove consultant button click function
	public function unApproveConsultant()
	{
		$id = base64_decode($this->input->post('id'));
		$result = array();
		if ($id) {
			$data['isselfregisterApproved'] = 0;
			$data['IsActive'] = 0;
			$this->consultant_details->updateConsultant($id, $data);
			$result['status'] = _SUCCESS;
			$result['message'] = "Consulatant is Unapproved";
		} else {
			$result['status'] = _FAILED;
			$result['message'] = 'Failed try after sometimes.';
		}
		echo json_encode($result);
		exit;
	}

	public function checkMail()
	{

		$data['mailHead'] = "Dear HR";
		$data['mailBody'] = "MR.Raja has successfully registered as a consultant. Kindly review and approve his registration using below link.";
		$data['toHr'] = 1;
		$data['id'] = base64_encode(132);
		$subject = 'Approve Consultant';
		$message = $this->load->view('email/consultant_register_email.php', $data, TRUE);
		$this->send_email($message, HR_MAIL, $subject);
	}

}
