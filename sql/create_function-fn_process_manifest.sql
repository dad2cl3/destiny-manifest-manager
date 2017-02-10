CREATE OR REPLACE FUNCTION manifest.fn_process_manifest (IN p_manifest_version varchar)
RETURNS TEXT 
AS
$BODY$
DECLARE
	v_manifest_count INT DEFAULT 0;
	v_affected_count INT DEFAULT 0;
	v_manifest_id INT DEFAULT 0;
	v_return_string VARCHAR;
BEGIN
	SELECT COUNT(*) INTO v_manifest_count FROM manifest.t_manifest_version WHERE version = p_manifest_version;

	IF v_manifest_count = 0 THEN
		-- Update the previous version
		UPDATE manifest.t_manifest_version
		SET current = false
		WHERE current = true;

		GET DIAGNOSTICS v_affected_count = ROW_COUNT;

		-- Insert the new version
		INSERT INTO manifest.t_manifest_version (version) VALUES (p_manifest_version) RETURNING manifest_id INTO v_manifest_id;

		GET DIAGNOSTICS v_affected_count = ROW_COUNT;

		v_return_string := '{"process":"yes","manifest_id":' || v_manifest_id || '}';
	ELSE
		v_return_string := '{"process":"no"}';
	END IF;
		
	RETURN v_return_string;
END;
$BODY$
LANGUAGE plpgsql;