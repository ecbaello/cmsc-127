<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcf extends MY_DBarraycontroller {

	public function __construct()
	{
		parent::__construct();
		$this->filepath = __FILE__;
		$this->model = new MY_DBpcfmodel();
	}
	
	protected function switchModel($subtable){
        $modelName = $this->model->getModel($subtable);	
        $this->load->model($modelName);
        return $this->$modelName;
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

	protected function makeSelector($table = null, $replacelink = null, $modifiable = false) {
		$permission = $this->getUserPermission();

		$settings = ['permission' => -1, 'show_category' => $modifiable, 'title' => 'Petty Cash Fund'];

		if (!empty($table))
			$settings['current_tbl'] = $this->model->convertNameToCategory($table);

		if (!empty($replacelink)) 
			$settings['url'] = $replacelink;
		
		$this->load->view('model_selector', $settings);
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
