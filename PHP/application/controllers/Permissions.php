<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permissions extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('permission_model');

		define('NAV_SELECT', 2);
	}

	public function index()
	{
		if ( $this->permission_model->adminAllow() ) {
			// load view
			$this->load->view('header');
			echo 'no scope';
			$this->load->view('footer');
		} else {
			show_error('You\'re not allowed to access this page.', 403);
		}
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
					'data' => $this->permission_model->getPermissionGroups()->result()
				]
				,JSON_NUMERIC_CHECK);
			// json encode stuff
			// send csrf
		} else {
			show_error('You\'re not allowed to access this page.', 403);
		}
	}

	public function set()
	{
		if ( $this->permission_model->adminAllow() ) {
			$token = $this->security->get_csrf_token_name();
			$hash = $this->security->get_csrf_hash();

			echo json_encode(
				[
					'csrf' => $token,
					'csrf_hash' => $hash,
					'success' => $true
				]
				,JSON_NUMERIC_CHECK);

		} else {
			show_error('You\'re not allowed to do this action.', 403);
		}
	}
}
