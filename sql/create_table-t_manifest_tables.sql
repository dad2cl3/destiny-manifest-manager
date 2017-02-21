DROP TABLE IF EXISTS manifest.t_manifest_tables;

CREATE TABLE manifest.t_manifest_tables (
	table_id SERIAL NOT NULL,
	table_name varchar(50) NOT NULL,
	normal_layout bool DEFAULT true,
	CONSTRAINT pk_table_id PRIMARY KEY (table_id)
);