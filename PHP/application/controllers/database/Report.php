<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends CI_Controller {


	private $model= null;
	
	public function __construct(){
		parent::__construct();
		$this->load->model('report_model');
		$this->load->model('permission_model');
	}
	
    protected function makeHeader(){
        $this->load->view('html',array("html"=>'<script src="'.base_url().'js/controllers/report.js"></script>'));
    }

    public function index(){

        $this->load->view('header');
		$this->makeHeader();
		if(!empty($this->report_model->getModelNames())){
			$this->load->view('graph', array('url'=>site_url(str_replace('\\','/',$this->getAccessURL(__FILE__))) ));
			
			$this->makeModelSelector();
		}else{
			$this->load->view('html',array("html"=>'<md-card><md-content layout-padding>No Tables Found</md-content></md-card>'));
		}
        $this->load->view('footer');
    }

	protected function makeHTML($modelName){
		$this->load->view('header');
		$this->makeHeader();
		
		$this->load->view('html',array('html'=>"<md-content layout-padding><h2>Financial Report for ".$modelName."</h2></md-content>"));
		
		$this->makeModelSelector($modelName);
		
		$this->load->view('reports',array('url'=>site_url(str_replace('\\','/',$this->getAccessURL(__FILE__))),'current_model'=>$modelName ));

        $this->load->view('footer');
	}
	
	public function table($modelName = null,$action=null,$fromDate=null,$toDate=null){
		if($modelName != null){
			$modelName = urldecode($modelName);
			switch($action){
				case null:
					$this->makeHTML($modelName);
					break;
				case 'reports':
					$input = $this->input->get('data');
					if($input!==null){
						$this->getReports($modelName,$input);
					}else{
						$this->getReports($modelName);
					}
					break;
				case 'custom':
					$this->getCustomReport($modelName,$fromDate,$toDate);
					break;
				case 'fields':
					$input = $this->input->get('data');
					if($input===null){
						echo json_encode($this->report_model->getNumericalFields($modelName));
					}else{
						$this->changeFields($modelName,$input);
					}
					break;
				default:
					show_404();
			}
		}else{
			//echo json_encode($this->report_model->getNumer));
		}
	}

	public function changeFields($modelName,$data){
		if(!$this->permission_model->adminAllow()) show_404();
		foreach($data as $value){
			$this->report_model->changeFieldOption($modelName,$value['field'],$value['option']);
		}
		echo json_encode($this->report_model->getNumericalFields($modelName));
	}
	
	public function getMonthlyExpenses($year){

        $expenses = array();
		
        $categories = $this->getModelNames();
		ob_clean();
		
        foreach($categories as $model) {
            for ($i = 1; $i <= 12; $i++) {
                $expenses[$model][date("F", mktime(0, 0, 0, $i, 10))] = $this->report_model->getExpenseTotal($model,date($year .'-'. $i . '-01'), date($year . '-'.$i . '-t'));
            }
        }
        echo json_encode($expenses);

        return $expenses;
    }

	
	protected function makeModelSelector($current_tbl = null){
		
		$options = array(
			'url'=>site_url(str_replace('\\','/',$this->getAccessURL(__FILE__)))
		);
		
		if($current_tbl != null){
			$current_tbl = urldecode($current_tbl);
			$options['current_tbl'] = $current_tbl;
		}

		$this->load->view('table_selector',$options);
	}
	
	public function getModelNames(){
		$models = $this->report_model->getModelNames();
		echo json_encode($models);
		return $models;
	}

	protected function getAccessURL($file_url) {
		return preg_replace('/\\.[^.\\s]{3,4}$/', '', str_replace(APPPATH.'controllers'.DIRECTORY_SEPARATOR, '', $file_url));
	}
	
	public function getReports($modelName){
		$modelName = urldecode($modelName);
		$table = array();
		
		$headers = array('Field','All Time','Annual','Quarterly', 'Monthly');
		array_push($table,$headers);
		
		/** Getting the Current Quarter **/
		$current_month = date('m');

		if($current_month>=1 && $current_month<=3)
		{
		$start_date = strtotime('first day of January');
		$end_date = strtotime('last day of March');
		}
		else  if($current_month>=4 && $current_month<=6)
		{
		$start_date = strtotime('first day of April');
		$end_date = strtotime('last day of June');
		}
		else  if($current_month>=7 && $current_month<=9)
		{
		$start_date = strtotime('first day of July');
		$end_date = strtotime('last day of September');
		}
		else  if($current_month>=10 && $current_month<=12)
		{
		$start_date = strtotime('first day of October');
		$end_date = strtotime('last day of December'); 
		}
		/** **/
		
		foreach($this->report_model->getNumericalFields($modelName) as $field){
			$body = array();
			array_push($body,$field['name']);
			array_push($body,round($this->report_model->getExpenseField($modelName,null,null,$field['field']),2));
			array_push($body,round($this->report_model->getExpenseField($modelName,date('Y'),date('Y-m-t',strtotime('Dec 31')),$field['field']),2)); //Anual
			array_push($body,round($this->report_model->getExpenseField($modelName,date('Y-m-d',$start_date),date('Y-m-d',$end_date),$field['field']),2)); //Quarterly
			array_push($body,round($this->report_model->getExpenseField($modelName,date('Y-m-01'),date('Y-m-t'),$field['field']),2)); //Monthly
			array_push($table,$body);		
		}
		
		$footer=array(
			'Overall',
			round($this->report_model->getExpenseTotal($modelName,null,null),2),
			round($this->report_model->getExpenseTotal($modelName,date('Y'),date('Y-m-t',strtotime('Dec 31'))),2),
			round($this->report_model->getExpenseTotal($modelName,date('Y-m-d',$start_date),date('Y-m-d',$end_date)),2),
			round($this->report_model->getExpenseTotal($modelName,date('Y-m-01'),date('Y-m-t')),2)
		);
		array_push($table,$footer);
		
		echo json_encode($table);
		return $table;
		
		
	}
	
	public function getCustomReport($modelName,$fromDate,$toDate){
        $table = array();

		/** Header Names **/
        $fields = $this->report_model->getFields($modelName);

        $headers = array();
        foreach($fields as $field) {
            $headers[$field['field']] = $field['title'];
        }
        $headers['total']='Total';
        array_push($table,$headers);
		/** **/
		
		
		/** Table Body **/
		$result = $this->report_model->getCustomExpenseTable($modelName,$fromDate,$toDate);
		foreach($result as $r){
			if(isset($r['total']))
				$r['total'] = round($r['total'],2);
            array_push($table,$r);
        }
		
		/** **/

        echo json_encode($table);
		return $table;
		
	}


}
