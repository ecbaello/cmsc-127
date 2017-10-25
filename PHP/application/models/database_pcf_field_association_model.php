<?php

class Database_pcf_field_association_model extends MY_DBmodel
{
	public $TableName = 'pcf_field_association';

	/**
	* The constructor method
	*
	*/

	public function createTable()
	{
		if (!($this->db->table_exists($this->TableName)))
		{
			$this->dbforge->add_field		("pcf_name varchar(30) not null default 'General'");
			$this->dbforge->add_field		("field varchar(100) not null default ''");
			$this->dbforge->create_table	($this->TableName);
			$this->resetDefaultAssociations();
		}
	}
	
	public function insertAssociation($pcfName,$fieldName){
		
		$this->db->from($this->TableName);
		$this->db->where('pcf_name',$pcfName);
		$this->db->where('field',$fieldName);
		
		if($this->db->get()->num_rows() == 0){
			$data = array(
				'field'=>$fieldName,
				'pcf_name'=>$pcfName
			);
			$this->db->insert($this->TableName,$data);
		}
		
	}
	
	public function resetDefaultAssociations(){
		
		$query = "truncate ".$this->TableName;
		$this->db->query($query);
		
		$query = "insert into ".$this->TableName." values
			('General','pcf_particulars'),
			('General','pcf_supporting_documents'),
			('General','pcf_screening_training'),
			('General','pcf_meals_snacks'),
			('General','pcf_travel'),
			('General','pcf_office_supplies'),
			('General','pcf_water'),
			('General','pcf_communications'),
			('General','pcf_other_expenses'),
			
			('Smile Train','pcf_particulars'),
			('Smile Train','pcf_supporting_documents'),
			('Smile Train','pcf_meals_snacks'),
			('Smile Train','pcf_travel'),
			('Smile Train','pcf_medical_supplies'),
			('Smile Train','pcf_other_expenses'),
			
			('Cataract','pcf_particulars'),
			('Cataract','pcf_supporting_documents'),
			('Cataract','pcf_meals_snacks'),
			('Cataract','pcf_travel'),
			('Cataract','pcf_medical_supplies'),
			('Cataract','pcf_other_expenses')
		";
		
		
		$this->db->query($query);

	}
}

?>