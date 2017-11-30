<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcfreport extends MY_DBarraycontroller {

    public function __construct()
    {
        parent::__construct();
		$this->filepath = __FILE__;
		$this->model = new MY_DBpcfmodel();
    }

    protected function makeHeader(){
        $this->load->view('html',array("html"=>'<script src="'.base_url().'js/controllers/report.js"></script>'));
        $this->load->view('html',array('html'=>"<md-content layout-padding><h2>Petty Cash Fund Report</h2></md-content>"));
    }

    public function index(){

        $this->load->view('header');
		$this->makeHeader();

        $this->load->view('graph');
		
		$this->load->view('reports',array('url'=>site_url(str_replace('\\','/',$this->getAccessURL(__FILE__))) ));
        $this->makeSelector();

        $this->load->view('footer');
    }

    protected function makeHTML($subtable){

        $this->load->view('header');
		$this->makeHeader();

        $this->load->view('graph', array('url'=>site_url(str_replace('\\','/',$this->getAccessURL(__FILE__))) ));
		$this->load->view('reports',array('url'=>site_url(str_replace('\\','/',$this->getAccessURL(__FILE__))) ));
        $this->makeSelector($subtable, site_url(str_replace('\\','/',$this->getAccessURL(__FILE__))) );
		$this->load->view('date_range_selector',array('subtable'=>$subtable, 'url'=>site_url(str_replace('\\','/',$this->getAccessURL(__FILE__)))));

        $this->load->view('footer');


    }

	protected function switchModel($subtable){
        $modelName = $this->model->getModel($subtable);	
        $this->load->model($modelName);
        return $this->$modelName;
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

            $grandtotal = $this->model->getExpenses(1);
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
        $this->makeHeader();

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
			$this->model = $this->switchModel($category);
			$body = array();
			array_push($body,$category);
			
			array_push($body,round($this->model->getExpense(date('Y'),date('Y-m-t',strtotime('Dec 31'))),2)); //Anual
			array_push($body,round($this->model->getExpense(date('Y-m-d',$start_date),date('Y-m-d',$end_date)),2)); //Quarterly
			array_push($body,round($this->model->getExpense(date('Y-m-01'),date('Y-m-t')),2)); //Monthly
			array_push($table,$body);
		}
		
		echo json_encode($table);
		return $table;
		
		
	}

    public function getExpenseTable($subtable,$mode=0,$fromDate=null,$toDate=null){
		// mode 0 - all expenses
		// mode 1 - unreplenished expenses
		$subtable = urldecode($subtable);
		$this->model = $this->switchModel($subtable);
        $table = array();

		/** Header Names **/
        $fields = $this->model->getFields();
		$firstField = reset($fields);

        $headers = array();
        foreach($fields as $field) {
            $headers[$field] = $this->model->getFieldTitle($field);
        }
        $headers['total']='Total';
        array_push($table,$headers);
		/** **/
		
		
		/** Table Body **/
		$result = $this->model->getExpenses($mode,$fromDate,$toDate);
		
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
