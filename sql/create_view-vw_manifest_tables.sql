CREATE OR REPLACE VIEW manifest.vw_manifest_tables AS
	SELECT DISTINCT table_name
	FROM manifest.t_manifest
	WHERE deleted IS NULL;
