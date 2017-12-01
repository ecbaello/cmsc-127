<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tablemanager extends CI_Controller {

	const prefix = 'fin_cust_';

	public function __construct()
	{
		parent::__construct();
		$this->load->model('permission_model');
		$this->load->model('registry_model');
		$this->load->helper('csrf_helper');

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

			$name = $this->input->post('title');
			$prefix = $this->input->post('prefix');

			$arraytype = $this->input->post('array');

			$title = self::prefix.strtr($name, [' '=>'_', '_'=>'+']); // replace special characters
				
			if ($arraytype != 1) {
				$this->load->model('custom_model');
	            $this->custom_model->loadCustom($name, $title, $prefix);
				$success = $this->custom_model->createTableWithID();
			} else {
				$categtable = $title.'_type_table';
				$arrid = $title.'_type';
				$cattitle = $title.'_type_title';

				$this->load->model('custom_array_model');
	            $this->custom_array_model->loadCustom($name, $title, $prefix, $categtable, $arrid, $cattitle);
				$success = $this->custom_array_model->createTableWithID();
			}

			csrf_json_response(['success' => $success]);
			
		} else show_404();
	}

	public function delete()
	{
		if ( $this->permission_model->adminAllow() ) {

			$meta = MY_DBmodel::metaTableName;

			$table = $this->input->post('table');
			$regmod = $this->registry_model;

			$this->db->where('table_name', $table);
			$qry = $this->db->get($regmod::modelTableName)->row();

			$success = false;

			if ( $qry->mdl_class == null && $this->dbforge->drop_table($table, TRUE) ) {
				$this->db->where('table_name', $table);
				$success = $this->db->delete($meta);

				$this->db->where('table_name', $table);
				$success = $success && $this->db->delete($regmod::modelTableName);

				$permiss = $this->permission_model;

				$this->db->where('table_name', $table);
				$success = $success && $this->db->delete($permiss::tableName);
			}

			csrf_json_response(['success' => $success]);
		} else show_404();
	}

	public function data()
	{
		if ( $this->permission_model->adminAllow() ) {

			$meta = MY_DBmodel::metaTableName;

			$table = $this->input->post('table');
			$regmod = $this->registry_model;

			$qry = $regmod->customs()->result();

			csrf_json_response(['data' => $qry]);
		} else show_404();
	}
}
