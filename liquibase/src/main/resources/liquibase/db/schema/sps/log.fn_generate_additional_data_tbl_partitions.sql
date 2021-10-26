Drop function if exists log.fn_generate_additional_data_tbl_partitions
    (p_ods_table character varying,p_context integer,p_nested character varying,p_period_from bigint,p_period_to bigint);
CREATE OR REPLACE FUNCTION log.fn_generate_additional_data_tbl_partitions
(
p_ods_table character varying,
p_context character varying,
p_nested character varying DEFAULT 'N' ,
p_period_from varchar DEFAULT'20190101',
p_period_to varchar DEFAULT'20200101'
)
RETURNS void
LANGUAGE plpgsql
AS
$function$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure  : log.fn_generate_additional_data_tbl_partitions()
		Version	    : v1.1
		Date		    : 2021-10-17
		Purpose 	: Generates Partitions(Basic/Nested) for the configurations
		Author	    : CPM (SWE/Sarvesh)
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
DECLARE

create_query text;
index_query text;
alter_query text;
default_query text;

--v_p_ods_table varchar(50):=p_ods_table;
v_p_orig_table varchar(50):=p_ods_table;
v_context varchar(200):=p_context;
v_period_from varchar(50):=p_period_from;
v_period_to varchar(50):=p_period_to;
v_error_info text;

BEGIN

IF p_nested='N' THEN
	create_query :=
		    'CREATE TABLE '
			||v_p_orig_table||'_'||v_context
			||' PARTITION OF '
			||v_p_orig_table||'  FOR VALUES IN ('||v_context||')';
			RAISE NOTICE 'create_query : %',create_query ;
EXECUTE create_query;

ELSIF  p_nested='Y' THEN

	create_query :=
		    'CREATE TABLE '
			||v_p_orig_table||'_'||v_context
			||' PARTITION OF '
			||v_p_orig_table||'  FOR VALUES IN ('||v_context||') PARTITION BY RANGE  (created)';
			RAISE NOTICE 'create_query : %',create_query ;
EXECUTE create_query;

FOR create_query IN SELECT
                                'create table '
                                ||v_p_orig_table||'_'||v_context||'_'||to_char(d,'YYYYMM')
                                ||' PARTITION OF '
                                ||v_p_orig_table||'_'||v_context
                                ||' FOR VALUES FROM ('||d||') TO ('||d+ interval '1 month'||')'
                    FROM	generate_Series(v_period_from::timestamp,v_period_to::timestamp,'1 month'::interval) as d
                        LOOP

                        RAISE NOTICE 'create_query : %',create_query ;
EXECUTE create_query;
END LOOP;


FOR alter_query IN SELECT
                           'ALTER TABLE '
                           ||v_p_orig_table||'_'||v_context||'_'||to_char(d,'YYYYMM')
                           ||' ADD CONSTRAINT uq_type_externalid_name_'||v_context||'_'||to_char(d,'YYYYMM')||' UNIQUE (type,externalid, name)'
                   FROM	generate_Series(v_period_from::timestamp,v_period_to::timestamp,'1 month'::interval) as d
                       LOOP

		RAISE NOTICE 'alter_query : %',alter_query ;
EXECUTE alter_query;
END LOOP;

--	CREATE TABLE Log.TxnPassbook_Tbl_10101_Default PARTITION OF Log.TxnPassbook_Tbl_10101 DEFAULT;


	default_query :=
		    'CREATE TABLE '
			||v_p_orig_table||'_'||v_context||'_DEFAULT'
			||' PARTITION OF '
			||v_p_orig_table||'_'||v_context ||' DEFAULT';
			RAISE NOTICE 'default_query : %',default_query ;
EXECUTE default_query;

/*
	FOR default_query IN SELECT
			'create table '
			||v_p_orig_table||'_'||v_context||'_'||to_char(d,'YYYYMM')||'_DEFAULT'
			||' PARTITION OF '
			||v_p_orig_table||'_'||v_context
			||' FOR VALUES FROM ('||d||') TO ('||d+ interval '1 month'||')'
	FROM	generate_Series(v_period_from::timestamp,v_period_to::timestamp,'1 month'::interval) as d
	LOOP

		RAISE NOTICE 'default_query : %',default_query ;
		EXECUTE default_query;
	END LOOP;

*/

ELSIF  p_nested='-1' THEN

/*	create_query :=
		    'CREATE TABLE '
			||v_p_orig_table||'_'||v_context
			||' PARTITION OF '
			||v_p_orig_table||'  FOR VALUES IN ('||v_context||') PARTITION BY RANGE  (created)';
		--	RAISE NOTICE 'create_query : %',create_query ;
		--	EXECUTE create_query; */
	FOR create_query IN SELECT
                                           'create table '
                                           ||v_p_orig_table||'_'||v_context||'_'||to_char(d,'YYYYMM')
                                           ||' PARTITION OF '
                                           ||v_p_orig_table||'_'||v_context
                                           ||' FOR VALUES FROM ('||d||') TO ('||d+ interval '1 month'||')'
                        FROM	generate_Series(v_period_from::timestamp,v_period_to::timestamp,'1 month'::interval) as d
                                   LOOP

                                   RAISE NOTICE 'create_query : %',create_query ;
EXECUTE create_query;
END LOOP;

FOR alter_query IN SELECT
                           'ALTER TABLE '
                           ||v_p_orig_table||'_'||v_context||'_'||to_char(d,'YYYYMM')
                           ||' ADD CONSTRAINT uq_type_externalid_name_'||v_context||'_'||to_char(d,'YYYYMM')||' UNIQUE (type,externalid, name)'
                   FROM	generate_Series(v_period_from::timestamp,v_period_to::timestamp,'1 month'::interval) as d
                       LOOP

		RAISE NOTICE 'alter_query : %',alter_query ;
EXECUTE alter_query;
END LOOP;

END IF;
EXCEPTION
		   --Catch All Other Errors
		   WHEN OTHERS THEN

				v_error_info := '['||SQLSTATE ||']'||SQLERRM;--'Run Time Exception';
				RAISE WARNING '[log.fn_generate_additional_data_tbl_partitions] - UDP ERROR [OTHER] - SQLSTATE: %, SQLERRM: %', SQLSTATE, SQLERRM;

END;
$function$
;