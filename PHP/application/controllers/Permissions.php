<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permissions extends CI_Controller {

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
			// load view
			$this->load->view('header');
			$this->load->view('permission_view');
			$this->load->view('footer');
		} else show_404();
	}

	public function data()
	{
		if ( $this->permission_model->adminAllow() ) {
			$token = $this->security->get_csrf_token_name();
			$hash = $this->security->get_csrf_hash();

			echo json_encode(
				[
					'csrf' => $token,
					'csrf_hash' => $hash,
					'models' => $this->registry_model->models()->result(),
					'groups' => $this->permission_model->groups()->result(),
					'data' => $this->permission_model->getPermissionGroups()

				]
				,JSON_NUMERIC_CHECK);
		} else show_404();
	}

	public function set()
	{
		if ( $this->permission_model->adminAllow() ) {

			$token = $this->security->get_csrf_token_name();
			$hash = $this->security->get_csrf_hash();
			
			$permission = $this->input->post('permission');
			$table = $this->input->post('table');
			$for = $this->input->post('group');

			$result = false;

			if (is_numeric ($permission))
				$result = $this->permission_model->setPermission($table, $for, $permission);

			echo json_encode(
				[
					'csrf' => $token,
					'csrf_hash' => $hash,
					'permission' => $this->permission_model->groupPermission($table, $for),
					'success' => $result
				]
				,JSON_NUMERIC_CHECK);

		} else show_404();
	}
}
