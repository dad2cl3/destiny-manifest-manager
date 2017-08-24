DROP SEQUENCE IF EXISTS manifest.seq_t_manifest_version_manifest_id CASCADE;

CREATE SEQUENCE manifest.seq_t_manifest_version_manifest_id;

GRANT USAGE ON SEQUENCE manifest.seq_t_manifest_version_manifest_id TO node_batch;