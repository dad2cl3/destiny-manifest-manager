CREATE OR REPLACE FUNCTION manifest.fn_load_manifest (IN pManifestId int8)
RETURNS VARCHAR
AS $BODY$
DECLARE
	vInserts INTEGER := 0;
	vUpdates INTEGER := 0;
	vDeletes INTEGER := 0;
	vResults VARCHAR;
BEGIN

	INSERT INTO manifest.t_manifest (
		created_by, last_updated_by, table_name, pk, json
		) (
		SELECT pManifestId created_by, pManifestId last_updated_by, table_name, pk, json
		FROM manifest.t_manifest_stage tms
		WHERE NOT EXISTS (
			SELECT 'x'
			FROM manifest.t_manifest tm
			WHERE tm.table_name = tms.table_name
			AND tm.pk = tms.pk
			-- AND tm.json = tms.json
		)
	);

	GET DIAGNOSTICS vInserts = ROW_COUNT;

	UPDATE manifest.t_manifest
	SET 
		json = t_manifest_stage.json,
		last_updated_by = pManifestId
	FROM manifest.t_manifest_stage
	WHERE 
		t_manifest.table_name = t_manifest_stage.table_name
		AND t_manifest.pk = t_manifest_stage.pk
		AND t_manifest.json != t_manifest_stage.json;

	GET DIAGNOSTICS vUpdates = ROW_COUNT;
	
	UPDATE
		manifest.t_manifest
	SET
		deleted = NOW(),
		deleted_by = 1
	WHERE deleted IS NULL AND
		NOT EXISTS (
			SELECT 'x'
			FROM manifest.t_manifest_stage
			WHERE t_manifest.table_name = t_manifest_stage.table_name
			AND t_manifest.pk = t_manifest_stage.pk);
	
	GET DIAGNOSTICS vDeletes = ROW_COUNT;

	vResults := '{ "inserts" : ' || vInserts;
	vResults := vResults || ', "updates" : ' || vUpdates;
	vResults := vResults || ', "deletes" : ' || vDeletes;
	vResults := vResults || '}';

	RETURN vResults;
END;
$BODY$
	LANGUAGE plpgsql;

ALTER FUNCTION manifest.fn_load_manifest (IN pManifestId int8) OWNER TO "YOUR-USER";