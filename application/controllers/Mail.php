<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mail extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('form');
	}

	public function index()
	{
		$this->load->helper('form');
		$this->load->view('contact_email_form');
	}

	public function send_mail()
	{
		$config = array(
			'protocol' => 'smtp', // 'mail', 'sendmail', or 'smtp'
			'smtp_host' => 'ssl://smtp.googlemail.com',
			'smtp_port' => 465,
			'smtp_user' => 'cgvakconsulant@gmail.com',
			'smtp_pass' => 'CGv@k_123',
			'mailtype' => 'html', //plaintext 'text' mails or 'html'
			'charset' => 'iso-8859-1',
			'wordwrap' => TRUE
		);

		$to_email = $this->input->post('email');

		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		$this->email->from('cgvakconsulant@gmail.com', 'admin');
		$this->email->to($to_email);
		$this->email->subject('Send Email Codeigniter');
		$this->email->message('The email send using codeigniter library');

		//Send mail
		if ($this->email->send()) {
			$this->session->set_flashdata("email_sent", "Congratulation Email Send Successfully.");
		} else {
			show_error($this->email->print_debugger());
			$this->session->set_flashdata("email_sent", "You have encountered an error");
		}
		$this->load->view('contact_email_form');
	}
}

?>
