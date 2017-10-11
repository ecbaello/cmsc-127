create table pcf{
	pcf_id SMALLINT NOT NULL UNIQUE,
	pcf_type VARCHAR(100) NOT NULL DEFAULT '',
	pcf_partifculars varchar(100) default '',
	pcf_supporting_documents VARCHAR(100) DEFAULT '',
	pcf_screening_training FLOAT NOT NULL DEFAULT 0.0,
	pcf_meals_snacks FLOAT NOT NULL DEFAULT 0.0,
	pcf_travel FLOAT NOT NULL DEFAULT 0.0,
	pcf_office_supplies FLOAT NOT NULL DEFAULT 0.0,
	pcf_communications FLOAT NOT NULL DEFAULT 0.0,
	pcf_medical_supplies FLOAT NOT NULL DEFAULT 0.0,
	pcf_rr_other_expenses FLOAT NOT NULL DEFAULT 0.0,
	pcf_rr_op_desc VARCHAR(100) DEFAULT '',
	primary key pcf_id
}
