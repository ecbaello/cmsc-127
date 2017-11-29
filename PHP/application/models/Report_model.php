<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_model extends CI_Model
{
	const TableName = 'fin_reporting_meta';
	const tableFieldName = 'table_name';
	const dateFieldName = 'date_field_name';

	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->database();
		$this->load->dbforge();
		$this->load->model('registry_model');
		
		$this->createTable();
	}

	public function createTable() {
		if (!($this->db->table_exists(self::TableName))) {

			$fields = array(
				'table_name' => array(
	                'type' => 'VARCHAR',
	                'constraint' => 100
	            ),
	            'date_field_name' => array(
	                'type' => 'VARCHAR',
	                'constraint' => 50
	            )
       		);

			$this->dbforge->add_field		($fields);
			$this->dbforge->create_table	(self::TableName);
			$this->db->query('alter table '.self::TableName.' add foreign key (table_name) references '.Registry_model::modelTableName.'(table_name) ON DELETE CASCADE');
		}
	}

	public function register($tableName,$dateField) {
		
		$this->db->select('table_name','date_field_name');
		$this->db->where('table_name',$tableName);
		$this->db->where('date_field_name',$dateField);
		
		if($this->db->get(self::TableName)->num_rows()>0)
			return null;
		
		$this->db->where(self::tableFieldName,$tableName);
		$this->db->where(MY_DBmodel::fieldInputTypeField,'FLOAT');
		$this->db->update(MY_DBmodel::metaTableName,array('reporting_option'=>1));
		
		return $this->db->insert(self::TableName,
			array(
				'table_name' => $tableName,
				'date_field_name' => $dateField
			)
		);
	}

	public function getModelNames() {
		$this->db->select(self::tableFieldName);
		$this->db->from(self::TableName);
		$subquery = $this->db->get_compiled_select();
		
		$this->db->select(MDL_NAME);
		$this->db->from(Registry_model::modelTableName);
		$this->db->where_in(self::tableFieldName,$subquery,false);		
		$query = $this->db->get()->result_array();
		
		if ( empty($query) ) return $query;

		$arr = array();
		foreach ($query as $field) {
			$item = $field[MDL_NAME];
			array_push( $arr,  $item);
		}

		return $arr;
	}
	
	public function getModelDateField($tableName){
		$this->db->select(self::dateFieldName);
		$this->db->where(self::tableFieldName,$tableName);
		$query = $this->db->get(self::TableName)->result_array();
		
		if(empty($query)) return null;
		
		return $query[0][self::dateFieldName];
	}
	
	protected function getFieldOption($field){
		$this->db->select('reporting_option');
		$this->db->where('table_field',$field);
		$query = $this->db->get(MY_DBmodel::metaTableName)->result_array();
		if(empty($query)) return null;
		return $query[0]['reporting_option'];
	}
	
	protected function convertModelToTable($modelName){
		$this->db->select(self::tableFieldName);
		$this->db->where(MDL_NAME,$modelName);
		$query = $this->db->get(Registry_model::modelTableName)->result_array(); 
		if(empty($query))
			return null;
		return $query[0][self::tableFieldName];
	}
	
	protected function convertTableToModel($tableName){
		$this->db->select(MDL_NAME);
		$this->db->where(self::tableFieldName,$tableName);
		$query = $this->db->get(Registry_model::modelTableName)->result_array(); 
		if(empty($query))
			return null;
		return $query[0][MDL_NAME];
	}

	public function getNumericalFields($modelName){
		$tableName = $this->convertModelToTable($modelName);
		$fields = $this->getFields($modelName);
		$names = array();
		
        foreach($fields as $field => $attributes){
            if($attributes['type'] == 'FLOAT'){
				$temp=array();
				$temp['field']=$attributes['field'];
				$temp['name']=$attributes['title'];
				$temp['option']=$attributes['option'];
                array_push($names,$temp);
            }
        }
		return $names;
	}
	
	public function getFields($modelName) {
		$tableName = $this->convertModelToTable($modelName);
		$this->db->select('table_field,table_field_title,'.MY_DBmodel::fieldInputTypeField.',reporting_option');
		$this->db->where('table_name', $tableName);
		$query = $this->db->get(MY_DBmodel::metaTableName)->result_array();
		if ( empty($query) ) return $query;
		$arr = array();
		foreach ($query as $field) {
			$item = array(
				'field'=>$field['table_field'],
				'title'=>$field['table_field_title'],
				'option'=>$field['reporting_option'],
				TBL_INPUT=>$field[MY_DBmodel::fieldInputTypeField]
			);
			array_push( $arr,  $item);
		}
		return $arr;
	}

	public function getExpenseTotal($modelName,$fromDate=null,$toDate=null){
		
		$tableName = $this->convertModelToTable($modelName);
		$dateField = $this->getModelDateField($tableName);
		$numerics = $this->getNumericalFields($modelName);
		
		$numericsQuery = array();
		foreach($numerics as $numeric){
			switch($numeric['option']){
				case 1:
					array_push($numericsQuery,'SUM('.$numeric['field'].')');
					break;
				case 2:
					array_push($numericsQuery,'-SUM('.$numeric['field'].')');
					break;
				default:
					break;
			}
		}

		if(empty($numericsQuery)) return 0;
		
        $this->db->select(implode(" + ",$numericsQuery).' as total');
		if($fromDate !== null && $toDate!==null){
			$this->db->where($dateField.' between "'.$fromDate.'" and "'.$toDate.'"');
		}
        $result= $this->db->get($tableName)->result_array();
		if(empty($result)) return 0;
        $total = $result[0]['total'];
        return $total===null?0:$total;

    }
	
	public function getExpenseField($modelName,$fromDate=null,$toDate=null,$field){
		
		$tableName = $this->convertModelToTable($modelName);
		$dateField = $this->getModelDateField($tableName);
		$fieldOption = $this->getFieldOption($field);
		
		if($fieldOption == 0) return 0;
		
        $this->db->select( 'SUM('.($fieldOption==2?'-':'').$field.') as total');
		
		if($fromDate !== null && $toDate !==null){
			$this->db->where($dateField.' between "'.$fromDate.'" and "'.$toDate.'"');
		}
        $result= $this->db->get($tableName)->result_array();
		if(empty($result)) return 0;
        $total = $result[0]['total'];
        return $total===null?0:$total;

    }
	
	public function changeFieldOption($modelName,$field,$option){
		$this->db->where('table_field',$field);
		return $this->db->update(MY_DBmodel::metaTableName,array('reporting_option'=>$option));
	}
	
	
	public function getCustomExpenseTable($modelName,$fromDate,$toDate,$constraints = array()){
		
		$tableName = $this->convertModelToTable($modelName);
		$dateField = $this->getModelDateField($tableName);
		$numerics = $this->getNumericalFields($modelName);
		$numericFields = array();
		$result = array();
		
		$preFields = $this->getFields($modelName);
		$fields = array();
		foreach($preFields as $value){
			array_push($fields,$value['field']);
		}
		
		$summations = array();
		$numericsQuery = array();
		foreach($numerics as $numeric){
			switch($numeric['option']){
				case 1:
					array_push($numericsQuery,'SUM('.$numeric['field'].')');
					array_push($summations,'SUM('.$numeric['field'].') as "'.$numeric['field'].'"');
					array_push($numericFields,$numeric['field']);
					break;
				case 2:
					array_push($numericsQuery,'-SUM('.$numeric['field'].')');
					array_push($summations,'-SUM('.$numeric['field'].') as "'.$numeric['field'].'"');
					array_push($numericFields,$numeric['field']);
					break;
				default:
					array_diff($fields,array($numeric['field']));
					break;
			}
		}
		
		$this->db->select(implode(' , ',$fields));
		if(!empty($numericsQuery)){
			$this->db->select('('.implode(" + ",$numericsQuery).') as total');
		}
		$this->db->where($dateField. ' between "' . $fromDate . '" and "' . $toDate . '"');
		$result = $this->db->get($tableName)->result_array();
		
		/** Sub-Totals **/
		
        $this->db->select(implode(" , ",$summations));
		$this->db->where($dateField . ' between "' . $fromDate . '" and "' . $toDate . '"');
		$data = $this->db->get($tableName)->result_array();

        $subtotals = array();
		
        $grandtotal = 0;
        if(sizeof($data) > 0) {
            foreach ($fields as $field) {
                if(in_array($field,$numericFields)) {
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

	
}