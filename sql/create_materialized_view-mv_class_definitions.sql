CREATE MATERIALIZED VIEW IF NOT EXISTS manifest.mv_class_definitions AS
	SELECT
		dcd.json->>'hash' class_hash,
		dcd.json->>'classType' class_type,
		dcd.json->'displayProperties'->>'name' class_name
	FROM manifest.t_manifest dcd
	WHERE dcd.deleted IS NULL
	AND dcd.table_name = 'DestinyClassDefinition'
	ORDER BY class_hash
WITH DATA;

CREATE INDEX idx_class_definition_class_hash ON mv_class_definitions USING btree (class_hash);