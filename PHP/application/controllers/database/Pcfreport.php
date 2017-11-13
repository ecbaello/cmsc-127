<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcfreport extends MY_DBarraycontroller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('database_pcf_model');
        $this->model = $this->database_pcf_model;
    }

    public function index(){

        $this->load->view('header');

        $this->load->view('html',array('html'=>"<md-content layout-padding><h2>Petty Cash Fund Report</h2></md-content>"));

        $this->load->view('graph');

        $this->makeSelector();

        $this->load->view('footer');
    }

    protected function makeHTML($subtable){

        $this->load->view('header');

        $this->load->view('html',array('html'=>"<md-content layout-padding><h2>Petty Cash Fund Report</h2></md-content>"));

        $this->load->view('graph', array('url'=>site_url(str_replace('\\','/',$this->getAccessURL(__FILE__))) ));
        $this->makeSelector($subtable, site_url(str_replace('\\','/',$this->getAccessURL(__FILE__))) );
        $this->makeDateSelector($subtable);

        $this->load->view('footer');


    }

    protected function makeDateSelector($subtable){

        $this->load->view('html',array(
            'html'=>'<div layout-padding>This Month: '.$this->getExpense($subtable,null,date('Y-m-01'),date('Y-m-t')).'<br/>
                    This Year: '.$this->getExpense($subtable,null,date('Y'),date('Y-m-t',strtotime('Dec 31'))).'</div>'
        ));

        $this->load->view('date_range_selector');

    }

    protected function getTotal($expense_id){



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

    protected function getNumericalFields(){

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

        $subtable = $this->model->convertNameToCategory($subtable);
        $numerics = $this->getNumericalFields();
        $table = array();

        $fields = $this->model->getFields();

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

        foreach($result as $r){
            array_push($table,$r);
        }

        $summations = array();
        foreach ($numerics as $field){
            array_push($summations, 'SUM(' . $field . ') as "'.$field.'"');
        }

        $this->db->select(implode(" , ",$summations));
        $this->db->where($pcfDateName.' between "'.$fromDate.'" and "'.$toDate.'"');
        $this->db->where($pcfIdName,$subtable);
        $result = $this->db->get($this->model->TableName)->result_array();

        $subtotals = array();
        $grandtotal = 0;
        if(sizeof($result) > 0) {
            foreach ($fields as $field) {
                if(in_array($field,$numerics)) {
                    $subtotals[$field] = $result[0][$field];
                    $grandtotal +=  $result[0][$field];
                }else{
                    $subtotals[$field] = '';
                }
            }
            $subtotals['total'] = $grandtotal;
        }

        array_push($table,$subtotals);

        echo json_encode($table);
        //print_r($table);die();

    }




}
