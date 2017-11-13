<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Permission_model extends CI_Model {

	const alterPermission = 3;
	const changePermission = 2;
	const addPermission = 1;
	const viewPermission = 0;

	const tableName = 'fin_group_permissions';

	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->database();
		$this->load->dbforge();
		$this->load->library('ion_auth');

		$this->createTable();

		defined('PERMISSION_VIEW') OR define('PERMISSION_VIEW', 0);
		defined('PERMISSION_ADD') OR define('PERMISSION_ADD', 1);
		defined('PERMISSION_CHANGE') OR define('PERMISSION_CHANGE', 2);
		defined('PERMISSION_ALTER') OR define('PERMISSION_ALTER', 3);

		// group_id, table_name, permission
	}

	public function createTable() {
		if (!($this->db->table_exists(self::tableName)))
		{
			$this->dbforge->add_field		("table_name VARCHAR(100) NOT NULL");
			$this->dbforge->add_field		("group_id int NOT NULL");
			$this->dbforge->add_field		("permission int DEFAULT 0");
			$this->dbforge->create_table	( self::tableName);
		}
	}

	public function userPermission($table, $userid = null) {
		if ( $userid = null ) {
			if ( !$this->ion_auth->logged_in() ) return -1;
			if ( $this->ion_auth->is_admin() ) return 3;
			$userid = $this->ion_auth->user()->row()->id;
		}
		$groups = $this->ion_auth->get_users_groups($userid)->result();
		$maxPermission = -1;
		// for each group permission
		foreach ($groups as $group) {
			$grpid = $group->id;
			$permission = $this->groupPermission($table, $grpid);
			$maxPermission = max( $permission, $maxPermission );
		}

		log_message('debug', 'user: '.$maxPermission);
		// return max
		return $maxPermission;
	}

	public function groupPermission($table, $groupid, $returnNull = false) {
		if ($groupid == 1) return 3; // admin group

		$this->db->select('permission');
		$this->db->where('table_name', $table);
		$this->db->where('group_id', $groupid);
		$qry = $this->db->get(self::tableName);

		return $qry->num_rows() > 0 ? $qry->row()->permission : ($returnNull ? null : -1);
	}

	public function setPermission($table, $groupid, $permission) {
		$res = false;

		if ( $this->groupPermission($table, $groupid, true) == null ) {
			$res = $this->db->insert(self::tableName, [
				'table_name' => $table,
				'group_id' => $groupid,
				'permission' => $permission
			]);
		} else {
			$this->db->where('table_name', $table);
			$this->db->where('group_id', $groupid);
			$res = $this->db->update(self::tableName, [
				'permission' => $permission
			]);
		}
		return $res;
	}

	public function getPermissionGroups($≈ = [], $includeAdmin = false) {
		if (!empty($groupids))
			$this->db->where_in('group_id', $groupids);

		if ($includeAdmin)
			$this->db->where('group_id !=', 1); // admin

		$qry = $this->db->get(self::tableName);

		$array = [];

		foreach ($qry->result() as $permit) {
			$table = $permit->table_name;
			$group = $permit->group_id;

			if (!isset($array[ $table ])) {
				$array[ $table ] = [];
			}

			$array[ $table ][ $group ] = $permit->permission;
		}

		return $array;
	}

	public function groups() {
		$this->ion_auth->where('id !=','1');
		return $this->ion_auth->groups();
	}

	public function adminAllow() {
		return $this->ion_auth->is_admin();
	}
}
?>