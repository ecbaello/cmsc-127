<?php

class MY_DBmodel extends CI_Model
{
	const metaTableName = 'db_meta';
	const inputTypesTableName = 'input_types';
	const modelTableName = 'model_registry';



	public $ModelTitle = '';
	protected $TableName = ''; // Overideable
	protected $TablePrimaryKey = 'id'; // Overideable
	protected $isArrayModel = FALSE;
	protected $willRegister = TRUE;

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->database();
		$this->load->dbforge();

		$this->createInputsTable();
		$this->createMetaTable();
		if ($this->willRegister) $this->registerModel();

		$this->createTable();

		
	}

	private function registerModel() {
		if (!($this->db->table_exists(self::modelTableName))) {

			$fields = array(
        		MDL_CLASS => array(
	                'type' => 'VARCHAR',
	                'constraint' => 100
	            ),
	            MDL_NAME => array(
	                'type' => 'VARCHAR',
	                'constraint' => 100
	            ),
	            MDL_ARRAY => array(
	                'type' => 'TINYINT',
	                'constraint' => 1
	            )
       		);

			$this->dbforge->add_field		($fields);
			$this->dbforge->add_key 		(MDL_CLASS, TRUE);
			$this->dbforge->create_table	(self::modelTableName);
		}

		$this->db->insert(self::modelTableName,
			array(
				MDL_NAME => $this->ModelTitle,
				MDL_CLASS => strtolower(get_class($this)),
				MDL_ARRAY => $this->isArrayModel?1:0
			)
		);
	}

	public function createInputsTable() {
		if (!($this->db->table_exists(self::inputTypesTableName)))
		{
			$this->dbforge->add_field		('id');
			$fields = array(
        		"input_type" => array(
	                'type' => 'VARCHAR',
	                'constraint' => 20
	            )
       		);
			$this->dbforge->add_field		($fields);

			$this->dbforge->create_table	(self::inputTypesTableName);

			$this->db->insert(self::inputTypesTableName, array('input_type' => 'TEXT'));
			$this->db->insert(self::inputTypesTableName, array('input_type' => 'DROPDOWN'));
			$this->db->insert(self::inputTypesTableName, array('input_type' => 'RADIO'));
			$this->db->insert(self::inputTypesTableName, array('input_type' => 'NUMBER'));
			$this->db->insert(self::inputTypesTableName, array('input_type' => 'CHECKBOX'));
		}
	}


	private function createMetaTable() {
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

	public function registerFieldTitle( $table_field, $field_title, $inputType = 'TEXT', $isInput = true ) {
		// Input Types: TEXT, TEXTAREA, CHECKBOX, DROPDOWN, RADIO, NUMBER

		$data = array(
		        'table_name' => $this->TableName,
		        'table_field' => $table_field,	
		        'table_field_title' => $field_title,
		        'table_field_inputs' => $isInput,
		        'table_field_input_type' => $inputType, 
		);

		$this->db->insert(self::metaTableName, $data);
	}

	public function makeTableWithDelete($link, $script)
	{

		$query = $this->get();

		$fields = $query->list_fields();
		$headers = $this->convertFields($fields);

		$this->db_table->set_heading($headers);

		return $this->db_table->generateDBUsingPK($query, $this->TablePrimaryKey, $link, NULL);
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

		unset($arr[ $this->TablePrimaryKey ]);
		return $arr;
	}

	public function getFieldTitle( $table_field, $table = NULL ) {

		$this->db->select('table_field_title');
		$this->db->where('table_field', $table_field);

		if ( empty($table) )
			$this->db->where('table_name',  $this->TableName);
		else 
			$this->db->where('table_name',  $table);

		$query = $this->db->get(self::metaTableName)->result_array();
		if ( empty($query) ) return $query;
		
		return $query[0]['table_field_title'];
	}

	public function getField( $table_field_title, $table = NULL) {
		

		$this->db->select('table_field');

		$this->db->where('table_field_title', $table_field_title);

		if ( empty($table) )
			$this->db->where('table_name',  $this->TableName);
		else 
			$this->db->where('table_name',  $table);

		$query = $this->db->get(self::metaTableName);

		if ( empty($query) ) return $query;
		$query = $query->result_array();

		if ( empty($query) ) return $query;
		return $query[0]['table_field'];
	}

	public function get()
	{
		return $this->db->get( $this->TableName);
	}

	public function convertFields( $fields, $table = NULL ) {
		$arr = array();
		foreach ($fields as $field) {
			$item = $this->getFieldTitle($field, $table);
			if ( empty($item) )  $item = '';
			array_push( $arr,  $item);
		}
		return $arr;
	}

	
	public function find ($search)
	{
		$this->db->reset_query();
		return $this->doFind($search) ? $this->get() : NULL;
	}

	public function doFind ($search)
	{
		$arr = array();
		if (!empty($search)){
			$queries = explode ( "," , $search);
			foreach ($queries as $search) {
				$search = explode ( ":" , $search);
				$field = $this->getField($search[0]);
				if (!empty($field) && !empty($search[1])) $arr[$field] = $search[1];
			}
		}
		foreach ($arr as $key => $value) {
			$this->db->or_where($key, $value);
		}
		return !empty($arr);
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


	public function makeTable($query)
	{
		if (empty($query)) return NULL;
		$fields = $query->list_fields();
		$headers = $this->convertFields($fields);

		$this->db_table->set_heading($headers);
		return $this->db_table->generate($query);
	}
}

?>