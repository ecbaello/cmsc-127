<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcfrr extends CI_Controller {

	public function index()
	{
		$this->load->model('database_model');
		$this->load->model('database_pcfrr_model');
		
		$this->handleRequest();

		$this->load->view('header');
			
		$this->loadTable();

		$form = $this->makeInputHtml(true);

		$modal = array(
			'actiontitle' => 'Input a row',
			'modalid' => 'input-form',
			'modalcontent' => $form
		);
		$this->load->view('popup_generator', $modal);

		$this->load->view('footer');
	}

	public function loadTable()
	{
		$this->load->helper('url');

		$model = $this->database_pcfrr_model;
		$link = current_url();

		$data = array(
			'tablehtml' => $this->database_model->makeTableWithDelete($model::TableName, 'pcf_rr_request_id', $link)
		);
		$this->load->view('table_view', $data);
	}

	public function makeInputHtml($isGet = false)
	{
		$this->load->helper('url');

		$fields = $this->database_pcfrr_model->getFieldAssociations();
		$link = current_url().'?s=i';

		$data = array(
			'fields' => $fields,
			'link' => $link
		);
		return $this->load->view('form_generator', $data, $isGet);

	}

	public function handleRequest() {
		$submit = $this->input->get('s');
		if ($submit == 'i') {
			$this->takeInput ();
		} else if ($submit == 'r') {
			$submit = $this->input->get('id');
			if ( !empty($submit) ) $this->database_pcfrr_model->deleteWithPK($submit);
		}
	}

	public function takeInput () {
		$inputs = $this->database_pcfrr_model->getFields();
		$arr = array();
		foreach ($inputs as $input) {
			$value = $this->input->post($input);
			if (! empty($value) ) $arr[$input] = $value; 
		}
		$this->database_pcfrr_model->insertIntoTable($arr);
	}
	
}
