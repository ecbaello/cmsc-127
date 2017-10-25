<?php

class MY_DBarraymodel extends MY_DBmodel
{
	public $categoryTableName = 'pcf_type_table';
	public $arrayFieldName = 'pcf_type';
	public $categoryFieldName = 'pcf_name';

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
		$this->db->select($this->getFields($this->TableName));
		$this->db->from($this->TableName);
		$this->db->join($this->categoryTableName, $this->TableName.'.'.$this->arrayFieldName.' = '.$this->categoryTableName.'.'.$this->arrayFieldName);
		$this->db->where($this->categoryFieldName, $query);
		$query = $this->db->get();
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

	public function checkCategoryExists($name) {
		$this->db->where($this->categoryFieldName, $name);
		$query = $this->db->get($this->categoryTableName);
		return !empty( $query->result_array() );
		
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
		$this->insertIntoTable($values);
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