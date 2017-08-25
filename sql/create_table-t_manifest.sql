DROP TABLE IF EXISTS manifest.t_manifest;

CREATE TABLE manifest.t_manifest (
    table_name VARCHAR(100) NOT NULL,
    pk JSONB NOT NULL,
    json JSONB NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_manifest_table_name ON manifest.t_manifest USING BTREE(table_name);
CREATE INDEX IF NOT EXISTS idx_manifest_json ON manifest.t_manifest USING GIN(json);
GRANT SELECT, INSERT, UPDATE, DELETE, TRUNCATE ON manifest.t_manifest TO node_batch;
-- GRANT SELECT ON manifest.t_manifest TO manifest_role;