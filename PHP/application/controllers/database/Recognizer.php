<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Recognizer extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper("csrf_helper");
		$this->load->library('registry');
		$this->load->model('permission_model');
		// permissions
	}

	public function index()
	{
		if (!$this->permission_model->adminAllow()) {
			show_404();
			return;
		}

		$this->load->view('header');
		$this->load->view('recognizer_view');
		$this->load->view('footer');
	}

	public function data() {
		if (!$this->permission_model->adminAllow()) {
			show_404();
			return;
		}

		$data = [
			'data' => $this->registry->notRegistered(),
			'recognized' => $this->registry->imports()->result()
		];
		csrf_json_response($data);
	}

	public function identify () {
		if (!$this->permission_model->adminAllow()) {
			show_404();
			return;
		}

		$key = $this->input->post('table');
		$title = $this->input->post('title');

		$success = $this->registry->registerTable($key, $title);

		csrf_json_response(
			[ 'success' => $success, 'error_message' => 'Sorry, the table is probably not indexable.' ]);
		
	}

	public function unidentify () {
		if (!$this->permission_model->adminAllow()) {
			show_404();
			return;
		}

		$key = $this->input->post('table');

		$success = $this->registry->unregisterTable($key);

		csrf_json_response(
			[ 'success' => $success, 'error_message' => 'Sorry, table not found.' ]);
		
	}


}
