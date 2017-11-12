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

	// DIRECTORY_SEPARATOR
	protected function getAccessURL($file_url) {
		return preg_replace('/\\.[^.\\s]{3,4}$/', '', str_replace(APPPATH.'controllers'.DIRECTORY_SEPARATOR, '', $file_url));
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
	public function table($subtable = null, $action = null, $arg0 = null, $arg1 = 0) {
		
		if ($subtable !== null) {
			$subtable = urldecode($subtable);
			$this->model->hideFields($subtable);
			if ($action === null) $this->makeHTML($subtable);
			else {
				switch ($action) {
					case 'add':
						$this->add($subtable);
						break;
					case 'get':
						if ($arg0 !== null) $this->get($subtable, $arg0);
						else show_404();
						break;
					case 'update':
						if ($arg0 !== null) $this->update($subtable, $arg0);
						else show_404();
						break;
					case 'remove':
						if ($arg0 !== null) $this->remove($subtable, $arg0);
						else show_404();
						break;
					case 'data':
						$this->data($subtable, $arg0, $arg1);
						break;
					case 'filter':
						$this->filter($subtable);
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

		$qry = null;

		$settings = [];

		$orderby = $this->input->get('orderby');
		if (!empty($orderby)) {
			$settings['order_by'] = $orderby;
			$order = $this->input->get('order');
			if (!empty($order)) $settings['order_dir'] = $order;
		}

		if (!empty($limit)) {
			$settings['limit_by'] = $limit;
			$settings['limit_offset'] = $page*$limit;
		}

		$headers = $this->model->getFieldAssociations();

		$filter = $this->input->post('filter');
		if (!empty($filter)) {
			$query = json_decode($filter, true);
			$qry = $this->model->find($table, $query, $settings, $headers);
		} else {
			$qry = $this->model->find($table, null, $settings, $headers);
		}

		$response = 
		[
			'data'=> $qry ? $qry->result() : '',
			'csrf' => $token,
			'csrf_hash' => $hash,
			'count' => $qry ? $qry->num_rows() : -1,
			'success' => !empty($qry)
		];

		if ($this->input->get('headers') == 1) {
			$response['headers'] = $headers;
			$response['id'] = $this->model->TablePrimaryKey;
		}

		echo json_encode( 
			$response

		, JSON_NUMERIC_CHECK);
	}

	protected function filters ($action) {
		if ($action == 'add') {
			$this->model->saveSearch( $this->input->post('data') );
		} else if ($action == 'remove') {
			$this->model->saveSearch( $this->input->post('data') );
		}
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
