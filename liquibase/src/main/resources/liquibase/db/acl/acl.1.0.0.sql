DO $$
BEGIN
    CREATE ROLE mpoint;
    EXCEPTION WHEN duplicate_object THEN
        RAISE NOTICE '%, skipping', SQLERRM USING ERRCODE = SQLSTATE;
END
$$;

DO $$
BEGIN
    CREATE TRUSTED PROCEDURAL LANGUAGE 'plpgsql' HANDLER plpgsql_call_handler VALIDATOR plpgsql_validator;
    ALTER LANGUAGE plpgsql OWNER TO postgres;
    EXCEPTION WHEN duplicate_object THEN
        RAISE NOTICE '%, skipping', SQLERRM USING ERRCODE = SQLSTATE;
END
$$;