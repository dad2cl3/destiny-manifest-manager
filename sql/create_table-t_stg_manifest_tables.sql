DROP TABLE IF EXISTS manifest.t_stg_manifest_tables;

CREATE TABLE manifest.t_stg_manifest_tables (
	manifest_id int4,
	table_name varchar(100) NOT NULL,
	record_count int4 DEFAULT 0,
	CONSTRAINT fk_stg_manifest_table_manifest_id FOREIGN KEY (manifest_id)
		REFERENCES manifest.t_manifest_version (manifest_id)
);