WITH stats AS (
	SELECT pk->>'key' stat_name, jsonb_array_elements_text(json->'modes') game_mode
	FROM manifest.t_manifest
	WHERE table_name = 'DestinyHistoricalStatsDefinition'
	
),
modes AS (
	SELECT json->>'modeType' game_mode, json->>'modeName' mode_name
	FROM manifest.t_manifest WHERE table_name = 'DestinyActivityModeDefinition'
	/*AND json->>'modeName' = 'Rumble'*/)
SELECT stats.stat_name, modes.mode_name
FROM stats, modes
WHERE stats.game_mode = modes.game_mode
ORDER BY modes.mode_name, stats.stat_name;