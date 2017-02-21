CREATE TABLE manifest.t_manifest_manifest_tables (
	manifest_manifest_table_id SERIAL NOT NULL,
	manifest_id INTEGER NOT NULL,
	table_id INTEGER NOT NULL,
	CONSTRAINT pk_manifest_manifest_table_id PRIMARY KEY (manifest_manifest_table_id),
	CONSTRAINT fk_manifest_id FOREIGN KEY (manifest_id) REFERENCES manifest.t_manifest_version (manifest_id),
	CONSTRAINT fk_manifest_table_id FOREIGN KEY (table_id) REFERENCES manifest.t_manifest_tables (table_id)
);