<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	public function index()
	{
		$this->load->view('header');
		$this->load->view('field_exp_builder');
		$this->load->view('footer');
	}
}
