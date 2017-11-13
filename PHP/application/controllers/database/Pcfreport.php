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

        $this->makeSelector($subtable, site_url(str_replace('\\','/',$this->getAccessURL(__FILE__))) );

        $this->load->view('graph', array('url'=>site_url(str_replace('\\','/',$this->getAccessURL(__FILE__))) ));
        $this->makeDateSelector($subtable);

        $this->load->view('footer');


    }

    protected function makeDateSelector($subtable){

        $this->load->view('html',array(
            'html'=>'<div layout-padding><p>This Month: '.$this->getExpense($subtable,null,date('Y-m-01'),date('Y-m-t')).'</p>
                    <p>This Year: '.$this->getExpense($subtable,null,date('Y'),date('Y-m-t',strtotime('Dec 31'))).'</p></div>'
        ));

    }

    protected function getTotal($expense_id){



    }

    //Get total from date 1 to date 2
    public function getExpense($subtable,$action=null, $fromDate = null, $toDate=null){
    //public function getExpense(){

        $pcfDateName = 'pcf_date';
        $pcfIdName = 'pcf_type';

        //DOESN'T WORK YET FOR TABLES WITH DERIVED FLOAT ATTRIBUTES

        $fields = $this->model->getFieldAssociations();
        $subtable = $this->model->convertNameToCategory($subtable);
        $numerics = array();

        foreach($fields as $field => $attributes){
            if($attributes['type'] == 'FLOAT'){
                array_push($numerics,$field);
            }
        }

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

        return $total;

    }




}
