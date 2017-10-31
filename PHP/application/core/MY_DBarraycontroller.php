<?php

class MY_DBarraycontroller extends CI_Controller {

	public $model = NULL;

	public function __construct()
	{
		parent::__construct(); // do constructor for parent class
		
		$this->load->library('session');
		$this->load->helper('url');
	}

	public function makeSelectorHTML ($table) {
		$link = current_url();

		$categs = $this->model->getCategories();
		$build = '<select id="sub-selector" name="subtable-select">';
		foreach ($categs as $categ) {
			$build.= '<option value="'.current_url().'?'.QRY_SUBTABLE.'='.urlencode($categ).'" '.($table==$categ?'selected':'').'>'.$categ.'</option>';
		}
		$build.= '</select>';

		$build.= '
		<script>
			$("#sub-selector").change(function () {
				window.location.href = $(this).val();
			});
		</script>
		';
		return $build;
	}

	public function handleRequest($table)
	{
		$submit = $this->input->get(DB_REQUEST);
		if ($submit == DB_INSERT) {
			$this->takeInput ($table);
		} else if ($submit == DB_DELETE) {
			$id = $this->input->post(DB_PKWORD);
			$this->model->deleteFromCategoryTable($table, $id);
		} else if ($submit == DB_UPDATE) {
			$id = $this->input->post(DB_PKWORD);
			$model = $this->model;
			$fields = $model->getFields();

			$arr = array();
			foreach ($fields as $field) {
				$arr[$field] = $this->input->post($field);
			}

			$this->model->updateOnCategoryTable($table, $id, $arr);
		}
		$_SESSION[get_class($this).':table'] = urlencode($table);
	}

	public function loadSession(){
		$input = $this->input->get(QRY_SUBTABLE);

		// check if subtable post data was set, change by redirecting
		if (!empty($input)) return $input;

		// redirect to session variable
		if (isset($_SESSION[get_class($this).':table'])) {
			$link = uri_string().'?'.QRY_SUBTABLE.'='.$_SESSION[get_class($this).':table'];
			redirect($link, 'location');
		}else{
			$categs = $this->model->getCategories();
			$link = uri_string().'?'.QRY_SUBTABLE.'='.urlencode($categs[0]);
			redirect($link, 'location');
		}

		return NULL;
	}
	
	public function makeTableHTML($subtable)
	{	
		$link = current_url().'?'.QRY_SUBTABLE.'='.$subtable;

		$form = $this->makeInputHtml($subtable, true);

		$modal = array(
			'actiontitle' => 'Input a row',
			'modalid' => 'input-form',
			'modalcontent' => $form
		);
		

		$data = array(
			'tablehtml' => $form.$this->model->makeTableWithDelete($subtable, $link)
		);


		$this->load->view('table_view', $data);
		
	}

	public function makeInputHtml($subtablename, $isGet = false)
	{
		$this->load->helper('url');

		$fields = $this->model->getFieldAssociations();
		$link = uri_string().'?'.QRY_SUBTABLE.'='.urlencode($subtablename).'&'.DB_REQUEST.'='.DB_INSERT;

		$data = array(
			'fields' => $fields,
			'constants' => array(DB_REQUEST=>DB_INSERT),
			'link' => $link
		);
		return $this->load->view('form_generator', $data, $isGet);

	}

	public function takeInput ($table) {
		$inputs = $this->model->getFields();
		$arr = array();
		foreach ($inputs as $input) {
			$value = $this->input->post($input);
			if (! empty($value) ) $arr[$input] = $value; 
		}
		$this->model->insertIntoCategoryTable($table, $arr);
	}
}