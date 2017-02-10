CREATE OR REPLACE FUNCTION manifest.fn_add_column (
	IN p_source_table varchar, IN p_source_field varchar,
	IN p_target_field varchar, IN p_target_data_type varchar, IN p_staging bool)
RETURNS TEXT 
AS
$BODY$
DECLARE
	v_manifest_table_id INTEGER DEFAULT 0;
	v_base_table_name VARCHAR;
	v_alter_table_sql VARCHAR;
	-- v_insert_data_sql VARCHAR;
	v_update_data_sql VARCHAR;
	v_insert_count INTEGER DEFAULT 0;
	v_update_count INTEGER DEFAULT 0;
	v_return_string TEXT;

BEGIN
	-- Retrieve the manifest table id
	SELECT table_id INTO v_manifest_table_id FROM manifest.t_manifest_tables
	WHERE table_name = p_source_table;

	IF v_manifest_table_id != 0 THEN

		-- Convert the manifest table name to the base table name
		v_base_table_name := manifest.fn_convert_table_name(p_source_table);

		-- Add the column
		v_alter_table_sql = 'ALTER TABLE manifest.' || v_base_table_name || ' ADD ' || p_target_field || ' ' || p_target_data_type;
		-- ALTER TABLE v_base_table_name ADD p_target_field p_target_data_type;
		v_return_string := '{"alter_sql":"' || v_alter_table_sql || '"';
		EXECUTE v_alter_table_sql;

		-- Add the field MAPPING
		INSERT INTO manifest.t_field_mappings (table_id, source_field, target_field, staging)
		VALUES (v_manifest_table_id, p_source_field, p_target_field, p_staging);

		GET DIAGNOSTICS v_insert_count = ROW_COUNT;
		v_return_string := v_return_string || ',"field_mapping":' || v_insert_count;

		-- Add the data
		v_update_data_sql := 'UPDATE manifest.' || v_base_table_name || ' SET ' || p_target_field || ' = (' || p_source_field || ')::' || p_target_data_type;
		-- UPDATE manifest.v_base_table_name
		-- SET p_target_field = p_source_field;
		v_return_string := v_return_string || ',"update_sql":"' || v_update_data_sql || '"';
		EXECUTE v_update_data_sql;

		GET DIAGNOSTICS v_update_count = ROW_COUNT;
		v_return_string := v_return_string || ',"update":' || v_update_count || '}';

	ELSE
		v_return_string := '{"proceed":"no"}';
	END IF;

	RETURN v_return_string;
END;
$BODY$
LANGUAGE plpgsql;