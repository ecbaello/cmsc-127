<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search_model extends CI_Model
{
	const TableName = 'fin_searches';

	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->database();
		$this->load->dbforge();

		$this->createTable();
	}

	public function createTable() {
		if ( !($this->db->table_exists(self::TableName)) ) {
			$this->dbforge->add_field		("id");
			$this->dbforge->add_field 		("user_id int DEFAULT 0");
			$this->dbforge->add_field		("table_name TEXT NOT NULL");
			$this->dbforge->add_field 		("query_title VARCHAR(100) DEFAULT ''");

			$this->dbforge->add_field 		("search_query TEXT DEFAULT ''");
			$this->dbforge->create_table	( self::TableName );
		}
	}

	protected function getUserId() {
		$this->load->library('ion_auth');
		if ($this->ion_auth->logged_in()) {
			return $this->ion_auth->user()->row()->id;
		}
		return 0;
	}

	public function registerSearch($tablename, $title, $expression, $userid = null) {
		if ($userid == null) {
			$userid = $this->getUserId();
		}

		return $this->db->insert(self::modelTableName,
			array(
				'table_name' => $tablename,
				'search_query' => $expression,
				'user_id' => $userid,
				'query_title' => $title
			)
		);
	}

	public function tableName () {
		return self::TableName;
	}

	public function searches($tablename = '', $userid = null) {
		if($userid == null) $userid = $this->getUserId();
		$tbl = empty($tablename);

		$this->db->select('id, query_title, search_query'.($tbl?', table_name':''));
		$this->db->where('user_id', $userid);
		if (!$tbl) $this->db->where('table_name', $tbl);
		return $this->db->get(self::TableName);
	}

	public function delete($pk) {
		$this->db->where('id', $pk);
		$this->db->delete(self::TableName);
	}

}