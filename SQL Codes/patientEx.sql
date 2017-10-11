create table patient_ex{
  foreign key (patient_data_patient_id) references patient_data (patient_id) on update cascade,
  primary key (patient_ex_transaction_id),
  patient_ex_hospital_bill FLOAT default 0.0,
  patient_ex_laboratory FLOAT default 0.0,
  patient_ex_medical_supplies FLOAT default 0.0,
  patient_ex_travel FLOAT default 0.0,
  patient_ex_meals FLOAT default 0.0,
  patient_ex_other FLOAT default 0.0,
  patient_ex_pat_counter NUMERIC default 0.0,
  patient_ex_op_desc VARCHAR(100) default ' ',
}
