<?php

class MY_DBmodel extends CI_Model
{
	const metaTableName = 'db_meta';
	const modelTableName = 'model_registry';
	const fieldInputTypeField = 'table_field_input_type';

	protected $tabletemplate = array(
        'table_open'  => '<table class="table table-striped table-hover">'
	);


	public $ModelTitle = '';
	public $TableName = ''; // Overideable
	public $TablePrimaryKey = 'id'; // Overideable
	public $fieldsToHide = array ();
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
		
		$this->db->from(self::modelTableName);
		$this->db->where(MDL_CLASS, strtolower(get_class($this)));
		if($this->db->get()->num_rows() != 0) return;
		
		$this->db->insert(self::modelTableName,
			array(
				MDL_NAME => $this->ModelTitle,
				MDL_CLASS => strtolower(get_class($this)),
				MDL_ARRAY => $this->isArrayModel?1:0
			)
		);
	}


	private function createMetaTable() {
		if (!($this->db->table_exists(self::metaTableName)))
		{
			$this->dbforge->add_field		("table_name VARCHAR(100) NOT NULL");
			$this->dbforge->add_field		("table_field VARCHAR(100) NOT NULL");
			$this->dbforge->add_field		("table_field_title VARCHAR(100) NOT NULL");
			$this->dbforge->add_field		("table_field_inputs BOOLEAN NOT NULL DEFAULT TRUE");
			$this->dbforge->add_field		( self::fieldInputTypeField . " VARCHAR(100) NOT NULL DEFAULT 'text'");

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

	public function makeTableWithDelete($link)
	{
		$query = $this->get();
		$this->db_table->set_template($this->tabletemplate);
		return $this->db_table->generateDBUsingPK($query, $this->TablePrimaryKey, $link, NULL, $this->getFieldAssociations(false));
	}

	public function getFields($hide_items = false) {
		$this->db->select('table_field');
		$this->db->where('table_name', $this->TableName);

		$query = $this->db->get(self::metaTableName)->result_array();
		if ( empty($query) ) return $query;

		$arr = array();
		foreach ($query as $field) {
			$item = $field['table_field'];
			array_push( $arr,  $item);
		}

		if ($hide_items) $arr = array_diff($arr, $this->fieldsToHide, array($this->TablePrimaryKey));
		return $arr;
	}

	public function getFieldAssociations($hide_items = true) {
		$this->db->select('table_field, table_field_title, '. self::fieldInputTypeField);
		$this->db->where('table_name', $this->TableName);

		$inp = $this->db->get(self::metaTableName)->result_array();
		$arr = array();
		foreach ($inp as $assoc) {
			$arr[ $assoc['table_field'] ] = array(
				TBL_TITLE => $assoc['table_field_title'],
				TBL_INPUT => $assoc[self::fieldInputTypeField]
			);
		}

		if ($hide_items) {
			foreach ($this->fieldsToHide as $field) {
				unset($arr[$field]);
			}
			unset($arr[$this->TablePrimaryKey]);
		}
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
		$first = TRUE;
		$this->db->group_start();
		foreach ($arr as $key => $value) {
			if ($first) $this->db->like($key, $value);
			else $this->db->or_like($key, $value);
			$first = FALSE;
		}
		$this->db->group_end();
		return !empty($arr);
	}

	public function insertIntoTable($data) {
		return $this->db->insert( $this->TableName, $data);
	}

	public function updateWithPK($id, $data) {
		$mdata = $data;
		unset($mdata[$this->TablePrimaryKey]);
		$this->db->where( $this->TablePrimaryKey, $id);
	    $this->db->update( $this->TableName, $mdata); 
	}

	public function deleteWithPK($id) {
		$this->db->where( $this->TablePrimaryKey, $id);
	    return $this->db->delete( $this->TableName); 
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