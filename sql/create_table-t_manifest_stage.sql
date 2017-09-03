DROP TABLE IF EXISTS manifest.t_manifest_stage;

CREATE TABLE manifest.t_manifest_stage (
	table_name VARCHAR(100) NOT NULL,
	pk JSONB NOT NULL,
	json JSONB NOT NULL
);

GRANT SELECT, INSERT, UPDATE, DELETE, TRUNCATE ON manifest.t_manifest_stage TO node_batch;