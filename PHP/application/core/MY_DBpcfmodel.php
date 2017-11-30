<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class MY_DBpcfmodel extends MY_DBarraymodel
{
	public $arrayFieldName = 'pcf_type';
	public $categoryTableName = 'fin_pcf_type_table';	
    public $categoryFieldName = 'pcf_name';
    public $categoryModelName = 'model_name';
    public $booleanFieldName = 'replenished';

    public $afFieldName = 'pcf_allotted_fund';
    public $etFieldName = 'pcf_expense_threshold';
	public $dateField = 'pcf_date';

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class
		$this->createCategoryTable();
		
		$this->registerCategoryTable('General','database_pcf_general_model','fin_pcf_general');
        $this->registerCategoryTable('Smile Train','database_pcf_smiletrain_model','fin_pcf_smiletrain');
        $this->registerCategoryTable('Cataract','database_pcf_cataract_model','fin_pcf_cataract');
		
		$this->load->model('report_model');
	}

	protected function registerModel() {

		$this->load->model('registry_model');

		return $this->registry_model->registerModel(
			$this->ModelTitle,
			$this->getModelClass(),
			0,
			$this->TableName,
			$this->TablePrimaryKey,
			$this->FieldPrefix
		);
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
            $this->dbforge->add_field("model_name VARCHAR(100)");
            $this->dbforge->add_field("model_title VARCHAR(100)");
            $this->dbforge->add_field("table_name VARCHAR(100)");
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
		
	    $this->db->select($this->categoryModelName.',model_title,table_name');
	    $this->db->where($this->categoryFieldName,$name);
	    $result = $this->db->get($this->categoryTableName);
	    if(!empty($result->result_array())){
			$r = $result->result_array()[0];
	        $modelName = $r[$this->categoryModelName];
			if($modelName === null){
				$this->load->model('custom_pcf_model');
				$this->custom_pcf_model->loadCustom($r['model_title'], $r['table_name']);
				return $this->custom_pcf_model;
			}else{
				$this->load->model($modelName);
				return $this->$modelName;
			}
	    }else{
	        return null;
		}
    }


	public function registerCategoryTable($name, $modelName = null,$tableName=null) {
		if ( !$this->checkCategoryExists($name)) {
			if($modelName != null){
				$data = array(
					$this->categoryFieldName => $name,
					$this->categoryModelName => $modelName,
					'table_name'=>$tableName
				);
				return $this->db->insert($this->categoryTableName, $data);
			}else{
				
				$prefix = '';
				$title = 'fin_pcf_'.strtolower(str_replace(' ', '_', $name));
				
				$this->load->model('custom_pcf_model');
				$this->custom_pcf_model->loadCustom('Petty Cash Fund: '.$name, $title);
				if($this->custom_pcf_model->initializeCustomTable()){
					$success = $this->db->insert($this->categoryTableName,array(
						$this->categoryFieldName => $name,
						'model_title'=>'Petty Cash Fund: '.$name,
						'table_name'=>$title
					));
					
					if($success){
						$this->db->where('table_name',$title);
						$this->db->update(Registry_model::modelTableName,array(MDL_CLASS=>'pcf'));
						return true;
					}else{
						return false;
					}
					
				}else{
					return false;
				}
			}
		}
		return false;
	}

	public function unregisterCategoryTable($name) {
		$categ = $this->convertNameToCategory($name);
		
		if($name==='Smile Train'||$name==='General'||$name==='Cataract')
			return false;
		
		if ( $categ != null ) {
			$success = true;
			$this->db->select('table_name');
			$this->db->where($this->categoryFieldName,$name);
			$result = $this->db->get($this->categoryTableName)->result_array();
			if(empty($result)) return false;
			$tableName = $result[0]['table_name'];
			
			$this->db->where($this->categoryFieldName, $name);
			$success = $success && $this->db->delete($this->categoryTableName);
			
			$this->db->where('table_name',$tableName);
			$success = $success && $this->db->delete(Registry_model::modelTableName);
			
			$this->db->where('table_name',$tableName);
			$success = $success && $this->db->delete(MY_DBmodel::metaTableName);
			
			return $success && $this->dbforge->drop_table($tableName,true);
		}
		return false;
	}
	
	public function registerReport(){
		$this->report_model->register($this->TableName,$this->dateField);
	}
	
	public function getNumericalFields(){

        $fields = $this->getFieldAssociations();
        $numerics = array();

        foreach($fields as $field => $attributes){
            if($attributes['type'] == 'FLOAT'){
                array_push($numerics,$field);
            }
        }

        return $numerics;
    }
	
	public function getExpense($fromDate = null, $toDate=null){
		
        $numerics = $this->getNumericalFields();
		
        $numericsQuery = array();
        foreach ($numerics as $field){
            array_push($numericsQuery ,'SUM('.$field.')');
        }

        $this->db->select(implode(" + ",$numericsQuery).' as total');
        $this->db->where($this->dateField.' between "'.$fromDate.'" and "'.$toDate.'"');
		
        $result= $this->db->get($this->TableName)->result_array();

        $total = $result[0]['total'];
		
        return $total === null ? 0:$total;

    }
	
	public function getExpenses($mode,$fromDate=null,$toDate=null){
		
		//mode 0 = all expenses
		//mode 1 = unreplenished expenses
		
		$result = array();
		
		$fields = $this->getFields();
		$numerics = $this->getNumericalFields();
		
		$this->db->select(implode(' , ',$fields));
        $this->db->select('('.implode(" + ",$numerics).') as total');
		if($mode == 0)
			$this->db->where($this->dateField . ' between "' . $fromDate . '" and "' . $toDate . '"');
		if($mode == 1)
			$this->db->where($this->booleanFieldName,0);
		
        $result = $this->db->get($this->TableName)->result_array();
		
		/** Sub-Totals **/
		$summations = array();
        foreach ($numerics as $field){
            array_push($summations, 'SUM(' . $field . ') as "'.$field.'"');
        }

        $this->db->select(implode(" , ",$summations));
		
        if($mode == 0)
			$this->db->where($this->dateField. ' between "' . $fromDate . '" and "' . $toDate . '"');
		if($mode == 1)
			$this->db->where($this->booleanFieldName,0);
			
        $data = $this->db->get($this->TableName)->result_array();

        $subtotals = array();
		
        $grandtotal = 0;
        if(sizeof($data) > 0) {
            foreach ($fields as $field) {
                if(in_array($field,$numerics)) {
                    $subtotals[$field] = round($data[0][$field],2);
                    $grandtotal +=  $data[0][$field];
                }else{
                    $subtotals[$field] = '';
                }
            }
			$subtotals['total'] = $grandtotal;
			$subtotals[reset($fields)]='Sub-Totals';
        }
		
		array_push($result,$subtotals);
		
		return $result;
		
	}
	
	public function checkValidExpense($category,$data){
		
		$grandtotal = $this->getExpenses(1);
		try {
			$grandtotal = end($grandtotal);
			$grandtotal = array_pop($grandtotal);
		}catch(Exception $e){
			return null;
		}
		
		$numerics = $this->getNumericalFields();
		$total = 0;
		
		foreach($data as $key=>$value){
			if(in_array($key,$numerics)){
				$total += $data[$key];
			}
		}
		return $this->getFieldsFromTypeTable($category,array($this->afFieldName))[$this->afFieldName] >= $grandtotal+$total;
	}

}

?>