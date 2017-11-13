<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->model('registry_model');
	}

	public function index()
	{
		define('NAV_SELECT', 1);
		
		
		$this->load->view('header');

		$extratables = $this->registry_model->customs()->result();

		$this->load->view('db_ui/index', ['extratables' => $extratables]);
		$this->load->view('footer');
		
	}

	public function custom($table_name) {
		$custom = $this->registry_model->customtable($table_name);
		var_dump($table_name);

		if (!empty($custom)) {
			$custom = $custom->row();
			$model = new MY_DBmodel;
			$model->ModelTitle = $custom->name;
			$model->TableName = $custom->title;
			$model->FieldPrefix = $custom->prefix; // validate not empty
			$model->init();
			$model->createTableWithID();
		} else show_404();

		
	}

	
}
