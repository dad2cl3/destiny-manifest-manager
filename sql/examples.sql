/*
	The stats subquery expands the json array that stores the game modes within the json

	Ex. { "modes" : [1, 2, 3, 4, 5, 6, 7, 8, 9, 10] }

	The modes subquery pulls the game modes and joins them to the modes in the stats subquery

*/

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

/*

	The query below uses the manifest data to resolve hashes returned
	by the AggregateActivityStats endpoint

	The json @> { "key" : "value"} syntax looks for the key on the root of the json

*/

SELECT
	characterId,
	tas.activitycompletions,
	tm.json ->> 'activityName' activityName,
	tm.json ->> 'activityLevel' activityLevel,
	tm.json->'skulls' skulls,
	jsonb_array_length(tm.json->'skulls') modifiers,
	tm.json ->> 'activityTypeHash' activityTypeHash,
	-- tm.json -> 'activityDescription' activityDescription,
	datd.json ->> 'identifier' activityType,
	datd.json ->> 'activityTypeName' activityTypeName
FROM
	manifest.t_manifest tm,
	stg.t_activity_stats tas,
	(
		SELECT *
		FROM manifest.t_manifest
		WHERE
			t_manifest.table_name = 'DestinyActivityTypeDefinition'
	) datd
WHERE
	tm.table_name = 'DestinyActivityDefinition'
	AND datd.json @> ('{ "hash" : ' || (tm.json ->> 'activityTypeHash') || ' }')::jsonb
	AND tas.destinyid = 4611686018444413962 -- AND tas.characterid = 2305843009334426500
	AND tm.json @> ('{ "hash" : ' || tas.activityhash || ' }')::jsonb
	AND datd.json->>'identifier' IS NOT NULL
	AND (datd.json->>'activityTypeName') = 'Raid'
ORDER BY
	tas.characterId,
	tm.json ->> 'activityName',
	tm.json ->> 'activityLevel';