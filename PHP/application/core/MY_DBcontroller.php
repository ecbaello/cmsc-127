<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class MY_DBcontroller extends CI_Controller
{

	protected $model = null;
	protected $userPermission = null;

	public function __construct($model = null)
	{
		parent::__construct(); // do constructor for parent class

		$this->load->library('session');
		$this->load->helper('url');

		$this->load->model('permission_model');

		if (!empty($model)) {
			$this->model = $model;
		}

		defined('NAV_SELECT') or define('NAV_SELECT', 1);
	}

	public function index() {
		$this->makeHTML();
	}

	protected function getAccessURL($file_url) {
		return preg_replace('/\\.[^.\\s]{2,4}$/', '', str_replace(APPPATH.'controllers/', '', $file_url));
	}

	protected function loggedIn() {
		return $this->permission_model->ion_auth->logged_in();
	}

	protected function getUser() {
		return $this->permission_model->ion_auth->user()->row()->id;
	}

	protected function getUserPermission() {
		return $this->permission_model->userPermission($this->model->TableName);
	}

	protected function makeHTML() {
		$this->load->view('header');

		$this->makeTableHTML();

		if ($this->getUserPermission() >= PERMISSION_ALTER)
			$this->load->view('table_settings');

		$this->load->view('footer');
	}

	protected function permissionError() {
		show_error('The user doesn\'t have the permission to perform this action.', 403, 'Forbidden');
	}

	public function makeTableHTML()
	{
		$this->load->view('table_view', ['title' => $this->model->ModelTitle, 'permission' => $this->getUserPermission()]);
	}


	public function get ($id) {
		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();
    	echo json_encode( 
    	[
    		'data'=>$this->model->getByPK($id),
    		'csrf' => $token,
			'csrf_hash' => $hash
		], JSON_NUMERIC_CHECK);
	}

	public function filters ($action = null, $id = null) {
		if (!$this->loggedIn()) {
			show_404();
			return;
		}

		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();

		$return = [
			'csrf' => $token,
			'csrf_hash' => $hash
		];

		if ($action == 'add') {
			$title = $this->input->post('title');
			$data = $this->input->post('data');
			if ( !empty($title) && !empty($data) )
				$this->model->saveSearch( $title, $data, $this->getUser());
		} else if ($action == 'remove') {
			if ( !empty($id) )
				$this->model->removeSearch( $id, $this->getUser());
		} else if ($action == 'update') {
			$data = $this->input->post('data');
			if ( !empty($id) && !empty($data) )
				$this->model->updateSearch( $id, $this->getUser(), $data);
		} else {
			$return['data'] = $this->model->getSearches( $this->getUser() );
		}
		echo json_encode (
			$return,
			JSON_NUMERIC_CHECK
		);
	}

	public function export() {
		if (!$this->permission_model->adminAllow()) return;

		$this->load->helper('download');

		$name = $this->model->ModelTitle.' - '.date("D M d, Y").'(exported).csv';
		$data = $this->model->getAsCSV();

		force_download($name, $data, true);
	}

	public function update ($id) {

		if ($this->getUserPermission() < PERMISSION_CHANGE) {
			show_404();
			return;
		}

		$insert = json_decode($this->input->post('data'), true);
    	$this->model->updateWithPK($id, $insert);
    	$this->get($id);
	}

	public function remove ($id) {

		if ($this->getUserPermission() >= PERMISSION_CHANGE) {
			$token = $this->security->get_csrf_token_name();
			$hash = $this->security->get_csrf_hash();

			$this->model->deleteWithPK($id);

			echo json_encode(['success'=>true,'csrf' => $token,
					'csrf_hash' => $hash], JSON_NUMERIC_CHECK);
		} else $this->permissionError();
		
	}

	public function add () {

		if ($this->getUserPermission() < PERMISSION_ADD) {
			show_404();
			return;
		}

		$insert = json_decode($this->input->post('data'), true);

		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();

		$inputs = $this->model->getFields();
		$arr = array();
		foreach ($inputs as $input) {
			if (isset($insert[$input])) {
				$arr[$input] = $insert[$input]; 
			}
		}
		
		$success = $this->model->insertIntoTable($arr);

		echo json_encode( 
			array(
				'csrf' => $token,
				'csrf_hash' => $hash,
				'success' => $success
			)

		, JSON_NUMERIC_CHECK);
		
		
	}

	public function addfield () {

		if ($this->getUserPermission() < PERMISSION_ALTER) {
			show_404();
			return;
		}

		$data = json_decode($this->input->post('data'), true);

		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();

		$prefix = null;
		$suffix = null;
		$required = false;

		if (isset($data['prefix'])) $prefix = $data['prefix'];
		if (isset($data['suffix'])) $suffix = $data['suffix'];
		if (isset($data['required'])) $required = $data['required'];

		$success = false;
		if ($data['derived']) {
			$success = $this->model->insertDerivedField($data['title'], $data['expression'], $prefix, $suffix, $required);
		} else {
			$success = $this->model->insertField($data['title'], $data['kind'], $data['default'], $prefix, $suffix);
		}

		echo json_encode( 
			array(
				'csrf' => $token,
				'csrf_hash' => $hash,
				'success' => $success
			)

		, JSON_NUMERIC_CHECK);

		
	}

	public function removefield () {

		if ($this->getUserPermission() < PERMISSION_ALTER) {
			show_404();
			return;	
		}

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

	public function headers () {
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

	public function data () {
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

		$limit = $this->input->get('limit');
		
		if (!empty($limit)) {
			$settings['limit_by'] = $limit;
			$page = $this->input->get('page');
			$settings['limit_offset'] = ( empty($page) ? 0 : $page )*$limit;
		}

		$headers =  $this->model->getFieldAssociations();

		$filter = $this->input->post('filter');
		if (!empty($filter)) {
			$query = json_decode($filter, true);
			$qry = $this->model->find($query, $settings, $headers);
		} else {
			$qry = $this->model->find(null, $settings, $headers);
		}

		$response = 
		[
			'data'=> $qry ? $qry->result() : '',
			'csrf' => $token,
			'csrf_hash' => $hash,
			'count' => $this->model->lastFindCount,
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
}

?>