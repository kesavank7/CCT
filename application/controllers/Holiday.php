<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Holiday extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		/*-Session LogOut Catch Clear -*/
		$this->output->set_header('cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header("cache-Control: post-check=0, pre-check=0", false);
		$this->output->set_header("Pragma: no-cache");
		$this->output->set_header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	}

	public function index()
	{
		$this->load->view('holiday');
	}
}
