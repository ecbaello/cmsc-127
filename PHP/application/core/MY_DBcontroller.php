<?php

class MY_DBcontroller extends CI_Controller
{

	protected $model = NULL;

	public function __construct()
	{
		parent::__construct(); // do constructor for parent class
		
		$this->load->library('session');
		$this->load->helper('url');
	}

	public function makeTableHTML()
	{
		$link = current_url();

		$form = $this->makeInputHtml(true);

		$modal = array(
			'actiontitle' => 'Input a row',
			'modalid' => 'input-form',
			'modalcontent' => $form
		);

		$data = array(
			'tablehtml' => $this->model->makeTableWithDelete($link).$this->load->view('popup_generator', $modal, true)
		);

		$this->load->view('table_view', $data);
	}

	public function makeInputHtml($getHTML = false)
	{

		$fields = $this->model->getFieldAssociations();
		$link = uri_string().'?'.DB_REQUEST.'='.DB_INSERT;

		$data = array(
			'fields' => $fields,
			'constants' => array(),
			'link' => $link
		);
		return $this->load->view('form_generator', $data, $getHTML);

	}

	public function handleRequest() {
		$submit = $this->input->get(DB_REQUEST);

		if ($submit == DB_INSERT) {
			$this->takeInput();

		} else if ($submit == DB_DELETE) {
			$id = $this->input->post(DB_PKWORD);
			$this->model->deleteWithPK($id);
		} else if ($submit == DB_UPDATE) {
			$id = $this->input->post(DB_PKWORD);
			$model = $this->model;
			$fields = $model->getFields();
			unset($fields[$model->TablePrimaryKey]);

			$arr = array();
			foreach ($fields as $field) {
				$arr[$field] = $this->input->post($field);
			}

			$this->model->updateWithPK($id, $arr);
		}

	}

	public function takeInput () {
		$inputs = $this->model->getFields();
		$arr = array();
		foreach ($inputs as $input) {
			$value = $this->input->post($input);
			if (! empty($value) ) $arr[$input] = $value; 
		}
		$this->model->insertIntoTable($arr);
	}
}

?>