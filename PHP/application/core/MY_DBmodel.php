<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class MY_DBmodel extends CI_Model
{
	const searchTableName = 'fin_searches';
	const metaTableName = 'fin_db_meta';
	const fieldInputTypeField = 'table_field_input_type';
	const fieldTypes = ['TEXT', 'TEXTAREA', 'CHECKBOX', 'FLOAT', 'NUMBER', 'DATE'];

	public $ModelTitle = '';
	public $TableName = ''; // Overideable
	public $TablePrimaryKey = 'id'; // Overideable
	public $FieldPrefix = null;

	public $ReadOnlyFields = array();

	public $lastFindCount = 0;

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

		

		$this->load->model('registry_model');
	}

	public function init() {

		if (!$this->db->table_exists($this->TableName))
		{
			if ($this->willRegister)
			$this->registerModel();
		}

		$this->createTable();

		$this->createMetaTable();

		
	}

	private function registerModel() {

		$this->registry_model->registerModel(
			$this->ModelTitle,
			strtolower(get_class($this)),
			$this->isArrayModel?1:0,
			$this->TableName,
			$this->TablePrimaryKey,
			$this->FieldPrefix
		);
	}


	private function createMetaTable() {
		if (!($this->db->table_exists(self::metaTableName)))
		{
			$this->dbforge->add_field		("table_name VARCHAR(100) NOT NULL");
			$this->dbforge->add_field		("table_field TEXT NOT NULL");
			$this->dbforge->add_field		("table_field_title VARCHAR(100) NOT NULL");
			$this->dbforge->add_field		("table_field_derived TEXT DEFAULT NULL");
			$this->dbforge->add_field		("field_prefix VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		("field_suffix VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		( self::fieldInputTypeField . " VARCHAR(100) NOT NULL DEFAULT 'text'");
			$this->dbforge->create_table	( self::metaTableName);
		}
		if ( !($this->db->table_exists(self::searchTableName)) ) {
			$this->dbforge->add_field		("id");
			$this->dbforge->add_field		("table_name TEXT NOT NULL");
			$this->dbforge->add_field 		("query_title VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field 		("search_query TEXT DEFAULT ''");
			$this->dbforge->create_table	( self::searchTableName );
		}
	}

	public function createTable() {
		
	}

	public function registerFieldTitle( $table_field, $field_title, $inputType = 'TEXT', $presuff = ['', '']) {
		// Input Types: TEXT, TEXTAREA, CHECKBOX, DROPDOWN, RADIO, NUMBER

		$data = array(
		        'table_name' => $this->TableName,
		        'table_field' => $table_field,	
		        'table_field_title' => $field_title,
		        'table_field_input_type' => $inputType, 
		        'field_prefix' => $presuff[0],
		        'field_suffix' => $presuff[1]
		);
		return $this->db->insert(self::metaTableName, $data);

	}

	public function registerDerivedFieldTitle( $table_field, $field_title, $derivation, $presuff = ['', '']) {
		// Input Types: TEXT, TEXTAREA, CHECKBOX, DROPDOWN, RADIO, NUMBER
		$success = false;


		$data = array(
		        'table_name' => $this->TableName,
		        'table_field' => $table_field,	
		        'table_field_title' => $field_title,
		        'table_field_derived' => $derivation, 
		        'field_prefix' => $presuff[0],
		        'field_suffix' => $presuff[1]
		);
		return $this->db->insert(self::metaTableName, $data);

	}

	public function fieldExists( $table_field ) {
		$this->db->where( "table_field", $table_field );
		$this->db->where( "table_name", $this->TableName );
		return $this->db->get(self::metaTableName)->num_rows() > 0;


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

		//if ($hide_items) $arr = array_diff($arr, $this->fieldsToHide, array($this->TablePrimaryKey));
		return $arr;
	}

	public function getFieldAssociations() {
		$this->db->select('table_field, table_field_title, table_field_derived, field_prefix, field_suffix, '.self::fieldInputTypeField);
		$this->db->where('table_name', $this->TableName);

		$inp = $this->db->get(self::metaTableName)->result_array();
		$arr = array();
		foreach ($inp as $assoc) {
			$read_only = !empty($assoc['table_field_derived']) // is Derived 
						|| ($this->TablePrimaryKey == $assoc['table_field']) // is ID
						|| (
								isset($this->ReadOnlyFields['table_field']) ? // is Specified
								$this->ReadOnlyFields['table_field'] :
								FALSE
							);

			$arr[ $assoc['table_field'] ] = array(
				TBL_TITLE => $assoc['table_field_title'],
				TBL_INPUT => $assoc[self::fieldInputTypeField],
				RD_ONLY	  => $read_only,
				FLD_DERIVED => !empty($assoc['table_field_derived']),
				FLD_DERIVATION => !empty($assoc['table_field_derived'])?$assoc['table_field_derived']:null,
				'prefix' => $assoc['field_prefix'],
				'suffix' => $assoc['field_suffix']
			);
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

	public function select($fields = null) {
		$select = '';
		if ($fields == null) $fields = $this->getFieldAssociations();

		$first = true;
		foreach ($fields as $field => $info) {
			
			if (!$first) $select .= ', ';
			$first = false;
			$select .= $this->selectHeader($field, $info);
		}

		$this->db->select($select, false);
	}

	public function selectHeader($field, $info) {
		$select = '';
		if ($info[FLD_DERIVED]) {
			$select .= $info[FLD_DERIVATION];
			$select .= ' AS ';
			$select .= $field;
		} else {
			$select .= '`'.$this->TableName.'`.`'.$field.'`';
		}
		return $select;
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

	
	public function find ($search = null, $settings = [], $fields = null)
	{
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

	public function getSearches () {
		$this->db->where('table_name', $this->TableName);
		return $this->db->get(self::searchTableName);
	}

	public function saveSearch ($title, $search) {

		return $this->db->insert( self::searchTableName, 
			[
				'table_name' => $this->TableName,
				'query_title' => $title,
				'search_query' => json_encode($search)
			]
		);
	}

	public function removeSearch ($id) {
		$this->db->where('id', $id);
		$this->db->where('table_name', $this->TableName);
		return $this->db->delete( self::searchTableName);
	}

	public function updateSearch ($id, $search) {
		$this->db->where('id', $id);
		$this->db->where('table_name', $this->TableName);
		return $this->db->update( $this->TableName, ['search_query' => json_encode($search)]); 
	}

	public function insertIntoTable($data) {
		return $this->db->insert( $this->TableName, $data);
	}

	public function updateWithPK($id, $data) {
		$mdata = $data;
		unset($mdata[$this->TablePrimaryKey]);
		$this->db->where( $this->TablePrimaryKey, $id);
	    return $this->db->update( $this->TableName, $mdata); 
	}

	public function deleteWithPK($id) {
		$this->db->where( $this->TablePrimaryKey, $id);
	    return $this->db->delete( $this->TableName); 
	}

	public function getByPK($id, $fields = null) {
		$this->select($fields);
		$this->db->where( $this->TablePrimaryKey, $id);
	    return $this->db->get( $this->TableName)->row(); 
	}

	public function insertField($title, $kind, $default = null, $prefix = null, $suffix = null) {
		$field = str_replace(' ', '_', $title);
		$field = strtolower(
			($this->FieldPrefix!=null?$this->FieldPrefix:$this->TableName)
			.'_'.$field);

		if (!$this->fieldExists($field)) {
			$fieldset = array();

			switch ($kind) {
				case 'TEXT':
					$fieldset['type'] = 'VARCHAR';
					$fieldset['constraint'] = '100';
					break;
				case 'TEXTAREA':
					$fieldset['type'] = 'VARCHAR';
					$fieldset['constraint'] = '300';
					break;
				case 'CHECKBOX':
					$fieldset['type'] = 'BIT';
					$fieldset['constraint'] = '1';
					break;
				case 'FLOAT':
					$fieldset['type'] = 'NUMERIC';
					$fieldset['constraint'] = '12,2';
					break;
				case 'NUMBER':
					$fieldset['type'] = 'INT';
					$fieldset['constraint'] = '9';
					break;
				case 'DATE':
					$fieldset['type'] = 'DATE';
					break;
				default:
					$fieldset = null;
					break;
			}

			if ($default == null)
				$fieldset['null'] = TRUE;
			else
				$fieldset['default'] = $default;

			$ins = array( $field => $fieldset );

			if (empty($prefix)) $prefix = '';
			if (empty($suffix)) $suffix = '';

			return
				!$this->fieldExists($field)
				&& $this->dbforge->add_column($this->TableName, $ins) // if field exists skip this
				&& $this->registerFieldTitle($field, $title, $kind, [$prefix, $suffix]); // if column was not added skip this
		}
	}

	public function removeField($field) {
		$done = false;

		if ($field != $this->TablePrimaryKey) {
			
			
			$this->db->where( "table_name", $this->TableName );
			$this->db->like( "table_field_derived", '`'.$field.'`' );
			$this->db->delete( self::metaTableName );

			$this->db->where( "table_field", $field );
			$this->db->where( "table_name", $this->TableName );

			$done = $this->db->delete( self::metaTableName );



			$done = $done && $this->dbforge->drop_column( $this->TableName, $field );
		}

		return $done; 
	}

	public function insertDerivedField($title, $expression, $prefix = null, $suffix = null) {

		$selectValue = '';
		// field operand
		// [(field type: title: key: )(expression type: value: )(field)]
		$field = str_replace(' ', '_', $title);
		$field = strtolower(
			($this->FieldPrefix!=null?$this->FieldPrefix:$this->TableName)
			.'_'.$field);

		if (!$this->fieldExists($field)) {
			$allfields = $this->getFieldAssociations();

			if (empty($prefix)) $prefix = '';
			if (empty($suffix)) $suffix = '';

			foreach ($expression as $item) {
				if ($item['type'] == 'field') {
					if ($allfields[ $item['header'] ][FLD_DERIVED]) {
						$selectValue .= $allfields[ $item['header'] ][FLD_DERIVATION];
					} else {
						$selectValue .= '`'.$this->TableName.'`.`'.$item['header'].'`';
					}
				} else if ($item['type'] == 'operation') {
					$selectValue .= $item['value'];
				}
			}
			return $this->registerDerivedFieldTitle($field, $title, $selectValue, [$prefix, $suffix]);
		}
		return false;

		
	}

	public function registerSearchQuery($title, $query) {
		return $this->db->insert( self::searchTableName, 
			[
				'search_query'=>json_encode($query),
				'query_title'=>$title,
				'table_name'=>$this->TableName
			]
		);
	}

	/* ---------------------
	*	Static Functions
	*  ---------------------
	*/
}

?>