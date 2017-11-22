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

		$this->load->view('table_view', ['url'=>current_url(), 'title'=>$this->model->ModelTitle, 'permission' => $this->getUserPermission()]);

		$html = '<md-button href="'.base_url().'database/pcfreport/UnreplenishedPCF/'.urlencode($subtable).'" class="md-primary md-raised" >
					View Unreplenished Funds >>>>
                </md-button>';
		$this->load->view('html',array('html'=>$html));
		
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

}
