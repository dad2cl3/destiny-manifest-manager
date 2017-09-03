DROP TABLE IF EXISTS manifest.t_manifest;

CREATE TABLE manifest.t_manifest (
	effective_date DATE NOT NULL DEFAULT CURRENT_DATE,
	created TIMESTAMP(6) NOT NULL DEFAULT NOW(),
	created_by int8 NOT NULL,
	last_updated TIMESTAMP(6) NOT NULL DEFAULT NOW(),
	last_updated_by int8 NOT NULL,
	deleted TIMESTAMP(6) NULL,
	deleted_by int8,
	table_name VARCHAR(100) NOT NULL,
	pk JSONB NOT NULL,
	json JSONB NOT NULL,
	CONSTRAINT fk_t_manifest_created_by FOREIGN KEY (created_by) REFERENCES manifest.t_manifest_version (manifest_id),
	CONSTRAINT fk_t_manifest_last_updated_by FOREIGN KEY (last_updated_by) REFERENCES manifest.t_manifest_version (manifest_id),
	CONSTRAINT fk_t_manifest_deleted_by FOREIGN KEY (deleted_by) REFERENCES manifest.t_manifest_version (manifest_id)
);

CREATE INDEX  idx_t_manifest_json ON manifest.t_manifest USING gin(json) WITH (FASTUPDATE = YES);
CREATE INDEX  idx_t_manifest_pk ON manifest.t_manifest USING gin(pk) WITH (FASTUPDATE = YES);
CREATE INDEX  idx_t_manifest_table_name ON manifest.t_manifest USING btree(table_name);

CREATE TRIGGER trg_last_updated BEFORE UPDATE ON manifest.t_manifest FOR EACH ROW EXECUTE PROCEDURE fn_last_updated();

GRANT SELECT, INSERT, UPDATE, DELETE, TRUNCATE ON manifest.t_manifest TO node_batch;
-- GRANT SELECT ON manifest.t_manifest TO manifest_user;