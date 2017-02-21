DROP TABLE IF EXISTS manifest.t_manifest_version;

CREATE TABLE manifest.t_manifest_version (
	manifest_id SERIAL NOT NULL,
	effective_date date DEFAULT NOW(),
	version varchar(100) NOT NULL,
	current bool DEFAULT true,
	CONSTRAINT pk_manifest_id PRIMARY KEY (manifest_id)
);