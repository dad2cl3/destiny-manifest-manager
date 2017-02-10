CREATE OR REPLACE FUNCTION manifest.fn_truncate_manifest_data_tables()
RETURNS VARCHAR
AS
$BODY$
DECLARE
	c_tables CURSOR FOR
		SELECT 'manifest.' || target_name target_table
		FROM manifest.t_table_mapping
		ORDER BY target_name;

	v_table VARCHAR;

	v_return_string VARCHAR;
BEGIN
	v_return_string := 'something';

	FOR table_record IN c_tables LOOP
		EXECUTE 'TRUNCATE TABLE ' || table_record.target_table;
	END LOOP;

	RETURN v_return_string;
END;
$BODY$
	LANGUAGE plpgsql;