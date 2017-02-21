DROP TABLE IF EXISTS manifest.t_table_mappings;

CREATE TABLE manifest.t_table_mappings (
	table_mapping_id SERIAL NOT NULL,
	table_id integer,
	target_name varchar(50) NOT NULL,
	CONSTRAINT pk_table_mapping_id PRIMARY KEY (table_mapping_id),
	CONSTRAINT fk_table_mapping_table_id FOREIGN KEY (table_id)
		REFERENCES manifest.t_manifest_tables (table_id)
);