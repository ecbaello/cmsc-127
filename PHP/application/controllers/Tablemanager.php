<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tablemanager extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('permission_model');
		$this->load->model('registry_model');

		define('NAV_SELECT', 2);
	}

	public function index()
	{	
		if ( $this->permission_model->adminAllow() ) {
			$this->load->view('header');

			$this->load->view('footer');
		} else show_404();
	}

	public function new()
	{
		if ( $this->permission_model->adminAllow() ) {

			$token = $this->security->get_csrf_token_name();
			$hash = $this->security->get_csrf_hash();

			$name = $this->input->post('title');
			$prefix = $this->input->post('prefix');
			

			$field = str_replace(' ', '_', $title);
			$field = strtolower(
				($this->FieldPrefix!=null?$this->FieldPrefix:$this->TableName)
				.'_'.$field);

			echo json_encode(
				[
					'csrf' => $token,
					'csrf_hash' => $hash


				]
				,JSON_NUMERIC_CHECK);

			
		} else show_404();
	}

	
}