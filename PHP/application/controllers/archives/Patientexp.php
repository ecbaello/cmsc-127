<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Patientexp extends MY_DBcontroller {

	protected $tableView = 'table_archive_view';

	public function __construct()
	{
		defined('NAV_SELECT') or define('NAV_SELECT', 5);

		parent::__construct();
		
		$this->load->model('Database_patient_expenses_model');
		$this->model = $this->Database_patient_expenses_model;
		$this->model->toggleArchive();
	}

	protected function getUserPermission() {
		return $this->permission_model->userPermission($this->model->modelTableName());
	}
	
	protected function makeHTML() {
		$this->load->view('header');

		$this->makeTableHTML();

		$this->load->view('footer');
	}
	
}
