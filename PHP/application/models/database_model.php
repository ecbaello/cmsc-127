<?php

class Database_model extends CI_Model
{
	const TableName = 'db_meta';

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->database();
		$this->load->dbforge();

		$this->load->library('db_table');

		$this->createTable();
	}

	/**
	* Make PCF table if does not exists
	*
	*/
	public function createTable() {
		if (!($this->db->table_exists(self::TableName)))
		{
			$this->dbforge->add_field		("table_name VARCHAR(100) NOT NULL");
			$this->dbforge->add_field		("table_field VARCHAR(100) NOT NULL");
			$this->dbforge->add_field		("table_field_title VARCHAR(100) NOT NULL");
			$this->dbforge->add_field		("table_field_inputs BOOLEAN NOT NULL DEFAULT TRUE");
			$this->dbforge->add_field		("table_field_input_type VARCHAR(100) NOT NULL DEFAULT 'text'");

			$this->dbforge->create_table	(self::TableName);
		}
	}

	public function registerFieldTitle( $table_name, $table_field, $field_title, $isInput = true ) {

		$data = array(
		        'table_name' => $table_name,
		        'table_field' => $table_field,
		        'table_field_title' => $field_title,
		        'table_field_inputs' => $isInput
		);

		$this->db->insert(self::TableName, $data);
	}

	public function getFields( $table ) {
		$this->db->select('table_field');
		$this->db->where('table_name', $table);
		$query = $this->db->get(self::TableName)->result_array();
		if ( empty($query) ) return $query;

		$arr = array();
		foreach ($query as $field) {
			array_push( $arr,  $field['table_field']);
		}
		return $arr;
	}

	public function getFieldAssociations( $table ) {
		$this->db->select('table_field, table_field_title');
		$this->db->where('table_name', $table);

		$inp = $this->db->get(self::TableName)->result_array();
		$arr = array();
		foreach ($inp as $assoc) {
			$arr[ $assoc['table_field'] ] = $assoc['table_field_title'];
		}
		return $arr;
	}

	public function getFieldTitle( $table_field ) {
		return $this->getFieldTitleByTable($table_field, NULL);
	}

	public function getFieldTitleByTable( $table_field, $table ) {

		$this->db->select('table_field_title');
		$this->db->where('table_field', $table_field);
		if (!empty($table))
			$this->db->where('table_name', $table);
		$query = $this->db->get(self::TableName)->result_array();
		if ( empty($query) ) return $query;
		
		return $query[0]['table_field_title'];
	}

	public function getField( $table_field_title, $table) {
		$this->db->select('table_field');
		$this->db->where('table_field_title', $table_field_title);
		$this->db->where('table_name', $table);
		$query = $this->db->get(self::TableName);
		if ( empty($query) ) return $query;
		$query = $query->result_array();
		if ( empty($query) ) return $query;
		return $query[0]['table_field'];
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

	public function makeTable($query)
	{
		if (empty($query)) return NULL;
		$fields = $query->list_fields();
		$headers = $this->convertFields($fields);

		$this->db_table->set_heading($headers);
		return $this->db_table->generate($query);
	}

	public function makeTableWithDelete($table_name, $pk, $link, $script)
	{

		$query = $this->db->get($table_name);

		$fields = $query->list_fields();
		$headers = $this->convertFields($fields);

		$this->db_table->set_heading($headers);

		return $this->db_table->generateDBUsingPK($query, $pk, $link, NULL, $script);
	}

	public function getData($tableName)
	{
		return $this->db->get($tableName)->result_array();
	}

	public function find ($search, $table_name)
	{
		if (!empty($search)){
			$queries = explode ( "," , $search);
			foreach ($queries as $search) {
				$search = explode ( ":" , $search);
				$field = $this->getField($search[0], $table_name);
				$this->db->where($search[0], $search[1]);
			}
		}
		return $this->db->get($table_name);
	}
}

?>