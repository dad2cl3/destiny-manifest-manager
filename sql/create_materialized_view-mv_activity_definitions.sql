DROP MATERIALIZED VIEW IF EXISTS manifest.mv_activity_definitions;

CREATE MATERIALIZED VIEW manifest.mv_activity_definitions AS
	SELECT
		(dad.json->>'hash')::BIGINT activity_hash,
		dad.json->'displayProperties'->>'name' activity_name,
		(dad.json->>'isPvP')::BOOLEAN pvp_flag
	FROM manifest.t_manifest dad
	WHERE dad.deleted IS NULL
	AND dad.table_name = 'DestinyActivityDefinition'
	ORDER BY activity_hash
WITH DATA;

CREATE INDEX idx_activity_definition_activity_hash ON mv_activity_definitions USING btree(activity_hash);
CREATE INDEX idx_ad_pvp_flag_activity_hash ON mv_activity_definitions USING btree (pvp_flag, activity_hash);