<?php

class MY_DBarraycontroller extends CI_Controller {

	public $model = NULL;

	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		define('NAV_SELECT', 1);
	}

	public function index() {
		$this->load->view('header');

		$this->makeSelector();
		
		$this->load->view('footer');
	}

	protected function getAccessURL($file_url) {
		return preg_replace('/\\.[^.\\s]{3,4}$/', '', str_replace(APPPATH.'controllers/', '', $file_url));
	}

	protected function makeSelector($table = null, $replacelink = null) {
		$settings = array();

		if (!empty($table))
			$settings['current_tbl'] = $this->model->convertNameToCategory($table);

		if (!empty($replacelink)) 
			$settings['url'] = $replacelink;
		
		$this->load->view('model_selector', $settings);
	}

	// next up: use id variable for pagination
	public function table($subtable = null, $action = null, $id = null, $other = null) {
		
		if ($subtable !== null) {
			$subtable = urldecode($subtable);
			if ($action === null) $this->makeHTML($subtable);
			else {
				switch ($action) {
					case 'add':
						$this->add($subtable);
						break;
					case 'get':
						if ($id !== null) $this->get($subtable, $id);
						else show_404();
						break;
					case 'update':
						if ($id !== null) $this->update($subtable, $id);
						else show_404();
						break;
					case 'remove':
						if ($id !== null) $this->remove($subtable, $id);
						else show_404();
						break;
					case 'data':
						$this->data($subtable);
						break;
					case 'search':
						$this->search($subtable);
						break;
					case 'addfield';
						$this->addfield();
						break;
					case 'removefield';
						$this->removefield();
						break;
					case 'headers';
						$this->headers();
						break;
					
					default:
						show_404();
						break;
				}


			}
		} else {
			$array = array();
			foreach ($this->model->getCategories() as $key => $value) {
				$arr = array();
				$arr['title'] = $value;
				$arr['link'] = urlencode($value);
				$array[$key] = $arr;
			}
			echo json_encode(['data'=>$array]);
		}
	}

	protected function headers () {
		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();
		echo json_encode( 
			[
				'id'=>$this->model->TablePrimaryKey,
				'headers'=>$this->model->getFieldAssociations(),
	    		'csrf' => $token,
				'csrf_hash' => $hash
			]
		);
	}

	protected function addfield () {
		$data = json_decode($this->input->post('data'), true);

		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();



		$success = false;
		if ($data['derived']) {
			$success = $this->model->insertDerivedField($data['title'], $data['expression']);
		} else {
			$success = $this->model->insertField($data['title'], $data['kind'], $data['default']);
		}

		echo json_encode( 
			array(
				'csrf' => $token,
				'csrf_hash' => $hash,
				'success' => $success
			)

		, JSON_NUMERIC_CHECK);
	}

	protected function removefield () {
		$key = $this->input->post('header');

		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();

		$success = $this->model->removeField($key);

		echo json_encode( 
			array(
				'csrf' => $token,
				'csrf_hash' => $hash,
				'success' => $success
			)

		, JSON_NUMERIC_CHECK);
	}

	protected function makeHTML($subtable)
	{
		$this->load->view('header');
		
		$this->makeSelector($subtable, site_url($this->uri->segment(2)));
		
		$this->load->view('table_view', ['url'=>current_url(), 'title'=>$this->model->ModelTitle]);

		$this->load->view('footer');
	}

	protected function data($table) {

		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();
		$qry = $this->model->getCategoryTable($table);
		echo json_encode( 
			array(
				'id'=>$this->model->TablePrimaryKey,
				'headers'=>$this->model->getFieldAssociations(),
				'data'=>$qry->result(),
				'csrf' => $token,
				'csrf_hash' => $hash,
				'count' => $qry->num_rows()
			)
		, JSON_NUMERIC_CHECK);
	}

	public function search ($table) {
		$query = json_decode($this->input->post('data'), true);

		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();
		$qry = $this->model->find($query, $table);

		echo json_encode( 
			array(
				'id'=>$this->model->TablePrimaryKey,
				'headers'=>$this->model->getFieldAssociations(),
				'data'=> $qry->result(),
				'csrf' => $token,
				'csrf_hash' => $hash,
				'count' => $qry->num_rows()
			)

		, JSON_NUMERIC_CHECK);
	}

	protected function add($subtable) {
		$insert = json_decode($this->input->post('data'), true);

		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();

		if (!empty($insert)) {
			$inputs = $this->model->getFields();
			$arr = array();
			foreach ($inputs as $input) {
				if (isset($insert[$input])) {
					$arr[$input] = $insert[$input]; 
				}
			}
			$success = $this->model->insertIntoCategoryTable($subtable, $arr);
		} else 
			$success = false;

		echo json_encode( 
			array(
				'csrf' => $token,
				'csrf_hash' => $hash,
				'success' => $success
			)

		, JSON_NUMERIC_CHECK);
	}

	protected function get($subtable, $id) {
		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();
    	echo json_encode( [
    		'data'=>$this->model->getByPK($id),
    		'csrf' => $token,
			'csrf_hash' => $hash
		], JSON_NUMERIC_CHECK);
	}

	protected function remove($subtable, $id) {
		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();

		$this->model->deleteFromCategoryTable($subtable, $id);

		echo json_encode(['success'=>true,'csrf' => $token,
				'csrf_hash' => $hash], JSON_NUMERIC_CHECK);
	}

	protected function update($subtable, $id) {
		$insert = json_decode($this->input->post('data'), true);

    	$this->model->updateOnCategoryTable($subtable, $id, $insert);

    	$this->get($subtable, $id);
	}
}