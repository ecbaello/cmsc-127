<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class MY_DBarraymodel extends MY_DBmodel
{
	public $categoryTableName = null;
	public $arrayFieldName = null;
	public $categoryFieldName = null;

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		if ($this->willRegister) $this->createCategoryTable();
	}

	protected function registerModel() {

		$this->load->model('registry_model');

		return $this->registry_model->registerModel(
			$this->ModelTitle,
			$this->getModelClass(),
			1,
			$this->TableName,
			$this->TablePrimaryKey,
			$this->FieldPrefix,
			false,
			$this->categoryTableName,
			$this->arrayFieldName,
			$this->categoryFieldName
		);
	}

	public function createTableWithID() {
		if (!$this->db->table_exists($this->TableName)){
			$fields = array(
        		$this->TablePrimaryKey => array(
	                'type' => 'INT',
	                'constraint' => 9,
	                'auto_increment' => TRUE
	            )
       		);
       		$this->dbforge->add_field		($this->arrayFieldName." INT NOT NULL");
       		$this->dbforge->add_field		($fields);

       		$this->dbforge->add_key 		($this->TablePrimaryKey, TRUE);
       		
			$success = $this->dbforge->create_table	($this->TableName);

			$success = $success && $this->registerFieldTitle( $this->TablePrimaryKey, '#');
			$success = $success && $this->registerModel();

			$this->createCategoryTable();
			return $success;
		} else return false;
	}

    public function createCategoryTable()
    {
    	if (!($this->db->table_exists($this->categoryTableName))) {
            $fields = array(
                $this->arrayFieldName => array(
                    'type' => 'INT',
                    'auto_increment' => TRUE
                )
            );
            $this->dbforge->add_field($fields);
            $this->dbforge->add_field($this->categoryFieldName.' VARCHAR(100) NOT NULL');
            $this->dbforge->add_key($this->arrayFieldName, TRUE);
            $this->dbforge->create_table($this->categoryTableName);
        }
    }

	public function getFieldAssociations() {
		$fields = parent::getFieldAssociations();
		unset($fields[$this->arrayFieldName]);
		return $fields;
	}

	public function getIndividual($id) {
		$this->db->where($this->TablePrimaryKey, $id);
		$query = $this->db->get($this->TableName)->row();
		$field = $this->arrayFieldName;
		unset($query->$field);
		return $query;
	}

	public function registerCategoryTable($name) {
		if ( !$this->checkCategoryExists($name) ) {
			$data = array(
			    $this->categoryFieldName => $name
			);
			$this->db->insert($this->categoryTableName, $data);
		}
	}

	public function unregisterCategoryTable($name) {
		$categ = $this->convertNameToCategory($name);

		if ( $categ != null ) {
			$success = true;
			$this->db->where($this->categoryFieldName, $name);
			$success = $success && $this->db->delete($this->categoryTableName);
			$this->db->where($this->arrayFieldName, $categ);
			$success = $success && $this->db->delete($this->TableName);
			return $success;
		}
		return false;
	}

	public function getFields($hide_items = true) {
		$fields = parent::getFields($hide_items);
		return $hide_items ? array_diff($fields, array($this->arrayFieldName)) : $fields;
	}

	public function find ($subtable, $search = null, $settings = [], $fields = null)
	{	
		if ($search == null)
			$search = [];

		$tableIndex = $this->convertNameToCategory($subtable);

		$condition = [	'condition'	=> null,
						'header'	=>
							[	'key'		=> $this->arrayFieldName,
								'derived'	=> false,
								'derivation'=> null
							],
						'operation'	=> 'equals',
						'values'	=> [$tableIndex]
					];

		array_push($search, $condition);

		$search = ['condition'=>'AND', 'rules'=>$search, 'not'=>false];

		return parent::find($search, $settings, $fields);
	}

	public function checkCategoryExists($name) {
		$this->db->where($this->categoryFieldName, $name);
		$query = $this->db->get($this->categoryTableName);
		return !empty( $query->result_array() );
		
	}

	public function getCategories() {
		$query = $this->db->get($this->categoryTableName);

		$assocs = array();
		$arr = $query->result_array();
		foreach ($arr as $assoc) {
			$assocs[ $assoc[$this->arrayFieldName] ] = $assoc[$this->categoryFieldName];
		}
		return $assocs;
	}

	public function getCategoryAssociations() {
		$query = $this->db->get($this->categoryTableName);

		$assocs = array();
		$arr = $query->result_array();
		foreach ($arr as $assoc) {
			$assocs[ $assoc[$this->categoryFieldName] ] = $assoc[$this->arrayFieldName];
		}
		return $assocs;
	}

	public function getSubtableAsCSV($table) {
		$this->load->dbutil();

		$categ = $this->convertNameToCategory($table);
		$fields = $this->getFieldAssociations();

		$this->select($fields, true);
		$this->db->where($this->arrayFieldName, $categ);
		$query = $this->db->get($this->TableName);

		return $this->dbutil->csv_from_result($query);
	}

	public function insertIntoCategoryTable($name, $values) {
		$values[$this->arrayFieldName] = $this->convertNameToCategory($name);
		return parent::insertIntoTable($values);
	}

	public function updateOnCategoryTable($name, $pk, $values) {
		unset($values[$this->arrayFieldName]);
		$this->db->where( $this->arrayFieldName, $this-> convertNameToCategory($name));
	    return $this->updateWithPK($pk, $values); 
	}

	public function deleteFromCategoryTable($name, $pk) {
		$table = $this->convertNameToCategory($name);
		$this->db->where( $this->arrayFieldName, $table);
	    return parent::deleteWithPK( $pk ); 
	}

	public function convertNameToCategory($name) {
		$this->db->select($this->arrayFieldName);
		$this->db->where($this->categoryFieldName, $name);
		$query = $this->db->get($this->categoryTableName);

		$query = $query->row();

		if (empty($query)) return null;
		return $query->{$this->arrayFieldName};
	}
}

?>