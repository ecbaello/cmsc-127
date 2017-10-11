create table pcf_request{
   pcf_rr_request_id SMALLINT NOT NULL UNIQUE,
   pcf_rr_date DATE NOT NULL,
   pcf_id SMALLINT,
   pcf_rr_particulars VARCHAR(100) DEFAULT ' ',
   pcf_rr_supporting_documents VARCHAR(100) DEFAULT ' ',
   pcf_rr_screening_training FLOAT NOT NULL DEFAULT 0.0,
   pcf_rr_meals_snacks FLOAT NOT NULL DEFAULT 0.0,
   pcf_rr_travel FLOAT NOT NULL DEFAULT 0.0,
   pcf_rr_office_supplies FLOAT NOT NULL DEFAULT 0.0,
   pcf_rr_communications FLOAT NOT NULL DEFAULT 0.0,
   pcf_rr_medical_supplies FLOAT NOT NULL DEFAULT 0.0,
   pcf_other_expenses FLOAT NOT NULL DEFAULT 0.0
   pcf_rr_op_desc VARCHAR(100) DEFAULT ' '
   primary key pcf_rr_request_id
}
