<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class MY_DBarraymodel extends MY_DBmodel
{
	public $categoryTableName = '';
	public $arrayFieldName = '';
	public $categoryFieldName = '';

	private $selectedSubtable = '';

	protected $isArrayModel = TRUE;

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->createCategoryTable();
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
		$fromtable = $this->TableName;
		$totable = $fromtable."_arch";
		$condition = $this->TablePrimaryKey."=$pk";
		$tempsql = "INSERT INTO $totable SELECT * FROM $fromtable WHERE $condition";
		$this->db->query($tempsql);
		$table = $this->convertNameToCategory($name);
		$this->db->where( $this->TablePrimaryKey, $pk);
		$this->db->where( $this->arrayFieldName, $table);
	    return parent::deleteWithPK( $pk ); 
	}

	public function convertNameToCategory($name) {
		$this->db->select($this->arrayFieldName);
		$this->db->where($this->categoryFieldName, $name);
		$query = $this->db->get($this->categoryTableName);

		$query = $query->result_array();
		$query = $query[0];

		return $query[$this->arrayFieldName];
	}
}

?>