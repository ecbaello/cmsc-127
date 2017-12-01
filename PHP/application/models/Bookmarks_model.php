<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bookmarks_model extends CI_Model
{
	const tableName = 'fin_user_bookmarks_model';

	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->database();
		
		$this->createTable();
	}

	public function createTable() {
		if (!($this->db->table_exists(self::tableName))) {

			$this->load->dbforge();

			$fields = array(
				'user' => array(
					'type' => 'INT',
					'constraint' => 11,
					'null' => true
				),
				'title' => array(
					'type' => 'VARCHAR',
					'constraint' => 100
				),
				'link' => array(
					'type' => 'TEXT',
					'null' => true
				)
	   		);

			$this->dbforge->add_field		($fields);
			$this->dbforge->create_table	(self::tableName);
		}
	}

	public function getCurrentUserId() {
		$this->load->library('ion_auth');
		if (!$this->ion_auth->logged_in()) return;

		$user = $this->ion_auth->user()->row();
		return $user->id;
	}

	public function getBookmarks($user = null) {
		if (empty($user)) $user = $this->getCurrentUserId();

		$this->db->select('title, link');
		$this->db->where('user', $user);
		return $this->db->get(self::tableName);
	}

	public function checkExists($title, $user = null) {
		if (empty($user)) $user = $this->getCurrentUserId();

		$this->db->where('title', $title);
		$this->db->where('user', $user);
		$query = $this->db->get(self::tableName);
		return $query->num_rows() > 0;
	}

	public function newBookmark($title, $link, $user = null) {
		if (empty($user)) $user = $this->getCurrentUserId();

		if (!$this->checkExists($title, $user))
			return $this->db->insert(self::tableName, [
				'user' => $user,
				'title' => $title,
				'link' => $link
			]);
	}

	public function deleteBookmark($title, $user = null) {
		if (empty($user)) $user = $this->getCurrentUserId();

		$this->db->where('title', $title);
		$this->db->where('user', $user);
		return $this->db->delete(self::tableName);
	}

}