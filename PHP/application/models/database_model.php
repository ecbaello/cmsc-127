<?php

class Database_model extends CI_Model
{
	const DB_LabelMetaTableName = 'db_meta';

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->database();
		$this->load->dbforge();

		$this->makeMeta();
	}

	/**
	* Make PCF table if does not exists
	*
	*/
	public function makeMeta() {
		if (!($this->db->table_exists(self::DB_LabelMetaTableName)))
		{
			$this->dbforge->add_field		("table_name VARCHAR(100) NOT NULL");
			$this->dbforge->add_field		("table_field VARCHAR(100) NOT NULL");
			$this->dbforge->add_field		("table_field_title VARCHAR(100) NOT NULL");
			$this->dbforge->add_field		("table_field_inputs BOOLEAN NOT NULL DEFAULT TRUE");

			$this->dbforge->create_table	(self::DB_LabelMetaTableName);
		}
	}

	public function registerFieldTitle( $table_name, $table_field, $field_title, $isInput = true ) {

		$data = array(
		        'table_name' => $table_name,
		        'table_field' => $table_field,
		        'table_field_title' => $field_title,
		        'table_field_inputs' => $isInput
		);

		$this->db->insert(self::DB_LabelMetaTableName, $data);
	}

	public function getFields( $table ) {
		$this->db->select('table_field');
		$this->db->where('table_name', $table);
		$query = $this->db->get(self::DB_LabelMetaTableName)->result_array();
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

		$inp = $this->db->get(self::DB_LabelMetaTableName)->result_array();
		$arr = array();
		foreach ($inp as $assoc) {
			$arr[ $assoc['table_field'] ] = $assoc['table_field_title'];
		}
		return $arr;
	}

	public function getFieldTitle( $table_field ) {
		$this->db->select('table_field_title');
		$this->db->where('table_field', $table_field);
		$query = $this->db->get(self::DB_LabelMetaTableName)->result_array();
		if ( empty($query) ) return $query;
		return $query[0]['table_field_title'];
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

	public $template = array(
        'table_open'            => '<table border="0" cellpadding="4" cellspacing="0">',

        'thead_open'            => '<thead>',
        'thead_close'           => '</thead>',

        'heading_row_start'     => '<tr>',
        'heading_row_end'       => '</tr>',
        'heading_cell_start'    => '<th>',
        'heading_cell_end'      => '</th>',

        'tbody_open'            => '<tbody>',
        'tbody_close'           => '</tbody>',

        'row_start'             => '<tr>',
        'row_end'               => '</tr>',
        'cell_start'            => '<td>',
        'cell_end'              => '</td>',

        'row_alt_start'         => '<tr>',
        'row_alt_end'           => '</tr>',
        'cell_alt_start'        => '<td>',
        'cell_alt_end'          => '</td>',

        'table_close'           => '</table>'
	);

	public function makeTable($query)
	{
		$this->load->library('table');

		$this->table->set_template($this->template);

		$fields = $query->list_fields();
		$headers = $this->convertFields($fields);

		$this->table->set_heading($headers);

		return $this->table->generate($query);
	}

	public function makeTableWithDelete($table_name, $pk, $link)
	{
		$this->load->library('db_table');

		$query = $this->db->get($table_name);

		$fields = $query->list_fields();
		$headers = $this->convertFields($fields);

		$this->db_table->set_heading($headers);

		return $this->db_table->generateDBUsingPK($query, $pk, $link, $table_name);
	}

	public function getData($tableName)
	{
		return $this->db->get($tableName)->result_array();
	}
}

?>