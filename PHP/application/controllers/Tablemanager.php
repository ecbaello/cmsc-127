<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function index()
	{
		define('NAV_SELECT', 1);
		
		$this->load->view('header');
		
		$this->load->view('footer');
		
	}

	public function custom($table_name) {

	}

	
}