<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcf extends MY_Archarraycontroller {

	public function __construct()
	{
		parent::__construct();
		$this->filepath = __FILE__;
		$this->model = new MY_Archpcfmodel();
	}
	
	protected function switchModel($subtable){
        $modelName = $this->model->getModel($subtable);	
        $this->load->model($modelName);
        return $this->$modelName;
    }
	protected function makeHTML($subtable)
	{		
		$this->load->view('header');

		$this->load->view('arch_table', ['url'=>current_url(), 'title'=>$this->model->ModelTitle, 'permission' => $this->getUserPermission()]);
		
		$this->makeSelector($subtable, site_url(str_replace('\\','/',$this->getAccessURL($this->filepath))) );
		
		if ($this->getUserPermission() >= PERMISSION_ALTER)
			$this->load->view('table_settings');
		
		$this->load->view('footer');
	}
	
	public function table($subtable = null, $action = null, $arg0 = null, $arg1 = 0) {
		if($subtable!=null){
			$subtable = urldecode($subtable);
			$this->model = $this->switchModel($subtable);
		}
		parent::table($subtable,$action,$arg0,$arg1);
	}
	
	protected function add($subtable) {
		
		$this->model = $this->switchModel($subtable);
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

}
