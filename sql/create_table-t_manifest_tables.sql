DROP TABLE IF EXISTS manifest.t_manifest_tables;

CREATE TABLE manifest.t_manifest_tables (
	table_id SERIAL NOT NULL,
	manifest_id int4,
	table_name varchar(50) NOT NULL,
	record_count int4 NOT NULL DEFAULT 0,
	normal_layout bool DEFAULT true,
	CONSTRAINT t_manifest_tables_pkey PRIMARY KEY (table_id),
	CONSTRAINT fk_manifest_table_manifest_id FOREIGN KEY (manifest_id) REFERENCES manifest.t_manifest_version (manifest_id)
);