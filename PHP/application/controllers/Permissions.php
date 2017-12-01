<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permissions extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('permission_model');
		$this->load->model('registry_model');
		$this->load->helper('csrf_helper');

		define('NAV_SELECT', 2);
	}

	public function index()
	{
		if ( $this->permission_model->adminAllow() ) {
			// load view
			$this->load->view('header');
			$this->load->view('permission_view');
			$this->load->view('footer');
		} else show_404();
	}

	public function data()
	{
		if ( $this->permission_model->adminAllow() ) {
			csrf_json_response([
				'models' => $this->registry_model->models()->result(),
				'groups' => $this->permission_model->groups()->result(),
				'data' => $this->permission_model->getPermissionGroups()]);
		} else show_404();
	}

	public function set()
	{
		if ( $this->permission_model->adminAllow() ) {
			
			$permission = $this->input->post('permission');
			$table = $this->input->post('table');
			$for = $this->input->post('group');

			$result = false;

			if (is_numeric ($permission))
				$result = $this->permission_model->setPermission($table, $for, $permission);


			csrf_json_response([
				'permission' => $this->permission_model->groupPermission($table, $for),
				'success' => $result
			]);

		} else show_404();
	}
}
