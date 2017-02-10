CREATE OR REPLACE FUNCTION manifest.fn_convert_table_name (IN p_manifest_table_name varchar)
RETURNS VARCHAR
AS
$BODY$
DECLARE
	v_prefix VARCHAR := 'Destiny';
	v_suffix VARCHAR := 'Definition';
	v_stub VARCHAR;
	v_stub_length INTEGER;
	v_final_string VARCHAR;
	v_loop_count INTEGER := 0;
BEGIN
	v_stub := REPLACE(REPLACE(p_manifest_table_name, 'Destiny', ''), 'Definition', '');
	v_stub_length := LENGTH(v_stub);

	v_final_string := v_stub;

	FOR v_loop_count IN 2..v_stub_length LOOP
		IF ASCII(SUBSTRING(v_stub FROM v_loop_count FOR 1)) BETWEEN 65 AND 90 THEN
			v_final_string := LEFT(v_stub, v_loop_count - 1) || '_' || SUBSTRING(v_stub FROM v_loop_count FOR (v_stub_length - v_loop_count + 1));
		END IF;
	END LOOP;

	v_final_string := LOWER('t_' || v_final_string);

	RETURN v_final_string;
END;
$BODY$
	LANGUAGE plpgsql;