<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class MY_DBpcfmodel extends MY_DBarraymodel
{
	public $arrayFieldName = 'pcf_type';
	public $categoryTableName = 'pcf_type_table';	
    public $categoryFieldName = 'pcf_name';
    public $categoryModelName = 'model_name';
    public $booleanFieldName = 'replenished';

    public $afFieldName = 'pcf_allotted_fund';
    public $etFieldName = 'pcf_expense_threshold';


	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class
		$this->createCategoryTable();
		
		$this->registerCategoryTable('General','database_pcf_general_model');
        $this->registerCategoryTable('Smile Train','database_pcf_smiletrain_model');
        $this->registerCategoryTable('Cataract','database_pcf_cataract_model');
	}

    public function createCategoryTable()
    {
        if (!($this->db->table_exists($this->categoryTableName))) {
            $fields = array(
                'pcf_type' => array(
                    'type' => 'INT',
                    'auto_increment' => TRUE
                )
            );
            $this->dbforge->add_field($fields);
            $this->dbforge->add_field("pcf_name VARCHAR(100) NOT NULL");
            $this->dbforge->add_field("model_name VARCHAR(100) NOT NULL");
            $this->dbforge->add_field("pcf_allotted_fund FLOAT NOT NULL DEFAULT 5000.0");
            $this->dbforge->add_field("pcf_expense_threshold FLOAT NOT NULL DEFAULT 3000.0");
            $this->dbforge->add_key('pcf_type', TRUE);
            $this->dbforge->create_table($this->categoryTableName);
        }
    }

    public function getFieldsFromTypeTable($category,$fields=array()){
	    $this->db->select(implode(',',$fields));
	    $this->db->where($this->categoryFieldName,$category);
	    $result =  $this->db->get($this->categoryTableName)->result_array();
	    return isset($result[0]) ? $result[0]:$result;
    }

    public function replenish($tableName){
        $this->db->where($this->booleanFieldName,0);
        return $this->db->update($tableName,array($this->booleanFieldName=>1));
    }

    public function changeAllottedFund($category,$desiredFund){
        $this->db->where($this->categoryFieldName,$category);
        return $this->db->update($this->categoryTableName,array($this->afFieldName=>$desiredFund));
    }

    public function changeExpenseThreshold($category,$desiredThreshold){
        $this->db->where($this->categoryFieldName,$category);
        return $this->db->update($this->categoryTableName,array($this->etFieldName=>$desiredThreshold));
    }

    public function getModel($name){
		
	    $this->db->select($this->categoryModelName);
	    $this->db->where($this->categoryFieldName,$name);
	    $result = $this->db->get($this->categoryTableName);
	    if(!empty($result->result_array()))
	        return $result->result_array()[0][$this->categoryModelName];
	    else
	        return '';
    }


	public function registerCategoryTable($name, $modelName = '') {
		if ( !$this->checkCategoryExists($name) ) {
			$data = array(
			    $this->categoryFieldName => $name,
                $this->categoryModelName => $modelName
			);
			$this->db->insert($this->categoryTableName, $data);
		}
	}


}

?>