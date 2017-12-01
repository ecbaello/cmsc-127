<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcf extends MY_DBarraycontroller {

	public function __construct()
	{
		parent::__construct();
		$this->filepath = __FILE__;
		$this->model = new MY_DBpcfmodel();
	}
	
	protected function makeHTML($subtable)
	{		
		$this->load->view('header');

		$this->makeSelector($subtable, site_url(str_replace('\\','/',$this->getAccessURL($this->filepath))), false);
		
		$this->load->view('table_view', ['url'=>current_url(), 'title'=>$this->model->ModelTitle, 'permission' => $this->getUserPermission()]);

		$html = '<md-button href="'.base_url().'database/pcfreport/UnreplenishedPCF/'.urlencode($subtable).'" class="md-primary md-raised" >
					View Unreplenished Funds >>>>
                </md-button>';
		$this->load->view('html',array('html'=>$html));

		if ($this->getUserPermission() >= PERMISSION_ALTER)
			$this->load->view('table_settings');
		
		$this->load->view('footer');
	}
	
	public function table($subtable = null, $action = null, $arg0 = null, $arg1 = 0) {
		if($subtable!=null){
			$subtable = urldecode($subtable);
			$this->model = $this->model->getModel($subtable);
		}
		parent::table($subtable,$action,$arg0,$arg1);
	}
	
	protected function add($subtable) {
		
		$this->model = $this->model->getModel($subtable);
		$insert = json_decode($this->input->post('data'), true);
		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();
		$errorMsg = '';
		
		if (!empty($insert)) {
			$inputs = $this->model->getFields();
			$arr = array();
			foreach ($inputs as $input) {
				if (isset($insert[$input])) {
					$arr[$input] = $insert[$input]; 
				}
			}
			
			if($this->model->checkValidExpense($subtable,$arr)){
				$success = $this->model->insertIntoCategoryTable($subtable, $arr);
			}else{
				$errorMsg = 'Expense total exceeds alotted fund';
				$success = false;
			}
			
		} else 
			$success = false;

		echo json_encode( 
			array(
				'csrf' => $token,
				'csrf_hash' => $hash,
				'success' => $success,
				'error_message'=>$errorMsg
			)

		, JSON_NUMERIC_CHECK);
	}
	
	public function addcategory()
	{
		if ($this->getUserPermission() < PERMISSION_ALTER) {
			show_404();
			return;
		}

		$success = false;
		$name = $this->input->post('title');

		if (!empty($name))
			$success = $this->model->registerCategoryTable($name);

		csrf_json_response([
    		'success' => $success,
			'error_message'=>'Action Not Allowed'
		]);
	}

	public function removecategory()
	{
		if ($this->getUserPermission() < PERMISSION_ALTER) {
			show_404();
			return;
		}

		$success = false;
		$name = $this->input->post('title');

		if (!empty($name))
			$success = $this->model->unregisterCategoryTable($name);
		
		csrf_json_response([
    		'success' => $success,
			'error_message'=>'Action Not Allowed'
		]);
	}

	public function renamecategory()
	{
		if ($this->getUserPermission() < PERMISSION_ALTER) {
			show_404();
			return;
		}

		$success = false;
		$name = $this->input->post('name');
		$title = $this->input->post('title');

		if (!empty($name))
			$success = $this->model->renameCategory($title, $name);

		csrf_json_response([
    		'success' => $success,
    		'error_message'=>'Action Not Allowed'
		]);
	}

}
