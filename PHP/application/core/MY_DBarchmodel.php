<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class MY_DBArchmodel extends MY_DBmodel // lazy class for archive support
{

	private $altName = '';
	private $archiveMode = false;

	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		if ($this->willRegister)
			$this->createArchiveTable();
	}

	public function getFieldAssociations() {
		if ($this->archiveMode)
			return parent::getFieldAssociations($this->altName);
		return parent::getFieldAssociations();
	}

	public function isArchive() {
		return $archiveMode;
	}

	public function modelTableName() {
		if ($this->archiveMode) return $this->altName;
		return $this->TableName;
	}

	public function toggleArchive() {
		if ($this->archiveMode) {
			$this->TableName = $this->altName.'';
			$this->archiveMode = false;
		} else {
			$this->altName = $this->TableName.'';
			$this->TableName = $this->archiveTableName();
			$this->archiveMode = true;
		}
	}

	public function archiveTableName() {
		return $this->TableName."_arch";
	}

	public function createArchiveTable() {
		if (!$this->db->table_exists($this->archiveTableName())){
			// this is database dependent
			$fields = $this->db->field_data($this->TableName);

			$insertable = array();
			foreach ($fields as $field) {
				$setting = array(
						'null' => true
					);

				$setting['constraint'] = $field->max_length;
				$setting['type'] = $field->type;

				$insertable[$field->name] = $setting;
			}

			$this->dbforge->add_field ($insertable);
			$this->dbforge->create_table ($this->archiveTableName());
		}
	}

	public function insertIntoTable($data) {
		return $this->archiveMode || parent::insertIntoTable($data);
	}

	public function updateWithPK($data) {
		return $this->archiveMode || parent::updateWithPK($data);
	}

	public function deleteWithPK($id) {
		if (!$this->archiveMode) {
			$fromtable = $this->TableName;
			$totable = $this->archiveTableName();
			if (is_array($id)){
				$this->db->insert_batch($totable, $this->getByPK($id)->result_array());
			} else {
				$this->db->insert($totable, $this->getByPK($id)->row());
			}
		}
		return parent::deleteWithPK($id);
	}

	protected function addColumn($fields) {
		if ($this->archiveMode) return false;
		$add = parent::addColumn($fields);
		$add = $add && $this->dbforge->add_column($this->archiveTableName(), $fields);
		return $add;
	}

	public function removeField($field) {
		if ($this->archiveMode) return false;
		$remove = parent::removeField($field);
		$remove = $remove && $this->dbforge->drop_column($this->archiveTableName(), $field );
		return $remove;
	}

	public function setPrivate($ye) {
		return true;
	}

	public function isPrivate() {
		return true;
	}
}

?>