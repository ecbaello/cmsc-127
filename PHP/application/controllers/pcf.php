<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcf extends CI_Controller {

	public function index()
	{
		$this->load->model('database_pcf_model');
		

		
		
		$table = $this->changePCF();
		$submit = $this->input->post(DB_REQUEST);
		$link = current_url();
		
		if ( empty($table) ) {
			echo 'Select table';
		} else {
			if ($this->database_pcf_model->checkCategoryExists($table)){
				$this->handleRequest($submit, $table);

				$request = $this->input->post(DB_GET);
				if ($request == BOOL_ON) {
					$this->loadTable($table);
					
				} else {
					$this->load->view('header');

					$data = array(
						'link' => current_url(),
						'pcf_names' => $this->database_pcf_model->db->query('select * from pcf_type_table')
					);
					$this->load->view('pcf_selector',$data);
								
					$this->loadTable($table);
					$form = $this->makeInputHtml($table, true);

					$modal = array(
						'actiontitle' => 'Input a row',
						'modalid' => 'input-form',
						'modalcontent' => $form
					);
					$this->load->view('popup_generator', $modal);
					$this->load->view('footer');
				}
				

			}
			
		}

		
	}
	
	public function handleRequest($submit, $table)
	{
		if ($submit == DB_INSERT) {
			$this->takeInput ($table);
		} else if ($submit == DB_DELETE) {
			$submit = $this->input->post('id');
			if ( !empty($submit) ) $this->database_pcf_model->deleteWithPK($submit);
		}
	}

	public function changePCF(){
		$this->load->library('session');
		if(!empty($this->input->post('t'))){
			$_SESSION['pcfname']=$this->input->post('t');
		}else if(!isset($_SESSION['pcfname'])){
			$_SESSION['pcfname']='General';
		}
		return $_SESSION['pcfname'];
	}
	
	public function loadTable($subtable)
	{	
		$result = $this->database_pcf_model->getCategoryTable($subtable);

		$data = array(
			'tablehtml' => $this->makePCFTableWithDelete($subtable, $result)
		);
		$this->load->view('table_view', $data);
		
	}
	
	public function makePCFTableWithDelete($subtablename, $result_table)
	{
		$this->load->library('db_table');

		$fields = $result_table->list_fields();
		$model = $this->database_pcf_model;

		// Make headers
		$headers = $model->convertFields($fields);
		$this->db_table->set_heading($headers);

		$link = current_url().'?t='.urlencode($subtablename);

		$postScript = 'window.location = "'.$link.'";';

		return $this->db_table->generateDBUsingPK($result_table, 'pcf_id', $link, '', $postScript);
	}

	public function makeInputHtml($subtablename, $isGet = false)
	{
		$this->load->helper('url');

		$fields = $this->database_pcf_model->getFieldAssociations();
		$link = current_url().'?t='.urlencode($subtablename);

		$data = array(
			'fields' => $fields,
			'constants' => array(DB_REQUEST=>DB_INSERT),
			'link' => $link
		);
		return $this->load->view('form_generator', $data, $isGet);

	}

	public function takeInput ($table) {
		$inputs = $this->database_pcf_model->getFields();
		$arr = array();
		foreach ($inputs as $input) {
			$value = $this->input->post($input);
			if (! empty($value) ) $arr[$input] = $value; 
		}
		$this->database_pcf_model->insertIntoCategoryTable($table, $arr);
	}
}
