create table detailed_charges (
	patient_id varchar(5),
	charge_id varchar(5) not null unique,
	ref_num varchar(8),
	date date not null,
	description varchar(30),
	quantity int(3),
	amount numeric(7,2) not null,
	Primary key (charge_id),
	Foreign key (patient_id) references Patient (patient_id) on update cascade
);