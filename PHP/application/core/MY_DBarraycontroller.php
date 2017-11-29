<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class MY_DBarraycontroller extends CI_Controller {

	public $model = NULL;
	protected $userPermission = null;
	
	protected $filepath = ''; 
	
	public function __construct($file = __FILE__)
	{
		parent::__construct(); // do constructor for parent class
		$this->filepath = $file;

		$this->load->helper("csrf_helper");

		$this->load->model('permission_model');

		define('NAV_SELECT', 1);
	}

	public function index() {
		$this->load->view('header');
        
		$this->makeSelector();
		
		$this->load->view('footer');
	}

	protected function makeHTML($subtable)
	{
		$this->load->view('header');

		$this->load->view('table_view', ['url'=>current_url(), 'title'=>$this->model->ModelTitle.': '.$subtable, 'permission' => $this->getUserPermission()]);

		$this->makeSelector($subtable, site_url(str_replace('\\','/',$this->getAccessURL($this->filepath))) );

		if ($this->getUserPermission() >= PERMISSION_ALTER)
			$this->load->view('table_settings');
		
		$this->load->view('footer');
	}

	protected function permissionError() {
		show_error('The user doesn\'t have the permission to perform this action.', 403, 'Forbidden');
	}

	protected function getUserPermission() {
		if ($this->userPermission == null)
			$this->userPermission = $this->permission_model->userPermission($this->model->TableName);
		return $this->userPermission;
	}

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

	public function table() {
		$arguments = func_get_args();

		$subtable = isset($arguments[0])?$arguments[0]:null;
		$action = isset($arguments[1])?$arguments[1]:null;

		unset($arguments[0]);
		unset($arguments[1]);

		$arg0 = isset($arguments[2])?$arguments[2]:null;
		$arg1 = isset($arguments[3])?$arguments[3]:null;


		
		if ($subtable !== null) {
			$subtable = urldecode($subtable);
			if ($action === null) $this->makeHTML($subtable);
			else {
				switch ($action) {
					case 'add':
						if ($this->getUserPermission() >= PERMISSION_ADD)
							$this->add($subtable);
						else 
							$this->permissionError();
						break;
					case 'update':
						if ($arg0 !== null) {
							if ($this->getUserPermission() >= PERMISSION_CHANGE)
								$this->update($subtable, $arg0);
							else 
								$this->permissionError();
						}
						else show_404();
						break;
					case 'remove':
						if ($arg0 !== null) {
							if ($this->getUserPermission() >= PERMISSION_CHANGE)
								$this->remove($subtable, $arg0);
							else 
								$this->permissionError();
						}
						else show_404();
						break;
					case 'data':
						$this->data($subtable, $arg0, $arg1);
						break;
					
					default:
						if (!method_exists($this, $action) )
						{
							show_404();
							return;
						}
						return call_user_func_array( array($this, $action), $arguments);
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

	// Functions same with source

	protected function filters ($action = null, $id = null) {

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

	protected function export() {

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

	

	protected function editor($id = null) {
		
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

	protected function hide() {
		if ($this->getUserPermission() < PERMISSION_ALTER) {
			show_404();
			return;
		}

		$set = $this->input->get('set');
		$set = $set == 1;

		$this->model->setPrivate($set);
	}
	
	protected function rows() {

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
				$success = $this->model->deleteWithPK($rows);
				break;
			
			default:
				show_404();
				return;
				break;
		}
		csrf_json_response(
			[	'success' => $success ]);
	}

	protected function addfield () {

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

	protected function removefield () {

		if ($this->getUserPermission() < PERMISSION_ALTER) {
			show_404();
			return;	
		}

		$key = $this->input->post('header');

		$success = $this->model->removeField($key);

		csrf_json_response(
			[ 'success' => $success ]);

		
	}

	protected function headers () {

		if ($this->getUserPermission() < PERMISSION_PUBLIC) {
			show_404();
			return;	
		}

		csrf_json_response(
			[ 'id'=>$this->model->TablePrimaryKey,
				'headers'=>$this->model->getFieldAssociations()]);
	}

	protected function get ($id = null) {
		if ($this->getUserPermission() < PERMISSION_PUBLIC) {
			show_404();
			return;	
		}
		
		csrf_json_response([
    		'data'=>$this->model->getByPK($id)
		]);
	}

	// Functions different from source

	public function addcategory()
	{
		$success = false;
		$name = $this->input->post('title');

		if (!empty($name))
			$success = $this->model->registerCategoryTable($name);
		echo json_encode( 
			array(
				'csrf' => $token,
				'csrf_hash' => $hash,
				'success' => $success
			)

		, JSON_NUMERIC_CHECK);
	}

	public function removecategory()
	{
		$success = false;
		$name = $this->input->post('title');

		if (!empty($name))
			$success = $this->model->unregisterCategoryTable($name);
		echo json_encode( 
			array(
				'csrf' => $token,
				'csrf_hash' => $hash,
				'success' => $success
			)
		, JSON_NUMERIC_CHECK);
	}

	// Functions similar to source

	protected function add ($subtable) {

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
		
		$success = $this->model->insertIntoCategoryTable($subtable, $arr);

		csrf_json_response(
			[	'success' => $success ]);
	}

	public function update ($subtable, $id = null) {

		if ($this->getUserPermission() < PERMISSION_CHANGE) {
			show_404();
			return;
		}

		$insert = json_decode($this->input->post('data'), true);
    	if ( $this->model->updateOnCategoryTable($subtable, $id, $insert) )
    		$this->get($id);
    	else
    		csrf_json_response(
			[ 'success' => false ]);
	}

	public function remove ($subtable, $id = null) {

		if ($this->getUserPermission() < PERMISSION_CHANGE) {
			show_404();
			return;
		}

		$success = $this->model->deleteFromCategoryTable($subtable, $id);

		csrf_json_response(
			[ 'success' => $success ]);
		
	}

	public function data ($table) {

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
			$qry = $this->model->find($table, $query, $settings, $headers);
		} else {
			$qry = $this->model->find($table, null, $settings, $headers);
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
