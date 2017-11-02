<?php

class MY_DBarraymodel extends MY_DBmodel
{
	public $categoryTableName = '';
	public $arrayFieldName = '';
	public $categoryFieldName = '';

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
	}

	public function getCategoryTable($query) {
		$this->db->select(implode(',',$this->getFields()));
		$this->db->from($this->TableName);
		$this->db->join($this->categoryTableName, $this->TableName.'.'.$this->arrayFieldName.' = '.$this->categoryTableName.'.'.$this->arrayFieldName);
		$this->db->where($this->categoryFieldName, $query);
		$query = $this->db->get();
		return $query;
	}

	public function makeTableWithDelete($subtable, $link)
	{	
		$this->db->reset_query();
		$query = $this->getCategoryTable($subtable);
		$this->db_table->set_template($this->tabletemplate);
		return $this->db_table->generateDBUsingPK($query, $this->TablePrimaryKey, $link, NULL, $this->getFieldAssociations(false));
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

	public function find ($search, $subtable)
	{	
		$this->doFind($search);
		$this->db->where($this->arrayFieldName, $subtable);
		return $this->db->get($this->TableName);
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
			array_push($assocs, $assoc[$this->categoryFieldName]);
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
		return $this->insertIntoTable($values);
	}

	public function updateOnCategoryTable($name, $pk, $values) {
		$mvalues = $values;
		unset($mvalues[$this->TablePrimaryKey]);
		$mvalues[$this->arrayFieldName] = $this-> convertNameToCategory($name);

		$this->db->where( $this->TablePrimaryKey, $pk);

	    return $this->db->update( $this->TableName, $mvalues); 
	}

	public function deleteFromCategoryTable($name, $pk) {
		$table = $this->convertNameToCategory($name);

		$this->db->where( $this->TablePrimaryKey, $pk);
		$this->db->where( $this->arrayFieldName, $table);
	    return $this->db->delete( $this->TableName ); 
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