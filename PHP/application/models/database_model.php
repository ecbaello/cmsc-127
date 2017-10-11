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

			$this->dbforge->create_table	(self::DB_LabelMetaTableName);
		}
	}

	public function registerFieldTitle($table_name, $table_field, $field_title) {
		$data = array(
		        'table_name' => $table_name,
		        'table_field' => $table_field,
		        'table_field_title' => $field_title
		);

		$this->db->insert(self::DB_LabelMetaTableName, $data);
	}
}

?>