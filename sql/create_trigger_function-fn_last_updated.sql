CREATE OR REPLACE FUNCTION manifest.fn_last_updated()
RETURNS TRIGGER
AS $BODY$

BEGIN
    NEW.last_updated = now();
    RETURN NEW;
END;
$BODY$
    LANGUAGE plpgsql;