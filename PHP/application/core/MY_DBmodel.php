<?php

class MY_DBmodel extends CI_Model
{
	const metaTableName = 'db_meta';
	protected $TableName = ''; // Overideable
	protected $TablePrimaryKey = ''; // Overideable

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->database();
		$this->load->dbforge();

		if (!($this->db->table_exists(self::metaTableName)))
		{
			$this->dbforge->add_field		("table_name VARCHAR(100) NOT NULL");
			$this->dbforge->add_field		("table_field VARCHAR(100) NOT NULL");
			$this->dbforge->add_field		("table_field_title VARCHAR(100) NOT NULL");
			$this->dbforge->add_field		("table_field_inputs BOOLEAN NOT NULL DEFAULT TRUE");
			$this->dbforge->add_field		("table_field_input_type VARCHAR(100) NOT NULL DEFAULT 'text'");

			$this->dbforge->create_table	(self::metaTableName);
		}

		

		$this->load->library('db_table');
	}

	public function createTable() {
		
	}

	public function registerFieldTitle( $table_field, $field_title, $isInput = true ) {

		$data = array(
		        'table_name' => self::TableName,
		        'table_field' => $table_field,
		        'table_field_title' => $field_title,
		        'table_field_inputs' => $isInput
		);

		$this->db->insert(self::metaTableName, $data);
	}

	




	public function makeTableWithDelete($link, $script)
	{

		$query = $this->db->get( $this->TableName);

		$fields = $query->list_fields();
		$headers = $this->convertFields($fields);

		$this->db_table->set_heading($headers);

		return $this->db_table->generateDBUsingPK($query, $this->TablePrimaryKey, $link, NULL, $script);
	}

	public function getFields() {
		$this->db->select('table_field');
		$this->db->where('table_name', $this->TableName);
		$query = $this->db->get(self::metaTableName)->result_array();
		if ( empty($query) ) return $query;

		$arr = array();
		foreach ($query as $field) {
			array_push( $arr,  $field['table_field']);
		}
		return $arr;
	}

	public function getFieldAssociations() {
		$this->db->select('table_field, table_field_title');
		$this->db->where('table_name', $this->TableName);

		$inp = $this->db->get(self::metaTableName)->result_array();
		$arr = array();
		foreach ($inp as $assoc) {
			$arr[ $assoc['table_field'] ] = $assoc['table_field_title'];
		}
		return $arr;
	}

	public function getFieldTitle( $table_field ) {

		$this->db->select('table_field_title');
		$this->db->where('table_field', $table_field);
		$this->db->where('table_name',  $this->TableName);
		$query = $this->db->get(self::metaTableName)->result_array();
		if ( empty($query) ) return $query;
		
		return $query[0]['table_field_title'];
	}

	public function getField( $table_field_title ) {
		$this->db->select('table_field');
		$this->db->where('table_field_title', $table_field_title);
		$this->db->where('table_name',  $this->TableName);
		$query = $this->db->get(self::metaTableName);
		if ( empty($query) ) return $query;
		$query = $query->result_array();
		if ( empty($query) ) return $query;
		return $query[0]['table_field'];
	}

	public function getData()
	{
		return $this->db->get( $this->TableName)->result_array();
	}

	public function convertFields( $fields ) {
		$arr = array();
		foreach ($fields as $field) {
			$item = $this->getFieldTitle($field);
			if ( empty($item) )  $item = '';
			array_push( $arr,  $item);
		}
		return $arr;
	}

	

	

	public function find ($search)
	{
		if (!empty($search)){
			$queries = explode ( "," , $search);
			foreach ($queries as $search) {
				$search = explode ( ":" , $search);
				$field = $this->getField($search[0], self::TableName);
				$this->db->where($search[0], $search[1]);
			}
		}
		return $this->db->get($table_name);
	}

	public function insertIntoTable($data) {
		$this->db->insert( $this->TableName, $data);
	}

	public function updateWithPK($id, $data) {
		$this->db->where( $this->TablePrimaryKey, $id);
	    $this->db->update( $this->TableName, $data); 
	}

	public function deleteWithPK($id) {
		$this->db->where( $this->TablePrimaryKey, $id);
	    $this->db->delete( $this->TableName); 
	}

	

	/* ---------------------
	*	Static Functions
	*  ---------------------
	*/

	public static function getProbableFieldTitle( $table_field ) {

		$this->db->select('table_field_title');
		$this->db->where('table_field', $table_field);
		$query = $this->db->get(self::metaTableName)->result_array();
		if ( empty($query) ) return $query;
		
		return $query[0]['table_field_title'];
	}

	public static function tryConvertFields( $fields ) {
		$arr = array();
		foreach ($fields as $field) {
			$item = $this->getProbableFieldTitle($field);
			if ( empty($item) )  $item = '';
			array_push( $arr,  $item);
		}
		return $arr;
	}

	public static function makeTable($query)
	{
		if (empty($query)) return NULL;
		$fields = $query->list_fields();
		$headers = $this->convertFields($fields);

		$this->db_table->set_heading($headers);
		return $this->db_table->generate($query);
	}
}

?>