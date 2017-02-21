CREATE TABLE manifest.t_manifest_history (
	manifest_version varchar(100) NOT NULL,
	effective_date date NOT NULL,
	table_name varchar(100) NOT NULL,
	record_count INTEGER NOT NULL,
	CONSTRAINT pk_manifest_version PRIMARY KEY (manifest_version)
);