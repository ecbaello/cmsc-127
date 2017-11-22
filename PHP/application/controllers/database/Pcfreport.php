<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcfreport extends MY_DBarraycontroller {

    public function __construct()
    {
        parent::__construct();
		$this->filepath = __FILE__;

        $this->load->model('database_pcf_model');
        $this->model = $this->database_pcf_model;
		$this->model->init();

		$this->model = new MY_DBarraymodel();
    }

    protected function makeHeader(){
        $this->load->view('html',array("html"=>'<script src="'.base_url().'js/controllers/report.js"></script>'));
        $this->load->view('html',array('html'=>"<md-content layout-padding><h2>Petty Cash Fund Report</h2></md-content>"));
    }

    public function index(){

        $this->load->view('header');
		$this->load->view('html',array("html"=>'<script src="'.base_url().'js/controllers/report.js"></script>'));
        $this->load->view('html',array('html'=>"<md-content layout-padding><h2>Petty Cash Fund Report</h2></md-content>"));

        $this->load->view('graph');
		
		$this->load->view('reports',array('url'=>site_url(str_replace('\\','/',$this->getAccessURL(__FILE__))) ));
        $this->makeSelector();

        $this->load->view('footer');
    }

    protected function makeHTML($subtable){

        $this->load->view('header');
		$this->load->view('html',array("html"=>'<script src="'.base_url().'js/controllers/report.js"></script>'));
        $this->load->view('html',array('html'=>"<md-content layout-padding><h2>Petty Cash Fund Report</h2></md-content>"));

        $this->load->view('graph', array('url'=>site_url(str_replace('\\','/',$this->getAccessURL(__FILE__))) ));
		$this->load->view('reports',array('url'=>site_url(str_replace('\\','/',$this->getAccessURL(__FILE__))) ));
        $this->makeSelector($subtable, site_url(str_replace('\\','/',$this->getAccessURL(__FILE__))) );
        $this->makeDateSelector($subtable, site_url(str_replace('\\','/',$this->getAccessURL(__FILE__))));

        $this->load->view('footer');


    }

    public function administrate($subtable,$action = null,$data = null){

        $subtable = urldecode($subtable);

        $this->model = $this->switchModel($subtable);

        if($action != null){
			if(!$this->permission_model->adminAllow()) {
				$this->permissionError();
				return null;
			}
            switch($action){
                case 'fund':
                    echo $this->model->changeAllottedFund($subtable,$data);
                    break;
                case 'threshold':
                    echo $this->model->changeExpenseThreshold($subtable,$data);
                    break;
                case 'replenish':
                    echo $this->model->replenish($this->model->TableName);
                    break;

                default:
                    show_404();
                    break;
            }
        }else{
            $data = $this->model->getFieldsFromTypeTable($subtable,array($this->model->afFieldName,$this->model->etFieldName));
            $table = array();

            $table['Allotted Fund'] = $data[$this->model->afFieldName];
            $table['Expense Threshold'] = $data[$this->model->etFieldName];

            $grandtotal = $this->getExpenseTable($subtable,1);
            try {
                $grandtotal = end($grandtotal);
                $grandtotal = array_pop($grandtotal);
            }catch(Exception $e){
                return null;
            }

            $table['Expense Total'] = $grandtotal;
            $table['Cash On Hand'] = $data[$this->model->afFieldName]-$grandtotal;

            ob_end_clean();

            echo json_encode($table);
        }

    }

    public function UnreplenishedPCF($subtable){
        $this->load->view('header');
        $this->load->view('html',array("html"=>'<script src="'.base_url().'js/controllers/report.js"></script>'));

        $this->load->view('pcf_report', array('url'=>site_url(str_replace('\\','/',$this->getAccessURL(__FILE__))),'subtable'=>$subtable ));

        $this->load->view('footer');
    }
	
	public function getReports(){
		
		$categories = $this->model->getCategories();
		$table = array();
		
		$headers = array('Category','Annual','Quarterly', 'Monthly');
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

		
		foreach($categories as $category){
			$body = array();
			array_push($body,$category);
			
			array_push($body,round($this->getExpense($category,null,date('Y'),date('Y-m-t',strtotime('Dec 31'))),2)); //Anual
			array_push($body,round($this->getExpense($category,null,date('Y-m-d',$start_date),date('Y-m-d',$end_date)),2)); //Quarterly
			array_push($body,round($this->getExpense($category,null,date('Y-m-01'),date('Y-m-t')),2)); //Monthly
			
			array_push($table,$body);
		}
		
		echo json_encode($table);
		return $table;
		
		
	}

    protected function makeDateSelector($subtable,$url){

        /*$this->load->view('html',array(
            'html'=>'<div layout-padding>This Month: '.$this->getExpense($subtable,null,date('Y-m-01'),date('Y-m-t')).'<br/>
                    This Year: '.$this->getExpense($subtable,null,date('Y'),date('Y-m-t',strtotime('Dec 31'))).'</div>'
        ));*/

        $this->load->view('date_range_selector',array('subtable'=>$subtable,'url'=>$url));

    }

    public function getMonthlyExpenses(){

        $expenses = array();

        $categories = $this->model->getCategories();

        foreach($categories as $subtable) {
            for ($i = 1; $i <= 12; $i++) {

                $expenses[$subtable][date("F", mktime(0, 0, 0, $i, 10))] = $this->getExpense($subtable, null, date('Y-' . $i . '-01'), date('Y-' . $i . '-t'));

            }
        }
        echo json_encode($expenses);

        return $expenses;
    }

    protected function getNumericalFields($subtable){

        $this->model = $this->switchModel($subtable);

        $fields = $this->model->getFieldAssociations();
        $numerics = array();

        foreach($fields as $field => $attributes){
            if($attributes['type'] == 'FLOAT'){
                array_push($numerics,$field);
            }
        }

        return $numerics;
    }

    //Get total from date 1 to date 2
    public function getExpense($subtable,$action=null, $fromDate = null, $toDate=null){

        $pcfDateName = 'pcf_date';
        $pcfIdName = 'pcf_type';

        $subtable = $this->model->convertNameToCategory($subtable);
        $numerics = $this->getNumericalFields();


        $numericsQuery = array();
        foreach ($numerics as $field){
            array_push($numericsQuery ,'SUM('.$field.')');
        }

        $this->db->select(implode(" + ",$numericsQuery).' as total');
        $this->db->where($pcfDateName.' between "'.$fromDate.'" and "'.$toDate.'"');
        $this->db->where($pcfIdName,$subtable);

        $result= $this->db->get($this->model->TableName)->result_array();

        $total = $result[0]['total'];

        if($action !==null)
            echo json_encode(array('total'=>$total));

        return $total === null ? 0:$total;

    }

    public function getExpenseTable($subtable,$fromDate,$toDate){
		
        $pcfDateName = 'pcf_date';
        $pcfIdName = 'pcf_type';
		
		$subtable = urldecode($subtable);
        $subtable = $this->model->convertNameToCategory($subtable);
        $numerics = $this->getNumericalFields();
        $table = array();

        $fields = $this->model->getFields();
		$firstField = reset($fields);

        $headers = array();
        foreach($fields as $field) {
            $headers[$field] = $this->model->getFieldTitle($field);
        }
        $headers['total']='Total';
        array_push($table,$headers);


        $this->db->select(implode(' , ',$fields));
        $this->db->select('('.implode(" + ",$numerics).') as total');
        $this->db->where($pcfDateName.' between "'.$fromDate.'" and "'.$toDate.'"');
        $this->db->where($pcfIdName,$subtable);

        $result = $this->db->get($this->model->TableName)->result_array();
		
        foreach($result as $k=>$r){
			if(isset($r['total']))
				$r['total'] = round($r['total'],2);
            array_push($table,$r);
        }

        $summations = array();
        foreach ($numerics as $field){
            array_push($summations, 'SUM(' . $field . ') as "'.$field.'"');
        }

        $this->db->select(implode(" , ",$summations));
        $this->db->where($pcfDateName.' between "'.$fromDate.'" and "'.$toDate.'"');
        $this->db->where($pcfIdName,$subtable);
		//print_r($this->db->get_compiled_select($this->model->TableName));die();
        $result = $this->db->get($this->model->TableName)->result_array();

        $subtotals = array();
        $grandtotal = 0;
        if(sizeof($result) > 0) {
            foreach ($fields as $field) {
                if(in_array($field,$numerics)) {
                    $subtotals[$field] = round($result[0][$field],2);
                    $grandtotal +=  $result[0][$field];
                }else{
                    $subtotals[$field] = '';
                }
            }
            $subtotals['total'] = round($grandtotal,2);
			$subtotals[$firstField]='Sub-Totals';
        }

        array_push($table,$subtotals);

        echo json_encode($table);
		
		return $table;
        //print_r($table);die();

    }




}
