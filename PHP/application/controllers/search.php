<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

	public function index()
	{
		$this->load->database();
		$this->load->library('db_table');
		$this->load->model('search_model');

		$this->load->view('header');

		$query = $this->db->get($this->search_model::modelTableName);
		$results = $query->result_array();

		$arr = array();
		foreach ($results as $result) {
			$option = array();

			$table_title = $result[MDL_NAME];
			$is_array = $result[MDL_ARRAY]==1;
			$model = $result[MDL_CLASS];

			if ($is_array) {
				$this->load->model($model);

				$query2 = $this->db->get($this->$model->categoryTableName);
				$categories = $query2->result_array();

				$name = $this->$model->arrayFieldName;
				$title = $this->$model->categoryFieldName;

				foreach ($categories as $category) {
					$item_name = $category[$name];
					$item_title = $category[$title];

					$arr[$model.':'.$item_name] = $table_title.': '.$item_title;
				}
			} else {
				$arr[$model] = $table_title;
			}
		}

		// -- Generate option field for table

		$this->load->view('search-form', array('options' => $arr) );

		$submit = $this->input->get(SRCH_QRY);
		$model = $this->input->get(SRCH_TABLE);

		// -- Explode $model with ':' to find subtable
		// -- Check for subtable input

		$this->load->model($model);

		if (!empty ($submit) && !empty ($model)){

			$result = $this->$model->find($submit); // ** Change to two parameter option if has subtable
			$html = $this->$model->makeTable($result); 

			$this->load->view('table_view', array('tablehtml'=>$html));
		}
		$this->load->view('footer');
	}

}
