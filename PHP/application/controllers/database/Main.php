<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function index()
	{
		$this->load->view('header');
		$this->load->view('db_ui/index');
		$this->load->view('footer');
		
	}

	public function custom($table_name) {

	}

	
}
