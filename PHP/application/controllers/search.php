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

		$this->load->view('search-form', array('options' => $arr, 'path' => current_url() ) );

		$submit = $this->input->post(SRCH_QRY);
		$model = $this->input->post(SRCH_TABLE);
		
		// -- Explode $model with ':' to find subtable
		// -- Check for subtable input

		$model = explode(':', $model);
		$main = $model[0];


		$this->load->model($main);

		if (!empty ($submit) && !empty ($main)){

			$result = '';

			if (count($model) < 2) $result = $this->$main->find($submit);
			else $result = $this->$main->find($submit, $model[1]);

			$html = $this->$main->makeTable($result); 

			$count = $result->num_rows();

			$this->load->view('html', array('html'=>'<div class="result-count">Displaying '.$count.' result'.($count>1?'s':'').'.</div>'));

			$this->load->view('table_view', array('tablehtml'=>$html));
		}
		$this->load->view('footer');
	}

}
