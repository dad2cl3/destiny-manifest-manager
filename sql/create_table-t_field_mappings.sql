DROP TABLE IF EXISTS manifest.t_field_mappings;

CREATE TABLE manifest.t_field_mappings (
	field_mapping_id SERIAL NOT NULL,
	table_id int4,
	source_field varchar(100),
	target_field varchar(100),
	staging bool DEFAULT false,
	CONSTRAINT t_field_mappings_pkey PRIMARY KEY (field_mapping_id),
	CONSTRAINT fk_field_mapping_table_id FOREIGN KEY (table_id)
		REFERENCES manifest.t_manifest_tables (table_id)
);