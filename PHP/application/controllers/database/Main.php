<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->library('registry');
	}

	public function index()
	{
		define('NAV_SELECT', 1);
		
		
		$this->load->view('header');

		$extratables = $this->registry->customs()->result();

		$this->load->view('db_ui/index', ['extratables' => $extratables]);
		$this->load->view('footer');
	}
	
}
