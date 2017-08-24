DROP TABLE IF EXISTS manifest.t_manifest;

CREATE TABLE manifest.t_manifest (
    table_name VARCHAR(100) NOT NULL,
    pk JSONB NOT NULL,
    json JSONB NOT NULL
);

GRANT SELECT, INSERT, UPDATE, DELETE, TRUNCATE ON manifest.t_manifest TO node_batch;