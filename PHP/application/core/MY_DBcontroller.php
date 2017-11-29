<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class MY_DBcontroller extends CI_Controller
{

	protected $model = null;
	protected $userPermission = null;

	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->helper("csrf_helper");

		$this->load->model('permission_model');

		defined('NAV_SELECT') or define('NAV_SELECT', 1);
	}

	public function _loadCustom ($ModelTitle, $TableName, $FieldPrefix) {
		$this->load->model('custom_model');
		$this->model = $this->custom_model;
		return $this->model->loadCustom($ModelTitle, $TableName, $FieldPrefix);
	}

	public function _useModel ($model) {
		$this->model = $model;
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

	// Functions same with array

	public function filters ($action = null, $id = null) {

		if (!$this->loggedIn()) {
			show_404();
			return;
		}

		$return = [
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
		csrf_json_response($return);
	}

	public function export() {

		$permission = $this->getUserPermission();

		if ($permission < PERMISSION_CHANGE) {
			show_404();
			return;
		}

		$rows = $this->input->get('rows');

		if (!empty($rows)) $rows = json_decode($rows);
		else if ($permission < PERMISSION_ALTER) {
			show_404();
			return;
		}

		$this->load->helper('download');

		$name = $this->model->ModelTitle.' - '.date("D M d, Y").'('.(empty($rows)?'':'partial, ').'exported).csv';
		$data = $this->model->getAsCSV($rows);

		force_download($name, $data, true);
	}

	

	public function editor($id = null) {
		
		if ($id == null
			|| $this->getUserPermission() < PERMISSION_CHANGE) {
			show_404();
			return;
		}

		$action = $this->input->get('action');

		if ($action == null) {
			$data = $this->model->getByPK($id);

			$this->load->view('header');
			// load editor ui w/ data
			$this->load->view('footer');
		} else {
			$data = [];
			switch ($action) {
				case 'update':
					$this->update($id);
					break;

				case 'remove':
					$this->remove($id);
					break;
				
				default:
					show_404();
					break;
			}
		}
	}

	public function privacy() {
		$set = $this->input->post('private');
		if ($set !== null) {
			if ($this->getUserPermission() < PERMISSION_ALTER) {
				show_404();
				return;
			}

			$set = $set == 1;

			csrf_json_response(
				[ 'success' => $this->model->setPrivate($set) ]);
		} else {
			csrf_json_response(
				[ 'private' => $this->model->isPrivate() ]);
		}
	}
	
	public function rows() {

		if ($this->getUserPermission() < PERMISSION_CHANGE) {
			show_404();
			return;
		}

		$action = $this->input->get('action');
		$rows = json_decode($this->input->post('rows'), true);

		$success = false;

		$this->load->helper('download');

		switch ($action) {
			case 'remove':
				$this->model->deleteWithPK($rows);
				break;
			
			default:
				show_404();
				return;
				break;
		}
		csrf_json_response(
			[	'success' => $success ]);
	}

	public function addfield () {

		if ($this->getUserPermission() < PERMISSION_ALTER) {
			show_404();
			return;
		}

		$data = json_decode($this->input->post('data'), true);

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

		csrf_json_response(
			[	'success' => $success ]);
		
	}

	public function removefield () {

		if ($this->getUserPermission() < PERMISSION_ALTER) {
			show_404();
			return;	
		}

		$key = $this->input->post('header');

		$success = $this->model->removeField($key);

		csrf_json_response(
			[ 'success' => $success ]);

	}

	public function headers () {

		if ($this->getUserPermission() < PERMISSION_PUBLIC) {
			show_404();
			return;	
		}

		csrf_json_response(
			[ 'id'=>$this->model->TablePrimaryKey,
				'headers'=>$this->model->getFieldAssociations()]);
	}



	public function get ($id = null) {
		if ($this->getUserPermission() < PERMISSION_PUBLIC) {
			show_404();
			return;	
		}
		
		csrf_json_response([
    		'data'=>$this->model->getByPK($id)
		]);
	}

	// Functions different from array

	public function add () {

		if ($this->getUserPermission() < PERMISSION_ADD) {
			show_404();
			return;
		}

		$insert = json_decode($this->input->post('data'), true);

		$inputs = $this->model->getFields();
		$arr = array();
		foreach ($inputs as $input) {
			if (isset($insert[$input])) {
				$arr[$input] = $insert[$input]; 
			}
		}
		
		$success = $this->model->insertIntoTable($arr);

		csrf_json_response(
			[	'success' => $success ]);
	}

	public function update ($id = null) {

		if ($this->getUserPermission() < PERMISSION_CHANGE) {
			show_404();
			return;
		}

		$insert = json_decode($this->input->post('data'), true);
    	if ($this->model->updateWithPK($id, $insert))
    		$this->get($id);
    	else
    		csrf_json_response(
			[ 'success' => false ]);
	}

	public function remove ($id = null) {

		if ($this->getUserPermission() < PERMISSION_CHANGE) {
			show_404();
			return;
		}

		$success = $this->model->deleteWithPK($id);

		csrf_json_response(
			[ 'success' => $success ]);
		
	}

	public function data () {

		if ($this->getUserPermission() < PERMISSION_PUBLIC) {
			show_404();
			return;	
		}

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
			'count' => $this->model->lastFindCount,
			'success' => !empty($qry)
		];

		if ($this->input->get('headers') == 1) {
			$response['headers'] = $headers;
			$response['id'] = $this->model->TablePrimaryKey;
		}

		csrf_json_response($response);
	}
}

?>