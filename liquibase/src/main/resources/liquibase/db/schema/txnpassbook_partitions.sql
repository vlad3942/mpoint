--
-- PostgreSQL database dump
--

-- Dumped from database version 10.9
-- Dumped by pg_dump version 12.3 (Debian 12.3-1.pgdg80+1)

-- Started on 2021-07-23 08:24:52 UTC

--- Txnpassbook partitions script for CEBU, AV, PAL, OD Client


------------------------------------------

Drop function if exists log.fn_generate_txnpassbook_partitions
    (p_ods_table character varying,p_client_id integer,p_nested character varying,p_dim_from bigint,p_dim_to bigint,p_size bigint);
CREATE OR REPLACE FUNCTION log.fn_generate_txnpassbook_partitions
(
p_ods_table character varying,
p_client_id integer,
p_nested character varying DEFAULT 'N' ,
p_dim_from bigint DEFAULT 1,
p_dim_to bigint DEFAULT 10000000,
p_size bigint DEFAULT 1000000
)
RETURNS void
LANGUAGE plpgsql
AS
$function$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure : log.fn_generate_txnpassbook_partitions()
		Version	  : v1.1
		Date		  : 2021-07-12
		Purpose 	  : Generates Partitions(Basic/Nested) for the configurations
		Author	      : CPM (SWE/Sarvesh)
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
declare
create_query text;
index_query text;
--v_p_ods_table varchar(50):=p_ods_table;
v_p_orig_table varchar(50):=p_ods_table;
v_Client_id varchar(200):=p_client_id;
v_Dim_From bigint:=p_dim_from;
v_Dim_To bigint:=p_dim_to;
v_size bigint:= p_size;
v_error_info text;
begin
IF p_nested='N' THEN
	create_query :=
		    'CREATE TABLE '
			||v_p_orig_table||'_'||v_Client_id
			||' PARTITION OF '
			||v_p_orig_table||'  FOR VALUES IN ('||v_Client_id||')';
			RAISE NOTICE 'create_query : %',create_query ;
EXECUTE create_query;
ELSIF  p_nested='Y' THEN
	create_query :=
		    'CREATE TABLE '
			||v_p_orig_table||'_'||v_Client_id
			||' PARTITION OF '
			||v_p_orig_table||'  FOR VALUES IN ('||v_Client_id||') PARTITION BY RANGE  (transactionid)';
			RAISE NOTICE 'create_query : %',create_query ;
EXECUTE create_query;
FOR create_query IN SELECT
                                'create table '
                                ||v_p_orig_table||'_'||v_Client_id||'_'||d||'_'||d+v_size
                                    ||' PARTITION OF '
                                    ||v_p_orig_table||'_'||v_Client_id
                                    ||' FOR VALUES FROM ('||d||') TO ('||d+v_size||')'
                    FROM	generate_Series(v_Dim_From,v_Dim_To,v_size) as d
                        LOOP
		RAISE NOTICE 'create_query : %',create_query ;
EXECUTE create_query;
END LOOP;


ELSIF  p_nested='-1' THEN
	create_query :=
		    'CREATE TABLE '
			||v_p_orig_table||'_'||v_Client_id
			||' PARTITION OF '
			||v_p_orig_table||'  FOR VALUES IN ('||v_Client_id||') PARTITION BY RANGE  (transactionid)';
		--	RAISE NOTICE 'create_query : %',create_query ;
		--	EXECUTE create_query;
FOR create_query IN SELECT
                                'create table '
                                ||v_p_orig_table||'_'||v_Client_id||'_'||d||'_'||d+v_size
                                    ||' PARTITION OF '
                                    ||v_p_orig_table||'_'||v_Client_id
                                    ||' FOR VALUES FROM ('||d||') TO ('||d+v_size||')'
                    FROM	generate_Series(v_Dim_From,v_Dim_To,v_size) as d
                        LOOP
		RAISE NOTICE 'create_query : %',create_query ;
EXECUTE create_query;
END LOOP;


END IF;
EXCEPTION
		   --Catch All Other Errors
		   WHEN OTHERS THEN

				v_error_info := '['||SQLSTATE ||']'||SQLERRM;--'Run Time Exception';
				RAISE WARNING '[log.fn_generate_txnpassbook_partitions] - UDP ERROR [OTHER] - SQLSTATE: %, SQLERRM: %', SQLSTATE, SQLERRM;

END;
$function$
;

-------------------------------------------

--select * from log.fn_add_primary_key_nested_partitions('log.txnpassbook_tbl',10018,1,20000000,1000000);
drop function if exists log.fn_add_primary_key_nested_partitions(p_ods_table character varying, p_client_id integer, p_dim_from bigint, p_dim_to bigint,p_size bigint);
CREATE OR REPLACE FUNCTION log.fn_add_primary_key_nested_partitions(p_ods_table character varying, p_client_id integer, p_dim_from bigint, p_dim_to bigint,p_size bigint)
RETURNS void
LANGUAGE plpgsql
AS
$function$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure : log.fn_add_primary_key_nested_partitions()
		Version	  : v1.2
		Date		  : 2021-07-15
		Purpose 	  : Create Primary Keys on the Nested Partitions for the configurations
		Author	      : CPM (SWE/Sarvesh)
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
declare
create_query text;
index_query text;

v_p_ods_table varchar(50):=p_ods_table;
v_index varchar(50):=substring(v_P_ODS_table,5);
v_Client_id int4:=p_client_id;
v_Dim_From bigint:=p_dim_from;
v_Dim_To bigint:=p_dim_to;
v_size bigint:= p_size;

v_error_info text;

begin

if v_Client_id in (10018,10020,10021,10069,10077,10101)
then

	FOR index_query IN SELECT
                                      'ALTER TABLE '
                                      ||v_P_ODS_table||'_'||v_Client_id||'_'||d||'_'||d+v_size
                                          ||' ADD PRIMARY KEY (id)'
                       FROM	generate_Series(v_Dim_From,v_Dim_To,v_size) as d
                              LOOP

			RAISE NOTICE 'index_query : %',index_query ;
EXECUTE index_query;

END LOOP;

else

	index_query :=
		    'ALTER TABLE '
			||v_P_ODS_table||'_'||v_Client_id
			||' ADD PRIMARY KEY (id)'	;

			RAISE NOTICE 'index_query : %',index_query ;
EXECUTE index_query;

end if;

EXCEPTION
		   --Catch All Other Errors
		   WHEN OTHERS THEN

				v_error_info := '['||SQLSTATE ||']'||SQLERRM;--'Run Time Exception';

				RAISE WARNING '[log.fn_add_primary_key_nested_partitions] - UDP ERROR [OTHER] - SQLSTATE: %, SQLERRM: %', SQLSTATE, SQLERRM;


END;
$function$
;


-------------------------------------------

Drop function if exists log.fn_generate_txnpassbook_indexes_nested_partitions(p_ods_table character varying, p_client_id integer, p_dim_from bigint, p_dim_to bigint,p_size bigint);
CREATE OR REPLACE FUNCTION log.fn_generate_txnpassbook_indexes_nested_partitions(p_ods_table character varying, p_client_id integer, p_dim_from bigint, p_dim_to bigint,p_size bigint)
RETURNS void
LANGUAGE plpgsql
AS
$function$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure : log.fn_generate_txnpassbook_indexes_nested_partitions()
		Version	  : v1.1
		Date		  : 2021-07-15
		Purpose 	  : Create Indexes on the Nested Partitions for the configurations
		Author	      : CPM (SWE/Sarvesh)
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
declare
create_query text;
index_query text;

v_P_ODS_table varchar(50):=p_ods_table;
v_index varchar(50):=substring(v_P_ODS_table,5);
v_Client_id int4:=p_client_id;
v_Dim_From bigint:=p_dim_from;
v_Dim_To bigint:=p_dim_to;
v_size bigint:= p_size;

v_error_info text;

begin

if v_Client_id in (10018,10020,10021,10069,10077,10101)
then

	FOR index_query IN SELECT
                                      'create index idx_'
                                      ||v_index||'_'||v_Client_id||'_'||d||'_'||d+v_size
                                          ||' ON '
                                          ||v_P_ODS_table||'_'||v_Client_id||'_'||d||'_'||d+v_size
                                          ||' USING btree (clientid, transactionid)'
                       FROM	generate_Series(v_Dim_From,v_Dim_To,v_size) as d
                              LOOP

			RAISE NOTICE 'index_query : %',index_query ;
EXECUTE index_query;

END LOOP;

else

	index_query := 'CREATE INDEX idx_'||v_index||'_'||v_Client_id
			||' ON '
			||v_P_ODS_table||'_'||v_Client_id
			||' USING btree (clientid, transactionid)'	;

			RAISE NOTICE 'index_query : %',index_query ;
EXECUTE index_query;

end if;

EXCEPTION
		   --Catch All Other Errors
		   WHEN OTHERS THEN

				v_error_info := '['||SQLSTATE ||']'||SQLERRM;--'Run Time Exception';

				RAISE WARNING '[log.fn_generate_txnpassbook_indexes_nested_partitions] - UDP ERROR [OTHER] - SQLSTATE: %, SQLERRM: %', SQLSTATE, SQLERRM;


END;
$function$
;


-------------------------------------------

Drop function if exists log.fn_add_txnpassbook_triggers
    (p_ods_table character varying, p_client_id integer,
    p_nested character varying, p_dim_from bigint, p_dim_to bigint , p_size bigint);
CREATE OR REPLACE FUNCTION log.fn_add_txnpassbook_triggers
(
p_ods_table character varying,
p_client_id integer,
p_nested character varying DEFAULT 'N' ,
p_dim_from bigint DEFAULT 1,
p_dim_to bigint DEFAULT 10000000,
p_size bigint DEFAULT 1000000
)
RETURNS void
LANGUAGE plpgsql
AS
$function$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure : log.fn_add_txnpassbook_triggers()
		Version	  : v1.1
		Date		  : 2021-07-12
		Purpose 	  : Add Transaction Passbook Triggers (for Basic/Nested Partitions) for the configurations
		Author	      : CPM (SWE/Sarvesh)
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
declare
create_query text;
index_query text;
trigger_query text;

v_p_ods_table varchar(50):=p_ods_table;
--v_p_orig_table varchar(50):=substring(v_p_ods_table,1,length(v_p_ods_table)-5);
v_Client_id varchar(200):=p_client_id;
v_Dim_From bigint:=p_dim_from;
v_Dim_To bigint:=p_dim_to;
v_size bigint:= p_size;

v_error_info text;

begin

IF p_nested='N' THEN

	trigger_query := 'create trigger update_txnpassbook_'||v_Client_id||' before update on '||v_p_ods_table||'_'||v_Client_id||' for each row execute procedure update_table_proc()';

			RAISE NOTICE 'trigger_query : %',trigger_query ;

EXECUTE trigger_query;

ELSIF  p_nested='Y' THEN

	FOR trigger_query IN SELECT
                                            'create trigger update_txnpassbook_'||v_Client_id||'_'||d||'_'||d+v_size||' before update on '
                                        ||v_p_ods_table||'_'||v_Client_id||'_'||d||'_'||d+v_size||' for each row execute procedure update_table_proc()'
                         FROM	generate_Series(v_Dim_From,v_Dim_To,v_size) as d
                                    LOOP

	RAISE NOTICE 'trigger_query : %',trigger_query ;
EXECUTE trigger_query;

END LOOP;

END IF;

EXCEPTION

		   --Catch All Other Errors
		   WHEN OTHERS THEN

				v_error_info := '['||SQLSTATE ||']'||SQLERRM;--'Run Time Exception';

				RAISE WARNING '[log.fn_add_txnpassbook_triggers] - UDP ERROR [OTHER] - SQLSTATE: %, SQLERRM: %', SQLSTATE, SQLERRM;


END;
$function$
;

-------------------------------------------
DROP function log.fn_generate_txnpassbook_publications(p_ods_table character varying, p_client_id integer, p_nested character varying, p_dim_from bigint,
    p_dim_to bigint, p_size bigint);
CREATE OR REPLACE FUNCTION log.fn_generate_txnpassbook_publications(p_ods_table character varying, p_client_id integer, p_nested character varying DEFAULT 'N'::character varying, p_dim_from bigint DEFAULT 1, p_dim_to bigint DEFAULT 10000000, p_size bigint DEFAULT 1000000)
 RETURNS void
 LANGUAGE plpgsql
AS $function$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure : log.fn_generate_txnpassbook_publications()
		Version	  : v1.1
		Date		  : 2021-07-12
		Purpose 	  : Generates Publications(for Basic/Nested Partitions) for the configurations
		Author	      : CPM (SWE/Sarvesh)
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
declare
create_query text;
index_query text;
publication_query text;

v_p_ods_table varchar(50):=p_ods_table;
--v_p_orig_table varchar(50):=substring(v_p_ods_table,1,length(v_p_ods_table)-5);
v_Client_id varchar(200):=p_client_id;
v_Dim_From bigint:=p_dim_from;
v_Dim_To bigint:=p_dim_to;
v_size bigint:= p_size;

v_error_info text;

begin

IF p_nested='N' THEN

	publication_query :=
		    'ALTER PUBLICATION mpoint_log_pub ADD TABLE '
			||v_p_ods_table||'_'||v_Client_id;

			RAISE NOTICE 'publication_query : %',publication_query ;

EXECUTE publication_query;

ELSIF  p_nested='Y' THEN

	FOR publication_query IN SELECT
                                                'ALTER PUBLICATION mpoint_log_pub ADD TABLE '
                                                ||v_p_ods_table||'_'||v_Client_id||'_'||d||'_'||d+v_size
                             FROM	generate_Series(v_Dim_From,v_Dim_To,v_size) as d
                                        LOOP

	RAISE NOTICE 'publication_query : %',publication_query ;
EXECUTE publication_query;

END LOOP;

END IF;

EXCEPTION

		   --Catch All Other Errors
		   WHEN OTHERS THEN

				v_error_info := '['||SQLSTATE ||']'||SQLERRM;--'Run Time Exception';

				RAISE WARNING '[log.fn_generate_txnpassbook_publications] - UDP ERROR [OTHER] - SQLSTATE: %, SQLERRM: %', SQLSTATE, SQLERRM;


END;
$function$
;

-------------------------------------------
drop function log.fn_add_txnpassbook_permissions
    (
    p_ods_table character varying,
    p_client_id integer,
    p_nested character varying,
    p_dim_from bigint,
    p_dim_to bigint,
    p_size bigint
    );
CREATE OR REPLACE FUNCTION log.fn_add_txnpassbook_permissions
(
p_ods_table character varying,
p_client_id integer,
p_nested character varying DEFAULT 'N' ,
p_dim_from bigint DEFAULT 1,
p_dim_to bigint DEFAULT 10000000,
p_size bigint DEFAULT 1000000
)
RETURNS void
LANGUAGE plpgsql
AS
$function$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure : log.fn_add_txnpassbook_permissions()
		Version	  : v1.2
		Date		  : 2021-07-12
		Purpose 	  : Add Transaction Passbook Permissions (for Basic/Nested Partitions) for the configurations
		Author	      : CPM (SWE/Sarvesh)
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
declare
create_query text;
index_query text;
trigger_query text;

v_p_ods_table varchar(50):=p_ods_table;
--v_p_orig_table varchar(50):=substring(v_p_ods_table,1,length(v_p_ods_table)-5);
v_Client_id varchar(200):=p_client_id;
v_Dim_From bigint:=p_dim_from;
v_Dim_To bigint:=p_dim_to;
v_size bigint:= p_size;

v_error_info text;

begin

--ALTER TABLE log.TXNPASSBOOK_TBL_PART OWNER TO mpoint;

IF p_nested='N' THEN

	trigger_query := 'ALTER TABLE '||v_p_ods_table||'_'||v_Client_id||' OWNER TO mpoint';

			RAISE NOTICE 'trigger_query : %',trigger_query ;

EXECUTE trigger_query;

ELSIF  p_nested='Y' THEN

	trigger_query := 'ALTER TABLE '||v_p_ods_table||'_'||v_Client_id||' OWNER TO mpoint';

			RAISE NOTICE 'trigger_query : %',trigger_query ;

EXECUTE trigger_query;


FOR trigger_query IN SELECT
                                 'ALTER TABLE '||v_p_ods_table||'_'||v_Client_id||'_'||d||'_'||d+v_size||' OWNER TO mpoint'
                     FROM	generate_Series(v_Dim_From,v_Dim_To,v_size) as d
                         LOOP

	RAISE NOTICE 'trigger_query : %',trigger_query ;
EXECUTE trigger_query;

END LOOP;

END IF;

EXCEPTION

		   --Catch All Other Errors
		   WHEN OTHERS THEN

				v_error_info := '['||SQLSTATE ||']'||SQLERRM;--'Run Time Exception';

				RAISE WARNING '[log.fn_add_txnpassbook_permissions] - UDP ERROR [OTHER] - SQLSTATE: %, SQLERRM: %', SQLSTATE, SQLERRM;


END;
$function$
;

-------------------------------------------

drop function if exists log.fn_add_txnpassbook_permissions_repuser
    (
    p_ods_table character varying,
    p_client_id integer,
    p_nested character varying,
    p_dim_from bigint,
    p_dim_to bigint,
    p_size bigint
    );
CREATE OR REPLACE FUNCTION log.fn_add_txnpassbook_permissions_repuser
(
p_ods_table character varying,
p_client_id integer,
p_nested character varying DEFAULT 'N' ,
p_dim_from bigint DEFAULT 1,
p_dim_to bigint DEFAULT 10000000,
p_size bigint DEFAULT 1000000
)
RETURNS void
LANGUAGE plpgsql
AS
$function$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure : log.fn_add_txnpassbook_permissions_repuser()
		Version	  : v1.1
		Date		  : 2021-07-12
		Purpose 	  : Add Transaction Passbook Permissions (for Basic/Nested Partitions) for the configurations
		Author	      : CPM (SWE/Sarvesh)
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
declare
create_query text;
index_query text;
trigger_query text;

v_p_ods_table varchar(50):=p_ods_table;
--v_p_orig_table varchar(50):=substring(v_p_ods_table,1,length(v_p_ods_table)-5);
v_Client_id varchar(200):=p_client_id;
v_Dim_From bigint:=p_dim_from;
v_Dim_To bigint:=p_dim_to;
v_size bigint:= p_size;

v_error_info text;

begin

--ALTER TABLE log.TXNPASSBOOK_TBL_PART OWNER TO mpoint;

IF p_nested='N' THEN

	trigger_query := 'GRANT SELECT ON '||v_p_ods_table||'_'||v_Client_id||'  TO repuser';

			RAISE NOTICE 'trigger_query : %',trigger_query ;

EXECUTE trigger_query;

ELSIF  p_nested='Y' THEN

	trigger_query := 'GRANT SELECT ON '||v_p_ods_table||'_'||v_Client_id||'  TO repuser';

			RAISE NOTICE 'trigger_query : %',trigger_query ;

EXECUTE trigger_query;


FOR trigger_query IN SELECT
                                 'GRANT SELECT ON '||v_p_ods_table||'_'||v_Client_id||'_'||d||'_'||d+v_size||' TO repuser'
                     FROM	generate_Series(v_Dim_From,v_Dim_To,v_size) as d
                         LOOP

	RAISE NOTICE 'trigger_query : %',trigger_query ;
EXECUTE trigger_query;

END LOOP;

END IF;

EXCEPTION

		   --Catch All Other Errors
		   WHEN OTHERS THEN

				v_error_info := '['||SQLSTATE ||']'||SQLERRM;--'Run Time Exception';

				RAISE WARNING '[log.fn_add_txnpassbook_permissions] - UDP ERROR [OTHER] - SQLSTATE: %, SQLERRM: %', SQLSTATE, SQLERRM;


END;
$function$
;
-------------------------------------------
