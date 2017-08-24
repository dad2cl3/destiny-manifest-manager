DROP TABLE IF EXISTS manifest.t_manifest_version;

CREATE TABLE manifest.t_manifest_version (
	manifest_id SERIAL,
	effective_date DATE DEFAULT CURRENT_DATE,
	version VARCHAR(50),
	path VARCHAR(100),
	current BOOLEAN DEFAULT TRUE
);
