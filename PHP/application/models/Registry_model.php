<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Registry_model extends CI_Model
{
	const modelTableName = 'fin_model_registry';
	const systemTables = ["fin_db_meta","fin_db_meta_arch","fin_group_permissions","fin_groups","fin_login_attempts","fin_model_registry","fin_searches","fin_user_bookmarks_model","fin_users","fin_users_groups"];

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
	            ,
	            'table_array_table' => array(
	                'type' => 'TEXT',
	                'default'=>null
	            ),
	            'table_array_id' => array(
	                'type' => 'TEXT',
	                'default'=>null
	            ),
	            'table_array_title' => array(
	                'type' => 'TEXT',
	                'default'=>null
	            ),
	            'imported' => array(
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

	public function registerModel($title, $class, $array, $tablename, $pk, $prefix = null, $private = false, $arrayTable = null, $arrayId = null, $arrayTitle = null, $imported = false) {
		return $this->db->insert(self::modelTableName,
			array(
				MDL_NAME => $title,
				MDL_CLASS => $class,
				MDL_ARRAY => $array,
				'table_name' => $tablename,
				'table_prefix' => $prefix,
				'table_pk' => $pk,
				'private' => $private,
				'table_array_table' => $arrayTable,
				'table_array_id' => $arrayId,
				'table_array_title' => $arrayTitle,
				'imported' => $imported
			)
		);
	}

	public function models() {
		$this->db->select('table_name, table_pk, '.MDL_NAME);
		return $this->db->get(self::modelTableName);
	}

	public function customtable($table) {
		$this->db->where('table_name', $table);
		return $this->db->get(self::modelTableName);
	}

	public function loadable() {
		$this->db->where('mdl_class IS NULL', null, false);
		return $this->db->get(self::modelTableName);
	}

	public function customs() {
		$this->db->where('imported', 0);
		return $this->loadable();
	}

	public function imports() {
		$this->db->select('table_name, table_pk, '.MDL_NAME);
		$this->db->where('imported', 1);
		return $this->loadable();
	}

	public function setTablePrivate($table, $bl) {
		$this->db->where('table_name', $table);
		return $this->db->update(self::modelTableName, ['private' => $bl?1:0]);
	}

	public function tableIsPrivate($table) {
		$this->db->select('private');
		$this->db->where('table_name', $table);
		$query = $this->db->get(self::modelTableName)->row();
		return empty($query)||($query->private==1);
	}

	public function notRegistered() {
		$tables = $this->db->list_tables();

		$this->db->select('table_name');
		$result = $this->db->get(self::modelTableName)->result();
		$list_main = [];
		foreach ($result as $item) {
			array_push($list_main, $item->table_name);
		}

		$this->db->select('DISTINCT `table_array_table`', false);
		$result = $this->db->get(self::modelTableName)->result();
		foreach ($result as $item) {
			array_push($list_main, $item->table_array_table);
		}

		return array_values(array_diff($tables, $list_main, self::systemTables));
	}
}