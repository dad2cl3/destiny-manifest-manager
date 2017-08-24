DROP TABLE IF EXISTS manifest.t_manifest_version;

CREATE TABLE manifest.t_manifest_version (
	manifest_id INTEGER NOT NULL DEFAULT nextval('manifest.seq_t_manifest_version_manifest_id'),
	effective_date DATE NOT NULL DEFAULT CURRENT_DATE,
	version JSONB NOT NULL,
	current bool DEFAULT true,
	CONSTRAINT pk_manifest_id PRIMARY KEY (manifest_id)
);

GRANT SELECT, INSERT, UPDATE, DELETE, TRUNCATE ON manifest.t_manifest_version TO node_batch;

ALTER SEQUENCE manifest.seq_t_manifest_version_manifest_id OWNED BY manifest.t_manifest_version.manifest_id;