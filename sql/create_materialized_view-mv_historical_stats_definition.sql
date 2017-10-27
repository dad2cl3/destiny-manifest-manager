DROP MATERIALIZED VIEW IF EXISTS manifest.mv_historical_stats_definitions;

CREATE MATERIALIZED VIEW manifest.mv_historical_stats_definitions AS
	SELECT
		dhsd.json->>'statId' stat_id,
		dhsd.json->>'statName' stat_name
	FROM manifest.t_manifest dhsd
	WHERE dhsd.deleted IS NULL
	AND dhsd.table_name = 'DestinyHistoricalStatsDefinition'
	ORDER BY stat_id
WITH DATA;

CREATE INDEX idx_historical_stats_stat_id ON manifest.mv_historical_stats_definitions USING btree (stat_id);