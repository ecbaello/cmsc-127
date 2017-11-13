<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tablemanager extends CI_Controller {

	const prefix = 'fin_cust_';

	public function __construct()
	{
		parent::__construct();
		$this->load->model('permission_model');
		$this->load->model('registry_model');

		define('NAV_SELECT', 3);
	}

	public function index()
	{	
		if ( $this->permission_model->adminAllow() ) {
			$this->load->view('header');
			$this->load->view('tablemanager_view');
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

			$title = self::prefix.str_replace(' ', '_', $name);

			$model = new MY_DBmodel;
			$model->ModelTitle = $name;
			$model->TableName = $title;
			$model->FieldPrefix = $prefix; // validate not empty
			$model->init();
			$model->createTableWithID();

			echo json_encode(
				[
					'csrf' => $token,
					'csrf_hash' => $hash
				]
				,JSON_NUMERIC_CHECK);	
		} else show_404();
	}

	public function delete()
	{
		if ( $this->permission_model->adminAllow() ) {

			$meta = MY_DBmodel::metaTableName;

			$token = $this->security->get_csrf_token_name();
			$hash = $this->security->get_csrf_hash();

			$table = $this->input->post('table');
			$regmod = $this->registry_model;

			$this->db->where('table_name', $table);
			$qry = $this->db->get($regmod::modelTableName)->row();

			if ( $qry->mdl_class == null && $this->dbforge->drop_table($table, TRUE) ) {
				$this->db->where('table_name', $table);
				$this->db->delete($meta);

				$this->db->where('table_name', $table);
				$this->db->delete($regmod::modelTableName);

				$permiss = $this->permission_model;

				$this->db->where('table_name', $table);
				$this->db->delete($permiss::tableName);
			}

			echo json_encode(
				[
					'csrf' => $token,
					'csrf_hash' => $hash
				]
				,JSON_NUMERIC_CHECK);
		} else show_404();
	}

	public function data()
	{
		if ( $this->permission_model->adminAllow() ) {

			$meta = MY_DBmodel::metaTableName;

			$token = $this->security->get_csrf_token_name();
			$hash = $this->security->get_csrf_hash();

			$table = $this->input->post('table');
			$regmod = $this->registry_model;

			$qry = $regmod->customs()->result();

			echo json_encode(
				[
					'csrf' => $token,
					'csrf_hash' => $hash,
					'data' => $qry
				]
				,JSON_NUMERIC_CHECK);	
		} else show_404();
	}
}