<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Registry_model extends CI_Model
{
	const modelTableName = 'fin_model_registry';

	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->database();
		$this->load->dbforge();

		$this->createTable();
	}

	public function createTable() {
		if (!($this->db->table_exists(self::modelTableName))) {

			$fields = array(
				'table_name' => array(
	                'type' => 'VARCHAR',
	                'constraint' => 100
	            ),
	            'link' => array(
	                'type' => 'TEXT',
	                'null' => true
	            ),
	            'table_prefix' => array(
	                'type' => 'VARCHAR',
	                'constraint' => 100,
	                'null' => true
	            ),
	            'table_pk' => array(
	                'type' => 'VARCHAR',
	                'constraint' => 100,
	                'default'=>'id'
	            ),
        		MDL_CLASS => array(
	                'type' => 'VARCHAR',
	                'constraint' => 100,
	                'null' => true
	            ),
	            MDL_NAME => array(
	                'type' => 'TEXT'
	            ),
	            MDL_ARRAY => array(
	                'type' => 'TINYINT',
	                'constraint' => 1
	            ),
	            'private' => array(
	                'type' => 'TINYINT',
	                'constraint' => 1,
	                'default'=>0
	            )
       		);

			$this->dbforge->add_field		($fields);
			$this->dbforge->add_key 		('table_name', TRUE);
			$this->dbforge->create_table	(self::modelTableName);
		}
	}

	public function registerModel($title, $class, $array, $tablename, $pk, $prefix = null, $private = false) {
		$this->db->insert(self::modelTableName,
			array(
				MDL_NAME => $title,
				MDL_CLASS => $class,
				MDL_ARRAY => $array,
				'table_name' => $tablename,
				'table_prefix' => $prefix,
				'table_pk' => $pk,
				'private' => $private
			)
		);
	}

	public function models() {
		$this->db->select('table_name, '.MDL_NAME);
		return $this->db->get(self::modelTableName);
	}

}