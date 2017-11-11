<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function index()
	{
		define('NAV_SELECT', 0);

		$this->load->view('header');
		
		$this->load->view('welcome_message');

		$this->load->view('footer');
		
	}

	
}
