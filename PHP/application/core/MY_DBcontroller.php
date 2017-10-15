<?php

class MY_DBcontroller extends CI_Controller
{

	protected $model = NULL;

	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->helper('url');
	}

	public function makeTableHTML()
	{
		$link = current_url();
		$postScript = 'window.location = "'.$link.'";';

		$data = array(
			'tablehtml' => $this->model->makeTableWithDelete($link, $postScript)
		);
		$this->load->view('table_view', $data);
	}

	public function makeInputHtml($getHTML = false)
	{

		$fields = $this->model->getFieldAssociations();
		$link = uri_string();

		$data = array(
			'fields' => $fields,
			'constants' => array(DB_REQUEST=>DB_INSERT),
			'link' => $link
		);
		return $this->load->view('form_generator', $data, $getHTML);

	}

	public function handleRequest() {
		$submit = $this->input->post(DB_REQUEST);

		if ($submit == DB_INSERT) {
			$this->takeInput();

		} else if ($submit == DB_DELETE) {
			$submit = $this->input->post(DB_PKWORD);
			if ( !empty($submit) ) $this->model->deleteWithPK($submit);
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