<?php
defined('BASEPATH') OR exit('No direct script access allowed');
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
		// Separate querying name
		$name = $this->convertNameToCategory($subtable);

		$this->load->helper("query_helper");

		if ($fields == null) $fields = $this->getFieldAssociations();

		$this->db->reset_query();

		$defjoin = isset( $settings['limit_by'] );
		$ordered = isset( $settings['order_by']);

		// Use deffered join for limit N offset X
		// We search only for the Primary Keys of the rows we need
		// Then we use join for SQL to only fetch those rows.
		// More efficient because Primary Keys are stored differently on disk
 
		if ($defjoin) {
			$this->db->select( $this->TablePrimaryKey );

			if ($ordered && $this->TablePrimaryKey != $settings['order_by']) {
				$this->db->select(
					$this->selectHeader($settings['order_by'], $fields[ $settings['order_by'] ]), 
					false
				);
			}

			$this->db->start_cache();
		}
		else
			$this->select($fields);

		if ( !empty($search) )
			qry_evaluate($search, $this->db);

		$this->db->where($this->arrayFieldName, $name);

		if ( $defjoin )
			$this->db->stop_cache();

		if ( $ordered ) {
			$this->db->order_by(
				$settings['order_by'],
				isset( $settings['order_dir'] ) ? $settings['order_dir'] : ''
			);
		}

		if ($defjoin) {

			$this->db->limit(
				$settings['limit_by'],
				isset( $settings['limit_offset'] ) ? $settings['limit_offset'] : 0
			);

			$select = $this->db->get_compiled_select($this->TableName);

			$this->lastFindCount = $this->db->count_all_results($this->TableName);

			$this->db->flush_cache();

			$this->select($fields);
			$this->db->join('('.$select.') as t', 't.'.$this->TablePrimaryKey.' = '.$this->TableName.'.'.$this->TablePrimaryKey, '', FALSE );
		}

		$result = $this->db->get($this->TableName);

		if (!$defjoin)
			$this->lastFindCount = $result->num_rows();

		return $result;
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