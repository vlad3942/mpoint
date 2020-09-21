--
-- PostgreSQL database dump
--

-- Dumped from database version 10.9
-- Dumped by pg_dump version 12.3 (Debian 12.3-1.pgdg80+1)

-- Started on 2020-09-21 12:14:07 UTC

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 9 (class 2615 OID 16425)
-- Name: admin; Type: SCHEMA; Schema: -; Owner: mpoint
--

CREATE SCHEMA admin;


ALTER SCHEMA admin OWNER TO mpoint;

--
-- TOC entry 16 (class 2615 OID 16431)
-- Name: client; Type: SCHEMA; Schema: -; Owner: mpoint
--

CREATE SCHEMA client;


ALTER SCHEMA client OWNER TO mpoint;

--
-- TOC entry 18 (class 2615 OID 16434)
-- Name: enduser; Type: SCHEMA; Schema: -; Owner: mpoint
--

CREATE SCHEMA enduser;


ALTER SCHEMA enduser OWNER TO mpoint;

--
-- TOC entry 17 (class 2615 OID 16435)
-- Name: log; Type: SCHEMA; Schema: -; Owner: mpoint
--

CREATE SCHEMA log;


ALTER SCHEMA log OWNER TO mpoint;

--
-- TOC entry 19 (class 2615 OID 16437)
-- Name: system; Type: SCHEMA; Schema: -; Owner: mpoint
--

CREATE SCHEMA system;


ALTER SCHEMA system OWNER TO mpoint;

--
-- TOC entry 15 (class 2615 OID 16438)
-- Name: template; Type: SCHEMA; Schema: -; Owner: mpoint
--

CREATE SCHEMA template;


ALTER SCHEMA template OWNER TO mpoint;

--
-- TOC entry 805 (class 1247 OID 78263)
-- Name: additional_data_ref; Type: TYPE; Schema: log; Owner: mpoint
--

CREATE TYPE log.additional_data_ref AS ENUM (
    'Flight',
    'Passenger',
    'Order',
    'Transaction'
);


ALTER TYPE log.additional_data_ref OWNER TO mpoint;

--
-- TOC entry 887 (class 1247 OID 78268)
-- Name: address_tbl_ref; Type: TYPE; Schema: log; Owner: postgres
--

CREATE TYPE log.address_tbl_ref AS ENUM (
    'order',
    'transaction'
);


ALTER TYPE log.address_tbl_ref OWNER TO postgres;

--
-- TOC entry 504 (class 1255 OID 33260846)
-- Name: fn_add_primary_key_nested_partitions(character varying, integer, bigint, bigint, bigint); Type: FUNCTION; Schema: log; Owner: mpoint
--

CREATE FUNCTION log.fn_add_primary_key_nested_partitions(p_ods_table character varying, p_client_id integer, p_dim_from bigint, p_dim_to bigint, p_size bigint) RETURNS void
    LANGUAGE plpgsql
    AS $$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure : log.fn_add_primary_key_nested_partitions()
		Version	  : v1.1
		Date		  : 2020-12-12
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

if v_Client_id in (10018,10020,10021,10069)
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
$$;


ALTER FUNCTION log.fn_add_primary_key_nested_partitions(p_ods_table character varying, p_client_id integer, p_dim_from bigint, p_dim_to bigint, p_size bigint) OWNER TO mpoint;

--
-- TOC entry 505 (class 1255 OID 33315754)
-- Name: fn_add_txnpassbook_permissions(character varying, integer, character varying, bigint, bigint, bigint); Type: FUNCTION; Schema: log; Owner: mpoint
--

CREATE FUNCTION log.fn_add_txnpassbook_permissions(p_ods_table character varying, p_client_id integer, p_nested character varying DEFAULT 'N'::character varying, p_dim_from bigint DEFAULT 1, p_dim_to bigint DEFAULT 10000000, p_size bigint DEFAULT 1000000) RETURNS void
    LANGUAGE plpgsql
    AS $$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure : log.fn_add_txnpassbook_permissions()
		Version	  : v1.1
		Date		  : 2020-03-12
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
$$;


ALTER FUNCTION log.fn_add_txnpassbook_permissions(p_ods_table character varying, p_client_id integer, p_nested character varying, p_dim_from bigint, p_dim_to bigint, p_size bigint) OWNER TO mpoint;

--
-- TOC entry 503 (class 1255 OID 33315430)
-- Name: fn_add_txnpassbook_triggers(character varying, integer, character varying, bigint, bigint, bigint); Type: FUNCTION; Schema: log; Owner: mpoint
--

CREATE FUNCTION log.fn_add_txnpassbook_triggers(p_ods_table character varying, p_client_id integer, p_nested character varying DEFAULT 'N'::character varying, p_dim_from bigint DEFAULT 1, p_dim_to bigint DEFAULT 10000000, p_size bigint DEFAULT 1000000) RETURNS void
    LANGUAGE plpgsql
    AS $$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure : log.fn_add_txnpassbook_triggers()
		Version	  : v1.1
		Date		  : 2020-03-12
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
$$;


ALTER FUNCTION log.fn_add_txnpassbook_triggers(p_ods_table character varying, p_client_id integer, p_nested character varying, p_dim_from bigint, p_dim_to bigint, p_size bigint) OWNER TO mpoint;

--
-- TOC entry 499 (class 1255 OID 33260845)
-- Name: fn_generate_txnpassbook_indexes_nested_partitions(character varying, integer, bigint, bigint, bigint); Type: FUNCTION; Schema: log; Owner: mpoint
--

CREATE FUNCTION log.fn_generate_txnpassbook_indexes_nested_partitions(p_ods_table character varying, p_client_id integer, p_dim_from bigint, p_dim_to bigint, p_size bigint) RETURNS void
    LANGUAGE plpgsql
    AS $$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure : log.fn_generate_txnpassbook_indexes_nested_partitions()
		Version	  : v1.1
		Date		  : 2020-03-09
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

if v_Client_id in (10018,10020,10021,10069)
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
$$;


ALTER FUNCTION log.fn_generate_txnpassbook_indexes_nested_partitions(p_ods_table character varying, p_client_id integer, p_dim_from bigint, p_dim_to bigint, p_size bigint) OWNER TO mpoint;

--
-- TOC entry 500 (class 1255 OID 33260844)
-- Name: fn_generate_txnpassbook_nested_partitions(character varying, integer, bigint, bigint, bigint); Type: FUNCTION; Schema: log; Owner: mpoint
--

CREATE FUNCTION log.fn_generate_txnpassbook_nested_partitions(p_ods_table character varying, p_client_id integer, p_dim_from bigint, p_dim_to bigint, p_size bigint) RETURNS void
    LANGUAGE plpgsql
    AS $$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure : log.fn_generate_txnpassbook_nested_partitions()
		Version	  : v1.1
		Date		  : 2020-03-09
		Purpose 	  : Generates Nested Partitions for the configurations
		Author	      : CPM (SWE/Sarvesh)
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
declare
create_query text;
index_query text;

v_P_ODS_table varchar(50):=p_ods_table;
v_index varchar(50):=substring(v_P_ODS_table,5);
v_Client_id varchar(200):=p_client_id;
v_Dim_From bigint:=p_dim_from;
v_Dim_To bigint:=p_dim_to;
v_size bigint:= p_size;

v_error_info text;

begin
	
	FOR create_query IN SELECT
			'create table '
			||v_P_ODS_table||'_'||v_Client_id||'_'||d||'_'||d+v_size
			||' PARTITION OF '
			||v_P_ODS_table||'_'||v_Client_id
			||' FOR VALUES FROM ('||d||') TO ('||d+v_size||')'
	FROM	generate_Series(v_Dim_From,v_Dim_To,v_size) as d
	LOOP

	RAISE NOTICE 'create_query : %',create_query ;
		EXECUTE create_query;

	END LOOP;

/*
	FOR index_query IN SELECT
			'create index idx_'
			||v_index||'_'||v_Client_id||'_'||d||'_'||d+v_size-1
			||' ON '
			||v_P_ODS_table||'_'||v_Client_id||'_'||d||'_'||d+v_size-1
			||' USING btree (clientid, transactionid)'
	FROM	generate_Series(v_Dim_From,v_Dim_To,v_size) as d
	LOOP

	RAISE NOTICE 'index_query : %',index_query ;
	--	EXECUTE index_query;

	END LOOP;
*/

EXCEPTION

		   --Catch All Other Errors 
		   WHEN OTHERS THEN
			
				v_error_info := '['||SQLSTATE ||']'||SQLERRM;--'Run Time Exception';

				RAISE WARNING '[log.fn_generate_txnpassbook_nested_partitions] - UDP ERROR [OTHER] - SQLSTATE: %, SQLERRM: %', SQLSTATE, SQLERRM;

	
END;
$$;


ALTER FUNCTION log.fn_generate_txnpassbook_nested_partitions(p_ods_table character varying, p_client_id integer, p_dim_from bigint, p_dim_to bigint, p_size bigint) OWNER TO mpoint;

--
-- TOC entry 496 (class 1255 OID 33260835)
-- Name: fn_generate_txnpassbook_partitions(character varying, integer, character varying, bigint, bigint, bigint); Type: FUNCTION; Schema: log; Owner: mpoint
--

CREATE FUNCTION log.fn_generate_txnpassbook_partitions(p_ods_table character varying, p_client_id integer, p_nested character varying DEFAULT 'N'::character varying, p_dim_from bigint DEFAULT 1, p_dim_to bigint DEFAULT 10000000, p_size bigint DEFAULT 1000000) RETURNS void
    LANGUAGE plpgsql
    AS $$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure : log.fn_generate_txnpassbook_partitions()
		Version	  : v1.1
		Date		  : 2020-03-09
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
	
	
END IF;

EXCEPTION

		   --Catch All Other Errors 
		   WHEN OTHERS THEN
			
				v_error_info := '['||SQLSTATE ||']'||SQLERRM;--'Run Time Exception';

				RAISE WARNING '[log.fn_generate_txnpassbook_partitions] - UDP ERROR [OTHER] - SQLSTATE: %, SQLERRM: %', SQLSTATE, SQLERRM;

	
END;
$$;


ALTER FUNCTION log.fn_generate_txnpassbook_partitions(p_ods_table character varying, p_client_id integer, p_nested character varying, p_dim_from bigint, p_dim_to bigint, p_size bigint) OWNER TO mpoint;

--
-- TOC entry 502 (class 1255 OID 33320842)
-- Name: fn_generate_txnpassbook_publications(character varying, integer, character varying, bigint, bigint, bigint); Type: FUNCTION; Schema: log; Owner: mpoint
--

CREATE FUNCTION log.fn_generate_txnpassbook_publications(p_ods_table character varying, p_client_id integer, p_nested character varying DEFAULT 'N'::character varying, p_dim_from bigint DEFAULT 1, p_dim_to bigint DEFAULT 10000000, p_size bigint DEFAULT 1000000) RETURNS void
    LANGUAGE plpgsql
    AS $$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure : log.fn_generate_txnpassbook_publications()
		Version	  : v1.1
		Date		  : 2020-03-09
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
$$;


ALTER FUNCTION log.fn_generate_txnpassbook_publications(p_ods_table character varying, p_client_id integer, p_nested character varying, p_dim_from bigint, p_dim_to bigint, p_size bigint) OWNER TO mpoint;

--
-- TOC entry 497 (class 1255 OID 33260849)
-- Name: fn_migrate_txnpassbook_partition(bigint, bigint, bigint); Type: FUNCTION; Schema: log; Owner: mpoint
--

CREATE FUNCTION log.fn_migrate_txnpassbook_partition(p_clientid bigint, p_start bigint, p_end bigint) RETURNS void
    LANGUAGE plpgsql
    AS $$
/*----------------------------------------------------------------------------------------------
		Procedure : log.fn_migrate_txnpassbook_partition()
		Version	  : v1.0
		Date		  : 2020-03-08
		Purpose 	  : fn_migrate_txnpassbook_partition based on passbookids
		Author	      : CPM (SWE/Sarvesh)
		Accepts     : Client id , Start Passbook id & End Passbook id 
		Flags	      :  1 - Success , 0 - Failure
------------------------------------------------------------------------------------------------*/
declare


	v_rows_processed_txnpassbook_tbl int8;
	
	v_cnt_txnpassbook_tbl int8:= 0;

	v_etl_status_flag int4;
	v_error_info varchar(1000);
	v_error_info1 varchar(1000);
	
	v_start  bigint :=p_start;
	v_end   bigint :=p_end;

	v_clientid bigint := p_clientid;

begin

SET LOCAL TEMP_BUFFERS='786MB';

	 RAISE NOTICE 'v_clientid: %',    v_clientid;
	 	 RAISE NOTICE 'v_start: %',    v_start;
		 	 RAISE NOTICE 'v_end: %',    v_end;

	insert into log.txnpassbook_tbl_part 
		(		
				id,
				clientid,
				transactionid,
				amount,
				currencyid,
				requestedopt,
				performedopt,
				status,
				extref,
				extrefidentifier,
				enabled,
				created,
				modified
		) 
		select
					tp.id,
					tt.clientid,
					tp.transactionid,
					tp.amount,
					tp.currencyid,
					tp.requestedopt,
					tp.performedopt,
					tp.status,
					tp.extref,
					tp.extrefidentifier,
					tp.enabled,
					tp.created,
					tp.modified
		from
					log.txnpassbook_tbl tp
		inner join  log.transaction_tbl tt 
		on (tp.transactionid=tt.id and tt.clientid=v_clientid) --61271
		and tp.id between v_start and v_end;  --61271

    GET DIAGNOSTICS v_rows_processed_txnpassbook_tbl = ROW_COUNT;

	 RAISE NOTICE 'v_rows_processed_txnpassbook_tbl: %',    v_rows_processed_txnpassbook_tbl;

	 				v_etl_status_flag := 1.;
SET LOCAL TEMP_BUFFERS=default;

EXCEPTION

		   --Catch All Other Errors 
		   WHEN OTHERS THEN

						GET STACKED DIAGNOSTICS v_error_info = MESSAGE_TEXT,
                          v_error_info1 = PG_EXCEPTION_DETAIL;
						  			   RAISE NOTICE 'v_error_info: %', v_error_info;
										RAISE NOTICE 'v_error_info1: %', v_error_info1;							
			
				v_cnt_txnpassbook_tbl := -1;
				v_etl_status_flag := 0.;
				v_error_info := '['||SQLSTATE ||']'||SQLERRM;--'Run Time Exception';

				RAISE WARNING '[log.fn_migrate_txnpassbook_partition] - UDP ERROR [OTHER] - SQLSTATE: %, SQLERRM: %', SQLSTATE, SQLERRM;

	
END;
$$;


ALTER FUNCTION log.fn_migrate_txnpassbook_partition(p_clientid bigint, p_start bigint, p_end bigint) OWNER TO mpoint;

--
-- TOC entry 506 (class 1255 OID 33260852)
-- Name: fn_sync_txn_updates(integer, bigint, bigint); Type: FUNCTION; Schema: log; Owner: mpoint
--

CREATE FUNCTION log.fn_sync_txn_updates(p_client_id integer, p_id_start bigint, p_id_end bigint) RETURNS void
    LANGUAGE plpgsql
    AS $$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure : log.fn_sync_txn_updates()
		Version	  : v1.1
		Date		  : 2020-03-10
		Purpose 	  : Syncs updates for the client between the ranges
		Author	      : CPM (SWE/Sarvesh)
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
declare
create_query text;
index_query text;

v_rows_processed bigint;

v_Client_id int4:=p_client_id;
--v_Dim_From bigint:=p_dim_from;
--v_Dim_To bigint:=p_dim_to;
--v_size bigint:= p_size;

v_error_info text;

begin
/*
For each client:  (if required consider 15 days or 7 days batch)
2019-11-01 =>  7611338 (batch 5)
2019-12-01 => 10127766 (batch 4)
2020-01-01 => 12773455 (batch 3)
2020-02-01 => 15964034 (batch 2)
2020-03-01 => 18625332 (batch 1)
		   => 20043242 (select max(id)
						from log.txnpassbook_tbl tp
						where tp.created >= '2020-03-01')
*/

set local temp_buffers='512MB';
set local work_mem='512MB';

with delta_update 
as
(
select tp.id,tp.status,tp.modified from log.txnpassbook_tbl tp
inner join  log.transaction_tbl tt 
on (tp.transactionid=tt.id and tt.clientid=p_client_id) --61271
where 
tp.id between p_id_start and p_id_end
--and tp.id=77818
except
select tpp.id,tpp.status,tpp.modified from log.txnpassbook_tbl_part tpp
where tpp.clientid=p_client_id
and tpp.id between p_id_start and p_id_end
--and tp.id=77818
)
update log.txnpassbook_tbl_part tpp
set (status,modified)=(select status, modified from delta_update where id=tpp.id)
where clientid=p_client_id
and tpp.id between p_id_start and p_id_end
and exists (select 1 from delta_update where id=tpp.id);

GET DIAGNOSTICS v_rows_processed = ROW_COUNT;

			RAISE NOTICE 'p_client_id : %',p_client_id ;
			RAISE NOTICE 'p_id_start : %',p_id_start ;
						RAISE NOTICE 'p_id_end : %',p_id_end ;
						RAISE NOTICE 'v_rows_processed : %',v_rows_processed ;

set local temp_buffers=default;
set local work_mem=default;


EXCEPTION
		   --Catch All Other Errors 
		   WHEN OTHERS THEN
			
				v_error_info := '['||SQLSTATE ||']'||SQLERRM;--'Run Time Exception';

				RAISE WARNING '[log.fn_sync_txn_updates] - UDP ERROR [OTHER] - SQLSTATE: %, SQLERRM: %', SQLSTATE, SQLERRM;
	
END;
$$;


ALTER FUNCTION log.fn_sync_txn_updates(p_client_id integer, p_id_start bigint, p_id_end bigint) OWNER TO mpoint;

--
-- TOC entry 482 (class 1255 OID 16445)
-- Name: const_date_proc(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: mpoint
--

CREATE FUNCTION public.const_date_proc(integer, integer, integer) RETURNS date
    LANGUAGE plpgsql
    AS $_$
DECLARE
	-- Declare aliases for input
	in_year ALIAS FOR $1;
	in_month ALIAS FOR $2;
	in_day ALIAS FOR $3;
BEGIN
	RETURN in_year || '-' || in_month || '-' || in_day;
END;
$_$;


ALTER FUNCTION public.const_date_proc(integer, integer, integer) OWNER TO mpoint;

--
-- TOC entry 507 (class 1255 OID 16449)
-- Name: modify_endusertxn_proc(); Type: FUNCTION; Schema: public; Owner: mpoint
--

CREATE FUNCTION public.modify_endusertxn_proc() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
	iAccountID INT4;
	iTypeID INT4;
BEGIN
	IF TG_OP = 'DELETE' THEN
		iAccountID := OLD.accountid;
		iTypeID := OLD.typeid;
	ELSE
		iAccountID := NEW.accountid;
		iTypeID := NEW.typeid;
	END IF;
	
	-- Update available balance on EndUser's e-Money based account
	IF 1000 <= iTypeID AND iTypeID <= 1003 THEN
		UPDATE EndUser.Account_Tbl
		SET balance = (SELECT (Sum(amount) + Sum(Abs(fee) * -1) )
					   FROM EndUser.Transaction_Tbl
					   WHERE accountid = iAccountID AND 1000 <= typeid AND typeid <= 1003 AND enabled = true AND stateid != 1809)
		WHERE id = iAccountID;
	-- Update available balance on EndUser's loyalty account
	ELSIF 1004 <= iTypeID AND iTypeID <= 1007 THEN
		UPDATE EndUser.Account_Tbl
		SET points = (SELECT (Sum(amount) + Sum(Abs(fee) * -1) )
					   FROM EndUser.Transaction_Tbl
					   WHERE accountid = iAccountID AND 1004 <= typeid AND typeid <= 1007 AND enabled = true AND stateid != 1809)
		WHERE id = iAccountID;
	END IF;
	
	IF TG_OP = 'DELETE' THEN
		RETURN OLD;
	ELSE
		NEW.Modified := NOW();
		RETURN NEW;
	END IF;
END;
$$;


ALTER FUNCTION public.modify_endusertxn_proc() OWNER TO mpoint;

--
-- TOC entry 501 (class 1255 OID 16450)
-- Name: modify_transfer_proc(); Type: FUNCTION; Schema: public; Owner: mpoint
--

CREATE FUNCTION public.modify_transfer_proc() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
	iAccountID INT4;
BEGIN
	IF TG_OP = 'DELETE' THEN
		iAccountID := OLD.accountid;
	ELSE
		iAccountID := NEW.accountid;
	END IF;
	
	-- Update available balance on EndUser's Account
	UPDATE EndUser.Account_Tbl
	SET balance = (SELECT Sum(amount)
				   FROM EndUser.Transfer_Tbl
				   WHERE accountid = iAccountID AND enabled = true)
	WHERE id = iAccountID;
	
	IF TG_OP = 'DELETE' THEN
		RETURN OLD;
	ELSE
		NEW.Modified := NOW();
		RETURN NEW;
	END IF;
END;
$$;


ALTER FUNCTION public.modify_transfer_proc() OWNER TO mpoint;

--
-- TOC entry 508 (class 1255 OID 16451)
-- Name: nextvalue(character varying); Type: FUNCTION; Schema: public; Owner: mpoint
--

CREATE FUNCTION public.nextvalue(character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
	-- Declare aliases for input
	sequence ALIAS FOR $1;
	num INT4;
BEGIN
	EXECUTE 'SELECT Nextval('''|| sequence || ''')' INTO num;
	
	RETURN num;
END;
$_$;


ALTER FUNCTION public.nextvalue(character varying) OWNER TO mpoint;

--
-- TOC entry 498 (class 1255 OID 36072358)
-- Name: no_ddl(); Type: FUNCTION; Schema: public; Owner: mpoint
--

CREATE FUNCTION public.no_ddl() RETURNS event_trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    r RECORD;
    tables text[] := '{' ||
                     '"system.producttype_tbl", ' ||
                     '"system.country_tbl", ' ||
                     '"system.currency_tbl", ' ||
                     '"system.flow_tbl", ' ||
                     '"system.sessiontype_tbl", ' ||
                     '"system.psp_tbl", ' ||
                     '"system.processortype_tbl", ' ||
                     '"system.type_tbl", ' ||
                     '"system.card_tbl", ' ||
                     '"system.paymenttype_tbl", ' ||
                     '"system.urltype_tbl", ' ||
                     '"system.iinaction_tbl", ' ||
                     '"system.cardstate_tbl", ' ||
                     '"system.triggerunit_tbl", ' ||
                     '"system.externalreferencetype_tbl", ' ||
                     '"log.message_tbl", ' ||
                     '"log.transaction_tbl", ' ||
                     '"log.state_tbl", ' ||
                     '"log.session_tbl", ' ||
                     '"log.settlement_tbl", ' ||
                     '"log.settlement_record_tbl", ' ||
                     '"log.txnpassbook_tbl", ' ||
                     '"log.externalreference_tbl", ' ||
                     '"client.account_tbl", ' ||
                     '"client.client_tbl", ' ||
                     '"client.keyword_tbl", ' ||
                     '"client.merchantaccount_tbl", ' ||
                     '"client.cardaccess_tbl", ' ||
                     '"client.iinlist_tbl", ' ||
                     '"client.additionalproperty_tbl", ' ||
                     '"client.gatewaytrigger_tbl", ' ||
                     '"client.product_tbl", ' ||
                     '"client.url_tbl", ' ||
                     '"enduser.account_tbl"' ||
                     '}';
BEGIN
    FOR r IN SELECT * FROM pg_event_trigger_ddl_commands() LOOP
            IF (select r.objid::regclass::text = ANY(tables))
            THEN
                RAISE EXCEPTION 'You are not allowed to change %. Please check with DWH team.', r.object_identity;
            END IF;
        END LOOP;
END
$$;


ALTER FUNCTION public.no_ddl() OWNER TO mpoint;

--
-- TOC entry 483 (class 1255 OID 16455)
-- Name: update_table_proc(); Type: FUNCTION; Schema: public; Owner: mpoint
--

CREATE FUNCTION public.update_table_proc() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
	NEW.modified := NOW();

	RETURN NEW;
END;
$$;


ALTER FUNCTION public.update_table_proc() OWNER TO mpoint;

SET default_tablespace = '';

--
-- TOC entry 208 (class 1259 OID 78278)
-- Name: access_tbl; Type: TABLE; Schema: admin; Owner: mpoint
--

CREATE TABLE admin.access_tbl (
    id integer NOT NULL,
    userid integer NOT NULL,
    clientid integer NOT NULL,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE admin.access_tbl OWNER TO mpoint;

--
-- TOC entry 209 (class 1259 OID 78284)
-- Name: access_tbl_id_seq; Type: SEQUENCE; Schema: admin; Owner: mpoint
--

CREATE SEQUENCE admin.access_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE admin.access_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6671 (class 0 OID 0)
-- Dependencies: 209
-- Name: access_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: admin; Owner: mpoint
--

ALTER SEQUENCE admin.access_tbl_id_seq OWNED BY admin.access_tbl.id;


--
-- TOC entry 210 (class 1259 OID 78286)
-- Name: role_tbl; Type: TABLE; Schema: admin; Owner: mpoint
--

CREATE TABLE admin.role_tbl (
    id integer NOT NULL,
    name character varying(100),
    assignable boolean DEFAULT true,
    note text,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE admin.role_tbl OWNER TO mpoint;

--
-- TOC entry 211 (class 1259 OID 78296)
-- Name: role_tbl_id_seq; Type: SEQUENCE; Schema: admin; Owner: mpoint
--

CREATE SEQUENCE admin.role_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE admin.role_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6673 (class 0 OID 0)
-- Dependencies: 211
-- Name: role_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: admin; Owner: mpoint
--

ALTER SEQUENCE admin.role_tbl_id_seq OWNED BY admin.role_tbl.id;


--
-- TOC entry 212 (class 1259 OID 78298)
-- Name: roleaccess_tbl; Type: TABLE; Schema: admin; Owner: mpoint
--

CREATE TABLE admin.roleaccess_tbl (
    id integer NOT NULL,
    roleid integer NOT NULL,
    userid integer NOT NULL,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE admin.roleaccess_tbl OWNER TO mpoint;

--
-- TOC entry 213 (class 1259 OID 78304)
-- Name: roleaccess_tbl_id_seq; Type: SEQUENCE; Schema: admin; Owner: mpoint
--

CREATE SEQUENCE admin.roleaccess_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE admin.roleaccess_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6675 (class 0 OID 0)
-- Dependencies: 213
-- Name: roleaccess_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: admin; Owner: mpoint
--

ALTER SEQUENCE admin.roleaccess_tbl_id_seq OWNED BY admin.roleaccess_tbl.id;


--
-- TOC entry 214 (class 1259 OID 78306)
-- Name: roleinfo_tbl; Type: TABLE; Schema: admin; Owner: mpoint
--

CREATE TABLE admin.roleinfo_tbl (
    id integer NOT NULL,
    roleid integer NOT NULL,
    languageid integer NOT NULL,
    name character varying(100),
    note text,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE admin.roleinfo_tbl OWNER TO mpoint;

--
-- TOC entry 215 (class 1259 OID 78315)
-- Name: roleinfo_tbl_id_seq; Type: SEQUENCE; Schema: admin; Owner: mpoint
--

CREATE SEQUENCE admin.roleinfo_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE admin.roleinfo_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6677 (class 0 OID 0)
-- Dependencies: 215
-- Name: roleinfo_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: admin; Owner: mpoint
--

ALTER SEQUENCE admin.roleinfo_tbl_id_seq OWNED BY admin.roleinfo_tbl.id;


--
-- TOC entry 216 (class 1259 OID 78325)
-- Name: account_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.account_tbl (
    id integer NOT NULL,
    clientid integer NOT NULL,
    name character varying(50),
    mobile character varying(15),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    markup character varying(20),
    businesstype integer DEFAULT 0
);


ALTER TABLE client.account_tbl OWNER TO mpoint;

--
-- TOC entry 217 (class 1259 OID 78331)
-- Name: account_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.account_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.account_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6679 (class 0 OID 0)
-- Dependencies: 217
-- Name: account_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.account_tbl_id_seq OWNED BY client.account_tbl.id;


--
-- TOC entry 328 (class 1259 OID 81023)
-- Name: additionalproperty_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.additionalproperty_tbl (
    id integer NOT NULL,
    key character varying(200) NOT NULL,
    value character varying(4000) NOT NULL,
    modified timestamp without time zone DEFAULT now(),
    created timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true NOT NULL,
    externalid integer NOT NULL,
    type character varying(20) NOT NULL,
    scope integer DEFAULT 0
);


ALTER TABLE client.additionalproperty_tbl OWNER TO mpoint;

--
-- TOC entry 6680 (class 0 OID 0)
-- Dependencies: 328
-- Name: COLUMN additionalproperty_tbl.scope; Type: COMMENT; Schema: client; Owner: mpoint
--

COMMENT ON COLUMN client.additionalproperty_tbl.scope IS 'Scope of properties
0 - Internal
1 - Private
2 - Public';


--
-- TOC entry 327 (class 1259 OID 81021)
-- Name: additionalproperty_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.additionalproperty_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.additionalproperty_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6682 (class 0 OID 0)
-- Dependencies: 327
-- Name: additionalproperty_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.additionalproperty_tbl_id_seq OWNED BY client.additionalproperty_tbl.id;


--
-- TOC entry 218 (class 1259 OID 78333)
-- Name: cardaccess_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.cardaccess_tbl (
    id integer NOT NULL,
    clientid integer NOT NULL,
    cardid integer NOT NULL,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    pspid integer NOT NULL,
    countryid integer,
    stateid integer DEFAULT 1,
    "position" integer,
    preferred boolean DEFAULT false,
    psp_type integer DEFAULT 1 NOT NULL,
    installment integer DEFAULT 0 NOT NULL,
    capture_method integer DEFAULT 0,
    capture_type smallint DEFAULT 1,
    walletid integer,
    dccenabled boolean DEFAULT false
);


ALTER TABLE client.cardaccess_tbl OWNER TO mpoint;

--
-- TOC entry 6683 (class 0 OID 0)
-- Dependencies: 218
-- Name: COLUMN cardaccess_tbl.installment; Type: COMMENT; Schema: client; Owner: mpoint
--

COMMENT ON COLUMN client.cardaccess_tbl.installment IS 'Default 0 - No installment option
1 - Offline Installment';


--
-- TOC entry 6684 (class 0 OID 0)
-- Dependencies: 218
-- Name: COLUMN cardaccess_tbl.capture_method; Type: COMMENT; Schema: client; Owner: mpoint
--

COMMENT ON COLUMN client.cardaccess_tbl.capture_method IS '0 - manual
2 - bulk capture
3 - bulk refund
6 - bulk capture + bulk  refund';


--
-- TOC entry 219 (class 1259 OID 78340)
-- Name: cardaccess_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.cardaccess_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.cardaccess_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6686 (class 0 OID 0)
-- Dependencies: 219
-- Name: cardaccess_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.cardaccess_tbl_id_seq OWNED BY client.cardaccess_tbl.id;


--
-- TOC entry 220 (class 1259 OID 78342)
-- Name: client_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.client_tbl (
    id integer NOT NULL,
    countryid integer NOT NULL,
    flowid integer NOT NULL,
    name character varying(50),
    username character varying(50),
    passwd character varying(50),
    logourl character varying(255),
    cssurl character varying(255),
    callbackurl character varying(255),
    accepturl character varying(255),
    cancelurl character varying(255),
    maxamount bigint,
    lang character(2) DEFAULT 'gb'::bpchar,
    smsrcpt boolean DEFAULT true,
    emailrcpt boolean DEFAULT true,
    method character varying(6) DEFAULT 'mPoint'::character varying,
    terms text,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    mode integer DEFAULT 0,
    send_pspid boolean DEFAULT true,
    store_card integer DEFAULT 0,
    iconurl character varying(255),
    show_all_cards boolean DEFAULT false,
    max_cards integer DEFAULT '-1'::integer,
    identification integer DEFAULT 7,
    transaction_ttl integer DEFAULT 0,
    num_masked_digits integer DEFAULT 4,
    declineurl character varying(255),
    salt character varying(20),
    secretkey character varying(100),
    communicationchannels integer DEFAULT 0,
    installment integer DEFAULT 0,
    max_installments integer DEFAULT 0,
    installment_frequency integer DEFAULT 0,
    enable_cvv boolean DEFAULT true,
    CONSTRAINT client_chk CHECK ((((method)::text = 'mPoint'::text) OR ((method)::text = 'PSP'::text))),
    CONSTRAINT maskeddigits_chk CHECK (((0 <= num_masked_digits) AND (num_masked_digits <= 4)))
);


ALTER TABLE client.client_tbl OWNER TO mpoint;

--
-- TOC entry 6687 (class 0 OID 0)
-- Dependencies: 220
-- Name: COLUMN client_tbl.transaction_ttl; Type: COMMENT; Schema: client; Owner: mpoint
--

COMMENT ON COLUMN client.client_tbl.transaction_ttl IS 'Transaction Time To Live in seconds';


--
-- TOC entry 6688 (class 0 OID 0)
-- Dependencies: 220
-- Name: COLUMN client_tbl.installment; Type: COMMENT; Schema: client; Owner: mpoint
--

COMMENT ON COLUMN client.client_tbl.installment IS 'Default to 0 installment not enabled
1 - offline Installments';


--
-- TOC entry 6689 (class 0 OID 0)
-- Dependencies: 220
-- Name: COLUMN client_tbl.max_installments; Type: COMMENT; Schema: client; Owner: mpoint
--

COMMENT ON COLUMN client.client_tbl.max_installments IS 'Max number of installments allowed,
Usually set by Acq';


--
-- TOC entry 6690 (class 0 OID 0)
-- Dependencies: 220
-- Name: COLUMN client_tbl.installment_frequency; Type: COMMENT; Schema: client; Owner: mpoint
--

COMMENT ON COLUMN client.client_tbl.installment_frequency IS 'defines the time frame for installment,
like 1- monthly, 3 - quarterly, 6 - semiannual.
For merchant financed is usually monthly ';


--
-- TOC entry 221 (class 1259 OID 78366)
-- Name: client_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.client_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.client_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6692 (class 0 OID 0)
-- Dependencies: 221
-- Name: client_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.client_tbl_id_seq OWNED BY client.client_tbl.id;


--
-- TOC entry 334 (class 1259 OID 81106)
-- Name: countrycurrency_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.countrycurrency_tbl (
    id integer NOT NULL,
    clientid integer NOT NULL,
    countryid integer NOT NULL,
    currencyid integer NOT NULL,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE client.countrycurrency_tbl OWNER TO mpoint;

--
-- TOC entry 333 (class 1259 OID 81104)
-- Name: countrycurrency_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.countrycurrency_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.countrycurrency_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6694 (class 0 OID 0)
-- Dependencies: 333
-- Name: countrycurrency_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.countrycurrency_tbl_id_seq OWNED BY client.countrycurrency_tbl.id;


--
-- TOC entry 349 (class 1259 OID 223963)
-- Name: gatewaystat_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.gatewaystat_tbl (
    id integer NOT NULL,
    gatewayid integer NOT NULL,
    clientid integer NOT NULL,
    statetypeid integer NOT NULL,
    statvalue numeric NOT NULL,
    enabled boolean DEFAULT true NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    modified timestamp without time zone DEFAULT now() NOT NULL,
    reseton timestamp without time zone
);


ALTER TABLE client.gatewaystat_tbl OWNER TO mpoint;

--
-- TOC entry 348 (class 1259 OID 223961)
-- Name: gatewaystat_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.gatewaystat_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.gatewaystat_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6696 (class 0 OID 0)
-- Dependencies: 348
-- Name: gatewaystat_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.gatewaystat_tbl_id_seq OWNED BY client.gatewaystat_tbl.id;


--
-- TOC entry 345 (class 1259 OID 223915)
-- Name: gatewaytrigger_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.gatewaytrigger_tbl (
    id integer NOT NULL,
    gatewayid integer,
    enabled boolean DEFAULT true NOT NULL,
    aggregationtriggerunit integer,
    clientid integer,
    aggregationtriggervalue integer,
    created timestamp without time zone DEFAULT now() NOT NULL,
    modified timestamp without time zone DEFAULT now() NOT NULL,
    status boolean DEFAULT false NOT NULL
);


ALTER TABLE client.gatewaytrigger_tbl OWNER TO mpoint;

--
-- TOC entry 344 (class 1259 OID 223913)
-- Name: gatewaytrigger_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.gatewaytrigger_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.gatewaytrigger_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6698 (class 0 OID 0)
-- Dependencies: 344
-- Name: gatewaytrigger_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.gatewaytrigger_tbl_id_seq OWNED BY client.gatewaytrigger_tbl.id;


--
-- TOC entry 326 (class 1259 OID 81007)
-- Name: gomobileconfiguration_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.gomobileconfiguration_tbl (
    id integer NOT NULL,
    clientid integer NOT NULL,
    name character varying(100),
    value character varying(100),
    channel character varying(5),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE client.gomobileconfiguration_tbl OWNER TO mpoint;

--
-- TOC entry 325 (class 1259 OID 81005)
-- Name: gomobileconfiguration_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.gomobileconfiguration_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.gomobileconfiguration_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6700 (class 0 OID 0)
-- Dependencies: 325
-- Name: gomobileconfiguration_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.gomobileconfiguration_tbl_id_seq OWNED BY client.gomobileconfiguration_tbl.id;


--
-- TOC entry 222 (class 1259 OID 78368)
-- Name: iinlist_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.iinlist_tbl (
    id integer NOT NULL,
    iinactionid integer NOT NULL,
    clientid integer NOT NULL,
    min bigint,
    max bigint,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE client.iinlist_tbl OWNER TO mpoint;

--
-- TOC entry 223 (class 1259 OID 78374)
-- Name: iinlist_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.iinlist_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.iinlist_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6702 (class 0 OID 0)
-- Dependencies: 223
-- Name: iinlist_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.iinlist_tbl_id_seq OWNED BY client.iinlist_tbl.id;


--
-- TOC entry 224 (class 1259 OID 78376)
-- Name: info_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.info_tbl (
    id integer NOT NULL,
    infotypeid integer NOT NULL,
    clientid integer NOT NULL,
    pspid integer,
    language character(2) DEFAULT 'gb'::bpchar,
    text text,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE client.info_tbl OWNER TO mpoint;

--
-- TOC entry 225 (class 1259 OID 78386)
-- Name: info_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.info_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.info_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6704 (class 0 OID 0)
-- Dependencies: 225
-- Name: info_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.info_tbl_id_seq OWNED BY client.info_tbl.id;


--
-- TOC entry 226 (class 1259 OID 78388)
-- Name: infotype_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.infotype_tbl (
    id integer NOT NULL,
    name character varying(100),
    note text,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE client.infotype_tbl OWNER TO mpoint;

--
-- TOC entry 227 (class 1259 OID 78397)
-- Name: infotype_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.infotype_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.infotype_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6706 (class 0 OID 0)
-- Dependencies: 227
-- Name: infotype_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.infotype_tbl_id_seq OWNED BY client.infotype_tbl.id;


--
-- TOC entry 228 (class 1259 OID 78399)
-- Name: ipaddress_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.ipaddress_tbl (
    id integer NOT NULL,
    clientid integer NOT NULL,
    ipaddress character varying(20),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE client.ipaddress_tbl OWNER TO mpoint;

--
-- TOC entry 229 (class 1259 OID 78405)
-- Name: ipaddress_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.ipaddress_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.ipaddress_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6708 (class 0 OID 0)
-- Dependencies: 229
-- Name: ipaddress_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.ipaddress_tbl_id_seq OWNED BY client.ipaddress_tbl.id;


--
-- TOC entry 230 (class 1259 OID 78407)
-- Name: keyword_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.keyword_tbl (
    id integer NOT NULL,
    clientid integer NOT NULL,
    name character varying(50),
    standard boolean DEFAULT false,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE client.keyword_tbl OWNER TO mpoint;

--
-- TOC entry 231 (class 1259 OID 78414)
-- Name: keyword_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.keyword_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.keyword_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6710 (class 0 OID 0)
-- Dependencies: 231
-- Name: keyword_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.keyword_tbl_id_seq OWNED BY client.keyword_tbl.id;


--
-- TOC entry 232 (class 1259 OID 78416)
-- Name: merchantaccount_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.merchantaccount_tbl (
    id integer NOT NULL,
    clientid integer NOT NULL,
    pspid integer NOT NULL,
    name character varying(255),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    username character varying(50) DEFAULT 'empty'::character varying,
    passwd character varying(4000) DEFAULT 'empty'::character varying,
    stored_card boolean,
    supportedpartialoperations integer DEFAULT 0 NOT NULL
);


ALTER TABLE client.merchantaccount_tbl OWNER TO mpoint;

--
-- TOC entry 233 (class 1259 OID 78425)
-- Name: merchantaccount_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.merchantaccount_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.merchantaccount_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6712 (class 0 OID 0)
-- Dependencies: 233
-- Name: merchantaccount_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.merchantaccount_tbl_id_seq OWNED BY client.merchantaccount_tbl.id;


--
-- TOC entry 234 (class 1259 OID 78427)
-- Name: merchantsubaccount_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.merchantsubaccount_tbl (
    id integer NOT NULL,
    accountid integer NOT NULL,
    pspid integer NOT NULL,
    name character varying(50),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE client.merchantsubaccount_tbl OWNER TO mpoint;

--
-- TOC entry 235 (class 1259 OID 78433)
-- Name: merchantsubaccount_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.merchantsubaccount_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.merchantsubaccount_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6714 (class 0 OID 0)
-- Dependencies: 235
-- Name: merchantsubaccount_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.merchantsubaccount_tbl_id_seq OWNED BY client.merchantsubaccount_tbl.id;


--
-- TOC entry 236 (class 1259 OID 78435)
-- Name: product_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.product_tbl (
    id integer NOT NULL,
    keywordid integer NOT NULL,
    name character varying(50),
    quantity integer DEFAULT 1,
    price integer,
    logourl character varying(255),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE client.product_tbl OWNER TO mpoint;

--
-- TOC entry 237 (class 1259 OID 78442)
-- Name: product_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.product_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.product_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6716 (class 0 OID 0)
-- Dependencies: 237
-- Name: product_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.product_tbl_id_seq OWNED BY client.product_tbl.id;


--
-- TOC entry 341 (class 1259 OID 223861)
-- Name: producttype_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.producttype_tbl (
    id integer NOT NULL,
    productid integer NOT NULL,
    clientid integer NOT NULL,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE client.producttype_tbl OWNER TO mpoint;

--
-- TOC entry 340 (class 1259 OID 223859)
-- Name: producttype_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.producttype_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.producttype_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6718 (class 0 OID 0)
-- Dependencies: 340
-- Name: producttype_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.producttype_tbl_id_seq OWNED BY client.producttype_tbl.id;


--
-- TOC entry 357 (class 1259 OID 1463586)
-- Name: retrial_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.retrial_tbl (
    id integer NOT NULL,
    typeid integer NOT NULL,
    retrialvalue character varying(255),
    delay integer,
    clientid integer NOT NULL,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE client.retrial_tbl OWNER TO mpoint;

--
-- TOC entry 356 (class 1259 OID 1463584)
-- Name: retrial_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.retrial_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.retrial_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6720 (class 0 OID 0)
-- Dependencies: 356
-- Name: retrial_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.retrial_tbl_id_seq OWNED BY client.retrial_tbl.id;


--
-- TOC entry 238 (class 1259 OID 78444)
-- Name: shipping_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.shipping_tbl (
    id integer NOT NULL,
    shippingid integer NOT NULL,
    shopid integer NOT NULL,
    cost integer,
    free_ship integer,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE client.shipping_tbl OWNER TO mpoint;

--
-- TOC entry 239 (class 1259 OID 78450)
-- Name: shipping_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.shipping_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.shipping_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6722 (class 0 OID 0)
-- Dependencies: 239
-- Name: shipping_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.shipping_tbl_id_seq OWNED BY client.shipping_tbl.id;


--
-- TOC entry 240 (class 1259 OID 78452)
-- Name: shop_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.shop_tbl (
    id integer NOT NULL,
    clientid integer NOT NULL,
    keywordid integer NOT NULL,
    del_date boolean,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE client.shop_tbl OWNER TO mpoint;

--
-- TOC entry 241 (class 1259 OID 78458)
-- Name: shop_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.shop_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.shop_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6724 (class 0 OID 0)
-- Dependencies: 241
-- Name: shop_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.shop_tbl_id_seq OWNED BY client.shop_tbl.id;


--
-- TOC entry 474 (class 1259 OID 33316461)
-- Name: staticroutelevelconfiguration; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.staticroutelevelconfiguration (
    id integer NOT NULL,
    cardaccessid integer NOT NULL,
    cvcmandatory boolean DEFAULT true NOT NULL,
    enabled boolean DEFAULT true NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    modified timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE client.staticroutelevelconfiguration OWNER TO mpoint;

--
-- TOC entry 6725 (class 0 OID 0)
-- Dependencies: 474
-- Name: TABLE staticroutelevelconfiguration; Type: COMMENT; Schema: client; Owner: mpoint
--

COMMENT ON TABLE client.staticroutelevelconfiguration IS 'This table will contain the configuration based on card schema, Provider and Country';


--
-- TOC entry 6726 (class 0 OID 0)
-- Dependencies: 474
-- Name: COLUMN staticroutelevelconfiguration.cardaccessid; Type: COMMENT; Schema: client; Owner: mpoint
--

COMMENT ON COLUMN client.staticroutelevelconfiguration.cardaccessid IS 'Primary key of client.cardaccess_tbl';


--
-- TOC entry 473 (class 1259 OID 33316459)
-- Name: staticroutelevelconfiguration_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.staticroutelevelconfiguration_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.staticroutelevelconfiguration_id_seq OWNER TO mpoint;

--
-- TOC entry 6728 (class 0 OID 0)
-- Dependencies: 473
-- Name: staticroutelevelconfiguration_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.staticroutelevelconfiguration_id_seq OWNED BY client.staticroutelevelconfiguration.id;


--
-- TOC entry 242 (class 1259 OID 78460)
-- Name: surepay_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.surepay_tbl (
    id integer NOT NULL,
    clientid integer,
    resend integer,
    notify integer,
    email character varying(50),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    max integer DEFAULT 1
);


ALTER TABLE client.surepay_tbl OWNER TO mpoint;

--
-- TOC entry 243 (class 1259 OID 78466)
-- Name: surepay_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.surepay_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.surepay_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6730 (class 0 OID 0)
-- Dependencies: 243
-- Name: surepay_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.surepay_tbl_id_seq OWNED BY client.surepay_tbl.id;


--
-- TOC entry 244 (class 1259 OID 78468)
-- Name: url_tbl; Type: TABLE; Schema: client; Owner: mpoint
--

CREATE TABLE client.url_tbl (
    id integer NOT NULL,
    urltypeid integer NOT NULL,
    clientid integer NOT NULL,
    url character varying(255),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE client.url_tbl OWNER TO mpoint;

--
-- TOC entry 245 (class 1259 OID 78474)
-- Name: url_tbl_id_seq; Type: SEQUENCE; Schema: client; Owner: mpoint
--

CREATE SEQUENCE client.url_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE client.url_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6732 (class 0 OID 0)
-- Dependencies: 245
-- Name: url_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: client; Owner: mpoint
--

ALTER SEQUENCE client.url_tbl_id_seq OWNED BY client.url_tbl.id;


--
-- TOC entry 246 (class 1259 OID 78476)
-- Name: account_tbl; Type: TABLE; Schema: enduser; Owner: mpoint
--

CREATE TABLE enduser.account_tbl (
    id integer NOT NULL,
    countryid integer NOT NULL,
    firstname character varying(50),
    lastname character varying(50),
    mobile character varying(15),
    email character varying(50),
    passwd character varying(50),
    balance integer DEFAULT 0,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    attempts integer DEFAULT 0,
    points integer DEFAULT 0,
    mobile_verified boolean DEFAULT false,
    externalid character varying(50),
    pushid character varying(100),
    profileid bigint
);


ALTER TABLE enduser.account_tbl OWNER TO mpoint;

--
-- TOC entry 6733 (class 0 OID 0)
-- Dependencies: 246
-- Name: COLUMN account_tbl.profileid; Type: COMMENT; Schema: enduser; Owner: mpoint
--

COMMENT ON COLUMN enduser.account_tbl.profileid IS 'mProfile id associated with the registered enduser';


--
-- TOC entry 247 (class 1259 OID 78486)
-- Name: account_tbl_id_seq; Type: SEQUENCE; Schema: enduser; Owner: mpoint
--

CREATE SEQUENCE enduser.account_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE enduser.account_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6735 (class 0 OID 0)
-- Dependencies: 247
-- Name: account_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: enduser; Owner: mpoint
--

ALTER SEQUENCE enduser.account_tbl_id_seq OWNED BY enduser.account_tbl.id;


--
-- TOC entry 248 (class 1259 OID 78488)
-- Name: general_tbl; Type: TABLE; Schema: template; Owner: mpoint
--

CREATE TABLE template.general_tbl (
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE template.general_tbl OWNER TO mpoint;

--
-- TOC entry 249 (class 1259 OID 78494)
-- Name: activation_tbl; Type: TABLE; Schema: enduser; Owner: mpoint
--

CREATE TABLE enduser.activation_tbl (
    id integer NOT NULL,
    accountid integer NOT NULL,
    code integer,
    address character varying(50),
    active boolean DEFAULT false,
    expiry timestamp without time zone DEFAULT (now() + '24:00:00'::interval)
)
INHERITS (template.general_tbl);


ALTER TABLE enduser.activation_tbl OWNER TO mpoint;

--
-- TOC entry 250 (class 1259 OID 78502)
-- Name: activation_tbl_id_seq; Type: SEQUENCE; Schema: enduser; Owner: mpoint
--

CREATE SEQUENCE enduser.activation_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE enduser.activation_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6738 (class 0 OID 0)
-- Dependencies: 250
-- Name: activation_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: enduser; Owner: mpoint
--

ALTER SEQUENCE enduser.activation_tbl_id_seq OWNED BY enduser.activation_tbl.id;


--
-- TOC entry 251 (class 1259 OID 78504)
-- Name: address_tbl; Type: TABLE; Schema: enduser; Owner: mpoint
--

CREATE TABLE enduser.address_tbl (
    id integer NOT NULL,
    accountid integer,
    cardid integer,
    countryid integer NOT NULL,
    firstname character varying(50),
    lastname character varying(50),
    company character varying(50),
    street character varying(100),
    postalcode character varying(10),
    city character varying(50),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    state character varying(200),
    CONSTRAINT address_tbl_check CHECK ((((accountid IS NULL) AND (cardid IS NOT NULL)) OR ((accountid IS NOT NULL) AND (cardid IS NULL))))
);


ALTER TABLE enduser.address_tbl OWNER TO mpoint;

--
-- TOC entry 252 (class 1259 OID 78511)
-- Name: address_tbl_id_seq; Type: SEQUENCE; Schema: enduser; Owner: mpoint
--

CREATE SEQUENCE enduser.address_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE enduser.address_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6740 (class 0 OID 0)
-- Dependencies: 252
-- Name: address_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: enduser; Owner: mpoint
--

ALTER SEQUENCE enduser.address_tbl_id_seq OWNED BY enduser.address_tbl.id;


--
-- TOC entry 253 (class 1259 OID 78513)
-- Name: card_tbl; Type: TABLE; Schema: enduser; Owner: mpoint
--

CREATE TABLE enduser.card_tbl (
    id integer NOT NULL,
    accountid integer NOT NULL,
    cardid integer NOT NULL,
    pspid integer NOT NULL,
    mask character varying(20),
    expiry character varying(5),
    preferred boolean DEFAULT false,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    clientid integer,
    name character varying(50),
    ticket character varying(255),
    card_holder_name character varying(255),
    chargetypeid integer DEFAULT 0
);


ALTER TABLE enduser.card_tbl OWNER TO mpoint;

--
-- TOC entry 254 (class 1259 OID 78524)
-- Name: card_tbl_id_seq; Type: SEQUENCE; Schema: enduser; Owner: mpoint
--

CREATE SEQUENCE enduser.card_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE enduser.card_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6742 (class 0 OID 0)
-- Dependencies: 254
-- Name: card_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: enduser; Owner: mpoint
--

ALTER SEQUENCE enduser.card_tbl_id_seq OWNED BY enduser.card_tbl.id;


--
-- TOC entry 255 (class 1259 OID 78526)
-- Name: claccess_tbl; Type: TABLE; Schema: enduser; Owner: mpoint
--

CREATE TABLE enduser.claccess_tbl (
    id integer NOT NULL,
    clientid integer NOT NULL,
    accountid integer NOT NULL,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE enduser.claccess_tbl OWNER TO mpoint;

--
-- TOC entry 256 (class 1259 OID 78532)
-- Name: claccess_tbl_id_seq; Type: SEQUENCE; Schema: enduser; Owner: mpoint
--

CREATE SEQUENCE enduser.claccess_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE enduser.claccess_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6744 (class 0 OID 0)
-- Dependencies: 256
-- Name: claccess_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: enduser; Owner: mpoint
--

ALTER SEQUENCE enduser.claccess_tbl_id_seq OWNED BY enduser.claccess_tbl.id;


--
-- TOC entry 257 (class 1259 OID 78534)
-- Name: transaction_tbl; Type: TABLE; Schema: enduser; Owner: mpoint
--

CREATE TABLE enduser.transaction_tbl (
    id integer NOT NULL,
    accountid integer NOT NULL,
    typeid integer NOT NULL,
    fromid integer,
    toid integer,
    txnid integer,
    amount integer,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    fee integer DEFAULT 0,
    ip inet,
    address character varying(50),
    message text,
    pending boolean DEFAULT false,
    stateid integer DEFAULT 1800,
    CONSTRAINT transaction_chk CHECK ((((fromid IS NULL) AND (toid IS NULL)) OR (txnid IS NULL)))
);


ALTER TABLE enduser.transaction_tbl OWNER TO mpoint;

--
-- TOC entry 258 (class 1259 OID 78547)
-- Name: transaction_tbl_id_seq; Type: SEQUENCE; Schema: enduser; Owner: mpoint
--

CREATE SEQUENCE enduser.transaction_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE enduser.transaction_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6746 (class 0 OID 0)
-- Dependencies: 258
-- Name: transaction_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: enduser; Owner: mpoint
--

ALTER SEQUENCE enduser.transaction_tbl_id_seq OWNED BY enduser.transaction_tbl.id;


--
-- TOC entry 259 (class 1259 OID 78549)
-- Name: additional_data_tbl; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.additional_data_tbl (
    id integer NOT NULL,
    name character varying(30),
    value character varying(50),
    type log.additional_data_ref,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    externalid integer
);


ALTER TABLE log.additional_data_tbl OWNER TO mpoint;

--
-- TOC entry 260 (class 1259 OID 78554)
-- Name: additional_data_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: mpoint
--

CREATE SEQUENCE log.additional_data_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log.additional_data_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6748 (class 0 OID 0)
-- Dependencies: 260
-- Name: additional_data_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: mpoint
--

ALTER SEQUENCE log.additional_data_tbl_id_seq OWNED BY log.additional_data_tbl.id;


--
-- TOC entry 261 (class 1259 OID 78556)
-- Name: address_tbl; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.address_tbl (
    id integer NOT NULL,
    first_name character varying(200),
    street text,
    street2 text,
    city character varying(200),
    state character varying(200),
    country character varying(200),
    zip character varying(200),
    reference_id integer,
    reference_type log.address_tbl_ref,
    last_name character varying(200)
);


ALTER TABLE log.address_tbl OWNER TO mpoint;

--
-- TOC entry 262 (class 1259 OID 78562)
-- Name: address_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: mpoint
--

CREATE SEQUENCE log.address_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log.address_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6750 (class 0 OID 0)
-- Dependencies: 262
-- Name: address_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: mpoint
--

ALTER SEQUENCE log.address_tbl_id_seq OWNED BY log.address_tbl.id;


--
-- TOC entry 263 (class 1259 OID 78564)
-- Name: auditlog_tbl; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.auditlog_tbl (
    id integer NOT NULL,
    operationid integer NOT NULL,
    mobile bigint,
    email character varying(255),
    customer_ref character varying(50),
    code integer NOT NULL,
    message character varying(255),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    entity character varying(255)
);


ALTER TABLE log.auditlog_tbl OWNER TO mpoint;

--
-- TOC entry 264 (class 1259 OID 78573)
-- Name: auditlog_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: mpoint
--

CREATE SEQUENCE log.auditlog_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log.auditlog_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6752 (class 0 OID 0)
-- Dependencies: 264
-- Name: auditlog_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: mpoint
--

ALTER SEQUENCE log.auditlog_tbl_id_seq OWNED BY log.auditlog_tbl.id;


--
-- TOC entry 477 (class 1259 OID 36070187)
-- Name: billing_summary_tbl; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.billing_summary_tbl (
    id integer NOT NULL,
    order_id integer NOT NULL,
    journey_ref character varying(50),
    bill_type character varying(25) NOT NULL,
    type_id integer NOT NULL,
    description character varying(50) NOT NULL,
    amount character varying(20),
    currency character varying(10) NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    modified timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE log.billing_summary_tbl OWNER TO mpoint;

--
-- TOC entry 476 (class 1259 OID 36070185)
-- Name: billing_summary_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: mpoint
--

CREATE SEQUENCE log.billing_summary_tbl_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log.billing_summary_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6754 (class 0 OID 0)
-- Dependencies: 476
-- Name: billing_summary_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: mpoint
--

ALTER SEQUENCE log.billing_summary_tbl_id_seq OWNED BY log.billing_summary_tbl.id;


--
-- TOC entry 359 (class 1259 OID 3504096)
-- Name: externalreference_tbl; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.externalreference_tbl (
    id integer NOT NULL,
    txnid integer NOT NULL,
    externalid bigint NOT NULL,
    pspid integer NOT NULL,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    type integer
);


ALTER TABLE log.externalreference_tbl OWNER TO mpoint;

--
-- TOC entry 358 (class 1259 OID 3504094)
-- Name: externalreference_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: mpoint
--

CREATE SEQUENCE log.externalreference_tbl_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log.externalreference_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6756 (class 0 OID 0)
-- Dependencies: 358
-- Name: externalreference_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: mpoint
--

ALTER SEQUENCE log.externalreference_tbl_id_seq OWNED BY log.externalreference_tbl.id;


--
-- TOC entry 265 (class 1259 OID 78575)
-- Name: flight_tbl; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.flight_tbl (
    id integer NOT NULL,
    service_class character varying(10) NOT NULL,
    departure_airport character varying(10) NOT NULL,
    arrival_airport character varying(10) NOT NULL,
    airline_code character varying(10) NOT NULL,
    order_id integer NOT NULL,
    arrival_date timestamp without time zone NOT NULL,
    departure_date timestamp without time zone NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    modified timestamp without time zone DEFAULT now() NOT NULL,
    flight_number character varying(20),
    tag character varying(2),
    trip_count character varying(2),
    service_level character varying(2),
    departure_countryid integer,
    arrival_countryid integer
);


ALTER TABLE log.flight_tbl OWNER TO mpoint;

--
-- TOC entry 266 (class 1259 OID 78580)
-- Name: flight_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: mpoint
--

CREATE SEQUENCE log.flight_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log.flight_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6758 (class 0 OID 0)
-- Dependencies: 266
-- Name: flight_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: mpoint
--

ALTER SEQUENCE log.flight_tbl_id_seq OWNED BY log.flight_tbl.id;


--
-- TOC entry 267 (class 1259 OID 78582)
-- Name: message_tbl; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.message_tbl (
    id integer NOT NULL,
    txnid integer NOT NULL,
    stateid integer NOT NULL,
    data text,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE log.message_tbl OWNER TO mpoint;

--
-- TOC entry 268 (class 1259 OID 78591)
-- Name: message_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: mpoint
--

CREATE SEQUENCE log.message_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log.message_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6760 (class 0 OID 0)
-- Dependencies: 268
-- Name: message_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: mpoint
--

ALTER SEQUENCE log.message_tbl_id_seq OWNED BY log.message_tbl.id;


--
-- TOC entry 269 (class 1259 OID 78593)
-- Name: note_tbl; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.note_tbl (
    id integer NOT NULL,
    txnid integer NOT NULL,
    userid integer NOT NULL,
    message text,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE log.note_tbl OWNER TO mpoint;

--
-- TOC entry 270 (class 1259 OID 78602)
-- Name: note_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: mpoint
--

CREATE SEQUENCE log.note_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log.note_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6763 (class 0 OID 0)
-- Dependencies: 270
-- Name: note_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: mpoint
--

ALTER SEQUENCE log.note_tbl_id_seq OWNED BY log.note_tbl.id;


--
-- TOC entry 271 (class 1259 OID 78604)
-- Name: operation_tbl; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.operation_tbl (
    id integer NOT NULL,
    name character varying(255),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE log.operation_tbl OWNER TO mpoint;

--
-- TOC entry 272 (class 1259 OID 78610)
-- Name: operation_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: mpoint
--

CREATE SEQUENCE log.operation_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log.operation_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6765 (class 0 OID 0)
-- Dependencies: 272
-- Name: operation_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: mpoint
--

ALTER SEQUENCE log.operation_tbl_id_seq OWNED BY log.operation_tbl.id;


--
-- TOC entry 273 (class 1259 OID 78612)
-- Name: order_tbl; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.order_tbl (
    id integer NOT NULL,
    txnid integer NOT NULL,
    countryid integer NOT NULL,
    amount numeric,
    productsku character varying(40),
    productname character varying(40),
    productdescription text,
    productimageurl character varying(255),
    points integer,
    reward integer,
    quantity integer,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    orderref character varying(40),
    fees integer DEFAULT 0
);


ALTER TABLE log.order_tbl OWNER TO mpoint;

--
-- TOC entry 274 (class 1259 OID 78621)
-- Name: order_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: mpoint
--

CREATE SEQUENCE log.order_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log.order_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6767 (class 0 OID 0)
-- Dependencies: 274
-- Name: order_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: mpoint
--

ALTER SEQUENCE log.order_tbl_id_seq OWNED BY log.order_tbl.id;


--
-- TOC entry 275 (class 1259 OID 78623)
-- Name: passenger_tbl; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.passenger_tbl (
    id integer NOT NULL,
    first_name character varying(50) NOT NULL,
    last_name character varying(50) NOT NULL,
    type character varying(10) NOT NULL,
    order_id integer NOT NULL,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    title character varying(20),
    email character varying(50),
    mobile character varying(15),
    country_id character varying(3)
);


ALTER TABLE log.passenger_tbl OWNER TO mpoint;

--
-- TOC entry 276 (class 1259 OID 78628)
-- Name: passenger_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: mpoint
--

CREATE SEQUENCE log.passenger_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log.passenger_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6769 (class 0 OID 0)
-- Dependencies: 276
-- Name: passenger_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: mpoint
--

ALTER SEQUENCE log.passenger_tbl_id_seq OWNED BY log.passenger_tbl.id;


--
-- TOC entry 481 (class 1259 OID 38040381)
-- Name: paymentroute_tbl; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.paymentroute_tbl (
    id integer NOT NULL,
    sessionid integer NOT NULL,
    pspid integer NOT NULL,
    preference integer NOT NULL,
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);


ALTER TABLE log.paymentroute_tbl OWNER TO mpoint;

--
-- TOC entry 480 (class 1259 OID 38040379)
-- Name: paymentroute_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: mpoint
--

CREATE SEQUENCE log.paymentroute_tbl_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log.paymentroute_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6771 (class 0 OID 0)
-- Dependencies: 480
-- Name: paymentroute_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: mpoint
--

ALTER SEQUENCE log.paymentroute_tbl_id_seq OWNED BY log.paymentroute_tbl.id;


--
-- TOC entry 338 (class 1259 OID 81145)
-- Name: session_tbl; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.session_tbl (
    id integer NOT NULL,
    clientid integer,
    accountid integer,
    currencyid integer,
    countryid integer,
    stateid integer,
    orderid character varying(128) NOT NULL,
    amount numeric NOT NULL,
    mobile numeric NOT NULL,
    deviceid character varying(128),
    ipaddress character varying(15),
    externalid integer,
    sessiontypeid integer,
    expire timestamp(6) without time zone DEFAULT now(),
    created timestamp(6) without time zone DEFAULT now(),
    modified timestamp(6) without time zone DEFAULT now()
);


ALTER TABLE log.session_tbl OWNER TO mpoint;

--
-- TOC entry 6772 (class 0 OID 0)
-- Dependencies: 338
-- Name: TABLE session_tbl; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON TABLE log.session_tbl IS 'Session table act as master table for transaction. Split transactions will track by Session id';


--
-- TOC entry 6773 (class 0 OID 0)
-- Dependencies: 338
-- Name: COLUMN session_tbl.clientid; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON COLUMN log.session_tbl.clientid IS 'Merchant Id';


--
-- TOC entry 6774 (class 0 OID 0)
-- Dependencies: 338
-- Name: COLUMN session_tbl.accountid; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON COLUMN log.session_tbl.accountid IS 'Storefront Id';


--
-- TOC entry 6775 (class 0 OID 0)
-- Dependencies: 338
-- Name: COLUMN session_tbl.currencyid; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON COLUMN log.session_tbl.currencyid IS 'Currency of transaction';


--
-- TOC entry 6776 (class 0 OID 0)
-- Dependencies: 338
-- Name: COLUMN session_tbl.countryid; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON COLUMN log.session_tbl.countryid IS 'Country of transaction';


--
-- TOC entry 6777 (class 0 OID 0)
-- Dependencies: 338
-- Name: COLUMN session_tbl.stateid; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON COLUMN log.session_tbl.stateid IS 'State of session';


--
-- TOC entry 6778 (class 0 OID 0)
-- Dependencies: 338
-- Name: COLUMN session_tbl.amount; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON COLUMN log.session_tbl.amount IS 'Total amount for payment';


--
-- TOC entry 6779 (class 0 OID 0)
-- Dependencies: 338
-- Name: COLUMN session_tbl.externalid; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON COLUMN log.session_tbl.externalid IS 'Profile id';


--
-- TOC entry 6780 (class 0 OID 0)
-- Dependencies: 338
-- Name: COLUMN session_tbl.sessiontypeid; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON COLUMN log.session_tbl.sessiontypeid IS 'Session Type id';


--
-- TOC entry 337 (class 1259 OID 81143)
-- Name: session_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: mpoint
--

CREATE SEQUENCE log.session_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log.session_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6782 (class 0 OID 0)
-- Dependencies: 337
-- Name: session_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: mpoint
--

ALTER SEQUENCE log.session_tbl_id_seq OWNED BY log.session_tbl.id;


--
-- TOC entry 353 (class 1259 OID 1463554)
-- Name: settlement_record_tbl; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.settlement_record_tbl (
    id integer NOT NULL,
    settlementid integer,
    transactionid integer,
    description character varying(100),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);


ALTER TABLE log.settlement_record_tbl OWNER TO mpoint;

--
-- TOC entry 352 (class 1259 OID 1463552)
-- Name: settlement_record_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: mpoint
--

CREATE SEQUENCE log.settlement_record_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log.settlement_record_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6784 (class 0 OID 0)
-- Dependencies: 352
-- Name: settlement_record_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: mpoint
--

ALTER SEQUENCE log.settlement_record_tbl_id_seq OWNED BY log.settlement_record_tbl.id;


--
-- TOC entry 351 (class 1259 OID 1463533)
-- Name: settlement_tbl; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.settlement_tbl (
    id integer NOT NULL,
    record_number integer NOT NULL,
    file_reference_number character varying(10) NOT NULL,
    file_sequence_number integer NOT NULL,
    created timestamp without time zone DEFAULT now(),
    client_id integer NOT NULL,
    psp_id integer NOT NULL,
    record_tracking_number character varying(20),
    record_type character varying(20),
    description character varying(100),
    status character varying(100) DEFAULT 'active'::character varying NOT NULL,
    modified timestamp without time zone DEFAULT now()
);


ALTER TABLE log.settlement_tbl OWNER TO mpoint;

--
-- TOC entry 350 (class 1259 OID 1463531)
-- Name: settlement_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: mpoint
--

CREATE SEQUENCE log.settlement_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log.settlement_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6786 (class 0 OID 0)
-- Dependencies: 350
-- Name: settlement_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: mpoint
--

ALTER SEQUENCE log.settlement_tbl_id_seq OWNED BY log.settlement_tbl.id;


--
-- TOC entry 277 (class 1259 OID 78630)
-- Name: state_tbl; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.state_tbl (
    id integer NOT NULL,
    name character varying(50),
    module character varying(255),
    func character varying(255),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE log.state_tbl OWNER TO mpoint;

--
-- TOC entry 278 (class 1259 OID 78639)
-- Name: state_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: mpoint
--

CREATE SEQUENCE log.state_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log.state_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6788 (class 0 OID 0)
-- Dependencies: 278
-- Name: state_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: mpoint
--

ALTER SEQUENCE log.state_tbl_id_seq OWNED BY log.state_tbl.id;


--
-- TOC entry 279 (class 1259 OID 78641)
-- Name: transaction_tbl; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.transaction_tbl (
    id integer NOT NULL,
    typeid integer NOT NULL,
    clientid integer NOT NULL,
    accountid integer NOT NULL,
    countryid integer NOT NULL,
    pspid integer,
    cardid integer,
    keywordid integer,
    amount bigint,
    orderid character varying(40),
    extid character varying(40),
    lang character(2) DEFAULT 'gb'::bpchar,
    mobile character varying(15),
    operatorid integer,
    logourl character varying(255),
    cssurl character varying(255),
    callbackurl character varying(255),
    accepturl character varying(255),
    cancelurl character varying(255),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    mode integer DEFAULT 0,
    email character varying(50),
    gomobileid integer DEFAULT '-1'::integer,
    auto_capture smallint DEFAULT 1,
    euaid integer,
    ip inet NOT NULL,
    iconurl character varying(255),
    markup character varying(20),
    points integer,
    reward integer,
    refund integer DEFAULT 0,
    authurl character varying(255),
    customer_ref character varying(50),
    description text,
    fee integer DEFAULT 0,
    captured bigint DEFAULT 0,
    deviceid character varying(50),
    mask character varying(20),
    expiry character varying(5),
    token character varying(512),
    authoriginaldata character varying(512),
    currencyid integer,
    sessionid integer,
    attempt integer DEFAULT 1,
    producttype integer DEFAULT 100 NOT NULL,
    approval_action_code character varying(40),
    virtualtoken character varying(512),
    declineurl character varying(255),
    installment_value integer DEFAULT 0,
    walletid integer,
    issuing_bank character varying(100),
    profileid bigint,
    convetredcurrencyid integer,
    convertedamount bigint,
    conversionrate numeric DEFAULT 1
)
WITH (autovacuum_enabled='true', toast.autovacuum_enabled='true');


ALTER TABLE log.transaction_tbl OWNER TO mpoint;

--
-- TOC entry 6789 (class 0 OID 0)
-- Dependencies: 279
-- Name: COLUMN transaction_tbl.producttype; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON COLUMN log.transaction_tbl.producttype IS 'Product type of transaction';


--
-- TOC entry 6790 (class 0 OID 0)
-- Dependencies: 279
-- Name: COLUMN transaction_tbl.approval_action_code; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON COLUMN log.transaction_tbl.approval_action_code IS 'This field contains an action code and approval code
"approval code":"action code"';


--
-- TOC entry 6791 (class 0 OID 0)
-- Dependencies: 279
-- Name: COLUMN transaction_tbl.installment_value; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON COLUMN log.transaction_tbl.installment_value IS 'Installment value is the number of installments selected by the user';


--
-- TOC entry 6792 (class 0 OID 0)
-- Dependencies: 279
-- Name: COLUMN transaction_tbl.profileid; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON COLUMN log.transaction_tbl.profileid IS 'mProfile id associated with the txn';


--
-- TOC entry 280 (class 1259 OID 78656)
-- Name: transaction_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: mpoint
--

CREATE SEQUENCE log.transaction_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log.transaction_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6794 (class 0 OID 0)
-- Dependencies: 280
-- Name: transaction_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: mpoint
--

ALTER SEQUENCE log.transaction_tbl_id_seq OWNED BY log.transaction_tbl.id;


--
-- TOC entry 365 (class 1259 OID 33260798)
-- Name: txnpassbook_tbl; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl (
    id integer NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
)
PARTITION BY LIST (clientid);


ALTER TABLE log.txnpassbook_tbl OWNER TO mpoint;

--
-- TOC entry 364 (class 1259 OID 33260796)
-- Name: txnpassbook_tbl_id_seq; Type: SEQUENCE; Schema: log; Owner: mpoint
--

CREATE SEQUENCE log.txnpassbook_tbl_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log.txnpassbook_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6796 (class 0 OID 0)
-- Dependencies: 364
-- Name: txnpassbook_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: log; Owner: mpoint
--

ALTER SEQUENCE log.txnpassbook_tbl_id_seq OWNED BY log.txnpassbook_tbl.id;


--
-- TOC entry 366 (class 1259 OID 33260861)
-- Name: txnpassbook_tbl_10018; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
)
PARTITION BY RANGE (transactionid);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10018 FOR VALUES IN (10018);


ALTER TABLE log.txnpassbook_tbl_10018 OWNER TO mpoint;

--
-- TOC entry 377 (class 1259 OID 33260971)
-- Name: txnpassbook_tbl_10018_10000001_11000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_10000001_11000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_10000001_11000001 FOR VALUES FROM (10000001) TO (11000001);


ALTER TABLE log.txnpassbook_tbl_10018_10000001_11000001 OWNER TO mpoint;

--
-- TOC entry 368 (class 1259 OID 33260881)
-- Name: txnpassbook_tbl_10018_1000001_2000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_1000001_2000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_1000001_2000001 FOR VALUES FROM (1000001) TO (2000001);


ALTER TABLE log.txnpassbook_tbl_10018_1000001_2000001 OWNER TO mpoint;

--
-- TOC entry 378 (class 1259 OID 33260981)
-- Name: txnpassbook_tbl_10018_11000001_12000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_11000001_12000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_11000001_12000001 FOR VALUES FROM (11000001) TO (12000001);


ALTER TABLE log.txnpassbook_tbl_10018_11000001_12000001 OWNER TO mpoint;

--
-- TOC entry 379 (class 1259 OID 33260991)
-- Name: txnpassbook_tbl_10018_12000001_13000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_12000001_13000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_12000001_13000001 FOR VALUES FROM (12000001) TO (13000001);


ALTER TABLE log.txnpassbook_tbl_10018_12000001_13000001 OWNER TO mpoint;

--
-- TOC entry 380 (class 1259 OID 33261001)
-- Name: txnpassbook_tbl_10018_13000001_14000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_13000001_14000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_13000001_14000001 FOR VALUES FROM (13000001) TO (14000001);


ALTER TABLE log.txnpassbook_tbl_10018_13000001_14000001 OWNER TO mpoint;

--
-- TOC entry 381 (class 1259 OID 33261011)
-- Name: txnpassbook_tbl_10018_14000001_15000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_14000001_15000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_14000001_15000001 FOR VALUES FROM (14000001) TO (15000001);


ALTER TABLE log.txnpassbook_tbl_10018_14000001_15000001 OWNER TO mpoint;

--
-- TOC entry 382 (class 1259 OID 33261021)
-- Name: txnpassbook_tbl_10018_15000001_16000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_15000001_16000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_15000001_16000001 FOR VALUES FROM (15000001) TO (16000001);


ALTER TABLE log.txnpassbook_tbl_10018_15000001_16000001 OWNER TO mpoint;

--
-- TOC entry 383 (class 1259 OID 33261031)
-- Name: txnpassbook_tbl_10018_16000001_17000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_16000001_17000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_16000001_17000001 FOR VALUES FROM (16000001) TO (17000001);


ALTER TABLE log.txnpassbook_tbl_10018_16000001_17000001 OWNER TO mpoint;

--
-- TOC entry 384 (class 1259 OID 33261041)
-- Name: txnpassbook_tbl_10018_17000001_18000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_17000001_18000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_17000001_18000001 FOR VALUES FROM (17000001) TO (18000001);


ALTER TABLE log.txnpassbook_tbl_10018_17000001_18000001 OWNER TO mpoint;

--
-- TOC entry 385 (class 1259 OID 33261051)
-- Name: txnpassbook_tbl_10018_18000001_19000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_18000001_19000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_18000001_19000001 FOR VALUES FROM (18000001) TO (19000001);


ALTER TABLE log.txnpassbook_tbl_10018_18000001_19000001 OWNER TO mpoint;

--
-- TOC entry 386 (class 1259 OID 33261061)
-- Name: txnpassbook_tbl_10018_19000001_20000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_19000001_20000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_19000001_20000001 FOR VALUES FROM (19000001) TO (20000001);


ALTER TABLE log.txnpassbook_tbl_10018_19000001_20000001 OWNER TO mpoint;

--
-- TOC entry 367 (class 1259 OID 33260871)
-- Name: txnpassbook_tbl_10018_1_1000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_1_1000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_1_1000001 FOR VALUES FROM (1) TO (1000001);


ALTER TABLE log.txnpassbook_tbl_10018_1_1000001 OWNER TO mpoint;

--
-- TOC entry 369 (class 1259 OID 33260891)
-- Name: txnpassbook_tbl_10018_2000001_3000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_2000001_3000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_2000001_3000001 FOR VALUES FROM (2000001) TO (3000001);


ALTER TABLE log.txnpassbook_tbl_10018_2000001_3000001 OWNER TO mpoint;

--
-- TOC entry 370 (class 1259 OID 33260901)
-- Name: txnpassbook_tbl_10018_3000001_4000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_3000001_4000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_3000001_4000001 FOR VALUES FROM (3000001) TO (4000001);


ALTER TABLE log.txnpassbook_tbl_10018_3000001_4000001 OWNER TO mpoint;

--
-- TOC entry 371 (class 1259 OID 33260911)
-- Name: txnpassbook_tbl_10018_4000001_5000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_4000001_5000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_4000001_5000001 FOR VALUES FROM (4000001) TO (5000001);


ALTER TABLE log.txnpassbook_tbl_10018_4000001_5000001 OWNER TO mpoint;

--
-- TOC entry 372 (class 1259 OID 33260921)
-- Name: txnpassbook_tbl_10018_5000001_6000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_5000001_6000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_5000001_6000001 FOR VALUES FROM (5000001) TO (6000001);


ALTER TABLE log.txnpassbook_tbl_10018_5000001_6000001 OWNER TO mpoint;

--
-- TOC entry 373 (class 1259 OID 33260931)
-- Name: txnpassbook_tbl_10018_6000001_7000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_6000001_7000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_6000001_7000001 FOR VALUES FROM (6000001) TO (7000001);


ALTER TABLE log.txnpassbook_tbl_10018_6000001_7000001 OWNER TO mpoint;

--
-- TOC entry 374 (class 1259 OID 33260941)
-- Name: txnpassbook_tbl_10018_7000001_8000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_7000001_8000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_7000001_8000001 FOR VALUES FROM (7000001) TO (8000001);


ALTER TABLE log.txnpassbook_tbl_10018_7000001_8000001 OWNER TO mpoint;

--
-- TOC entry 375 (class 1259 OID 33260951)
-- Name: txnpassbook_tbl_10018_8000001_9000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_8000001_9000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_8000001_9000001 FOR VALUES FROM (8000001) TO (9000001);


ALTER TABLE log.txnpassbook_tbl_10018_8000001_9000001 OWNER TO mpoint;

--
-- TOC entry 376 (class 1259 OID 33260961)
-- Name: txnpassbook_tbl_10018_9000001_10000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10018_9000001_10000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10018 ATTACH PARTITION log.txnpassbook_tbl_10018_9000001_10000001 FOR VALUES FROM (9000001) TO (10000001);


ALTER TABLE log.txnpassbook_tbl_10018_9000001_10000001 OWNER TO mpoint;

--
-- TOC entry 387 (class 1259 OID 33261077)
-- Name: txnpassbook_tbl_10020; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
)
PARTITION BY RANGE (transactionid);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10020 FOR VALUES IN (10020);


ALTER TABLE log.txnpassbook_tbl_10020 OWNER TO mpoint;

--
-- TOC entry 398 (class 1259 OID 33261187)
-- Name: txnpassbook_tbl_10020_10000001_11000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_10000001_11000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_10000001_11000001 FOR VALUES FROM (10000001) TO (11000001);


ALTER TABLE log.txnpassbook_tbl_10020_10000001_11000001 OWNER TO mpoint;

--
-- TOC entry 389 (class 1259 OID 33261097)
-- Name: txnpassbook_tbl_10020_1000001_2000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_1000001_2000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_1000001_2000001 FOR VALUES FROM (1000001) TO (2000001);


ALTER TABLE log.txnpassbook_tbl_10020_1000001_2000001 OWNER TO mpoint;

--
-- TOC entry 399 (class 1259 OID 33261197)
-- Name: txnpassbook_tbl_10020_11000001_12000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_11000001_12000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_11000001_12000001 FOR VALUES FROM (11000001) TO (12000001);


ALTER TABLE log.txnpassbook_tbl_10020_11000001_12000001 OWNER TO mpoint;

--
-- TOC entry 400 (class 1259 OID 33261207)
-- Name: txnpassbook_tbl_10020_12000001_13000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_12000001_13000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_12000001_13000001 FOR VALUES FROM (12000001) TO (13000001);


ALTER TABLE log.txnpassbook_tbl_10020_12000001_13000001 OWNER TO mpoint;

--
-- TOC entry 401 (class 1259 OID 33261217)
-- Name: txnpassbook_tbl_10020_13000001_14000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_13000001_14000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_13000001_14000001 FOR VALUES FROM (13000001) TO (14000001);


ALTER TABLE log.txnpassbook_tbl_10020_13000001_14000001 OWNER TO mpoint;

--
-- TOC entry 402 (class 1259 OID 33261227)
-- Name: txnpassbook_tbl_10020_14000001_15000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_14000001_15000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_14000001_15000001 FOR VALUES FROM (14000001) TO (15000001);


ALTER TABLE log.txnpassbook_tbl_10020_14000001_15000001 OWNER TO mpoint;

--
-- TOC entry 403 (class 1259 OID 33261237)
-- Name: txnpassbook_tbl_10020_15000001_16000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_15000001_16000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_15000001_16000001 FOR VALUES FROM (15000001) TO (16000001);


ALTER TABLE log.txnpassbook_tbl_10020_15000001_16000001 OWNER TO mpoint;

--
-- TOC entry 404 (class 1259 OID 33261247)
-- Name: txnpassbook_tbl_10020_16000001_17000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_16000001_17000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_16000001_17000001 FOR VALUES FROM (16000001) TO (17000001);


ALTER TABLE log.txnpassbook_tbl_10020_16000001_17000001 OWNER TO mpoint;

--
-- TOC entry 405 (class 1259 OID 33261257)
-- Name: txnpassbook_tbl_10020_17000001_18000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_17000001_18000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_17000001_18000001 FOR VALUES FROM (17000001) TO (18000001);


ALTER TABLE log.txnpassbook_tbl_10020_17000001_18000001 OWNER TO mpoint;

--
-- TOC entry 406 (class 1259 OID 33261267)
-- Name: txnpassbook_tbl_10020_18000001_19000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_18000001_19000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_18000001_19000001 FOR VALUES FROM (18000001) TO (19000001);


ALTER TABLE log.txnpassbook_tbl_10020_18000001_19000001 OWNER TO mpoint;

--
-- TOC entry 407 (class 1259 OID 33261277)
-- Name: txnpassbook_tbl_10020_19000001_20000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_19000001_20000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_19000001_20000001 FOR VALUES FROM (19000001) TO (20000001);


ALTER TABLE log.txnpassbook_tbl_10020_19000001_20000001 OWNER TO mpoint;

--
-- TOC entry 388 (class 1259 OID 33261087)
-- Name: txnpassbook_tbl_10020_1_1000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_1_1000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_1_1000001 FOR VALUES FROM (1) TO (1000001);


ALTER TABLE log.txnpassbook_tbl_10020_1_1000001 OWNER TO mpoint;

--
-- TOC entry 390 (class 1259 OID 33261107)
-- Name: txnpassbook_tbl_10020_2000001_3000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_2000001_3000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_2000001_3000001 FOR VALUES FROM (2000001) TO (3000001);


ALTER TABLE log.txnpassbook_tbl_10020_2000001_3000001 OWNER TO mpoint;

--
-- TOC entry 391 (class 1259 OID 33261117)
-- Name: txnpassbook_tbl_10020_3000001_4000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_3000001_4000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_3000001_4000001 FOR VALUES FROM (3000001) TO (4000001);


ALTER TABLE log.txnpassbook_tbl_10020_3000001_4000001 OWNER TO mpoint;

--
-- TOC entry 392 (class 1259 OID 33261127)
-- Name: txnpassbook_tbl_10020_4000001_5000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_4000001_5000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_4000001_5000001 FOR VALUES FROM (4000001) TO (5000001);


ALTER TABLE log.txnpassbook_tbl_10020_4000001_5000001 OWNER TO mpoint;

--
-- TOC entry 393 (class 1259 OID 33261137)
-- Name: txnpassbook_tbl_10020_5000001_6000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_5000001_6000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_5000001_6000001 FOR VALUES FROM (5000001) TO (6000001);


ALTER TABLE log.txnpassbook_tbl_10020_5000001_6000001 OWNER TO mpoint;

--
-- TOC entry 394 (class 1259 OID 33261147)
-- Name: txnpassbook_tbl_10020_6000001_7000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_6000001_7000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_6000001_7000001 FOR VALUES FROM (6000001) TO (7000001);


ALTER TABLE log.txnpassbook_tbl_10020_6000001_7000001 OWNER TO mpoint;

--
-- TOC entry 395 (class 1259 OID 33261157)
-- Name: txnpassbook_tbl_10020_7000001_8000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_7000001_8000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_7000001_8000001 FOR VALUES FROM (7000001) TO (8000001);


ALTER TABLE log.txnpassbook_tbl_10020_7000001_8000001 OWNER TO mpoint;

--
-- TOC entry 396 (class 1259 OID 33261167)
-- Name: txnpassbook_tbl_10020_8000001_9000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_8000001_9000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_8000001_9000001 FOR VALUES FROM (8000001) TO (9000001);


ALTER TABLE log.txnpassbook_tbl_10020_8000001_9000001 OWNER TO mpoint;

--
-- TOC entry 397 (class 1259 OID 33261177)
-- Name: txnpassbook_tbl_10020_9000001_10000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10020_9000001_10000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10020 ATTACH PARTITION log.txnpassbook_tbl_10020_9000001_10000001 FOR VALUES FROM (9000001) TO (10000001);


ALTER TABLE log.txnpassbook_tbl_10020_9000001_10000001 OWNER TO mpoint;

--
-- TOC entry 408 (class 1259 OID 33261289)
-- Name: txnpassbook_tbl_10021; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
)
PARTITION BY RANGE (transactionid);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10021 FOR VALUES IN (10021);


ALTER TABLE log.txnpassbook_tbl_10021 OWNER TO mpoint;

--
-- TOC entry 419 (class 1259 OID 33261399)
-- Name: txnpassbook_tbl_10021_10000001_11000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_10000001_11000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_10000001_11000001 FOR VALUES FROM (10000001) TO (11000001);


ALTER TABLE log.txnpassbook_tbl_10021_10000001_11000001 OWNER TO mpoint;

--
-- TOC entry 410 (class 1259 OID 33261309)
-- Name: txnpassbook_tbl_10021_1000001_2000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_1000001_2000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_1000001_2000001 FOR VALUES FROM (1000001) TO (2000001);


ALTER TABLE log.txnpassbook_tbl_10021_1000001_2000001 OWNER TO mpoint;

--
-- TOC entry 420 (class 1259 OID 33261409)
-- Name: txnpassbook_tbl_10021_11000001_12000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_11000001_12000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_11000001_12000001 FOR VALUES FROM (11000001) TO (12000001);


ALTER TABLE log.txnpassbook_tbl_10021_11000001_12000001 OWNER TO mpoint;

--
-- TOC entry 421 (class 1259 OID 33261419)
-- Name: txnpassbook_tbl_10021_12000001_13000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_12000001_13000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_12000001_13000001 FOR VALUES FROM (12000001) TO (13000001);


ALTER TABLE log.txnpassbook_tbl_10021_12000001_13000001 OWNER TO mpoint;

--
-- TOC entry 422 (class 1259 OID 33261429)
-- Name: txnpassbook_tbl_10021_13000001_14000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_13000001_14000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_13000001_14000001 FOR VALUES FROM (13000001) TO (14000001);


ALTER TABLE log.txnpassbook_tbl_10021_13000001_14000001 OWNER TO mpoint;

--
-- TOC entry 423 (class 1259 OID 33261439)
-- Name: txnpassbook_tbl_10021_14000001_15000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_14000001_15000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_14000001_15000001 FOR VALUES FROM (14000001) TO (15000001);


ALTER TABLE log.txnpassbook_tbl_10021_14000001_15000001 OWNER TO mpoint;

--
-- TOC entry 424 (class 1259 OID 33261449)
-- Name: txnpassbook_tbl_10021_15000001_16000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_15000001_16000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_15000001_16000001 FOR VALUES FROM (15000001) TO (16000001);


ALTER TABLE log.txnpassbook_tbl_10021_15000001_16000001 OWNER TO mpoint;

--
-- TOC entry 425 (class 1259 OID 33261459)
-- Name: txnpassbook_tbl_10021_16000001_17000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_16000001_17000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_16000001_17000001 FOR VALUES FROM (16000001) TO (17000001);


ALTER TABLE log.txnpassbook_tbl_10021_16000001_17000001 OWNER TO mpoint;

--
-- TOC entry 426 (class 1259 OID 33261469)
-- Name: txnpassbook_tbl_10021_17000001_18000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_17000001_18000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_17000001_18000001 FOR VALUES FROM (17000001) TO (18000001);


ALTER TABLE log.txnpassbook_tbl_10021_17000001_18000001 OWNER TO mpoint;

--
-- TOC entry 427 (class 1259 OID 33261479)
-- Name: txnpassbook_tbl_10021_18000001_19000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_18000001_19000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_18000001_19000001 FOR VALUES FROM (18000001) TO (19000001);


ALTER TABLE log.txnpassbook_tbl_10021_18000001_19000001 OWNER TO mpoint;

--
-- TOC entry 428 (class 1259 OID 33261489)
-- Name: txnpassbook_tbl_10021_19000001_20000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_19000001_20000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_19000001_20000001 FOR VALUES FROM (19000001) TO (20000001);


ALTER TABLE log.txnpassbook_tbl_10021_19000001_20000001 OWNER TO mpoint;

--
-- TOC entry 409 (class 1259 OID 33261299)
-- Name: txnpassbook_tbl_10021_1_1000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_1_1000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_1_1000001 FOR VALUES FROM (1) TO (1000001);


ALTER TABLE log.txnpassbook_tbl_10021_1_1000001 OWNER TO mpoint;

--
-- TOC entry 411 (class 1259 OID 33261319)
-- Name: txnpassbook_tbl_10021_2000001_3000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_2000001_3000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_2000001_3000001 FOR VALUES FROM (2000001) TO (3000001);


ALTER TABLE log.txnpassbook_tbl_10021_2000001_3000001 OWNER TO mpoint;

--
-- TOC entry 412 (class 1259 OID 33261329)
-- Name: txnpassbook_tbl_10021_3000001_4000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_3000001_4000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_3000001_4000001 FOR VALUES FROM (3000001) TO (4000001);


ALTER TABLE log.txnpassbook_tbl_10021_3000001_4000001 OWNER TO mpoint;

--
-- TOC entry 413 (class 1259 OID 33261339)
-- Name: txnpassbook_tbl_10021_4000001_5000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_4000001_5000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_4000001_5000001 FOR VALUES FROM (4000001) TO (5000001);


ALTER TABLE log.txnpassbook_tbl_10021_4000001_5000001 OWNER TO mpoint;

--
-- TOC entry 414 (class 1259 OID 33261349)
-- Name: txnpassbook_tbl_10021_5000001_6000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_5000001_6000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_5000001_6000001 FOR VALUES FROM (5000001) TO (6000001);


ALTER TABLE log.txnpassbook_tbl_10021_5000001_6000001 OWNER TO mpoint;

--
-- TOC entry 415 (class 1259 OID 33261359)
-- Name: txnpassbook_tbl_10021_6000001_7000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_6000001_7000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_6000001_7000001 FOR VALUES FROM (6000001) TO (7000001);


ALTER TABLE log.txnpassbook_tbl_10021_6000001_7000001 OWNER TO mpoint;

--
-- TOC entry 416 (class 1259 OID 33261369)
-- Name: txnpassbook_tbl_10021_7000001_8000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_7000001_8000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_7000001_8000001 FOR VALUES FROM (7000001) TO (8000001);


ALTER TABLE log.txnpassbook_tbl_10021_7000001_8000001 OWNER TO mpoint;

--
-- TOC entry 417 (class 1259 OID 33261379)
-- Name: txnpassbook_tbl_10021_8000001_9000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_8000001_9000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_8000001_9000001 FOR VALUES FROM (8000001) TO (9000001);


ALTER TABLE log.txnpassbook_tbl_10021_8000001_9000001 OWNER TO mpoint;

--
-- TOC entry 418 (class 1259 OID 33261389)
-- Name: txnpassbook_tbl_10021_9000001_10000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10021_9000001_10000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10021 ATTACH PARTITION log.txnpassbook_tbl_10021_9000001_10000001 FOR VALUES FROM (9000001) TO (10000001);


ALTER TABLE log.txnpassbook_tbl_10021_9000001_10000001 OWNER TO mpoint;

--
-- TOC entry 450 (class 1259 OID 33261716)
-- Name: txnpassbook_tbl_10022; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10022 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10022 FOR VALUES IN (10022);


ALTER TABLE log.txnpassbook_tbl_10022 OWNER TO mpoint;

--
-- TOC entry 451 (class 1259 OID 33261726)
-- Name: txnpassbook_tbl_10060; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10060 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10060 FOR VALUES IN (10060);


ALTER TABLE log.txnpassbook_tbl_10060 OWNER TO mpoint;

--
-- TOC entry 452 (class 1259 OID 33261736)
-- Name: txnpassbook_tbl_10061; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10061 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10061 FOR VALUES IN (10061);


ALTER TABLE log.txnpassbook_tbl_10061 OWNER TO mpoint;

--
-- TOC entry 453 (class 1259 OID 33261746)
-- Name: txnpassbook_tbl_10062; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10062 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10062 FOR VALUES IN (10062);


ALTER TABLE log.txnpassbook_tbl_10062 OWNER TO mpoint;

--
-- TOC entry 454 (class 1259 OID 33261756)
-- Name: txnpassbook_tbl_10065; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10065 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10065 FOR VALUES IN (10065);


ALTER TABLE log.txnpassbook_tbl_10065 OWNER TO mpoint;

--
-- TOC entry 458 (class 1259 OID 33261799)
-- Name: txnpassbook_tbl_10066; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10066 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10066 FOR VALUES IN (10066);


ALTER TABLE log.txnpassbook_tbl_10066 OWNER TO mpoint;

--
-- TOC entry 455 (class 1259 OID 33261766)
-- Name: txnpassbook_tbl_10067; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10067 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10067 FOR VALUES IN (10067);


ALTER TABLE log.txnpassbook_tbl_10067 OWNER TO mpoint;

--
-- TOC entry 429 (class 1259 OID 33261500)
-- Name: txnpassbook_tbl_10069; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
)
PARTITION BY RANGE (transactionid);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10069 FOR VALUES IN (10069);


ALTER TABLE log.txnpassbook_tbl_10069 OWNER TO mpoint;

--
-- TOC entry 440 (class 1259 OID 33261610)
-- Name: txnpassbook_tbl_10069_10000001_11000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_10000001_11000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_10000001_11000001 FOR VALUES FROM (10000001) TO (11000001);


ALTER TABLE log.txnpassbook_tbl_10069_10000001_11000001 OWNER TO mpoint;

--
-- TOC entry 431 (class 1259 OID 33261520)
-- Name: txnpassbook_tbl_10069_1000001_2000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_1000001_2000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_1000001_2000001 FOR VALUES FROM (1000001) TO (2000001);


ALTER TABLE log.txnpassbook_tbl_10069_1000001_2000001 OWNER TO mpoint;

--
-- TOC entry 441 (class 1259 OID 33261620)
-- Name: txnpassbook_tbl_10069_11000001_12000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_11000001_12000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_11000001_12000001 FOR VALUES FROM (11000001) TO (12000001);


ALTER TABLE log.txnpassbook_tbl_10069_11000001_12000001 OWNER TO mpoint;

--
-- TOC entry 442 (class 1259 OID 33261630)
-- Name: txnpassbook_tbl_10069_12000001_13000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_12000001_13000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_12000001_13000001 FOR VALUES FROM (12000001) TO (13000001);


ALTER TABLE log.txnpassbook_tbl_10069_12000001_13000001 OWNER TO mpoint;

--
-- TOC entry 443 (class 1259 OID 33261640)
-- Name: txnpassbook_tbl_10069_13000001_14000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_13000001_14000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_13000001_14000001 FOR VALUES FROM (13000001) TO (14000001);


ALTER TABLE log.txnpassbook_tbl_10069_13000001_14000001 OWNER TO mpoint;

--
-- TOC entry 444 (class 1259 OID 33261650)
-- Name: txnpassbook_tbl_10069_14000001_15000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_14000001_15000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_14000001_15000001 FOR VALUES FROM (14000001) TO (15000001);


ALTER TABLE log.txnpassbook_tbl_10069_14000001_15000001 OWNER TO mpoint;

--
-- TOC entry 445 (class 1259 OID 33261660)
-- Name: txnpassbook_tbl_10069_15000001_16000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_15000001_16000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_15000001_16000001 FOR VALUES FROM (15000001) TO (16000001);


ALTER TABLE log.txnpassbook_tbl_10069_15000001_16000001 OWNER TO mpoint;

--
-- TOC entry 446 (class 1259 OID 33261670)
-- Name: txnpassbook_tbl_10069_16000001_17000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_16000001_17000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_16000001_17000001 FOR VALUES FROM (16000001) TO (17000001);


ALTER TABLE log.txnpassbook_tbl_10069_16000001_17000001 OWNER TO mpoint;

--
-- TOC entry 447 (class 1259 OID 33261680)
-- Name: txnpassbook_tbl_10069_17000001_18000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_17000001_18000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_17000001_18000001 FOR VALUES FROM (17000001) TO (18000001);


ALTER TABLE log.txnpassbook_tbl_10069_17000001_18000001 OWNER TO mpoint;

--
-- TOC entry 448 (class 1259 OID 33261690)
-- Name: txnpassbook_tbl_10069_18000001_19000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_18000001_19000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_18000001_19000001 FOR VALUES FROM (18000001) TO (19000001);


ALTER TABLE log.txnpassbook_tbl_10069_18000001_19000001 OWNER TO mpoint;

--
-- TOC entry 449 (class 1259 OID 33261700)
-- Name: txnpassbook_tbl_10069_19000001_20000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_19000001_20000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_19000001_20000001 FOR VALUES FROM (19000001) TO (20000001);


ALTER TABLE log.txnpassbook_tbl_10069_19000001_20000001 OWNER TO mpoint;

--
-- TOC entry 430 (class 1259 OID 33261510)
-- Name: txnpassbook_tbl_10069_1_1000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_1_1000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_1_1000001 FOR VALUES FROM (1) TO (1000001);


ALTER TABLE log.txnpassbook_tbl_10069_1_1000001 OWNER TO mpoint;

--
-- TOC entry 432 (class 1259 OID 33261530)
-- Name: txnpassbook_tbl_10069_2000001_3000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_2000001_3000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_2000001_3000001 FOR VALUES FROM (2000001) TO (3000001);


ALTER TABLE log.txnpassbook_tbl_10069_2000001_3000001 OWNER TO mpoint;

--
-- TOC entry 433 (class 1259 OID 33261540)
-- Name: txnpassbook_tbl_10069_3000001_4000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_3000001_4000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_3000001_4000001 FOR VALUES FROM (3000001) TO (4000001);


ALTER TABLE log.txnpassbook_tbl_10069_3000001_4000001 OWNER TO mpoint;

--
-- TOC entry 434 (class 1259 OID 33261550)
-- Name: txnpassbook_tbl_10069_4000001_5000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_4000001_5000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_4000001_5000001 FOR VALUES FROM (4000001) TO (5000001);


ALTER TABLE log.txnpassbook_tbl_10069_4000001_5000001 OWNER TO mpoint;

--
-- TOC entry 435 (class 1259 OID 33261560)
-- Name: txnpassbook_tbl_10069_5000001_6000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_5000001_6000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_5000001_6000001 FOR VALUES FROM (5000001) TO (6000001);


ALTER TABLE log.txnpassbook_tbl_10069_5000001_6000001 OWNER TO mpoint;

--
-- TOC entry 436 (class 1259 OID 33261570)
-- Name: txnpassbook_tbl_10069_6000001_7000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_6000001_7000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_6000001_7000001 FOR VALUES FROM (6000001) TO (7000001);


ALTER TABLE log.txnpassbook_tbl_10069_6000001_7000001 OWNER TO mpoint;

--
-- TOC entry 437 (class 1259 OID 33261580)
-- Name: txnpassbook_tbl_10069_7000001_8000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_7000001_8000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_7000001_8000001 FOR VALUES FROM (7000001) TO (8000001);


ALTER TABLE log.txnpassbook_tbl_10069_7000001_8000001 OWNER TO mpoint;

--
-- TOC entry 438 (class 1259 OID 33261590)
-- Name: txnpassbook_tbl_10069_8000001_9000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_8000001_9000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_8000001_9000001 FOR VALUES FROM (8000001) TO (9000001);


ALTER TABLE log.txnpassbook_tbl_10069_8000001_9000001 OWNER TO mpoint;

--
-- TOC entry 439 (class 1259 OID 33261600)
-- Name: txnpassbook_tbl_10069_9000001_10000001; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10069_9000001_10000001 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl_10069 ATTACH PARTITION log.txnpassbook_tbl_10069_9000001_10000001 FOR VALUES FROM (9000001) TO (10000001);


ALTER TABLE log.txnpassbook_tbl_10069_9000001_10000001 OWNER TO mpoint;

--
-- TOC entry 459 (class 1259 OID 33261809)
-- Name: txnpassbook_tbl_10070; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10070 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10070 FOR VALUES IN (10070);


ALTER TABLE log.txnpassbook_tbl_10070 OWNER TO mpoint;

--
-- TOC entry 460 (class 1259 OID 33261819)
-- Name: txnpassbook_tbl_10071; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10071 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10071 FOR VALUES IN (10071);


ALTER TABLE log.txnpassbook_tbl_10071 OWNER TO mpoint;

--
-- TOC entry 461 (class 1259 OID 33261829)
-- Name: txnpassbook_tbl_10072; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10072 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10072 FOR VALUES IN (10072);


ALTER TABLE log.txnpassbook_tbl_10072 OWNER TO mpoint;

--
-- TOC entry 456 (class 1259 OID 33261776)
-- Name: txnpassbook_tbl_10073; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10073 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10073 FOR VALUES IN (10073);


ALTER TABLE log.txnpassbook_tbl_10073 OWNER TO mpoint;

--
-- TOC entry 462 (class 1259 OID 33261839)
-- Name: txnpassbook_tbl_10074; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10074 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10074 FOR VALUES IN (10074);


ALTER TABLE log.txnpassbook_tbl_10074 OWNER TO mpoint;

--
-- TOC entry 463 (class 1259 OID 33261849)
-- Name: txnpassbook_tbl_10075; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10075 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10075 FOR VALUES IN (10075);


ALTER TABLE log.txnpassbook_tbl_10075 OWNER TO mpoint;

--
-- TOC entry 464 (class 1259 OID 33261859)
-- Name: txnpassbook_tbl_10076; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10076 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10076 FOR VALUES IN (10076);


ALTER TABLE log.txnpassbook_tbl_10076 OWNER TO mpoint;

--
-- TOC entry 465 (class 1259 OID 33261869)
-- Name: txnpassbook_tbl_10077; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10077 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10077 FOR VALUES IN (10077);


ALTER TABLE log.txnpassbook_tbl_10077 OWNER TO mpoint;

--
-- TOC entry 466 (class 1259 OID 33261879)
-- Name: txnpassbook_tbl_10078; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10078 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10078 FOR VALUES IN (10078);


ALTER TABLE log.txnpassbook_tbl_10078 OWNER TO mpoint;

--
-- TOC entry 467 (class 1259 OID 33261889)
-- Name: txnpassbook_tbl_10079; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10079 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10079 FOR VALUES IN (10079);


ALTER TABLE log.txnpassbook_tbl_10079 OWNER TO mpoint;

--
-- TOC entry 468 (class 1259 OID 33261899)
-- Name: txnpassbook_tbl_10080; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10080 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10080 FOR VALUES IN (10080);


ALTER TABLE log.txnpassbook_tbl_10080 OWNER TO mpoint;

--
-- TOC entry 475 (class 1259 OID 33808081)
-- Name: txnpassbook_tbl_10081; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10081 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10081 FOR VALUES IN (10081);


ALTER TABLE log.txnpassbook_tbl_10081 OWNER TO mpoint;

--
-- TOC entry 469 (class 1259 OID 33261909)
-- Name: txnpassbook_tbl_10089; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10089 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10089 FOR VALUES IN (10089);


ALTER TABLE log.txnpassbook_tbl_10089 OWNER TO mpoint;

--
-- TOC entry 470 (class 1259 OID 33261919)
-- Name: txnpassbook_tbl_10098; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10098 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10098 FOR VALUES IN (10098);


ALTER TABLE log.txnpassbook_tbl_10098 OWNER TO mpoint;

--
-- TOC entry 457 (class 1259 OID 33261786)
-- Name: txnpassbook_tbl_10099; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_10099 (
    id integer DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass) NOT NULL,
    clientid integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);
ALTER TABLE ONLY log.txnpassbook_tbl ATTACH PARTITION log.txnpassbook_tbl_10099 FOR VALUES IN (10099);


ALTER TABLE log.txnpassbook_tbl_10099 OWNER TO mpoint;

--
-- TOC entry 361 (class 1259 OID 8365104)
-- Name: txnpassbook_tbl_backup_20200401; Type: TABLE; Schema: log; Owner: mpoint
--

CREATE TABLE log.txnpassbook_tbl_backup_20200401 (
    id integer NOT NULL,
    transactionid integer NOT NULL,
    amount integer NOT NULL,
    currencyid integer NOT NULL,
    requestedopt integer,
    performedopt integer,
    status character varying(20) NOT NULL,
    extref character varying(1000),
    extrefidentifier character varying(100),
    enabled boolean DEFAULT true,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now()
);


ALTER TABLE log.txnpassbook_tbl_backup_20200401 OWNER TO mpoint;

--
-- TOC entry 6904 (class 0 OID 0)
-- Dependencies: 361
-- Name: COLUMN txnpassbook_tbl_backup_20200401.transactionid; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON COLUMN log.txnpassbook_tbl_backup_20200401.transactionid IS 'Primary Key of log.transaction_tbl';


--
-- TOC entry 6905 (class 0 OID 0)
-- Dependencies: 361
-- Name: COLUMN txnpassbook_tbl_backup_20200401.amount; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON COLUMN log.txnpassbook_tbl_backup_20200401.amount IS 'Amount used for the operation';


--
-- TOC entry 6906 (class 0 OID 0)
-- Dependencies: 361
-- Name: COLUMN txnpassbook_tbl_backup_20200401.currencyid; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON COLUMN log.txnpassbook_tbl_backup_20200401.currencyid IS 'Current used for the operation
primary key of system.currency_tbl';


--
-- TOC entry 6907 (class 0 OID 0)
-- Dependencies: 361
-- Name: COLUMN txnpassbook_tbl_backup_20200401.requestedopt; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON COLUMN log.txnpassbook_tbl_backup_20200401.requestedopt IS 'Request operation
Â·         Initialize
Â·         Authorize
Â·         Cancel
Â·         Capture
Â·         Refund';


--
-- TOC entry 6908 (class 0 OID 0)
-- Dependencies: 361
-- Name: COLUMN txnpassbook_tbl_backup_20200401.performedopt; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON COLUMN log.txnpassbook_tbl_backup_20200401.performedopt IS 'Based on requested operations which are not performed or pending, next for performing operation will decide.
Entry will contain either requested or performed operation';


--
-- TOC entry 6909 (class 0 OID 0)
-- Dependencies: 361
-- Name: COLUMN txnpassbook_tbl_backup_20200401.extref; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON COLUMN log.txnpassbook_tbl_backup_20200401.extref IS 'Capture, refund and cancel may be related to order, line time, ticket or full txn
This contains the primary id of repective table to fetch all necessary in the callback';


--
-- TOC entry 6910 (class 0 OID 0)
-- Dependencies: 361
-- Name: COLUMN txnpassbook_tbl_backup_20200401.extrefidentifier; Type: COMMENT; Schema: log; Owner: mpoint
--

COMMENT ON COLUMN log.txnpassbook_tbl_backup_20200401.extrefidentifier IS 'Table or entity from which external reference is used';


--
-- TOC entry 360 (class 1259 OID 8365102)
-- Name: txnpassbook_tbl_id_seq_backup; Type: SEQUENCE; Schema: log; Owner: mpoint
--

CREATE SEQUENCE log.txnpassbook_tbl_id_seq_backup
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log.txnpassbook_tbl_id_seq_backup OWNER TO mpoint;

--
-- TOC entry 6912 (class 0 OID 0)
-- Dependencies: 360
-- Name: txnpassbook_tbl_id_seq_backup; Type: SEQUENCE OWNED BY; Schema: log; Owner: mpoint
--

ALTER SEQUENCE log.txnpassbook_tbl_id_seq_backup OWNED BY log.txnpassbook_tbl_backup_20200401.id;


--
-- TOC entry 281 (class 1259 OID 78658)
-- Name: dual; Type: VIEW; Schema: public; Owner: mpoint
--

CREATE VIEW public.dual AS
 SELECT 'Provides compatibility with Oracle when selecting from functions.
Use "SELECT [FUNCTION] FROM DUAL" rather than "SELECT [FUNCTION]"'::text;


ALTER TABLE public.dual OWNER TO mpoint;

--
-- TOC entry 363 (class 1259 OID 22052449)
-- Name: businesstype_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.businesstype_tbl (
    id integer NOT NULL,
    name character varying(50),
    enabled boolean DEFAULT true
);


ALTER TABLE system.businesstype_tbl OWNER TO mpoint;

--
-- TOC entry 362 (class 1259 OID 22052447)
-- Name: businesstype_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.businesstype_tbl_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.businesstype_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6916 (class 0 OID 0)
-- Dependencies: 362
-- Name: businesstype_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.businesstype_tbl_id_seq OWNED BY system.businesstype_tbl.id;


--
-- TOC entry 472 (class 1259 OID 33316421)
-- Name: capturetype_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.capturetype_tbl (
    id integer NOT NULL,
    name character varying(50),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.capturetype_tbl OWNER TO mpoint;

--
-- TOC entry 471 (class 1259 OID 33316419)
-- Name: capturetype_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.capturetype_tbl_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.capturetype_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6918 (class 0 OID 0)
-- Dependencies: 471
-- Name: capturetype_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.capturetype_tbl_id_seq OWNED BY system.capturetype_tbl.id;


--
-- TOC entry 282 (class 1259 OID 78662)
-- Name: card_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.card_tbl (
    id integer NOT NULL,
    name character varying(50),
    logo bytea,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    "position" integer,
    minlength integer,
    maxlength integer,
    cvclength integer,
    paymenttype integer DEFAULT 1 NOT NULL
);


ALTER TABLE system.card_tbl OWNER TO mpoint;

--
-- TOC entry 283 (class 1259 OID 78671)
-- Name: card_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.card_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.card_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6920 (class 0 OID 0)
-- Dependencies: 283
-- Name: card_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.card_tbl_id_seq OWNED BY system.card_tbl.id;


--
-- TOC entry 284 (class 1259 OID 78673)
-- Name: cardchargetype_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.cardchargetype_tbl (
    id integer NOT NULL,
    name character varying(100),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.cardchargetype_tbl OWNER TO mpoint;

--
-- TOC entry 285 (class 1259 OID 78679)
-- Name: cardchargetype_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.cardchargetype_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.cardchargetype_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6922 (class 0 OID 0)
-- Dependencies: 285
-- Name: cardchargetype_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.cardchargetype_tbl_id_seq OWNED BY system.cardchargetype_tbl.id;


--
-- TOC entry 286 (class 1259 OID 78681)
-- Name: cardprefix_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.cardprefix_tbl (
    id integer NOT NULL,
    cardid integer NOT NULL,
    min bigint,
    max bigint,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.cardprefix_tbl OWNER TO mpoint;

--
-- TOC entry 287 (class 1259 OID 78687)
-- Name: cardprefix_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.cardprefix_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.cardprefix_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6924 (class 0 OID 0)
-- Dependencies: 287
-- Name: cardprefix_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.cardprefix_tbl_id_seq OWNED BY system.cardprefix_tbl.id;


--
-- TOC entry 288 (class 1259 OID 78689)
-- Name: cardpricing_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.cardpricing_tbl (
    id integer NOT NULL,
    pricepointid integer,
    cardid integer,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.cardpricing_tbl OWNER TO mpoint;

--
-- TOC entry 289 (class 1259 OID 78695)
-- Name: cardpricing_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.cardpricing_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.cardpricing_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6926 (class 0 OID 0)
-- Dependencies: 289
-- Name: cardpricing_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.cardpricing_tbl_id_seq OWNED BY system.cardpricing_tbl.id;


--
-- TOC entry 290 (class 1259 OID 78697)
-- Name: cardstate_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.cardstate_tbl (
    id integer NOT NULL,
    name character varying(100),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.cardstate_tbl OWNER TO mpoint;

--
-- TOC entry 291 (class 1259 OID 78703)
-- Name: cardstate_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.cardstate_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.cardstate_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6928 (class 0 OID 0)
-- Dependencies: 291
-- Name: cardstate_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.cardstate_tbl_id_seq OWNED BY system.cardstate_tbl.id;


--
-- TOC entry 292 (class 1259 OID 78705)
-- Name: country_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.country_tbl (
    id integer NOT NULL,
    name character varying(100),
    minmob character varying(15),
    maxmob character varying(15),
    channel character varying(10),
    priceformat character varying(18),
    decimals integer,
    addr_lookup boolean DEFAULT false,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    doi boolean DEFAULT false,
    maxbalance integer,
    mintransfer integer,
    add_card_amount integer,
    max_psms_amount integer,
    min_pwd_amount integer,
    min_2fa_amount integer,
    alpha2code character(2),
    alpha3code character(3),
    code integer,
    currencyid integer,
    country_calling_code integer
);


ALTER TABLE system.country_tbl OWNER TO mpoint;

--
-- TOC entry 293 (class 1259 OID 78713)
-- Name: country_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.country_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.country_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6930 (class 0 OID 0)
-- Dependencies: 293
-- Name: country_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.country_tbl_id_seq OWNED BY system.country_tbl.id;


--
-- TOC entry 332 (class 1259 OID 81074)
-- Name: currency_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.currency_tbl (
    id integer NOT NULL,
    name character varying(100),
    code character(3),
    decimals integer,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    symbol character varying(7)
);


ALTER TABLE system.currency_tbl OWNER TO mpoint;

--
-- TOC entry 331 (class 1259 OID 81072)
-- Name: currency_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.currency_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.currency_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6932 (class 0 OID 0)
-- Dependencies: 331
-- Name: currency_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.currency_tbl_id_seq OWNED BY system.currency_tbl.id;


--
-- TOC entry 294 (class 1259 OID 78715)
-- Name: depositoption_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.depositoption_tbl (
    id integer NOT NULL,
    countryid integer NOT NULL,
    amount integer,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.depositoption_tbl OWNER TO mpoint;

--
-- TOC entry 295 (class 1259 OID 78721)
-- Name: depositoption_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.depositoption_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.depositoption_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6934 (class 0 OID 0)
-- Dependencies: 295
-- Name: depositoption_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.depositoption_tbl_id_seq OWNED BY system.depositoption_tbl.id;


--
-- TOC entry 479 (class 1259 OID 36070689)
-- Name: externalreferencetype_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.externalreferencetype_tbl (
    id integer NOT NULL,
    name text NOT NULL,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.externalreferencetype_tbl OWNER TO mpoint;

--
-- TOC entry 478 (class 1259 OID 36070687)
-- Name: externalreferencetype_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.externalreferencetype_tbl_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.externalreferencetype_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6936 (class 0 OID 0)
-- Dependencies: 478
-- Name: externalreferencetype_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.externalreferencetype_tbl_id_seq OWNED BY system.externalreferencetype_tbl.id;


--
-- TOC entry 296 (class 1259 OID 78723)
-- Name: fee_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.fee_tbl (
    id integer NOT NULL,
    typeid integer NOT NULL,
    fromid integer NOT NULL,
    toid integer NOT NULL,
    minfee integer,
    basefee integer,
    share double precision,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.fee_tbl OWNER TO mpoint;

--
-- TOC entry 297 (class 1259 OID 78729)
-- Name: fee_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.fee_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.fee_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6938 (class 0 OID 0)
-- Dependencies: 297
-- Name: fee_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.fee_tbl_id_seq OWNED BY system.fee_tbl.id;


--
-- TOC entry 298 (class 1259 OID 78731)
-- Name: feetype_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.feetype_tbl (
    id integer NOT NULL,
    name character varying(50),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.feetype_tbl OWNER TO mpoint;

--
-- TOC entry 299 (class 1259 OID 78737)
-- Name: feetype_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.feetype_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.feetype_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6940 (class 0 OID 0)
-- Dependencies: 299
-- Name: feetype_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.feetype_tbl_id_seq OWNED BY system.feetype_tbl.id;


--
-- TOC entry 300 (class 1259 OID 78739)
-- Name: flow_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.flow_tbl (
    id integer NOT NULL,
    name character varying(50),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.flow_tbl OWNER TO mpoint;

--
-- TOC entry 301 (class 1259 OID 78745)
-- Name: flow_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.flow_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.flow_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6942 (class 0 OID 0)
-- Dependencies: 301
-- Name: flow_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.flow_tbl_id_seq OWNED BY system.flow_tbl.id;


--
-- TOC entry 302 (class 1259 OID 78747)
-- Name: iinaction_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.iinaction_tbl (
    id integer NOT NULL,
    name character varying(100),
    note text,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.iinaction_tbl OWNER TO mpoint;

--
-- TOC entry 303 (class 1259 OID 78756)
-- Name: iprange_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.iprange_tbl (
    id integer NOT NULL,
    countryid integer NOT NULL,
    min bigint,
    max bigint,
    country character varying(50),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.iprange_tbl OWNER TO mpoint;

--
-- TOC entry 304 (class 1259 OID 78762)
-- Name: iprange_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.iprange_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.iprange_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6945 (class 0 OID 0)
-- Dependencies: 304
-- Name: iprange_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.iprange_tbl_id_seq OWNED BY system.iprange_tbl.id;


--
-- TOC entry 330 (class 1259 OID 81037)
-- Name: paymenttype_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.paymenttype_tbl (
    id integer NOT NULL,
    name character varying(50) NOT NULL
);


ALTER TABLE system.paymenttype_tbl OWNER TO mpoint;

--
-- TOC entry 329 (class 1259 OID 81035)
-- Name: paymenttype_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.paymenttype_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.paymenttype_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6947 (class 0 OID 0)
-- Dependencies: 329
-- Name: paymenttype_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.paymenttype_tbl_id_seq OWNED BY system.paymenttype_tbl.id;


--
-- TOC entry 305 (class 1259 OID 78764)
-- Name: postalcode_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.postalcode_tbl (
    id integer NOT NULL,
    stateid integer NOT NULL,
    code character varying(10),
    city character varying(50),
    latitude real,
    longitude real,
    utc_offset integer,
    vat real DEFAULT 0.0,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.postalcode_tbl OWNER TO mpoint;

--
-- TOC entry 306 (class 1259 OID 78771)
-- Name: postalcode_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.postalcode_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.postalcode_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6949 (class 0 OID 0)
-- Dependencies: 306
-- Name: postalcode_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.postalcode_tbl_id_seq OWNED BY system.postalcode_tbl.id;


--
-- TOC entry 307 (class 1259 OID 78773)
-- Name: pricepoint_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.pricepoint_tbl (
    id integer NOT NULL,
    amount integer,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    currencyid integer
);


ALTER TABLE system.pricepoint_tbl OWNER TO mpoint;

--
-- TOC entry 308 (class 1259 OID 78779)
-- Name: pricepoint_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.pricepoint_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.pricepoint_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6951 (class 0 OID 0)
-- Dependencies: 308
-- Name: pricepoint_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.pricepoint_tbl_id_seq OWNED BY system.pricepoint_tbl.id;


--
-- TOC entry 309 (class 1259 OID 78781)
-- Name: processortype_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.processortype_tbl (
    id integer NOT NULL,
    name character varying(50)
);


ALTER TABLE system.processortype_tbl OWNER TO mpoint;

--
-- TOC entry 310 (class 1259 OID 78784)
-- Name: processortype_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.processortype_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.processortype_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6953 (class 0 OID 0)
-- Dependencies: 310
-- Name: processortype_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.processortype_tbl_id_seq OWNED BY system.processortype_tbl.id;


--
-- TOC entry 339 (class 1259 OID 223831)
-- Name: producttype_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.producttype_tbl (
    id integer NOT NULL,
    name character varying(10) NOT NULL,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.producttype_tbl OWNER TO mpoint;

--
-- TOC entry 6954 (class 0 OID 0)
-- Dependencies: 339
-- Name: TABLE producttype_tbl; Type: COMMENT; Schema: system; Owner: mpoint
--

COMMENT ON TABLE system.producttype_tbl IS 'Contains all product types';


--
-- TOC entry 6955 (class 0 OID 0)
-- Dependencies: 339
-- Name: COLUMN producttype_tbl.id; Type: COMMENT; Schema: system; Owner: mpoint
--

COMMENT ON COLUMN system.producttype_tbl.id IS 'Unique number of product type';


--
-- TOC entry 6956 (class 0 OID 0)
-- Dependencies: 339
-- Name: COLUMN producttype_tbl.name; Type: COMMENT; Schema: system; Owner: mpoint
--

COMMENT ON COLUMN system.producttype_tbl.name IS 'Product type name';


--
-- TOC entry 311 (class 1259 OID 78786)
-- Name: psp_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.psp_tbl (
    id integer NOT NULL,
    name character varying(50),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    system_type integer,
    supportedpartialoperations integer DEFAULT 0 NOT NULL
);


ALTER TABLE system.psp_tbl OWNER TO mpoint;

--
-- TOC entry 6958 (class 0 OID 0)
-- Dependencies: 311
-- Name: COLUMN psp_tbl.supportedpartialoperations; Type: COMMENT; Schema: system; Owner: mpoint
--

COMMENT ON COLUMN system.psp_tbl.supportedpartialoperations IS 'Merchant''s Supported Partial Operations
and PSP''s supported Partial Operations
2 - Partial Capture
3 - Partial Refund
5 - Partial Cancel
Possible values % (constants)
30 % (2 || 3 || 5)   = Capture and Cancel and Refund
15 % (3 || 5) = Refund and Cancel
10 % (2 || 5) = Capture and Cancel
6 % (2 || 3)  = Capture and Refund
5 % 5  = Cancel
3 % 3  = Refund
2 % 2  = Capture';


--
-- TOC entry 312 (class 1259 OID 78792)
-- Name: psp_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.psp_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.psp_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6960 (class 0 OID 0)
-- Dependencies: 312
-- Name: psp_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.psp_tbl_id_seq OWNED BY system.psp_tbl.id;


--
-- TOC entry 313 (class 1259 OID 78794)
-- Name: pspcard_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.pspcard_tbl (
    id integer NOT NULL,
    cardid integer NOT NULL,
    pspid integer NOT NULL,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.pspcard_tbl OWNER TO mpoint;

--
-- TOC entry 314 (class 1259 OID 78800)
-- Name: pspcard_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.pspcard_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.pspcard_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6962 (class 0 OID 0)
-- Dependencies: 314
-- Name: pspcard_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.pspcard_tbl_id_seq OWNED BY system.pspcard_tbl.id;


--
-- TOC entry 315 (class 1259 OID 78802)
-- Name: pspcurrency_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.pspcurrency_tbl (
    id integer NOT NULL,
    pspid integer NOT NULL,
    name character(3),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true,
    currencyid integer
);


ALTER TABLE system.pspcurrency_tbl OWNER TO mpoint;

--
-- TOC entry 316 (class 1259 OID 78808)
-- Name: pspcurrency_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.pspcurrency_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.pspcurrency_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6964 (class 0 OID 0)
-- Dependencies: 316
-- Name: pspcurrency_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.pspcurrency_tbl_id_seq OWNED BY system.pspcurrency_tbl.id;


--
-- TOC entry 355 (class 1259 OID 1463572)
-- Name: retrialtype_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.retrialtype_tbl (
    id integer NOT NULL,
    name character varying(255),
    description character varying(255),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.retrialtype_tbl OWNER TO mpoint;

--
-- TOC entry 354 (class 1259 OID 1463570)
-- Name: retrialtype_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.retrialtype_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.retrialtype_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6966 (class 0 OID 0)
-- Dependencies: 354
-- Name: retrialtype_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.retrialtype_tbl_id_seq OWNED BY system.retrialtype_tbl.id;


--
-- TOC entry 336 (class 1259 OID 81136)
-- Name: sessiontype_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.sessiontype_tbl (
    id integer NOT NULL,
    name character varying(50),
    enable boolean DEFAULT true
);


ALTER TABLE system.sessiontype_tbl OWNER TO mpoint;

--
-- TOC entry 6967 (class 0 OID 0)
-- Dependencies: 336
-- Name: TABLE sessiontype_tbl; Type: COMMENT; Schema: system; Owner: mpoint
--

COMMENT ON TABLE system.sessiontype_tbl IS 'Contains all session type like full payment session, split payment session and etc';


--
-- TOC entry 335 (class 1259 OID 81134)
-- Name: sessiontype_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.sessiontype_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.sessiontype_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6969 (class 0 OID 0)
-- Dependencies: 335
-- Name: sessiontype_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.sessiontype_tbl_id_seq OWNED BY system.sessiontype_tbl.id;


--
-- TOC entry 317 (class 1259 OID 78810)
-- Name: shipping_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.shipping_tbl (
    id integer NOT NULL,
    name character varying(50),
    logourl character varying(100),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.shipping_tbl OWNER TO mpoint;

--
-- TOC entry 318 (class 1259 OID 78816)
-- Name: shipping_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.shipping_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.shipping_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6971 (class 0 OID 0)
-- Dependencies: 318
-- Name: shipping_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.shipping_tbl_id_seq OWNED BY system.shipping_tbl.id;


--
-- TOC entry 319 (class 1259 OID 78818)
-- Name: state_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.state_tbl (
    id integer NOT NULL,
    countryid integer NOT NULL,
    name character varying(50),
    code character varying(5),
    vat real DEFAULT 0.0,
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.state_tbl OWNER TO mpoint;

--
-- TOC entry 320 (class 1259 OID 78825)
-- Name: state_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.state_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.state_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6973 (class 0 OID 0)
-- Dependencies: 320
-- Name: state_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.state_tbl_id_seq OWNED BY system.state_tbl.id;


--
-- TOC entry 347 (class 1259 OID 223952)
-- Name: statisticstype_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.statisticstype_tbl (
    id integer NOT NULL,
    name character varying(200),
    description character varying(200),
    enabled boolean DEFAULT true NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    modified timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE system.statisticstype_tbl OWNER TO mpoint;

--
-- TOC entry 346 (class 1259 OID 223950)
-- Name: statisticstype_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.statisticstype_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.statisticstype_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6975 (class 0 OID 0)
-- Dependencies: 346
-- Name: statisticstype_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.statisticstype_tbl_id_seq OWNED BY system.statisticstype_tbl.id;


--
-- TOC entry 343 (class 1259 OID 223904)
-- Name: triggerunit_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.triggerunit_tbl (
    id integer NOT NULL,
    name character varying(200),
    description character varying(200),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.triggerunit_tbl OWNER TO mpoint;

--
-- TOC entry 342 (class 1259 OID 223902)
-- Name: triggerunit_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.triggerunit_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.triggerunit_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6977 (class 0 OID 0)
-- Dependencies: 342
-- Name: triggerunit_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.triggerunit_tbl_id_seq OWNED BY system.triggerunit_tbl.id;


--
-- TOC entry 321 (class 1259 OID 78827)
-- Name: type_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.type_tbl (
    id integer NOT NULL,
    name character varying(50),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.type_tbl OWNER TO mpoint;

--
-- TOC entry 322 (class 1259 OID 78833)
-- Name: type_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.type_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.type_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6979 (class 0 OID 0)
-- Dependencies: 322
-- Name: type_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.type_tbl_id_seq OWNED BY system.type_tbl.id;


--
-- TOC entry 323 (class 1259 OID 78835)
-- Name: urltype_tbl; Type: TABLE; Schema: system; Owner: mpoint
--

CREATE TABLE system.urltype_tbl (
    id integer NOT NULL,
    name character varying(50),
    created timestamp without time zone DEFAULT now(),
    modified timestamp without time zone DEFAULT now(),
    enabled boolean DEFAULT true
);


ALTER TABLE system.urltype_tbl OWNER TO mpoint;

--
-- TOC entry 324 (class 1259 OID 78841)
-- Name: urltype_tbl_id_seq; Type: SEQUENCE; Schema: system; Owner: mpoint
--

CREATE SEQUENCE system.urltype_tbl_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE system.urltype_tbl_id_seq OWNER TO mpoint;

--
-- TOC entry 6981 (class 0 OID 0)
-- Dependencies: 324
-- Name: urltype_tbl_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: mpoint
--

ALTER SEQUENCE system.urltype_tbl_id_seq OWNED BY system.urltype_tbl.id;


--
-- TOC entry 4756 (class 2604 OID 17098)
-- Name: access_tbl id; Type: DEFAULT; Schema: admin; Owner: mpoint
--

ALTER TABLE ONLY admin.access_tbl ALTER COLUMN id SET DEFAULT nextval('admin.access_tbl_id_seq'::regclass);


--
-- TOC entry 4760 (class 2604 OID 17099)
-- Name: role_tbl id; Type: DEFAULT; Schema: admin; Owner: mpoint
--

ALTER TABLE ONLY admin.role_tbl ALTER COLUMN id SET DEFAULT nextval('admin.role_tbl_id_seq'::regclass);


--
-- TOC entry 4765 (class 2604 OID 17100)
-- Name: roleaccess_tbl id; Type: DEFAULT; Schema: admin; Owner: mpoint
--

ALTER TABLE ONLY admin.roleaccess_tbl ALTER COLUMN id SET DEFAULT nextval('admin.roleaccess_tbl_id_seq'::regclass);


--
-- TOC entry 4769 (class 2604 OID 17101)
-- Name: roleinfo_tbl id; Type: DEFAULT; Schema: admin; Owner: mpoint
--

ALTER TABLE ONLY admin.roleinfo_tbl ALTER COLUMN id SET DEFAULT nextval('admin.roleinfo_tbl_id_seq'::regclass);


--
-- TOC entry 4773 (class 2604 OID 17102)
-- Name: account_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.account_tbl ALTER COLUMN id SET DEFAULT nextval('client.account_tbl_id_seq'::regclass);


--
-- TOC entry 5051 (class 2604 OID 17103)
-- Name: additionalproperty_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.additionalproperty_tbl ALTER COLUMN id SET DEFAULT nextval('client.additionalproperty_tbl_id_seq'::regclass);


--
-- TOC entry 4778 (class 2604 OID 17104)
-- Name: cardaccess_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.cardaccess_tbl ALTER COLUMN id SET DEFAULT nextval('client.cardaccess_tbl_id_seq'::regclass);


--
-- TOC entry 4789 (class 2604 OID 17105)
-- Name: client_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.client_tbl ALTER COLUMN id SET DEFAULT nextval('client.client_tbl_id_seq'::regclass);


--
-- TOC entry 5061 (class 2604 OID 17106)
-- Name: countrycurrency_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.countrycurrency_tbl ALTER COLUMN id SET DEFAULT nextval('client.countrycurrency_tbl_id_seq'::regclass);


--
-- TOC entry 5091 (class 2604 OID 17107)
-- Name: gatewaystat_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.gatewaystat_tbl ALTER COLUMN id SET DEFAULT nextval('client.gatewaystat_tbl_id_seq'::regclass);


--
-- TOC entry 5082 (class 2604 OID 17108)
-- Name: gatewaytrigger_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.gatewaytrigger_tbl ALTER COLUMN id SET DEFAULT nextval('client.gatewaytrigger_tbl_id_seq'::regclass);


--
-- TOC entry 5047 (class 2604 OID 17109)
-- Name: gomobileconfiguration_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.gomobileconfiguration_tbl ALTER COLUMN id SET DEFAULT nextval('client.gomobileconfiguration_tbl_id_seq'::regclass);


--
-- TOC entry 4812 (class 2604 OID 17110)
-- Name: iinlist_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.iinlist_tbl ALTER COLUMN id SET DEFAULT nextval('client.iinlist_tbl_id_seq'::regclass);


--
-- TOC entry 4816 (class 2604 OID 17111)
-- Name: info_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.info_tbl ALTER COLUMN id SET DEFAULT nextval('client.info_tbl_id_seq'::regclass);


--
-- TOC entry 4821 (class 2604 OID 17112)
-- Name: infotype_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.infotype_tbl ALTER COLUMN id SET DEFAULT nextval('client.infotype_tbl_id_seq'::regclass);


--
-- TOC entry 4825 (class 2604 OID 17113)
-- Name: ipaddress_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.ipaddress_tbl ALTER COLUMN id SET DEFAULT nextval('client.ipaddress_tbl_id_seq'::regclass);


--
-- TOC entry 4829 (class 2604 OID 17114)
-- Name: keyword_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.keyword_tbl ALTER COLUMN id SET DEFAULT nextval('client.keyword_tbl_id_seq'::regclass);


--
-- TOC entry 4834 (class 2604 OID 17115)
-- Name: merchantaccount_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.merchantaccount_tbl ALTER COLUMN id SET DEFAULT nextval('client.merchantaccount_tbl_id_seq'::regclass);


--
-- TOC entry 4841 (class 2604 OID 17116)
-- Name: merchantsubaccount_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.merchantsubaccount_tbl ALTER COLUMN id SET DEFAULT nextval('client.merchantsubaccount_tbl_id_seq'::regclass);


--
-- TOC entry 4845 (class 2604 OID 17117)
-- Name: product_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.product_tbl ALTER COLUMN id SET DEFAULT nextval('client.product_tbl_id_seq'::regclass);


--
-- TOC entry 5074 (class 2604 OID 17118)
-- Name: producttype_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.producttype_tbl ALTER COLUMN id SET DEFAULT nextval('client.producttype_tbl_id_seq'::regclass);


--
-- TOC entry 5106 (class 2604 OID 17119)
-- Name: retrial_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.retrial_tbl ALTER COLUMN id SET DEFAULT nextval('client.retrial_tbl_id_seq'::regclass);


--
-- TOC entry 4850 (class 2604 OID 17120)
-- Name: shipping_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.shipping_tbl ALTER COLUMN id SET DEFAULT nextval('client.shipping_tbl_id_seq'::regclass);


--
-- TOC entry 4854 (class 2604 OID 17121)
-- Name: shop_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.shop_tbl ALTER COLUMN id SET DEFAULT nextval('client.shop_tbl_id_seq'::regclass);


--
-- TOC entry 5548 (class 2604 OID 33316464)
-- Name: staticroutelevelconfiguration id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.staticroutelevelconfiguration ALTER COLUMN id SET DEFAULT nextval('client.staticroutelevelconfiguration_id_seq'::regclass);


--
-- TOC entry 4858 (class 2604 OID 17122)
-- Name: surepay_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.surepay_tbl ALTER COLUMN id SET DEFAULT nextval('client.surepay_tbl_id_seq'::regclass);


--
-- TOC entry 4863 (class 2604 OID 17123)
-- Name: url_tbl id; Type: DEFAULT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.url_tbl ALTER COLUMN id SET DEFAULT nextval('client.url_tbl_id_seq'::regclass);


--
-- TOC entry 4867 (class 2604 OID 17124)
-- Name: account_tbl id; Type: DEFAULT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.account_tbl ALTER COLUMN id SET DEFAULT nextval('enduser.account_tbl_id_seq'::regclass);


--
-- TOC entry 4878 (class 2604 OID 16692)
-- Name: activation_tbl created; Type: DEFAULT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.activation_tbl ALTER COLUMN created SET DEFAULT now();


--
-- TOC entry 4879 (class 2604 OID 16693)
-- Name: activation_tbl modified; Type: DEFAULT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.activation_tbl ALTER COLUMN modified SET DEFAULT now();


--
-- TOC entry 4880 (class 2604 OID 16694)
-- Name: activation_tbl enabled; Type: DEFAULT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.activation_tbl ALTER COLUMN enabled SET DEFAULT true;


--
-- TOC entry 4881 (class 2604 OID 17125)
-- Name: activation_tbl id; Type: DEFAULT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.activation_tbl ALTER COLUMN id SET DEFAULT nextval('enduser.activation_tbl_id_seq'::regclass);


--
-- TOC entry 4884 (class 2604 OID 17126)
-- Name: address_tbl id; Type: DEFAULT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.address_tbl ALTER COLUMN id SET DEFAULT nextval('enduser.address_tbl_id_seq'::regclass);


--
-- TOC entry 4889 (class 2604 OID 17127)
-- Name: card_tbl id; Type: DEFAULT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.card_tbl ALTER COLUMN id SET DEFAULT nextval('enduser.card_tbl_id_seq'::regclass);


--
-- TOC entry 4895 (class 2604 OID 17128)
-- Name: claccess_tbl id; Type: DEFAULT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.claccess_tbl ALTER COLUMN id SET DEFAULT nextval('enduser.claccess_tbl_id_seq'::regclass);


--
-- TOC entry 4899 (class 2604 OID 17129)
-- Name: transaction_tbl id; Type: DEFAULT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.transaction_tbl ALTER COLUMN id SET DEFAULT nextval('enduser.transaction_tbl_id_seq'::regclass);


--
-- TOC entry 4907 (class 2604 OID 17130)
-- Name: additional_data_tbl id; Type: DEFAULT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.additional_data_tbl ALTER COLUMN id SET DEFAULT nextval('log.additional_data_tbl_id_seq'::regclass);


--
-- TOC entry 4910 (class 2604 OID 17131)
-- Name: address_tbl id; Type: DEFAULT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.address_tbl ALTER COLUMN id SET DEFAULT nextval('log.address_tbl_id_seq'::regclass);


--
-- TOC entry 4911 (class 2604 OID 17132)
-- Name: auditlog_tbl id; Type: DEFAULT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.auditlog_tbl ALTER COLUMN id SET DEFAULT nextval('log.auditlog_tbl_id_seq'::regclass);


--
-- TOC entry 5557 (class 2604 OID 36070190)
-- Name: billing_summary_tbl id; Type: DEFAULT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.billing_summary_tbl ALTER COLUMN id SET DEFAULT nextval('log.billing_summary_tbl_id_seq'::regclass);


--
-- TOC entry 5110 (class 2604 OID 3504099)
-- Name: externalreference_tbl id; Type: DEFAULT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.externalreference_tbl ALTER COLUMN id SET DEFAULT nextval('log.externalreference_tbl_id_seq'::regclass);


--
-- TOC entry 4915 (class 2604 OID 17133)
-- Name: flight_tbl id; Type: DEFAULT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.flight_tbl ALTER COLUMN id SET DEFAULT nextval('log.flight_tbl_id_seq'::regclass);


--
-- TOC entry 4918 (class 2604 OID 17134)
-- Name: message_tbl id; Type: DEFAULT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.message_tbl ALTER COLUMN id SET DEFAULT nextval('log.message_tbl_id_seq'::regclass);


--
-- TOC entry 4922 (class 2604 OID 17135)
-- Name: note_tbl id; Type: DEFAULT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.note_tbl ALTER COLUMN id SET DEFAULT nextval('log.note_tbl_id_seq'::regclass);


--
-- TOC entry 4926 (class 2604 OID 17136)
-- Name: operation_tbl id; Type: DEFAULT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.operation_tbl ALTER COLUMN id SET DEFAULT nextval('log.operation_tbl_id_seq'::regclass);


--
-- TOC entry 4930 (class 2604 OID 17137)
-- Name: order_tbl id; Type: DEFAULT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.order_tbl ALTER COLUMN id SET DEFAULT nextval('log.order_tbl_id_seq'::regclass);


--
-- TOC entry 4935 (class 2604 OID 17138)
-- Name: passenger_tbl id; Type: DEFAULT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.passenger_tbl ALTER COLUMN id SET DEFAULT nextval('log.passenger_tbl_id_seq'::regclass);


--
-- TOC entry 5564 (class 2604 OID 38040384)
-- Name: paymentroute_tbl id; Type: DEFAULT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.paymentroute_tbl ALTER COLUMN id SET DEFAULT nextval('log.paymentroute_tbl_id_seq'::regclass);


--
-- TOC entry 5067 (class 2604 OID 17139)
-- Name: session_tbl id; Type: DEFAULT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.session_tbl ALTER COLUMN id SET DEFAULT nextval('log.session_tbl_id_seq'::regclass);


--
-- TOC entry 5099 (class 2604 OID 17140)
-- Name: settlement_record_tbl id; Type: DEFAULT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.settlement_record_tbl ALTER COLUMN id SET DEFAULT nextval('log.settlement_record_tbl_id_seq'::regclass);


--
-- TOC entry 5095 (class 2604 OID 17141)
-- Name: settlement_tbl id; Type: DEFAULT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.settlement_tbl ALTER COLUMN id SET DEFAULT nextval('log.settlement_tbl_id_seq'::regclass);


--
-- TOC entry 4938 (class 2604 OID 17142)
-- Name: state_tbl id; Type: DEFAULT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.state_tbl ALTER COLUMN id SET DEFAULT nextval('log.state_tbl_id_seq'::regclass);


--
-- TOC entry 4942 (class 2604 OID 17143)
-- Name: transaction_tbl id; Type: DEFAULT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.transaction_tbl ALTER COLUMN id SET DEFAULT nextval('log.transaction_tbl_id_seq'::regclass);


--
-- TOC entry 5120 (class 2604 OID 33260801)
-- Name: txnpassbook_tbl id; Type: DEFAULT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl ALTER COLUMN id SET DEFAULT nextval('log.txnpassbook_tbl_id_seq'::regclass);


--
-- TOC entry 5114 (class 2604 OID 8365107)
-- Name: txnpassbook_tbl_backup_20200401 id; Type: DEFAULT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_backup_20200401 ALTER COLUMN id SET DEFAULT nextval('log.txnpassbook_tbl_id_seq_backup'::regclass);


--
-- TOC entry 5118 (class 2604 OID 22052452)
-- Name: businesstype_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.businesstype_tbl ALTER COLUMN id SET DEFAULT nextval('system.businesstype_tbl_id_seq'::regclass);


--
-- TOC entry 5544 (class 2604 OID 33316424)
-- Name: capturetype_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.capturetype_tbl ALTER COLUMN id SET DEFAULT nextval('system.capturetype_tbl_id_seq'::regclass);


--
-- TOC entry 4957 (class 2604 OID 17144)
-- Name: card_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.card_tbl ALTER COLUMN id SET DEFAULT nextval('system.card_tbl_id_seq'::regclass);


--
-- TOC entry 4962 (class 2604 OID 17145)
-- Name: cardchargetype_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.cardchargetype_tbl ALTER COLUMN id SET DEFAULT nextval('system.cardchargetype_tbl_id_seq'::regclass);


--
-- TOC entry 4966 (class 2604 OID 17146)
-- Name: cardprefix_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.cardprefix_tbl ALTER COLUMN id SET DEFAULT nextval('system.cardprefix_tbl_id_seq'::regclass);


--
-- TOC entry 4970 (class 2604 OID 17147)
-- Name: cardpricing_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.cardpricing_tbl ALTER COLUMN id SET DEFAULT nextval('system.cardpricing_tbl_id_seq'::regclass);


--
-- TOC entry 4974 (class 2604 OID 17148)
-- Name: cardstate_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.cardstate_tbl ALTER COLUMN id SET DEFAULT nextval('system.cardstate_tbl_id_seq'::regclass);


--
-- TOC entry 4978 (class 2604 OID 17149)
-- Name: country_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.country_tbl ALTER COLUMN id SET DEFAULT nextval('system.country_tbl_id_seq'::regclass);


--
-- TOC entry 5057 (class 2604 OID 17150)
-- Name: currency_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.currency_tbl ALTER COLUMN id SET DEFAULT nextval('system.currency_tbl_id_seq'::regclass);


--
-- TOC entry 4984 (class 2604 OID 17151)
-- Name: depositoption_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.depositoption_tbl ALTER COLUMN id SET DEFAULT nextval('system.depositoption_tbl_id_seq'::regclass);


--
-- TOC entry 5560 (class 2604 OID 36070692)
-- Name: externalreferencetype_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.externalreferencetype_tbl ALTER COLUMN id SET DEFAULT nextval('system.externalreferencetype_tbl_id_seq'::regclass);


--
-- TOC entry 4988 (class 2604 OID 17152)
-- Name: fee_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.fee_tbl ALTER COLUMN id SET DEFAULT nextval('system.fee_tbl_id_seq'::regclass);


--
-- TOC entry 4992 (class 2604 OID 17153)
-- Name: feetype_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.feetype_tbl ALTER COLUMN id SET DEFAULT nextval('system.feetype_tbl_id_seq'::regclass);


--
-- TOC entry 4996 (class 2604 OID 17154)
-- Name: flow_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.flow_tbl ALTER COLUMN id SET DEFAULT nextval('system.flow_tbl_id_seq'::regclass);


--
-- TOC entry 5003 (class 2604 OID 17155)
-- Name: iprange_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.iprange_tbl ALTER COLUMN id SET DEFAULT nextval('system.iprange_tbl_id_seq'::regclass);


--
-- TOC entry 5056 (class 2604 OID 17156)
-- Name: paymenttype_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.paymenttype_tbl ALTER COLUMN id SET DEFAULT nextval('system.paymenttype_tbl_id_seq'::regclass);


--
-- TOC entry 5007 (class 2604 OID 17157)
-- Name: postalcode_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.postalcode_tbl ALTER COLUMN id SET DEFAULT nextval('system.postalcode_tbl_id_seq'::regclass);


--
-- TOC entry 5012 (class 2604 OID 17158)
-- Name: pricepoint_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.pricepoint_tbl ALTER COLUMN id SET DEFAULT nextval('system.pricepoint_tbl_id_seq'::regclass);


--
-- TOC entry 5016 (class 2604 OID 17159)
-- Name: processortype_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.processortype_tbl ALTER COLUMN id SET DEFAULT nextval('system.processortype_tbl_id_seq'::regclass);


--
-- TOC entry 5017 (class 2604 OID 17160)
-- Name: psp_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.psp_tbl ALTER COLUMN id SET DEFAULT nextval('system.psp_tbl_id_seq'::regclass);


--
-- TOC entry 5022 (class 2604 OID 17161)
-- Name: pspcard_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.pspcard_tbl ALTER COLUMN id SET DEFAULT nextval('system.pspcard_tbl_id_seq'::regclass);


--
-- TOC entry 5026 (class 2604 OID 17162)
-- Name: pspcurrency_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.pspcurrency_tbl ALTER COLUMN id SET DEFAULT nextval('system.pspcurrency_tbl_id_seq'::regclass);


--
-- TOC entry 5102 (class 2604 OID 17163)
-- Name: retrialtype_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.retrialtype_tbl ALTER COLUMN id SET DEFAULT nextval('system.retrialtype_tbl_id_seq'::regclass);


--
-- TOC entry 5065 (class 2604 OID 17164)
-- Name: sessiontype_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.sessiontype_tbl ALTER COLUMN id SET DEFAULT nextval('system.sessiontype_tbl_id_seq'::regclass);


--
-- TOC entry 5030 (class 2604 OID 17165)
-- Name: shipping_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.shipping_tbl ALTER COLUMN id SET DEFAULT nextval('system.shipping_tbl_id_seq'::regclass);


--
-- TOC entry 5034 (class 2604 OID 17166)
-- Name: state_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.state_tbl ALTER COLUMN id SET DEFAULT nextval('system.state_tbl_id_seq'::regclass);


--
-- TOC entry 5087 (class 2604 OID 17167)
-- Name: statisticstype_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.statisticstype_tbl ALTER COLUMN id SET DEFAULT nextval('system.statisticstype_tbl_id_seq'::regclass);


--
-- TOC entry 5078 (class 2604 OID 17168)
-- Name: triggerunit_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.triggerunit_tbl ALTER COLUMN id SET DEFAULT nextval('system.triggerunit_tbl_id_seq'::regclass);


--
-- TOC entry 5039 (class 2604 OID 17169)
-- Name: type_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.type_tbl ALTER COLUMN id SET DEFAULT nextval('system.type_tbl_id_seq'::regclass);


--
-- TOC entry 5043 (class 2604 OID 17170)
-- Name: urltype_tbl id; Type: DEFAULT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.urltype_tbl ALTER COLUMN id SET DEFAULT nextval('system.urltype_tbl_id_seq'::regclass);


--
-- TOC entry 5569 (class 2606 OID 17171)
-- Name: access_tbl access_pk; Type: CONSTRAINT; Schema: admin; Owner: mpoint
--

ALTER TABLE ONLY admin.access_tbl
    ADD CONSTRAINT access_pk PRIMARY KEY (id);


--
-- TOC entry 5571 (class 2606 OID 17172)
-- Name: access_tbl access_uq; Type: CONSTRAINT; Schema: admin; Owner: mpoint
--

ALTER TABLE ONLY admin.access_tbl
    ADD CONSTRAINT access_uq UNIQUE (userid, clientid);


--
-- TOC entry 5573 (class 2606 OID 17173)
-- Name: role_tbl role_pk; Type: CONSTRAINT; Schema: admin; Owner: mpoint
--

ALTER TABLE ONLY admin.role_tbl
    ADD CONSTRAINT role_pk PRIMARY KEY (id);


--
-- TOC entry 5576 (class 2606 OID 17174)
-- Name: roleaccess_tbl roleaccess_pk; Type: CONSTRAINT; Schema: admin; Owner: mpoint
--

ALTER TABLE ONLY admin.roleaccess_tbl
    ADD CONSTRAINT roleaccess_pk PRIMARY KEY (id);


--
-- TOC entry 5578 (class 2606 OID 17175)
-- Name: roleaccess_tbl roleaccess_uq; Type: CONSTRAINT; Schema: admin; Owner: mpoint
--

ALTER TABLE ONLY admin.roleaccess_tbl
    ADD CONSTRAINT roleaccess_uq UNIQUE (roleid, userid);


--
-- TOC entry 5580 (class 2606 OID 17176)
-- Name: roleinfo_tbl roleinfo_pk; Type: CONSTRAINT; Schema: admin; Owner: mpoint
--

ALTER TABLE ONLY admin.roleinfo_tbl
    ADD CONSTRAINT roleinfo_pk PRIMARY KEY (id);


--
-- TOC entry 5582 (class 2606 OID 17177)
-- Name: roleinfo_tbl roleinfo_uq; Type: CONSTRAINT; Schema: admin; Owner: mpoint
--

ALTER TABLE ONLY admin.roleinfo_tbl
    ADD CONSTRAINT roleinfo_uq UNIQUE (roleid, languageid);


--
-- TOC entry 5584 (class 2606 OID 17178)
-- Name: account_tbl account_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.account_tbl
    ADD CONSTRAINT account_pk PRIMARY KEY (id);


--
-- TOC entry 5587 (class 2606 OID 17179)
-- Name: account_tbl account_uq; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.account_tbl
    ADD CONSTRAINT account_uq UNIQUE (clientid, mobile);


--
-- TOC entry 5759 (class 2606 OID 17180)
-- Name: additionalproperty_tbl additionalprop_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.additionalproperty_tbl
    ADD CONSTRAINT additionalprop_pk PRIMARY KEY (id);


--
-- TOC entry 5591 (class 2606 OID 17181)
-- Name: cardaccess_tbl cardaccess_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.cardaccess_tbl
    ADD CONSTRAINT cardaccess_pk PRIMARY KEY (id);


--
-- TOC entry 5593 (class 2606 OID 17182)
-- Name: client_tbl client_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.client_tbl
    ADD CONSTRAINT client_pk PRIMARY KEY (id);


--
-- TOC entry 5775 (class 2606 OID 17183)
-- Name: producttype_tbl clientproducttype_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.producttype_tbl
    ADD CONSTRAINT clientproducttype_pk PRIMARY KEY (id);


--
-- TOC entry 5766 (class 2606 OID 17184)
-- Name: countrycurrency_tbl countrycurrency_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.countrycurrency_tbl
    ADD CONSTRAINT countrycurrency_pk PRIMARY KEY (id);


--
-- TOC entry 5757 (class 2606 OID 17185)
-- Name: gomobileconfiguration_tbl gomobileconfiguration_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.gomobileconfiguration_tbl
    ADD CONSTRAINT gomobileconfiguration_pk PRIMARY KEY (id);


--
-- TOC entry 5595 (class 2606 OID 17186)
-- Name: iinlist_tbl iinlist_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.iinlist_tbl
    ADD CONSTRAINT iinlist_pk PRIMARY KEY (id);


--
-- TOC entry 5598 (class 2606 OID 17187)
-- Name: info_tbl info_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.info_tbl
    ADD CONSTRAINT info_pk PRIMARY KEY (id);


--
-- TOC entry 5602 (class 2606 OID 17188)
-- Name: infotype_tbl infotype_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.infotype_tbl
    ADD CONSTRAINT infotype_pk PRIMARY KEY (id);


--
-- TOC entry 5605 (class 2606 OID 17189)
-- Name: ipaddress_tbl ipaddress_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.ipaddress_tbl
    ADD CONSTRAINT ipaddress_pk PRIMARY KEY (id);


--
-- TOC entry 5607 (class 2606 OID 17190)
-- Name: keyword_tbl keyword_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.keyword_tbl
    ADD CONSTRAINT keyword_pk PRIMARY KEY (id);


--
-- TOC entry 5610 (class 2606 OID 17191)
-- Name: merchantaccount_tbl merchantaccount_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.merchantaccount_tbl
    ADD CONSTRAINT merchantaccount_pk PRIMARY KEY (id);


--
-- TOC entry 5614 (class 2606 OID 17192)
-- Name: merchantsubaccount_tbl merchantsubaccount_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.merchantsubaccount_tbl
    ADD CONSTRAINT merchantsubaccount_pk PRIMARY KEY (id);


--
-- TOC entry 5616 (class 2606 OID 17193)
-- Name: merchantsubaccount_tbl merchantsubaccount_uq; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.merchantsubaccount_tbl
    ADD CONSTRAINT merchantsubaccount_uq UNIQUE (accountid, pspid);


--
-- TOC entry 5618 (class 2606 OID 17194)
-- Name: product_tbl product_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.product_tbl
    ADD CONSTRAINT product_pk PRIMARY KEY (id);


--
-- TOC entry 5795 (class 2606 OID 17195)
-- Name: retrial_tbl retrial_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.retrial_tbl
    ADD CONSTRAINT retrial_pk PRIMARY KEY (id);


--
-- TOC entry 5620 (class 2606 OID 17196)
-- Name: shipping_tbl shipping_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.shipping_tbl
    ADD CONSTRAINT shipping_pk PRIMARY KEY (id);


--
-- TOC entry 5622 (class 2606 OID 17197)
-- Name: shop_tbl shop_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.shop_tbl
    ADD CONSTRAINT shop_pk PRIMARY KEY (id);


--
-- TOC entry 5783 (class 2606 OID 17198)
-- Name: gatewaystat_tbl stat_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.gatewaystat_tbl
    ADD CONSTRAINT stat_pk PRIMARY KEY (id);


--
-- TOC entry 6115 (class 2606 OID 33316470)
-- Name: staticroutelevelconfiguration staticroutelevelconfiguration_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.staticroutelevelconfiguration
    ADD CONSTRAINT staticroutelevelconfiguration_pk PRIMARY KEY (id);


--
-- TOC entry 5624 (class 2606 OID 17199)
-- Name: surepay_tbl surepay_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.surepay_tbl
    ADD CONSTRAINT surepay_pk PRIMARY KEY (id);


--
-- TOC entry 5626 (class 2606 OID 17200)
-- Name: surepay_tbl surepay_uq; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.surepay_tbl
    ADD CONSTRAINT surepay_uq UNIQUE (clientid);


--
-- TOC entry 5779 (class 2606 OID 17201)
-- Name: gatewaytrigger_tbl trigger_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.gatewaytrigger_tbl
    ADD CONSTRAINT trigger_pk PRIMARY KEY (id);


--
-- TOC entry 5629 (class 2606 OID 17202)
-- Name: url_tbl url_pk; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.url_tbl
    ADD CONSTRAINT url_pk PRIMARY KEY (id);


--
-- TOC entry 5631 (class 2606 OID 17203)
-- Name: url_tbl url_uq; Type: CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.url_tbl
    ADD CONSTRAINT url_uq UNIQUE (urltypeid, clientid);


--
-- TOC entry 5635 (class 2606 OID 17204)
-- Name: account_tbl account_pk; Type: CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.account_tbl
    ADD CONSTRAINT account_pk PRIMARY KEY (id);


--
-- TOC entry 5638 (class 2606 OID 17205)
-- Name: activation_tbl activate_uq; Type: CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.activation_tbl
    ADD CONSTRAINT activate_uq UNIQUE (accountid, code);


--
-- TOC entry 5640 (class 2606 OID 17206)
-- Name: activation_tbl activation_pk; Type: CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.activation_tbl
    ADD CONSTRAINT activation_pk PRIMARY KEY (id);


--
-- TOC entry 5642 (class 2606 OID 17207)
-- Name: address_tbl address_pk; Type: CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.address_tbl
    ADD CONSTRAINT address_pk PRIMARY KEY (id);


--
-- TOC entry 5644 (class 2606 OID 17208)
-- Name: card_tbl card_pk; Type: CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.card_tbl
    ADD CONSTRAINT card_pk PRIMARY KEY (id);


--
-- TOC entry 5646 (class 2606 OID 17209)
-- Name: card_tbl card_uq; Type: CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.card_tbl
    ADD CONSTRAINT card_uq UNIQUE (accountid, clientid, cardid, mask, expiry);


--
-- TOC entry 5649 (class 2606 OID 17210)
-- Name: claccess_tbl claccess_pk; Type: CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.claccess_tbl
    ADD CONSTRAINT claccess_pk PRIMARY KEY (id);


--
-- TOC entry 5652 (class 2606 OID 17211)
-- Name: transaction_tbl transaction_pk; Type: CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.transaction_tbl
    ADD CONSTRAINT transaction_pk PRIMARY KEY (id);


--
-- TOC entry 5654 (class 2606 OID 17212)
-- Name: transaction_tbl transaction_uq; Type: CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.transaction_tbl
    ADD CONSTRAINT transaction_uq UNIQUE (typeid, txnid);


--
-- TOC entry 5656 (class 2606 OID 17213)
-- Name: additional_data_tbl additional_data_pk; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.additional_data_tbl
    ADD CONSTRAINT additional_data_pk PRIMARY KEY (id);


--
-- TOC entry 5659 (class 2606 OID 17214)
-- Name: address_tbl address_pk; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.address_tbl
    ADD CONSTRAINT address_pk PRIMARY KEY (id);


--
-- TOC entry 5662 (class 2606 OID 17215)
-- Name: auditlog_tbl auditlog_pk; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.auditlog_tbl
    ADD CONSTRAINT auditlog_pk PRIMARY KEY (id);


--
-- TOC entry 6120 (class 2606 OID 36070194)
-- Name: billing_summary_tbl billing_summary_pk; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.billing_summary_tbl
    ADD CONSTRAINT billing_summary_pk PRIMARY KEY (id);


--
-- TOC entry 5798 (class 2606 OID 3504104)
-- Name: externalreference_tbl externalreference_pk; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.externalreference_tbl
    ADD CONSTRAINT externalreference_pk PRIMARY KEY (id);


--
-- TOC entry 5664 (class 2606 OID 17216)
-- Name: flight_tbl flight_pk; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.flight_tbl
    ADD CONSTRAINT flight_pk PRIMARY KEY (id);


--
-- TOC entry 5667 (class 2606 OID 17217)
-- Name: message_tbl message_pk; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.message_tbl
    ADD CONSTRAINT message_pk PRIMARY KEY (id);


--
-- TOC entry 5671 (class 2606 OID 17218)
-- Name: note_tbl note_pk; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.note_tbl
    ADD CONSTRAINT note_pk PRIMARY KEY (id);


--
-- TOC entry 5673 (class 2606 OID 17219)
-- Name: operation_tbl operation_pk; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.operation_tbl
    ADD CONSTRAINT operation_pk PRIMARY KEY (id);


--
-- TOC entry 5675 (class 2606 OID 17220)
-- Name: order_tbl order_pk; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.order_tbl
    ADD CONSTRAINT order_pk PRIMARY KEY (id);


--
-- TOC entry 5679 (class 2606 OID 17221)
-- Name: passenger_tbl passenger_pk; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.passenger_tbl
    ADD CONSTRAINT passenger_pk PRIMARY KEY (id);


--
-- TOC entry 6124 (class 2606 OID 38040389)
-- Name: paymentroute_tbl paymentroute_pk; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.paymentroute_tbl
    ADD CONSTRAINT paymentroute_pk PRIMARY KEY (id);


--
-- TOC entry 5770 (class 2606 OID 17222)
-- Name: session_tbl session_tbl_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.session_tbl
    ADD CONSTRAINT session_tbl_pkey PRIMARY KEY (id);


--
-- TOC entry 5789 (class 2606 OID 17223)
-- Name: settlement_record_tbl settlement_record_tbl_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.settlement_record_tbl
    ADD CONSTRAINT settlement_record_tbl_pkey PRIMARY KEY (id);


--
-- TOC entry 5787 (class 2606 OID 17224)
-- Name: settlement_tbl settlement_tbl_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.settlement_tbl
    ADD CONSTRAINT settlement_tbl_pkey PRIMARY KEY (id);


--
-- TOC entry 5681 (class 2606 OID 17225)
-- Name: state_tbl state_pk; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.state_tbl
    ADD CONSTRAINT state_pk PRIMARY KEY (id);


--
-- TOC entry 5688 (class 2606 OID 17226)
-- Name: transaction_tbl transaction_pk; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.transaction_tbl
    ADD CONSTRAINT transaction_pk PRIMARY KEY (id);


--
-- TOC entry 5804 (class 2606 OID 8365112)
-- Name: txnpassbook_tbl_backup_20200401 txnpassbook_pk; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_backup_20200401
    ADD CONSTRAINT txnpassbook_pk PRIMARY KEY (id);


--
-- TOC entry 5840 (class 2606 OID 33262072)
-- Name: txnpassbook_tbl_10018_10000001_11000001 txnpassbook_tbl_10018_10000001_11000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_10000001_11000001
    ADD CONSTRAINT txnpassbook_tbl_10018_10000001_11000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5813 (class 2606 OID 33262054)
-- Name: txnpassbook_tbl_10018_1000001_2000001 txnpassbook_tbl_10018_1000001_2000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_1000001_2000001
    ADD CONSTRAINT txnpassbook_tbl_10018_1000001_2000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5843 (class 2606 OID 33262074)
-- Name: txnpassbook_tbl_10018_11000001_12000001 txnpassbook_tbl_10018_11000001_12000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_11000001_12000001
    ADD CONSTRAINT txnpassbook_tbl_10018_11000001_12000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5846 (class 2606 OID 33262076)
-- Name: txnpassbook_tbl_10018_12000001_13000001 txnpassbook_tbl_10018_12000001_13000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_12000001_13000001
    ADD CONSTRAINT txnpassbook_tbl_10018_12000001_13000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5849 (class 2606 OID 33262078)
-- Name: txnpassbook_tbl_10018_13000001_14000001 txnpassbook_tbl_10018_13000001_14000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_13000001_14000001
    ADD CONSTRAINT txnpassbook_tbl_10018_13000001_14000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5852 (class 2606 OID 33262080)
-- Name: txnpassbook_tbl_10018_14000001_15000001 txnpassbook_tbl_10018_14000001_15000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_14000001_15000001
    ADD CONSTRAINT txnpassbook_tbl_10018_14000001_15000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5855 (class 2606 OID 33262082)
-- Name: txnpassbook_tbl_10018_15000001_16000001 txnpassbook_tbl_10018_15000001_16000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_15000001_16000001
    ADD CONSTRAINT txnpassbook_tbl_10018_15000001_16000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5858 (class 2606 OID 33262084)
-- Name: txnpassbook_tbl_10018_16000001_17000001 txnpassbook_tbl_10018_16000001_17000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_16000001_17000001
    ADD CONSTRAINT txnpassbook_tbl_10018_16000001_17000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5861 (class 2606 OID 33262086)
-- Name: txnpassbook_tbl_10018_17000001_18000001 txnpassbook_tbl_10018_17000001_18000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_17000001_18000001
    ADD CONSTRAINT txnpassbook_tbl_10018_17000001_18000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5864 (class 2606 OID 33262088)
-- Name: txnpassbook_tbl_10018_18000001_19000001 txnpassbook_tbl_10018_18000001_19000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_18000001_19000001
    ADD CONSTRAINT txnpassbook_tbl_10018_18000001_19000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5867 (class 2606 OID 33262090)
-- Name: txnpassbook_tbl_10018_19000001_20000001 txnpassbook_tbl_10018_19000001_20000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_19000001_20000001
    ADD CONSTRAINT txnpassbook_tbl_10018_19000001_20000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5810 (class 2606 OID 33262052)
-- Name: txnpassbook_tbl_10018_1_1000001 txnpassbook_tbl_10018_1_1000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_1_1000001
    ADD CONSTRAINT txnpassbook_tbl_10018_1_1000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5816 (class 2606 OID 33262056)
-- Name: txnpassbook_tbl_10018_2000001_3000001 txnpassbook_tbl_10018_2000001_3000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_2000001_3000001
    ADD CONSTRAINT txnpassbook_tbl_10018_2000001_3000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5819 (class 2606 OID 33262058)
-- Name: txnpassbook_tbl_10018_3000001_4000001 txnpassbook_tbl_10018_3000001_4000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_3000001_4000001
    ADD CONSTRAINT txnpassbook_tbl_10018_3000001_4000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5822 (class 2606 OID 33262060)
-- Name: txnpassbook_tbl_10018_4000001_5000001 txnpassbook_tbl_10018_4000001_5000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_4000001_5000001
    ADD CONSTRAINT txnpassbook_tbl_10018_4000001_5000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5825 (class 2606 OID 33262062)
-- Name: txnpassbook_tbl_10018_5000001_6000001 txnpassbook_tbl_10018_5000001_6000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_5000001_6000001
    ADD CONSTRAINT txnpassbook_tbl_10018_5000001_6000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5828 (class 2606 OID 33262064)
-- Name: txnpassbook_tbl_10018_6000001_7000001 txnpassbook_tbl_10018_6000001_7000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_6000001_7000001
    ADD CONSTRAINT txnpassbook_tbl_10018_6000001_7000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5831 (class 2606 OID 33262066)
-- Name: txnpassbook_tbl_10018_7000001_8000001 txnpassbook_tbl_10018_7000001_8000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_7000001_8000001
    ADD CONSTRAINT txnpassbook_tbl_10018_7000001_8000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5834 (class 2606 OID 33262068)
-- Name: txnpassbook_tbl_10018_8000001_9000001 txnpassbook_tbl_10018_8000001_9000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_8000001_9000001
    ADD CONSTRAINT txnpassbook_tbl_10018_8000001_9000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5837 (class 2606 OID 33262070)
-- Name: txnpassbook_tbl_10018_9000001_10000001 txnpassbook_tbl_10018_9000001_10000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10018_9000001_10000001
    ADD CONSTRAINT txnpassbook_tbl_10018_9000001_10000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5900 (class 2606 OID 33262112)
-- Name: txnpassbook_tbl_10020_10000001_11000001 txnpassbook_tbl_10020_10000001_11000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_10000001_11000001
    ADD CONSTRAINT txnpassbook_tbl_10020_10000001_11000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5873 (class 2606 OID 33262094)
-- Name: txnpassbook_tbl_10020_1000001_2000001 txnpassbook_tbl_10020_1000001_2000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_1000001_2000001
    ADD CONSTRAINT txnpassbook_tbl_10020_1000001_2000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5903 (class 2606 OID 33262114)
-- Name: txnpassbook_tbl_10020_11000001_12000001 txnpassbook_tbl_10020_11000001_12000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_11000001_12000001
    ADD CONSTRAINT txnpassbook_tbl_10020_11000001_12000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5906 (class 2606 OID 33262116)
-- Name: txnpassbook_tbl_10020_12000001_13000001 txnpassbook_tbl_10020_12000001_13000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_12000001_13000001
    ADD CONSTRAINT txnpassbook_tbl_10020_12000001_13000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5909 (class 2606 OID 33262118)
-- Name: txnpassbook_tbl_10020_13000001_14000001 txnpassbook_tbl_10020_13000001_14000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_13000001_14000001
    ADD CONSTRAINT txnpassbook_tbl_10020_13000001_14000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5912 (class 2606 OID 33262120)
-- Name: txnpassbook_tbl_10020_14000001_15000001 txnpassbook_tbl_10020_14000001_15000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_14000001_15000001
    ADD CONSTRAINT txnpassbook_tbl_10020_14000001_15000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5915 (class 2606 OID 33262122)
-- Name: txnpassbook_tbl_10020_15000001_16000001 txnpassbook_tbl_10020_15000001_16000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_15000001_16000001
    ADD CONSTRAINT txnpassbook_tbl_10020_15000001_16000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5918 (class 2606 OID 33262124)
-- Name: txnpassbook_tbl_10020_16000001_17000001 txnpassbook_tbl_10020_16000001_17000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_16000001_17000001
    ADD CONSTRAINT txnpassbook_tbl_10020_16000001_17000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5921 (class 2606 OID 33262126)
-- Name: txnpassbook_tbl_10020_17000001_18000001 txnpassbook_tbl_10020_17000001_18000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_17000001_18000001
    ADD CONSTRAINT txnpassbook_tbl_10020_17000001_18000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5924 (class 2606 OID 33262128)
-- Name: txnpassbook_tbl_10020_18000001_19000001 txnpassbook_tbl_10020_18000001_19000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_18000001_19000001
    ADD CONSTRAINT txnpassbook_tbl_10020_18000001_19000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5927 (class 2606 OID 33262130)
-- Name: txnpassbook_tbl_10020_19000001_20000001 txnpassbook_tbl_10020_19000001_20000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_19000001_20000001
    ADD CONSTRAINT txnpassbook_tbl_10020_19000001_20000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5870 (class 2606 OID 33262092)
-- Name: txnpassbook_tbl_10020_1_1000001 txnpassbook_tbl_10020_1_1000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_1_1000001
    ADD CONSTRAINT txnpassbook_tbl_10020_1_1000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5876 (class 2606 OID 33262096)
-- Name: txnpassbook_tbl_10020_2000001_3000001 txnpassbook_tbl_10020_2000001_3000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_2000001_3000001
    ADD CONSTRAINT txnpassbook_tbl_10020_2000001_3000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5879 (class 2606 OID 33262098)
-- Name: txnpassbook_tbl_10020_3000001_4000001 txnpassbook_tbl_10020_3000001_4000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_3000001_4000001
    ADD CONSTRAINT txnpassbook_tbl_10020_3000001_4000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5882 (class 2606 OID 33262100)
-- Name: txnpassbook_tbl_10020_4000001_5000001 txnpassbook_tbl_10020_4000001_5000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_4000001_5000001
    ADD CONSTRAINT txnpassbook_tbl_10020_4000001_5000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5885 (class 2606 OID 33262102)
-- Name: txnpassbook_tbl_10020_5000001_6000001 txnpassbook_tbl_10020_5000001_6000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_5000001_6000001
    ADD CONSTRAINT txnpassbook_tbl_10020_5000001_6000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5888 (class 2606 OID 33262104)
-- Name: txnpassbook_tbl_10020_6000001_7000001 txnpassbook_tbl_10020_6000001_7000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_6000001_7000001
    ADD CONSTRAINT txnpassbook_tbl_10020_6000001_7000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5891 (class 2606 OID 33262106)
-- Name: txnpassbook_tbl_10020_7000001_8000001 txnpassbook_tbl_10020_7000001_8000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_7000001_8000001
    ADD CONSTRAINT txnpassbook_tbl_10020_7000001_8000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5894 (class 2606 OID 33262108)
-- Name: txnpassbook_tbl_10020_8000001_9000001 txnpassbook_tbl_10020_8000001_9000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_8000001_9000001
    ADD CONSTRAINT txnpassbook_tbl_10020_8000001_9000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5897 (class 2606 OID 33262110)
-- Name: txnpassbook_tbl_10020_9000001_10000001 txnpassbook_tbl_10020_9000001_10000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10020_9000001_10000001
    ADD CONSTRAINT txnpassbook_tbl_10020_9000001_10000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5960 (class 2606 OID 33262152)
-- Name: txnpassbook_tbl_10021_10000001_11000001 txnpassbook_tbl_10021_10000001_11000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_10000001_11000001
    ADD CONSTRAINT txnpassbook_tbl_10021_10000001_11000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5933 (class 2606 OID 33262134)
-- Name: txnpassbook_tbl_10021_1000001_2000001 txnpassbook_tbl_10021_1000001_2000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_1000001_2000001
    ADD CONSTRAINT txnpassbook_tbl_10021_1000001_2000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5963 (class 2606 OID 33262154)
-- Name: txnpassbook_tbl_10021_11000001_12000001 txnpassbook_tbl_10021_11000001_12000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_11000001_12000001
    ADD CONSTRAINT txnpassbook_tbl_10021_11000001_12000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5966 (class 2606 OID 33262156)
-- Name: txnpassbook_tbl_10021_12000001_13000001 txnpassbook_tbl_10021_12000001_13000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_12000001_13000001
    ADD CONSTRAINT txnpassbook_tbl_10021_12000001_13000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5969 (class 2606 OID 33262158)
-- Name: txnpassbook_tbl_10021_13000001_14000001 txnpassbook_tbl_10021_13000001_14000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_13000001_14000001
    ADD CONSTRAINT txnpassbook_tbl_10021_13000001_14000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5972 (class 2606 OID 33262160)
-- Name: txnpassbook_tbl_10021_14000001_15000001 txnpassbook_tbl_10021_14000001_15000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_14000001_15000001
    ADD CONSTRAINT txnpassbook_tbl_10021_14000001_15000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5975 (class 2606 OID 33262162)
-- Name: txnpassbook_tbl_10021_15000001_16000001 txnpassbook_tbl_10021_15000001_16000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_15000001_16000001
    ADD CONSTRAINT txnpassbook_tbl_10021_15000001_16000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5978 (class 2606 OID 33262164)
-- Name: txnpassbook_tbl_10021_16000001_17000001 txnpassbook_tbl_10021_16000001_17000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_16000001_17000001
    ADD CONSTRAINT txnpassbook_tbl_10021_16000001_17000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5981 (class 2606 OID 33262166)
-- Name: txnpassbook_tbl_10021_17000001_18000001 txnpassbook_tbl_10021_17000001_18000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_17000001_18000001
    ADD CONSTRAINT txnpassbook_tbl_10021_17000001_18000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5984 (class 2606 OID 33262168)
-- Name: txnpassbook_tbl_10021_18000001_19000001 txnpassbook_tbl_10021_18000001_19000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_18000001_19000001
    ADD CONSTRAINT txnpassbook_tbl_10021_18000001_19000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5987 (class 2606 OID 33262170)
-- Name: txnpassbook_tbl_10021_19000001_20000001 txnpassbook_tbl_10021_19000001_20000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_19000001_20000001
    ADD CONSTRAINT txnpassbook_tbl_10021_19000001_20000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5930 (class 2606 OID 33262132)
-- Name: txnpassbook_tbl_10021_1_1000001 txnpassbook_tbl_10021_1_1000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_1_1000001
    ADD CONSTRAINT txnpassbook_tbl_10021_1_1000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5936 (class 2606 OID 33262136)
-- Name: txnpassbook_tbl_10021_2000001_3000001 txnpassbook_tbl_10021_2000001_3000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_2000001_3000001
    ADD CONSTRAINT txnpassbook_tbl_10021_2000001_3000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5939 (class 2606 OID 33262138)
-- Name: txnpassbook_tbl_10021_3000001_4000001 txnpassbook_tbl_10021_3000001_4000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_3000001_4000001
    ADD CONSTRAINT txnpassbook_tbl_10021_3000001_4000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5942 (class 2606 OID 33262140)
-- Name: txnpassbook_tbl_10021_4000001_5000001 txnpassbook_tbl_10021_4000001_5000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_4000001_5000001
    ADD CONSTRAINT txnpassbook_tbl_10021_4000001_5000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5945 (class 2606 OID 33262142)
-- Name: txnpassbook_tbl_10021_5000001_6000001 txnpassbook_tbl_10021_5000001_6000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_5000001_6000001
    ADD CONSTRAINT txnpassbook_tbl_10021_5000001_6000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5948 (class 2606 OID 33262144)
-- Name: txnpassbook_tbl_10021_6000001_7000001 txnpassbook_tbl_10021_6000001_7000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_6000001_7000001
    ADD CONSTRAINT txnpassbook_tbl_10021_6000001_7000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5951 (class 2606 OID 33262146)
-- Name: txnpassbook_tbl_10021_7000001_8000001 txnpassbook_tbl_10021_7000001_8000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_7000001_8000001
    ADD CONSTRAINT txnpassbook_tbl_10021_7000001_8000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5954 (class 2606 OID 33262148)
-- Name: txnpassbook_tbl_10021_8000001_9000001 txnpassbook_tbl_10021_8000001_9000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_8000001_9000001
    ADD CONSTRAINT txnpassbook_tbl_10021_8000001_9000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5957 (class 2606 OID 33262150)
-- Name: txnpassbook_tbl_10021_9000001_10000001 txnpassbook_tbl_10021_9000001_10000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10021_9000001_10000001
    ADD CONSTRAINT txnpassbook_tbl_10021_9000001_10000001_pkey PRIMARY KEY (id);


--
-- TOC entry 6050 (class 2606 OID 33262212)
-- Name: txnpassbook_tbl_10022 txnpassbook_tbl_10022_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10022
    ADD CONSTRAINT txnpassbook_tbl_10022_pkey PRIMARY KEY (id);


--
-- TOC entry 6053 (class 2606 OID 33262214)
-- Name: txnpassbook_tbl_10060 txnpassbook_tbl_10060_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10060
    ADD CONSTRAINT txnpassbook_tbl_10060_pkey PRIMARY KEY (id);


--
-- TOC entry 6056 (class 2606 OID 33262216)
-- Name: txnpassbook_tbl_10061 txnpassbook_tbl_10061_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10061
    ADD CONSTRAINT txnpassbook_tbl_10061_pkey PRIMARY KEY (id);


--
-- TOC entry 6059 (class 2606 OID 33262218)
-- Name: txnpassbook_tbl_10062 txnpassbook_tbl_10062_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10062
    ADD CONSTRAINT txnpassbook_tbl_10062_pkey PRIMARY KEY (id);


--
-- TOC entry 6062 (class 2606 OID 33262220)
-- Name: txnpassbook_tbl_10065 txnpassbook_tbl_10065_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10065
    ADD CONSTRAINT txnpassbook_tbl_10065_pkey PRIMARY KEY (id);


--
-- TOC entry 6074 (class 2606 OID 33262228)
-- Name: txnpassbook_tbl_10066 txnpassbook_tbl_10066_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10066
    ADD CONSTRAINT txnpassbook_tbl_10066_pkey PRIMARY KEY (id);


--
-- TOC entry 6065 (class 2606 OID 33262222)
-- Name: txnpassbook_tbl_10067 txnpassbook_tbl_10067_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10067
    ADD CONSTRAINT txnpassbook_tbl_10067_pkey PRIMARY KEY (id);


--
-- TOC entry 6020 (class 2606 OID 33262192)
-- Name: txnpassbook_tbl_10069_10000001_11000001 txnpassbook_tbl_10069_10000001_11000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_10000001_11000001
    ADD CONSTRAINT txnpassbook_tbl_10069_10000001_11000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5993 (class 2606 OID 33262174)
-- Name: txnpassbook_tbl_10069_1000001_2000001 txnpassbook_tbl_10069_1000001_2000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_1000001_2000001
    ADD CONSTRAINT txnpassbook_tbl_10069_1000001_2000001_pkey PRIMARY KEY (id);


--
-- TOC entry 6023 (class 2606 OID 33262194)
-- Name: txnpassbook_tbl_10069_11000001_12000001 txnpassbook_tbl_10069_11000001_12000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_11000001_12000001
    ADD CONSTRAINT txnpassbook_tbl_10069_11000001_12000001_pkey PRIMARY KEY (id);


--
-- TOC entry 6026 (class 2606 OID 33262196)
-- Name: txnpassbook_tbl_10069_12000001_13000001 txnpassbook_tbl_10069_12000001_13000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_12000001_13000001
    ADD CONSTRAINT txnpassbook_tbl_10069_12000001_13000001_pkey PRIMARY KEY (id);


--
-- TOC entry 6029 (class 2606 OID 33262198)
-- Name: txnpassbook_tbl_10069_13000001_14000001 txnpassbook_tbl_10069_13000001_14000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_13000001_14000001
    ADD CONSTRAINT txnpassbook_tbl_10069_13000001_14000001_pkey PRIMARY KEY (id);


--
-- TOC entry 6032 (class 2606 OID 33262200)
-- Name: txnpassbook_tbl_10069_14000001_15000001 txnpassbook_tbl_10069_14000001_15000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_14000001_15000001
    ADD CONSTRAINT txnpassbook_tbl_10069_14000001_15000001_pkey PRIMARY KEY (id);


--
-- TOC entry 6035 (class 2606 OID 33262202)
-- Name: txnpassbook_tbl_10069_15000001_16000001 txnpassbook_tbl_10069_15000001_16000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_15000001_16000001
    ADD CONSTRAINT txnpassbook_tbl_10069_15000001_16000001_pkey PRIMARY KEY (id);


--
-- TOC entry 6038 (class 2606 OID 33262204)
-- Name: txnpassbook_tbl_10069_16000001_17000001 txnpassbook_tbl_10069_16000001_17000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_16000001_17000001
    ADD CONSTRAINT txnpassbook_tbl_10069_16000001_17000001_pkey PRIMARY KEY (id);


--
-- TOC entry 6041 (class 2606 OID 33262206)
-- Name: txnpassbook_tbl_10069_17000001_18000001 txnpassbook_tbl_10069_17000001_18000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_17000001_18000001
    ADD CONSTRAINT txnpassbook_tbl_10069_17000001_18000001_pkey PRIMARY KEY (id);


--
-- TOC entry 6044 (class 2606 OID 33262208)
-- Name: txnpassbook_tbl_10069_18000001_19000001 txnpassbook_tbl_10069_18000001_19000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_18000001_19000001
    ADD CONSTRAINT txnpassbook_tbl_10069_18000001_19000001_pkey PRIMARY KEY (id);


--
-- TOC entry 6047 (class 2606 OID 33262210)
-- Name: txnpassbook_tbl_10069_19000001_20000001 txnpassbook_tbl_10069_19000001_20000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_19000001_20000001
    ADD CONSTRAINT txnpassbook_tbl_10069_19000001_20000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5990 (class 2606 OID 33262172)
-- Name: txnpassbook_tbl_10069_1_1000001 txnpassbook_tbl_10069_1_1000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_1_1000001
    ADD CONSTRAINT txnpassbook_tbl_10069_1_1000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5996 (class 2606 OID 33262176)
-- Name: txnpassbook_tbl_10069_2000001_3000001 txnpassbook_tbl_10069_2000001_3000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_2000001_3000001
    ADD CONSTRAINT txnpassbook_tbl_10069_2000001_3000001_pkey PRIMARY KEY (id);


--
-- TOC entry 5999 (class 2606 OID 33262178)
-- Name: txnpassbook_tbl_10069_3000001_4000001 txnpassbook_tbl_10069_3000001_4000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_3000001_4000001
    ADD CONSTRAINT txnpassbook_tbl_10069_3000001_4000001_pkey PRIMARY KEY (id);


--
-- TOC entry 6002 (class 2606 OID 33262180)
-- Name: txnpassbook_tbl_10069_4000001_5000001 txnpassbook_tbl_10069_4000001_5000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_4000001_5000001
    ADD CONSTRAINT txnpassbook_tbl_10069_4000001_5000001_pkey PRIMARY KEY (id);


--
-- TOC entry 6005 (class 2606 OID 33262182)
-- Name: txnpassbook_tbl_10069_5000001_6000001 txnpassbook_tbl_10069_5000001_6000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_5000001_6000001
    ADD CONSTRAINT txnpassbook_tbl_10069_5000001_6000001_pkey PRIMARY KEY (id);


--
-- TOC entry 6008 (class 2606 OID 33262184)
-- Name: txnpassbook_tbl_10069_6000001_7000001 txnpassbook_tbl_10069_6000001_7000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_6000001_7000001
    ADD CONSTRAINT txnpassbook_tbl_10069_6000001_7000001_pkey PRIMARY KEY (id);


--
-- TOC entry 6011 (class 2606 OID 33262186)
-- Name: txnpassbook_tbl_10069_7000001_8000001 txnpassbook_tbl_10069_7000001_8000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_7000001_8000001
    ADD CONSTRAINT txnpassbook_tbl_10069_7000001_8000001_pkey PRIMARY KEY (id);


--
-- TOC entry 6014 (class 2606 OID 33262188)
-- Name: txnpassbook_tbl_10069_8000001_9000001 txnpassbook_tbl_10069_8000001_9000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_8000001_9000001
    ADD CONSTRAINT txnpassbook_tbl_10069_8000001_9000001_pkey PRIMARY KEY (id);


--
-- TOC entry 6017 (class 2606 OID 33262190)
-- Name: txnpassbook_tbl_10069_9000001_10000001 txnpassbook_tbl_10069_9000001_10000001_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10069_9000001_10000001
    ADD CONSTRAINT txnpassbook_tbl_10069_9000001_10000001_pkey PRIMARY KEY (id);


--
-- TOC entry 6077 (class 2606 OID 33262230)
-- Name: txnpassbook_tbl_10070 txnpassbook_tbl_10070_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10070
    ADD CONSTRAINT txnpassbook_tbl_10070_pkey PRIMARY KEY (id);


--
-- TOC entry 6080 (class 2606 OID 33262232)
-- Name: txnpassbook_tbl_10071 txnpassbook_tbl_10071_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10071
    ADD CONSTRAINT txnpassbook_tbl_10071_pkey PRIMARY KEY (id);


--
-- TOC entry 6083 (class 2606 OID 38043511)
-- Name: txnpassbook_tbl_10072 txnpassbook_tbl_10072_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10072
    ADD CONSTRAINT txnpassbook_tbl_10072_pkey PRIMARY KEY (id);


--
-- TOC entry 6068 (class 2606 OID 33262224)
-- Name: txnpassbook_tbl_10073 txnpassbook_tbl_10073_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10073
    ADD CONSTRAINT txnpassbook_tbl_10073_pkey PRIMARY KEY (id);


--
-- TOC entry 6086 (class 2606 OID 33262234)
-- Name: txnpassbook_tbl_10074 txnpassbook_tbl_10074_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10074
    ADD CONSTRAINT txnpassbook_tbl_10074_pkey PRIMARY KEY (id);


--
-- TOC entry 6089 (class 2606 OID 33262236)
-- Name: txnpassbook_tbl_10075 txnpassbook_tbl_10075_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10075
    ADD CONSTRAINT txnpassbook_tbl_10075_pkey PRIMARY KEY (id);


--
-- TOC entry 6092 (class 2606 OID 33262238)
-- Name: txnpassbook_tbl_10076 txnpassbook_tbl_10076_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10076
    ADD CONSTRAINT txnpassbook_tbl_10076_pkey PRIMARY KEY (id);


--
-- TOC entry 6095 (class 2606 OID 33262240)
-- Name: txnpassbook_tbl_10077 txnpassbook_tbl_10077_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10077
    ADD CONSTRAINT txnpassbook_tbl_10077_pkey PRIMARY KEY (id);


--
-- TOC entry 6098 (class 2606 OID 33262242)
-- Name: txnpassbook_tbl_10078 txnpassbook_tbl_10078_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10078
    ADD CONSTRAINT txnpassbook_tbl_10078_pkey PRIMARY KEY (id);


--
-- TOC entry 6101 (class 2606 OID 33262244)
-- Name: txnpassbook_tbl_10079 txnpassbook_tbl_10079_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10079
    ADD CONSTRAINT txnpassbook_tbl_10079_pkey PRIMARY KEY (id);


--
-- TOC entry 6104 (class 2606 OID 33262246)
-- Name: txnpassbook_tbl_10080 txnpassbook_tbl_10080_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10080
    ADD CONSTRAINT txnpassbook_tbl_10080_pkey PRIMARY KEY (id);


--
-- TOC entry 6118 (class 2606 OID 33808093)
-- Name: txnpassbook_tbl_10081 txnpassbook_tbl_10081_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10081
    ADD CONSTRAINT txnpassbook_tbl_10081_pkey PRIMARY KEY (id);


--
-- TOC entry 6107 (class 2606 OID 33262248)
-- Name: txnpassbook_tbl_10089 txnpassbook_tbl_10089_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10089
    ADD CONSTRAINT txnpassbook_tbl_10089_pkey PRIMARY KEY (id);


--
-- TOC entry 6110 (class 2606 OID 33262250)
-- Name: txnpassbook_tbl_10098 txnpassbook_tbl_10098_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10098
    ADD CONSTRAINT txnpassbook_tbl_10098_pkey PRIMARY KEY (id);


--
-- TOC entry 6071 (class 2606 OID 33262226)
-- Name: txnpassbook_tbl_10099 txnpassbook_tbl_10099_pkey; Type: CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_10099
    ADD CONSTRAINT txnpassbook_tbl_10099_pkey PRIMARY KEY (id);


--
-- TOC entry 5807 (class 2606 OID 22052455)
-- Name: businesstype_tbl businesstype_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.businesstype_tbl
    ADD CONSTRAINT businesstype_pk PRIMARY KEY (id);


--
-- TOC entry 6112 (class 2606 OID 33316429)
-- Name: capturetype_tbl capturetype_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.capturetype_tbl
    ADD CONSTRAINT capturetype_pk PRIMARY KEY (id);


--
-- TOC entry 5692 (class 2606 OID 17227)
-- Name: card_tbl card_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.card_tbl
    ADD CONSTRAINT card_pk PRIMARY KEY (id);


--
-- TOC entry 5695 (class 2606 OID 17228)
-- Name: cardchargetype_tbl cardcharge_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.cardchargetype_tbl
    ADD CONSTRAINT cardcharge_pk PRIMARY KEY (id);


--
-- TOC entry 5697 (class 2606 OID 17229)
-- Name: cardprefix_tbl cardprefix_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.cardprefix_tbl
    ADD CONSTRAINT cardprefix_pk PRIMARY KEY (id);


--
-- TOC entry 5699 (class 2606 OID 17230)
-- Name: cardpricing_tbl cardpricing_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.cardpricing_tbl
    ADD CONSTRAINT cardpricing_pk PRIMARY KEY (id);


--
-- TOC entry 5701 (class 2606 OID 17231)
-- Name: cardpricing_tbl cardpricing_uq; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.cardpricing_tbl
    ADD CONSTRAINT cardpricing_uq UNIQUE (pricepointid, cardid);


--
-- TOC entry 5703 (class 2606 OID 17232)
-- Name: cardstate_tbl cardstate_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.cardstate_tbl
    ADD CONSTRAINT cardstate_pk PRIMARY KEY (id);


--
-- TOC entry 5705 (class 2606 OID 17233)
-- Name: country_tbl country_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.country_tbl
    ADD CONSTRAINT country_pk PRIMARY KEY (id);


--
-- TOC entry 5764 (class 2606 OID 17234)
-- Name: currency_tbl currency_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.currency_tbl
    ADD CONSTRAINT currency_pk PRIMARY KEY (id);


--
-- TOC entry 5708 (class 2606 OID 17235)
-- Name: depositoption_tbl depositoption_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.depositoption_tbl
    ADD CONSTRAINT depositoption_pk PRIMARY KEY (id);


--
-- TOC entry 5710 (class 2606 OID 17236)
-- Name: depositoption_tbl depositoption_uq; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.depositoption_tbl
    ADD CONSTRAINT depositoption_uq UNIQUE (countryid, amount);


--
-- TOC entry 6122 (class 2606 OID 36070700)
-- Name: externalreferencetype_tbl externalreferencetype_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.externalreferencetype_tbl
    ADD CONSTRAINT externalreferencetype_pk PRIMARY KEY (id);


--
-- TOC entry 5712 (class 2606 OID 17237)
-- Name: fee_tbl fee_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.fee_tbl
    ADD CONSTRAINT fee_pk PRIMARY KEY (id);


--
-- TOC entry 5714 (class 2606 OID 17238)
-- Name: fee_tbl fee_uq; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.fee_tbl
    ADD CONSTRAINT fee_uq UNIQUE (typeid, fromid, toid);


--
-- TOC entry 5716 (class 2606 OID 17239)
-- Name: feetype_tbl feetype_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.feetype_tbl
    ADD CONSTRAINT feetype_pk PRIMARY KEY (id);


--
-- TOC entry 5719 (class 2606 OID 17240)
-- Name: flow_tbl flow_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.flow_tbl
    ADD CONSTRAINT flow_pk PRIMARY KEY (id);


--
-- TOC entry 5733 (class 2606 OID 17241)
-- Name: processortype_tbl id_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.processortype_tbl
    ADD CONSTRAINT id_pk PRIMARY KEY (id);


--
-- TOC entry 5722 (class 2606 OID 17242)
-- Name: iinaction_tbl iinaction_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.iinaction_tbl
    ADD CONSTRAINT iinaction_pk PRIMARY KEY (id);


--
-- TOC entry 5724 (class 2606 OID 17243)
-- Name: iprange_tbl iprange_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.iprange_tbl
    ADD CONSTRAINT iprange_pk PRIMARY KEY (id);


--
-- TOC entry 5726 (class 2606 OID 17244)
-- Name: iprange_tbl iprange_uq; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.iprange_tbl
    ADD CONSTRAINT iprange_uq UNIQUE (min, max);


--
-- TOC entry 5762 (class 2606 OID 17245)
-- Name: paymenttype_tbl paymenttype_tbl_pkey; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.paymenttype_tbl
    ADD CONSTRAINT paymenttype_tbl_pkey PRIMARY KEY (id);


--
-- TOC entry 5728 (class 2606 OID 17246)
-- Name: postalcode_tbl postalcode_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.postalcode_tbl
    ADD CONSTRAINT postalcode_pk PRIMARY KEY (id);


--
-- TOC entry 5731 (class 2606 OID 17247)
-- Name: pricepoint_tbl pricepoint_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.pricepoint_tbl
    ADD CONSTRAINT pricepoint_pk PRIMARY KEY (id);


--
-- TOC entry 5773 (class 2606 OID 17248)
-- Name: producttype_tbl producttype_tbl_pkey; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.producttype_tbl
    ADD CONSTRAINT producttype_tbl_pkey PRIMARY KEY (id);


--
-- TOC entry 5735 (class 2606 OID 17249)
-- Name: psp_tbl psp_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.psp_tbl
    ADD CONSTRAINT psp_pk PRIMARY KEY (id);


--
-- TOC entry 5738 (class 2606 OID 17250)
-- Name: pspcard_tbl pspcard_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.pspcard_tbl
    ADD CONSTRAINT pspcard_pk PRIMARY KEY (id);


--
-- TOC entry 5740 (class 2606 OID 17251)
-- Name: pspcard_tbl pspcard_uq; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.pspcard_tbl
    ADD CONSTRAINT pspcard_uq UNIQUE (cardid, pspid);


--
-- TOC entry 5742 (class 2606 OID 17252)
-- Name: pspcurrency_tbl pspcurrency_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.pspcurrency_tbl
    ADD CONSTRAINT pspcurrency_pk PRIMARY KEY (id);


--
-- TOC entry 5793 (class 2606 OID 17253)
-- Name: retrialtype_tbl retrialtype_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.retrialtype_tbl
    ADD CONSTRAINT retrialtype_pk PRIMARY KEY (id);


--
-- TOC entry 5768 (class 2606 OID 17254)
-- Name: sessiontype_tbl sessiontype_tbl_pkey; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.sessiontype_tbl
    ADD CONSTRAINT sessiontype_tbl_pkey PRIMARY KEY (id);


--
-- TOC entry 5745 (class 2606 OID 17255)
-- Name: shipping_tbl shipping_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.shipping_tbl
    ADD CONSTRAINT shipping_pk PRIMARY KEY (id);


--
-- TOC entry 5748 (class 2606 OID 17256)
-- Name: state_tbl state_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.state_tbl
    ADD CONSTRAINT state_pk PRIMARY KEY (id);


--
-- TOC entry 5781 (class 2606 OID 17257)
-- Name: statisticstype_tbl stattype_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.statisticstype_tbl
    ADD CONSTRAINT stattype_pk PRIMARY KEY (id);


--
-- TOC entry 5777 (class 2606 OID 17258)
-- Name: triggerunit_tbl trigger_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.triggerunit_tbl
    ADD CONSTRAINT trigger_pk PRIMARY KEY (id);


--
-- TOC entry 5751 (class 2606 OID 17259)
-- Name: type_tbl type_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.type_tbl
    ADD CONSTRAINT type_pk PRIMARY KEY (id);


--
-- TOC entry 5754 (class 2606 OID 17260)
-- Name: urltype_tbl urltype_pk; Type: CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.urltype_tbl
    ADD CONSTRAINT urltype_pk PRIMARY KEY (id);


--
-- TOC entry 5574 (class 1259 OID 79056)
-- Name: role_uq; Type: INDEX; Schema: admin; Owner: mpoint
--

CREATE UNIQUE INDEX role_uq ON admin.role_tbl USING btree (lower((name)::text));


--
-- TOC entry 5585 (class 1259 OID 22052521)
-- Name: account_tbl_businesstype_index; Type: INDEX; Schema: client; Owner: mpoint
--

CREATE INDEX account_tbl_businesstype_index ON client.account_tbl USING btree (businesstype);


--
-- TOC entry 5588 (class 1259 OID 79060)
-- Name: accountname_uq; Type: INDEX; Schema: client; Owner: mpoint
--

CREATE UNIQUE INDEX accountname_uq ON client.account_tbl USING btree (clientid, upper((name)::text));


--
-- TOC entry 5589 (class 1259 OID 36070284)
-- Name: cardaccess_card_country_uq; Type: INDEX; Schema: client; Owner: mpoint
--

CREATE UNIQUE INDEX cardaccess_card_country_uq ON client.cardaccess_tbl USING btree (clientid, cardid, pspid, countryid, psp_type, walletid) WHERE (enabled = true);


--
-- TOC entry 5627 (class 1259 OID 79061)
-- Name: client_url_uq; Type: INDEX; Schema: client; Owner: mpoint
--

CREATE UNIQUE INDEX client_url_uq ON client.url_tbl USING btree (clientid, lower((url)::text));


--
-- TOC entry 5596 (class 1259 OID 79062)
-- Name: iinranges_idx; Type: INDEX; Schema: client; Owner: mpoint
--

CREATE INDEX iinranges_idx ON client.iinlist_tbl USING btree (clientid, min, max);


--
-- TOC entry 5599 (class 1259 OID 79063)
-- Name: info_psp_uq; Type: INDEX; Schema: client; Owner: mpoint
--

CREATE UNIQUE INDEX info_psp_uq ON client.info_tbl USING btree (infotypeid, clientid, language, pspid) WHERE (pspid IS NOT NULL);


--
-- TOC entry 5600 (class 1259 OID 79064)
-- Name: info_uq; Type: INDEX; Schema: client; Owner: mpoint
--

CREATE UNIQUE INDEX info_uq ON client.info_tbl USING btree (infotypeid, clientid, language) WHERE (pspid IS NULL);


--
-- TOC entry 5603 (class 1259 OID 79065)
-- Name: infotype_uq; Type: INDEX; Schema: client; Owner: mpoint
--

CREATE UNIQUE INDEX infotype_uq ON client.infotype_tbl USING btree (lower((name)::text));


--
-- TOC entry 5608 (class 1259 OID 79066)
-- Name: keyword_uq; Type: INDEX; Schema: client; Owner: mpoint
--

CREATE UNIQUE INDEX keyword_uq ON client.keyword_tbl USING btree (clientid, upper((name)::text));


--
-- TOC entry 5611 (class 1259 OID 79067)
-- Name: merchantaccount_storedcard_uq; Type: INDEX; Schema: client; Owner: mpoint
--

CREATE UNIQUE INDEX merchantaccount_storedcard_uq ON client.merchantaccount_tbl USING btree (clientid, pspid, stored_card);


--
-- TOC entry 5612 (class 1259 OID 79068)
-- Name: merchantaccount_uq; Type: INDEX; Schema: client; Owner: mpoint
--

CREATE UNIQUE INDEX merchantaccount_uq ON client.merchantaccount_tbl USING btree (clientid, pspid) WHERE (stored_card IS NULL);


--
-- TOC entry 6113 (class 1259 OID 33316473)
-- Name: staticroutelevelconfiguration_cardaccessid_uindex; Type: INDEX; Schema: client; Owner: mpoint
--

CREATE UNIQUE INDEX staticroutelevelconfiguration_cardaccessid_uindex ON client.staticroutelevelconfiguration USING btree (cardaccessid);


--
-- TOC entry 5632 (class 1259 OID 79069)
-- Name: account_email_idx; Type: INDEX; Schema: enduser; Owner: mpoint
--

CREATE INDEX account_email_idx ON enduser.account_tbl USING btree (countryid, upper((email)::text), enabled) WHERE (enabled = true);


--
-- TOC entry 5633 (class 1259 OID 79070)
-- Name: account_mobile_idx; Type: INDEX; Schema: enduser; Owner: mpoint
--

CREATE INDEX account_mobile_idx ON enduser.account_tbl USING btree (countryid, mobile, enabled) WHERE (enabled = true);


--
-- TOC entry 5647 (class 1259 OID 79071)
-- Name: claccess_account; Type: INDEX; Schema: enduser; Owner: mpoint
--

CREATE INDEX claccess_account ON enduser.claccess_tbl USING btree (accountid);


--
-- TOC entry 5636 (class 1259 OID 22052703)
-- Name: eu_account_tbl_profileid_index; Type: INDEX; Schema: enduser; Owner: mpoint
--

CREATE INDEX eu_account_tbl_profileid_index ON enduser.account_tbl USING btree (profileid);


--
-- TOC entry 5650 (class 1259 OID 79072)
-- Name: transaction_account_idx; Type: INDEX; Schema: enduser; Owner: mpoint
--

CREATE INDEX transaction_account_idx ON enduser.transaction_tbl USING btree (accountid, txnid);


--
-- TOC entry 5657 (class 1259 OID 8447363)
-- Name: additional_data_tbl_externalid_type_index; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX additional_data_tbl_externalid_type_index ON log.additional_data_tbl USING btree (externalid, type);


--
-- TOC entry 5660 (class 1259 OID 38041496)
-- Name: address_tbl_referenceid_type_index; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX address_tbl_referenceid_type_index ON log.address_tbl USING btree (reference_id, reference_type);


--
-- TOC entry 5784 (class 1259 OID 8271699)
-- Name: client_id_idx; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX client_id_idx ON log.settlement_tbl USING btree (client_id);


--
-- TOC entry 5796 (class 1259 OID 3504115)
-- Name: external_reference_index; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX external_reference_index ON log.externalreference_tbl USING btree (externalid);


--
-- TOC entry 5799 (class 1259 OID 40538369)
-- Name: externalreference_transaction_idx; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX externalreference_transaction_idx ON log.externalreference_tbl USING btree (txnid, externalid, pspid, type);


--
-- TOC entry 5665 (class 1259 OID 8447370)
-- Name: flight_tbl_orderid_index; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX flight_tbl_orderid_index ON log.flight_tbl USING btree (order_id);


--
-- TOC entry 5838 (class 1259 OID 33261959)
-- Name: idx_txnpassbook_tbl_10018_10000001_11000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_10000001_11000001 ON log.txnpassbook_tbl_10018_10000001_11000001 USING btree (clientid, transactionid);


--
-- TOC entry 5811 (class 1259 OID 33261950)
-- Name: idx_txnpassbook_tbl_10018_1000001_2000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_1000001_2000001 ON log.txnpassbook_tbl_10018_1000001_2000001 USING btree (clientid, transactionid);


--
-- TOC entry 5841 (class 1259 OID 33261960)
-- Name: idx_txnpassbook_tbl_10018_11000001_12000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_11000001_12000001 ON log.txnpassbook_tbl_10018_11000001_12000001 USING btree (clientid, transactionid);


--
-- TOC entry 5844 (class 1259 OID 33261961)
-- Name: idx_txnpassbook_tbl_10018_12000001_13000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_12000001_13000001 ON log.txnpassbook_tbl_10018_12000001_13000001 USING btree (clientid, transactionid);


--
-- TOC entry 5847 (class 1259 OID 33261962)
-- Name: idx_txnpassbook_tbl_10018_13000001_14000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_13000001_14000001 ON log.txnpassbook_tbl_10018_13000001_14000001 USING btree (clientid, transactionid);


--
-- TOC entry 5850 (class 1259 OID 33261963)
-- Name: idx_txnpassbook_tbl_10018_14000001_15000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_14000001_15000001 ON log.txnpassbook_tbl_10018_14000001_15000001 USING btree (clientid, transactionid);


--
-- TOC entry 5853 (class 1259 OID 33261964)
-- Name: idx_txnpassbook_tbl_10018_15000001_16000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_15000001_16000001 ON log.txnpassbook_tbl_10018_15000001_16000001 USING btree (clientid, transactionid);


--
-- TOC entry 5856 (class 1259 OID 33261965)
-- Name: idx_txnpassbook_tbl_10018_16000001_17000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_16000001_17000001 ON log.txnpassbook_tbl_10018_16000001_17000001 USING btree (clientid, transactionid);


--
-- TOC entry 5859 (class 1259 OID 33261966)
-- Name: idx_txnpassbook_tbl_10018_17000001_18000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_17000001_18000001 ON log.txnpassbook_tbl_10018_17000001_18000001 USING btree (clientid, transactionid);


--
-- TOC entry 5862 (class 1259 OID 33261967)
-- Name: idx_txnpassbook_tbl_10018_18000001_19000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_18000001_19000001 ON log.txnpassbook_tbl_10018_18000001_19000001 USING btree (clientid, transactionid);


--
-- TOC entry 5865 (class 1259 OID 33261968)
-- Name: idx_txnpassbook_tbl_10018_19000001_20000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_19000001_20000001 ON log.txnpassbook_tbl_10018_19000001_20000001 USING btree (clientid, transactionid);


--
-- TOC entry 5808 (class 1259 OID 33261949)
-- Name: idx_txnpassbook_tbl_10018_1_1000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_1_1000001 ON log.txnpassbook_tbl_10018_1_1000001 USING btree (clientid, transactionid);


--
-- TOC entry 5814 (class 1259 OID 33261951)
-- Name: idx_txnpassbook_tbl_10018_2000001_3000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_2000001_3000001 ON log.txnpassbook_tbl_10018_2000001_3000001 USING btree (clientid, transactionid);


--
-- TOC entry 5817 (class 1259 OID 33261952)
-- Name: idx_txnpassbook_tbl_10018_3000001_4000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_3000001_4000001 ON log.txnpassbook_tbl_10018_3000001_4000001 USING btree (clientid, transactionid);


--
-- TOC entry 5820 (class 1259 OID 33261953)
-- Name: idx_txnpassbook_tbl_10018_4000001_5000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_4000001_5000001 ON log.txnpassbook_tbl_10018_4000001_5000001 USING btree (clientid, transactionid);


--
-- TOC entry 5823 (class 1259 OID 33261954)
-- Name: idx_txnpassbook_tbl_10018_5000001_6000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_5000001_6000001 ON log.txnpassbook_tbl_10018_5000001_6000001 USING btree (clientid, transactionid);


--
-- TOC entry 5826 (class 1259 OID 33261955)
-- Name: idx_txnpassbook_tbl_10018_6000001_7000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_6000001_7000001 ON log.txnpassbook_tbl_10018_6000001_7000001 USING btree (clientid, transactionid);


--
-- TOC entry 5829 (class 1259 OID 33261956)
-- Name: idx_txnpassbook_tbl_10018_7000001_8000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_7000001_8000001 ON log.txnpassbook_tbl_10018_7000001_8000001 USING btree (clientid, transactionid);


--
-- TOC entry 5832 (class 1259 OID 33261957)
-- Name: idx_txnpassbook_tbl_10018_8000001_9000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_8000001_9000001 ON log.txnpassbook_tbl_10018_8000001_9000001 USING btree (clientid, transactionid);


--
-- TOC entry 5835 (class 1259 OID 33261958)
-- Name: idx_txnpassbook_tbl_10018_9000001_10000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10018_9000001_10000001 ON log.txnpassbook_tbl_10018_9000001_10000001 USING btree (clientid, transactionid);


--
-- TOC entry 5898 (class 1259 OID 33261979)
-- Name: idx_txnpassbook_tbl_10020_10000001_11000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_10000001_11000001 ON log.txnpassbook_tbl_10020_10000001_11000001 USING btree (clientid, transactionid);


--
-- TOC entry 5871 (class 1259 OID 33261970)
-- Name: idx_txnpassbook_tbl_10020_1000001_2000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_1000001_2000001 ON log.txnpassbook_tbl_10020_1000001_2000001 USING btree (clientid, transactionid);


--
-- TOC entry 5901 (class 1259 OID 33261980)
-- Name: idx_txnpassbook_tbl_10020_11000001_12000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_11000001_12000001 ON log.txnpassbook_tbl_10020_11000001_12000001 USING btree (clientid, transactionid);


--
-- TOC entry 5904 (class 1259 OID 33261981)
-- Name: idx_txnpassbook_tbl_10020_12000001_13000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_12000001_13000001 ON log.txnpassbook_tbl_10020_12000001_13000001 USING btree (clientid, transactionid);


--
-- TOC entry 5907 (class 1259 OID 33261982)
-- Name: idx_txnpassbook_tbl_10020_13000001_14000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_13000001_14000001 ON log.txnpassbook_tbl_10020_13000001_14000001 USING btree (clientid, transactionid);


--
-- TOC entry 5910 (class 1259 OID 33261983)
-- Name: idx_txnpassbook_tbl_10020_14000001_15000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_14000001_15000001 ON log.txnpassbook_tbl_10020_14000001_15000001 USING btree (clientid, transactionid);


--
-- TOC entry 5913 (class 1259 OID 33261984)
-- Name: idx_txnpassbook_tbl_10020_15000001_16000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_15000001_16000001 ON log.txnpassbook_tbl_10020_15000001_16000001 USING btree (clientid, transactionid);


--
-- TOC entry 5916 (class 1259 OID 33261985)
-- Name: idx_txnpassbook_tbl_10020_16000001_17000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_16000001_17000001 ON log.txnpassbook_tbl_10020_16000001_17000001 USING btree (clientid, transactionid);


--
-- TOC entry 5919 (class 1259 OID 33261986)
-- Name: idx_txnpassbook_tbl_10020_17000001_18000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_17000001_18000001 ON log.txnpassbook_tbl_10020_17000001_18000001 USING btree (clientid, transactionid);


--
-- TOC entry 5922 (class 1259 OID 33261987)
-- Name: idx_txnpassbook_tbl_10020_18000001_19000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_18000001_19000001 ON log.txnpassbook_tbl_10020_18000001_19000001 USING btree (clientid, transactionid);


--
-- TOC entry 5925 (class 1259 OID 33261988)
-- Name: idx_txnpassbook_tbl_10020_19000001_20000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_19000001_20000001 ON log.txnpassbook_tbl_10020_19000001_20000001 USING btree (clientid, transactionid);


--
-- TOC entry 5868 (class 1259 OID 33261969)
-- Name: idx_txnpassbook_tbl_10020_1_1000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_1_1000001 ON log.txnpassbook_tbl_10020_1_1000001 USING btree (clientid, transactionid);


--
-- TOC entry 5874 (class 1259 OID 33261971)
-- Name: idx_txnpassbook_tbl_10020_2000001_3000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_2000001_3000001 ON log.txnpassbook_tbl_10020_2000001_3000001 USING btree (clientid, transactionid);


--
-- TOC entry 5877 (class 1259 OID 33261972)
-- Name: idx_txnpassbook_tbl_10020_3000001_4000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_3000001_4000001 ON log.txnpassbook_tbl_10020_3000001_4000001 USING btree (clientid, transactionid);


--
-- TOC entry 5880 (class 1259 OID 33261973)
-- Name: idx_txnpassbook_tbl_10020_4000001_5000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_4000001_5000001 ON log.txnpassbook_tbl_10020_4000001_5000001 USING btree (clientid, transactionid);


--
-- TOC entry 5883 (class 1259 OID 33261974)
-- Name: idx_txnpassbook_tbl_10020_5000001_6000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_5000001_6000001 ON log.txnpassbook_tbl_10020_5000001_6000001 USING btree (clientid, transactionid);


--
-- TOC entry 5886 (class 1259 OID 33261975)
-- Name: idx_txnpassbook_tbl_10020_6000001_7000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_6000001_7000001 ON log.txnpassbook_tbl_10020_6000001_7000001 USING btree (clientid, transactionid);


--
-- TOC entry 5889 (class 1259 OID 33261976)
-- Name: idx_txnpassbook_tbl_10020_7000001_8000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_7000001_8000001 ON log.txnpassbook_tbl_10020_7000001_8000001 USING btree (clientid, transactionid);


--
-- TOC entry 5892 (class 1259 OID 33261977)
-- Name: idx_txnpassbook_tbl_10020_8000001_9000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_8000001_9000001 ON log.txnpassbook_tbl_10020_8000001_9000001 USING btree (clientid, transactionid);


--
-- TOC entry 5895 (class 1259 OID 33261978)
-- Name: idx_txnpassbook_tbl_10020_9000001_10000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10020_9000001_10000001 ON log.txnpassbook_tbl_10020_9000001_10000001 USING btree (clientid, transactionid);


--
-- TOC entry 5958 (class 1259 OID 33261999)
-- Name: idx_txnpassbook_tbl_10021_10000001_11000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_10000001_11000001 ON log.txnpassbook_tbl_10021_10000001_11000001 USING btree (clientid, transactionid);


--
-- TOC entry 5931 (class 1259 OID 33261990)
-- Name: idx_txnpassbook_tbl_10021_1000001_2000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_1000001_2000001 ON log.txnpassbook_tbl_10021_1000001_2000001 USING btree (clientid, transactionid);


--
-- TOC entry 5961 (class 1259 OID 33262000)
-- Name: idx_txnpassbook_tbl_10021_11000001_12000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_11000001_12000001 ON log.txnpassbook_tbl_10021_11000001_12000001 USING btree (clientid, transactionid);


--
-- TOC entry 5964 (class 1259 OID 33262001)
-- Name: idx_txnpassbook_tbl_10021_12000001_13000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_12000001_13000001 ON log.txnpassbook_tbl_10021_12000001_13000001 USING btree (clientid, transactionid);


--
-- TOC entry 5967 (class 1259 OID 33262002)
-- Name: idx_txnpassbook_tbl_10021_13000001_14000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_13000001_14000001 ON log.txnpassbook_tbl_10021_13000001_14000001 USING btree (clientid, transactionid);


--
-- TOC entry 5970 (class 1259 OID 33262003)
-- Name: idx_txnpassbook_tbl_10021_14000001_15000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_14000001_15000001 ON log.txnpassbook_tbl_10021_14000001_15000001 USING btree (clientid, transactionid);


--
-- TOC entry 5973 (class 1259 OID 33262004)
-- Name: idx_txnpassbook_tbl_10021_15000001_16000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_15000001_16000001 ON log.txnpassbook_tbl_10021_15000001_16000001 USING btree (clientid, transactionid);


--
-- TOC entry 5976 (class 1259 OID 33262005)
-- Name: idx_txnpassbook_tbl_10021_16000001_17000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_16000001_17000001 ON log.txnpassbook_tbl_10021_16000001_17000001 USING btree (clientid, transactionid);


--
-- TOC entry 5979 (class 1259 OID 33262006)
-- Name: idx_txnpassbook_tbl_10021_17000001_18000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_17000001_18000001 ON log.txnpassbook_tbl_10021_17000001_18000001 USING btree (clientid, transactionid);


--
-- TOC entry 5982 (class 1259 OID 33262007)
-- Name: idx_txnpassbook_tbl_10021_18000001_19000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_18000001_19000001 ON log.txnpassbook_tbl_10021_18000001_19000001 USING btree (clientid, transactionid);


--
-- TOC entry 5985 (class 1259 OID 33262008)
-- Name: idx_txnpassbook_tbl_10021_19000001_20000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_19000001_20000001 ON log.txnpassbook_tbl_10021_19000001_20000001 USING btree (clientid, transactionid);


--
-- TOC entry 5928 (class 1259 OID 33261989)
-- Name: idx_txnpassbook_tbl_10021_1_1000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_1_1000001 ON log.txnpassbook_tbl_10021_1_1000001 USING btree (clientid, transactionid);


--
-- TOC entry 5934 (class 1259 OID 33261991)
-- Name: idx_txnpassbook_tbl_10021_2000001_3000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_2000001_3000001 ON log.txnpassbook_tbl_10021_2000001_3000001 USING btree (clientid, transactionid);


--
-- TOC entry 5937 (class 1259 OID 33261992)
-- Name: idx_txnpassbook_tbl_10021_3000001_4000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_3000001_4000001 ON log.txnpassbook_tbl_10021_3000001_4000001 USING btree (clientid, transactionid);


--
-- TOC entry 5940 (class 1259 OID 33261993)
-- Name: idx_txnpassbook_tbl_10021_4000001_5000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_4000001_5000001 ON log.txnpassbook_tbl_10021_4000001_5000001 USING btree (clientid, transactionid);


--
-- TOC entry 5943 (class 1259 OID 33261994)
-- Name: idx_txnpassbook_tbl_10021_5000001_6000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_5000001_6000001 ON log.txnpassbook_tbl_10021_5000001_6000001 USING btree (clientid, transactionid);


--
-- TOC entry 5946 (class 1259 OID 33261995)
-- Name: idx_txnpassbook_tbl_10021_6000001_7000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_6000001_7000001 ON log.txnpassbook_tbl_10021_6000001_7000001 USING btree (clientid, transactionid);


--
-- TOC entry 5949 (class 1259 OID 33261996)
-- Name: idx_txnpassbook_tbl_10021_7000001_8000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_7000001_8000001 ON log.txnpassbook_tbl_10021_7000001_8000001 USING btree (clientid, transactionid);


--
-- TOC entry 5952 (class 1259 OID 33261997)
-- Name: idx_txnpassbook_tbl_10021_8000001_9000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_8000001_9000001 ON log.txnpassbook_tbl_10021_8000001_9000001 USING btree (clientid, transactionid);


--
-- TOC entry 5955 (class 1259 OID 33261998)
-- Name: idx_txnpassbook_tbl_10021_9000001_10000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10021_9000001_10000001 ON log.txnpassbook_tbl_10021_9000001_10000001 USING btree (clientid, transactionid);


--
-- TOC entry 6048 (class 1259 OID 33262029)
-- Name: idx_txnpassbook_tbl_10022; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10022 ON log.txnpassbook_tbl_10022 USING btree (clientid, transactionid);


--
-- TOC entry 6051 (class 1259 OID 33262030)
-- Name: idx_txnpassbook_tbl_10060; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10060 ON log.txnpassbook_tbl_10060 USING btree (clientid, transactionid);


--
-- TOC entry 6054 (class 1259 OID 33262031)
-- Name: idx_txnpassbook_tbl_10061; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10061 ON log.txnpassbook_tbl_10061 USING btree (clientid, transactionid);


--
-- TOC entry 6057 (class 1259 OID 33262032)
-- Name: idx_txnpassbook_tbl_10062; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10062 ON log.txnpassbook_tbl_10062 USING btree (clientid, transactionid);


--
-- TOC entry 6060 (class 1259 OID 33262033)
-- Name: idx_txnpassbook_tbl_10065; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10065 ON log.txnpassbook_tbl_10065 USING btree (clientid, transactionid);


--
-- TOC entry 6072 (class 1259 OID 33262037)
-- Name: idx_txnpassbook_tbl_10066; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10066 ON log.txnpassbook_tbl_10066 USING btree (clientid, transactionid);


--
-- TOC entry 6063 (class 1259 OID 33262034)
-- Name: idx_txnpassbook_tbl_10067; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10067 ON log.txnpassbook_tbl_10067 USING btree (clientid, transactionid);


--
-- TOC entry 6018 (class 1259 OID 33262019)
-- Name: idx_txnpassbook_tbl_10069_10000001_11000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_10000001_11000001 ON log.txnpassbook_tbl_10069_10000001_11000001 USING btree (clientid, transactionid);


--
-- TOC entry 5991 (class 1259 OID 33262010)
-- Name: idx_txnpassbook_tbl_10069_1000001_2000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_1000001_2000001 ON log.txnpassbook_tbl_10069_1000001_2000001 USING btree (clientid, transactionid);


--
-- TOC entry 6021 (class 1259 OID 33262020)
-- Name: idx_txnpassbook_tbl_10069_11000001_12000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_11000001_12000001 ON log.txnpassbook_tbl_10069_11000001_12000001 USING btree (clientid, transactionid);


--
-- TOC entry 6024 (class 1259 OID 33262021)
-- Name: idx_txnpassbook_tbl_10069_12000001_13000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_12000001_13000001 ON log.txnpassbook_tbl_10069_12000001_13000001 USING btree (clientid, transactionid);


--
-- TOC entry 6027 (class 1259 OID 33262022)
-- Name: idx_txnpassbook_tbl_10069_13000001_14000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_13000001_14000001 ON log.txnpassbook_tbl_10069_13000001_14000001 USING btree (clientid, transactionid);


--
-- TOC entry 6030 (class 1259 OID 33262023)
-- Name: idx_txnpassbook_tbl_10069_14000001_15000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_14000001_15000001 ON log.txnpassbook_tbl_10069_14000001_15000001 USING btree (clientid, transactionid);


--
-- TOC entry 6033 (class 1259 OID 33262024)
-- Name: idx_txnpassbook_tbl_10069_15000001_16000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_15000001_16000001 ON log.txnpassbook_tbl_10069_15000001_16000001 USING btree (clientid, transactionid);


--
-- TOC entry 6036 (class 1259 OID 33262025)
-- Name: idx_txnpassbook_tbl_10069_16000001_17000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_16000001_17000001 ON log.txnpassbook_tbl_10069_16000001_17000001 USING btree (clientid, transactionid);


--
-- TOC entry 6039 (class 1259 OID 33262026)
-- Name: idx_txnpassbook_tbl_10069_17000001_18000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_17000001_18000001 ON log.txnpassbook_tbl_10069_17000001_18000001 USING btree (clientid, transactionid);


--
-- TOC entry 6042 (class 1259 OID 33262027)
-- Name: idx_txnpassbook_tbl_10069_18000001_19000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_18000001_19000001 ON log.txnpassbook_tbl_10069_18000001_19000001 USING btree (clientid, transactionid);


--
-- TOC entry 6045 (class 1259 OID 33262028)
-- Name: idx_txnpassbook_tbl_10069_19000001_20000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_19000001_20000001 ON log.txnpassbook_tbl_10069_19000001_20000001 USING btree (clientid, transactionid);


--
-- TOC entry 5988 (class 1259 OID 33262009)
-- Name: idx_txnpassbook_tbl_10069_1_1000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_1_1000001 ON log.txnpassbook_tbl_10069_1_1000001 USING btree (clientid, transactionid);


--
-- TOC entry 5994 (class 1259 OID 33262011)
-- Name: idx_txnpassbook_tbl_10069_2000001_3000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_2000001_3000001 ON log.txnpassbook_tbl_10069_2000001_3000001 USING btree (clientid, transactionid);


--
-- TOC entry 5997 (class 1259 OID 33262012)
-- Name: idx_txnpassbook_tbl_10069_3000001_4000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_3000001_4000001 ON log.txnpassbook_tbl_10069_3000001_4000001 USING btree (clientid, transactionid);


--
-- TOC entry 6000 (class 1259 OID 33262013)
-- Name: idx_txnpassbook_tbl_10069_4000001_5000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_4000001_5000001 ON log.txnpassbook_tbl_10069_4000001_5000001 USING btree (clientid, transactionid);


--
-- TOC entry 6003 (class 1259 OID 33262014)
-- Name: idx_txnpassbook_tbl_10069_5000001_6000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_5000001_6000001 ON log.txnpassbook_tbl_10069_5000001_6000001 USING btree (clientid, transactionid);


--
-- TOC entry 6006 (class 1259 OID 33262015)
-- Name: idx_txnpassbook_tbl_10069_6000001_7000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_6000001_7000001 ON log.txnpassbook_tbl_10069_6000001_7000001 USING btree (clientid, transactionid);


--
-- TOC entry 6009 (class 1259 OID 33262016)
-- Name: idx_txnpassbook_tbl_10069_7000001_8000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_7000001_8000001 ON log.txnpassbook_tbl_10069_7000001_8000001 USING btree (clientid, transactionid);


--
-- TOC entry 6012 (class 1259 OID 33262017)
-- Name: idx_txnpassbook_tbl_10069_8000001_9000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_8000001_9000001 ON log.txnpassbook_tbl_10069_8000001_9000001 USING btree (clientid, transactionid);


--
-- TOC entry 6015 (class 1259 OID 33262018)
-- Name: idx_txnpassbook_tbl_10069_9000001_10000001; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10069_9000001_10000001 ON log.txnpassbook_tbl_10069_9000001_10000001 USING btree (clientid, transactionid);


--
-- TOC entry 6075 (class 1259 OID 33262038)
-- Name: idx_txnpassbook_tbl_10070; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10070 ON log.txnpassbook_tbl_10070 USING btree (clientid, transactionid);


--
-- TOC entry 6078 (class 1259 OID 33262039)
-- Name: idx_txnpassbook_tbl_10071; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10071 ON log.txnpassbook_tbl_10071 USING btree (clientid, transactionid);


--
-- TOC entry 6081 (class 1259 OID 38043499)
-- Name: idx_txnpassbook_tbl_10072; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10072 ON log.txnpassbook_tbl_10072 USING btree (clientid, transactionid);


--
-- TOC entry 6066 (class 1259 OID 33262035)
-- Name: idx_txnpassbook_tbl_10073; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10073 ON log.txnpassbook_tbl_10073 USING btree (clientid, transactionid);


--
-- TOC entry 6084 (class 1259 OID 33262040)
-- Name: idx_txnpassbook_tbl_10074; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10074 ON log.txnpassbook_tbl_10074 USING btree (clientid, transactionid);


--
-- TOC entry 6087 (class 1259 OID 33262041)
-- Name: idx_txnpassbook_tbl_10075; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10075 ON log.txnpassbook_tbl_10075 USING btree (clientid, transactionid);


--
-- TOC entry 6090 (class 1259 OID 33262042)
-- Name: idx_txnpassbook_tbl_10076; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10076 ON log.txnpassbook_tbl_10076 USING btree (clientid, transactionid);


--
-- TOC entry 6093 (class 1259 OID 33262043)
-- Name: idx_txnpassbook_tbl_10077; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10077 ON log.txnpassbook_tbl_10077 USING btree (clientid, transactionid);


--
-- TOC entry 6096 (class 1259 OID 33262044)
-- Name: idx_txnpassbook_tbl_10078; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10078 ON log.txnpassbook_tbl_10078 USING btree (clientid, transactionid);


--
-- TOC entry 6099 (class 1259 OID 33262045)
-- Name: idx_txnpassbook_tbl_10079; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10079 ON log.txnpassbook_tbl_10079 USING btree (clientid, transactionid);


--
-- TOC entry 6102 (class 1259 OID 33262046)
-- Name: idx_txnpassbook_tbl_10080; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10080 ON log.txnpassbook_tbl_10080 USING btree (clientid, transactionid);


--
-- TOC entry 6116 (class 1259 OID 33808091)
-- Name: idx_txnpassbook_tbl_10081; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10081 ON log.txnpassbook_tbl_10081 USING btree (clientid, transactionid);


--
-- TOC entry 6105 (class 1259 OID 33262047)
-- Name: idx_txnpassbook_tbl_10089; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10089 ON log.txnpassbook_tbl_10089 USING btree (clientid, transactionid);


--
-- TOC entry 6108 (class 1259 OID 33262048)
-- Name: idx_txnpassbook_tbl_10098; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10098 ON log.txnpassbook_tbl_10098 USING btree (clientid, transactionid);


--
-- TOC entry 6069 (class 1259 OID 33262036)
-- Name: idx_txnpassbook_tbl_10099; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX idx_txnpassbook_tbl_10099 ON log.txnpassbook_tbl_10099 USING btree (clientid, transactionid);


--
-- TOC entry 5668 (class 1259 OID 17201900)
-- Name: message_transaction_state_idx; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX message_transaction_state_idx ON log.message_tbl USING btree (txnid, stateid, created);


--
-- TOC entry 5669 (class 1259 OID 18343402)
-- Name: message_virtual_card_idx; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX message_virtual_card_idx ON log.message_tbl USING btree (txnid, "substring"(data, 1, ("position"(data, ' '::text) - 1))) WHERE (stateid = 2030);


--
-- TOC entry 5676 (class 1259 OID 22052183)
-- Name: order_tbl_orderref_index; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX order_tbl_orderref_index ON log.order_tbl USING btree (orderref);


--
-- TOC entry 5677 (class 1259 OID 8447365)
-- Name: order_tbl_txnid_index; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX order_tbl_txnid_index ON log.order_tbl USING btree (txnid);


--
-- TOC entry 5800 (class 1259 OID 8365135)
-- Name: performedopt_idx; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX performedopt_idx ON log.txnpassbook_tbl_backup_20200401 USING btree (performedopt);


--
-- TOC entry 5785 (class 1259 OID 8271700)
-- Name: psp_id_idx; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX psp_id_idx ON log.settlement_tbl USING btree (psp_id);


--
-- TOC entry 5790 (class 1259 OID 8271698)
-- Name: settlementid_idx; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX settlementid_idx ON log.settlement_record_tbl USING btree (settlementid);


--
-- TOC entry 5791 (class 1259 OID 8271697)
-- Name: srtransactionid_idx; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX srtransactionid_idx ON log.settlement_record_tbl USING btree (transactionid);


--
-- TOC entry 5682 (class 1259 OID 79074)
-- Name: transaction_created_idx; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX transaction_created_idx ON log.transaction_tbl USING btree (created);


--
-- TOC entry 5683 (class 1259 OID 79075)
-- Name: transaction_customer_ref_idx; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX transaction_customer_ref_idx ON log.transaction_tbl USING btree (customer_ref);


--
-- TOC entry 5684 (class 1259 OID 79076)
-- Name: transaction_email_idx; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX transaction_email_idx ON log.transaction_tbl USING btree (email);


--
-- TOC entry 5685 (class 1259 OID 79077)
-- Name: transaction_mobile_idx; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX transaction_mobile_idx ON log.transaction_tbl USING btree (mobile);


--
-- TOC entry 5686 (class 1259 OID 79078)
-- Name: transaction_order_idx; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX transaction_order_idx ON log.transaction_tbl USING btree (clientid, orderid);


--
-- TOC entry 5689 (class 1259 OID 7472890)
-- Name: transaction_session_idx; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX transaction_session_idx ON log.transaction_tbl USING btree (sessionid);


--
-- TOC entry 5690 (class 1259 OID 8447373)
-- Name: transaction_tbl_clientid_pspid_index; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX transaction_tbl_clientid_pspid_index ON log.transaction_tbl USING btree (clientid, pspid);


--
-- TOC entry 5801 (class 1259 OID 8365134)
-- Name: transactionid_idx; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX transactionid_idx ON log.txnpassbook_tbl_backup_20200401 USING btree (transactionid);


--
-- TOC entry 5802 (class 1259 OID 15622294)
-- Name: txn_status; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX txn_status ON log.txnpassbook_tbl_backup_20200401 USING btree (performedopt, status);


--
-- TOC entry 5805 (class 1259 OID 22052706)
-- Name: txnpassbook_tbl_extref_index; Type: INDEX; Schema: log; Owner: mpoint
--

CREATE INDEX txnpassbook_tbl_extref_index ON log.txnpassbook_tbl_backup_20200401 USING btree (extref);


--
-- TOC entry 5693 (class 1259 OID 79079)
-- Name: card_uq; Type: INDEX; Schema: system; Owner: mpoint
--

CREATE UNIQUE INDEX card_uq ON system.card_tbl USING btree (upper((name)::text));


--
-- TOC entry 5706 (class 1259 OID 79080)
-- Name: country_uq; Type: INDEX; Schema: system; Owner: mpoint
--

CREATE UNIQUE INDEX country_uq ON system.country_tbl USING btree (upper((name)::text));


--
-- TOC entry 5717 (class 1259 OID 79081)
-- Name: feetype_uq; Type: INDEX; Schema: system; Owner: mpoint
--

CREATE UNIQUE INDEX feetype_uq ON system.feetype_tbl USING btree (upper((name)::text));


--
-- TOC entry 5720 (class 1259 OID 79082)
-- Name: flow_uq; Type: INDEX; Schema: system; Owner: mpoint
--

CREATE UNIQUE INDEX flow_uq ON system.flow_tbl USING btree (upper((name)::text));


--
-- TOC entry 5760 (class 1259 OID 81043)
-- Name: paymenttype_tbl_name_uindex; Type: INDEX; Schema: system; Owner: mpoint
--

CREATE UNIQUE INDEX paymenttype_tbl_name_uindex ON system.paymenttype_tbl USING btree (name);


--
-- TOC entry 5729 (class 1259 OID 79083)
-- Name: postalcode_uq; Type: INDEX; Schema: system; Owner: mpoint
--

CREATE UNIQUE INDEX postalcode_uq ON system.postalcode_tbl USING btree (latitude, longitude, code, lower((city)::text));


--
-- TOC entry 5771 (class 1259 OID 223836)
-- Name: producttype_tbl_name_uindex; Type: INDEX; Schema: system; Owner: mpoint
--

CREATE UNIQUE INDEX producttype_tbl_name_uindex ON system.producttype_tbl USING btree (name);


--
-- TOC entry 5736 (class 1259 OID 79084)
-- Name: psp_uq; Type: INDEX; Schema: system; Owner: mpoint
--

CREATE UNIQUE INDEX psp_uq ON system.psp_tbl USING btree (upper((name)::text));


--
-- TOC entry 5743 (class 1259 OID 8447385)
-- Name: pspcurrency_tbl_pspid_index; Type: INDEX; Schema: system; Owner: mpoint
--

CREATE INDEX pspcurrency_tbl_pspid_index ON system.pspcurrency_tbl USING btree (pspid);


--
-- TOC entry 5746 (class 1259 OID 79085)
-- Name: shipping_uq; Type: INDEX; Schema: system; Owner: mpoint
--

CREATE UNIQUE INDEX shipping_uq ON system.shipping_tbl USING btree (upper((name)::text));


--
-- TOC entry 5749 (class 1259 OID 79086)
-- Name: state_uq; Type: INDEX; Schema: system; Owner: mpoint
--

CREATE UNIQUE INDEX state_uq ON system.state_tbl USING btree (countryid, upper((code)::text));


--
-- TOC entry 5752 (class 1259 OID 79087)
-- Name: type_uq; Type: INDEX; Schema: system; Owner: mpoint
--

CREATE UNIQUE INDEX type_uq ON system.type_tbl USING btree (upper((name)::text));


--
-- TOC entry 5755 (class 1259 OID 79088)
-- Name: urltype_uq; Type: INDEX; Schema: system; Owner: mpoint
--

CREATE UNIQUE INDEX urltype_uq ON system.urltype_tbl USING btree (lower((name)::text));


--
-- TOC entry 6247 (class 2620 OID 17261)
-- Name: access_tbl update_access; Type: TRIGGER; Schema: admin; Owner: mpoint
--

CREATE TRIGGER update_access BEFORE UPDATE ON admin.access_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6248 (class 2620 OID 17262)
-- Name: role_tbl update_role; Type: TRIGGER; Schema: admin; Owner: mpoint
--

CREATE TRIGGER update_role BEFORE UPDATE ON admin.role_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6249 (class 2620 OID 17263)
-- Name: roleaccess_tbl update_roleaccess; Type: TRIGGER; Schema: admin; Owner: mpoint
--

CREATE TRIGGER update_roleaccess BEFORE UPDATE ON admin.roleaccess_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6250 (class 2620 OID 17264)
-- Name: roleinfo_tbl update_roleinfo; Type: TRIGGER; Schema: admin; Owner: mpoint
--

CREATE TRIGGER update_roleinfo BEFORE UPDATE ON admin.roleinfo_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6251 (class 2620 OID 17265)
-- Name: account_tbl update_account; Type: TRIGGER; Schema: client; Owner: mpoint
--

CREATE TRIGGER update_account BEFORE UPDATE ON client.account_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6252 (class 2620 OID 17266)
-- Name: cardaccess_tbl update_cardaccess; Type: TRIGGER; Schema: client; Owner: mpoint
--

CREATE TRIGGER update_cardaccess BEFORE UPDATE ON client.cardaccess_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6253 (class 2620 OID 17267)
-- Name: client_tbl update_client; Type: TRIGGER; Schema: client; Owner: mpoint
--

CREATE TRIGGER update_client BEFORE UPDATE ON client.client_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6254 (class 2620 OID 17268)
-- Name: iinlist_tbl update_iinlist; Type: TRIGGER; Schema: client; Owner: mpoint
--

CREATE TRIGGER update_iinlist BEFORE UPDATE ON client.iinlist_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6255 (class 2620 OID 17269)
-- Name: info_tbl update_info; Type: TRIGGER; Schema: client; Owner: mpoint
--

CREATE TRIGGER update_info BEFORE UPDATE ON client.info_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6256 (class 2620 OID 17270)
-- Name: infotype_tbl update_infotype; Type: TRIGGER; Schema: client; Owner: mpoint
--

CREATE TRIGGER update_infotype BEFORE UPDATE ON client.infotype_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6257 (class 2620 OID 17271)
-- Name: keyword_tbl update_keyword; Type: TRIGGER; Schema: client; Owner: mpoint
--

CREATE TRIGGER update_keyword BEFORE UPDATE ON client.keyword_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6258 (class 2620 OID 17272)
-- Name: merchantaccount_tbl update_merchantaccount; Type: TRIGGER; Schema: client; Owner: mpoint
--

CREATE TRIGGER update_merchantaccount BEFORE UPDATE ON client.merchantaccount_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6259 (class 2620 OID 17273)
-- Name: merchantsubaccount_tbl update_merchantsubaccount; Type: TRIGGER; Schema: client; Owner: mpoint
--

CREATE TRIGGER update_merchantsubaccount BEFORE UPDATE ON client.merchantsubaccount_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6260 (class 2620 OID 17274)
-- Name: product_tbl update_product; Type: TRIGGER; Schema: client; Owner: mpoint
--

CREATE TRIGGER update_product BEFORE UPDATE ON client.product_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6261 (class 2620 OID 17275)
-- Name: shipping_tbl update_shipping; Type: TRIGGER; Schema: client; Owner: mpoint
--

CREATE TRIGGER update_shipping BEFORE UPDATE ON client.shipping_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6262 (class 2620 OID 17276)
-- Name: shop_tbl update_shop; Type: TRIGGER; Schema: client; Owner: mpoint
--

CREATE TRIGGER update_shop BEFORE UPDATE ON client.shop_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6395 (class 2620 OID 33316476)
-- Name: staticroutelevelconfiguration update_staticroutelevelconfiguration; Type: TRIGGER; Schema: client; Owner: mpoint
--

CREATE TRIGGER update_staticroutelevelconfiguration BEFORE UPDATE ON client.staticroutelevelconfiguration FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6263 (class 2620 OID 17277)
-- Name: surepay_tbl update_surepay; Type: TRIGGER; Schema: client; Owner: mpoint
--

CREATE TRIGGER update_surepay BEFORE UPDATE ON client.surepay_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6264 (class 2620 OID 17278)
-- Name: url_tbl update_url; Type: TRIGGER; Schema: client; Owner: mpoint
--

CREATE TRIGGER update_url BEFORE UPDATE ON client.url_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6266 (class 2620 OID 17279)
-- Name: activation_tbl insert_activation; Type: TRIGGER; Schema: enduser; Owner: mpoint
--

CREATE TRIGGER insert_activation BEFORE UPDATE ON enduser.activation_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6270 (class 2620 OID 17280)
-- Name: transaction_tbl modify_transaction; Type: TRIGGER; Schema: enduser; Owner: mpoint
--

CREATE TRIGGER modify_transaction AFTER INSERT OR DELETE OR UPDATE ON enduser.transaction_tbl FOR EACH ROW EXECUTE PROCEDURE public.modify_endusertxn_proc();


--
-- TOC entry 6265 (class 2620 OID 17281)
-- Name: account_tbl update_account; Type: TRIGGER; Schema: enduser; Owner: mpoint
--

CREATE TRIGGER update_account BEFORE UPDATE ON enduser.account_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6267 (class 2620 OID 17282)
-- Name: address_tbl update_address; Type: TRIGGER; Schema: enduser; Owner: mpoint
--

CREATE TRIGGER update_address BEFORE UPDATE ON enduser.address_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6268 (class 2620 OID 17283)
-- Name: card_tbl update_card; Type: TRIGGER; Schema: enduser; Owner: mpoint
--

CREATE TRIGGER update_card BEFORE UPDATE ON enduser.card_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6269 (class 2620 OID 17284)
-- Name: claccess_tbl update_claccess; Type: TRIGGER; Schema: enduser; Owner: mpoint
--

CREATE TRIGGER update_claccess BEFORE UPDATE ON enduser.claccess_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6271 (class 2620 OID 17285)
-- Name: message_tbl update_message; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_message BEFORE UPDATE ON log.message_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6272 (class 2620 OID 17286)
-- Name: note_tbl update_note; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_note BEFORE UPDATE ON log.note_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6291 (class 2620 OID 22052604)
-- Name: settlement_tbl update_settlement; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_settlement BEFORE UPDATE ON log.settlement_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6292 (class 2620 OID 22052636)
-- Name: settlement_record_tbl update_settlement_record; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_settlement_record BEFORE UPDATE ON log.settlement_record_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6273 (class 2620 OID 17287)
-- Name: state_tbl update_state; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_state BEFORE UPDATE ON log.state_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6274 (class 2620 OID 17288)
-- Name: transaction_tbl update_transaction; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_transaction BEFORE UPDATE ON log.transaction_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6293 (class 2620 OID 8365133)
-- Name: txnpassbook_tbl_backup_20200401 update_txnpassbook; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook BEFORE UPDATE ON log.txnpassbook_tbl_backup_20200401 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6304 (class 2620 OID 33315453)
-- Name: txnpassbook_tbl_10018_10000001_11000001 update_txnpassbook_10018_10000001_11000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_10000001_11000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_10000001_11000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6295 (class 2620 OID 33315444)
-- Name: txnpassbook_tbl_10018_1000001_2000001 update_txnpassbook_10018_1000001_2000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_1000001_2000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_1000001_2000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6305 (class 2620 OID 33315454)
-- Name: txnpassbook_tbl_10018_11000001_12000001 update_txnpassbook_10018_11000001_12000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_11000001_12000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_11000001_12000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6306 (class 2620 OID 33315455)
-- Name: txnpassbook_tbl_10018_12000001_13000001 update_txnpassbook_10018_12000001_13000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_12000001_13000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_12000001_13000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6307 (class 2620 OID 33315456)
-- Name: txnpassbook_tbl_10018_13000001_14000001 update_txnpassbook_10018_13000001_14000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_13000001_14000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_13000001_14000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6308 (class 2620 OID 33315457)
-- Name: txnpassbook_tbl_10018_14000001_15000001 update_txnpassbook_10018_14000001_15000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_14000001_15000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_14000001_15000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6309 (class 2620 OID 33315458)
-- Name: txnpassbook_tbl_10018_15000001_16000001 update_txnpassbook_10018_15000001_16000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_15000001_16000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_15000001_16000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6310 (class 2620 OID 33315459)
-- Name: txnpassbook_tbl_10018_16000001_17000001 update_txnpassbook_10018_16000001_17000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_16000001_17000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_16000001_17000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6311 (class 2620 OID 33315460)
-- Name: txnpassbook_tbl_10018_17000001_18000001 update_txnpassbook_10018_17000001_18000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_17000001_18000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_17000001_18000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6312 (class 2620 OID 33315461)
-- Name: txnpassbook_tbl_10018_18000001_19000001 update_txnpassbook_10018_18000001_19000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_18000001_19000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_18000001_19000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6313 (class 2620 OID 33315462)
-- Name: txnpassbook_tbl_10018_19000001_20000001 update_txnpassbook_10018_19000001_20000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_19000001_20000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_19000001_20000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6294 (class 2620 OID 33315443)
-- Name: txnpassbook_tbl_10018_1_1000001 update_txnpassbook_10018_1_1000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_1_1000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_1_1000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6296 (class 2620 OID 33315445)
-- Name: txnpassbook_tbl_10018_2000001_3000001 update_txnpassbook_10018_2000001_3000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_2000001_3000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_2000001_3000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6297 (class 2620 OID 33315446)
-- Name: txnpassbook_tbl_10018_3000001_4000001 update_txnpassbook_10018_3000001_4000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_3000001_4000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_3000001_4000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6298 (class 2620 OID 33315447)
-- Name: txnpassbook_tbl_10018_4000001_5000001 update_txnpassbook_10018_4000001_5000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_4000001_5000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_4000001_5000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6299 (class 2620 OID 33315448)
-- Name: txnpassbook_tbl_10018_5000001_6000001 update_txnpassbook_10018_5000001_6000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_5000001_6000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_5000001_6000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6300 (class 2620 OID 33315449)
-- Name: txnpassbook_tbl_10018_6000001_7000001 update_txnpassbook_10018_6000001_7000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_6000001_7000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_6000001_7000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6301 (class 2620 OID 33315450)
-- Name: txnpassbook_tbl_10018_7000001_8000001 update_txnpassbook_10018_7000001_8000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_7000001_8000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_7000001_8000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6302 (class 2620 OID 33315451)
-- Name: txnpassbook_tbl_10018_8000001_9000001 update_txnpassbook_10018_8000001_9000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_8000001_9000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_8000001_9000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6303 (class 2620 OID 33315452)
-- Name: txnpassbook_tbl_10018_9000001_10000001 update_txnpassbook_10018_9000001_10000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10018_9000001_10000001 BEFORE UPDATE ON log.txnpassbook_tbl_10018_9000001_10000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6324 (class 2620 OID 33315482)
-- Name: txnpassbook_tbl_10020_10000001_11000001 update_txnpassbook_10020_10000001_11000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_10000001_11000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_10000001_11000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6315 (class 2620 OID 33315473)
-- Name: txnpassbook_tbl_10020_1000001_2000001 update_txnpassbook_10020_1000001_2000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_1000001_2000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_1000001_2000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6325 (class 2620 OID 33315483)
-- Name: txnpassbook_tbl_10020_11000001_12000001 update_txnpassbook_10020_11000001_12000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_11000001_12000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_11000001_12000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6326 (class 2620 OID 33315484)
-- Name: txnpassbook_tbl_10020_12000001_13000001 update_txnpassbook_10020_12000001_13000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_12000001_13000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_12000001_13000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6327 (class 2620 OID 33315485)
-- Name: txnpassbook_tbl_10020_13000001_14000001 update_txnpassbook_10020_13000001_14000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_13000001_14000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_13000001_14000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6328 (class 2620 OID 33315486)
-- Name: txnpassbook_tbl_10020_14000001_15000001 update_txnpassbook_10020_14000001_15000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_14000001_15000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_14000001_15000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6329 (class 2620 OID 33315487)
-- Name: txnpassbook_tbl_10020_15000001_16000001 update_txnpassbook_10020_15000001_16000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_15000001_16000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_15000001_16000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6330 (class 2620 OID 33315488)
-- Name: txnpassbook_tbl_10020_16000001_17000001 update_txnpassbook_10020_16000001_17000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_16000001_17000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_16000001_17000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6331 (class 2620 OID 33315489)
-- Name: txnpassbook_tbl_10020_17000001_18000001 update_txnpassbook_10020_17000001_18000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_17000001_18000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_17000001_18000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6332 (class 2620 OID 33315490)
-- Name: txnpassbook_tbl_10020_18000001_19000001 update_txnpassbook_10020_18000001_19000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_18000001_19000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_18000001_19000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6333 (class 2620 OID 33315491)
-- Name: txnpassbook_tbl_10020_19000001_20000001 update_txnpassbook_10020_19000001_20000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_19000001_20000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_19000001_20000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6314 (class 2620 OID 33315472)
-- Name: txnpassbook_tbl_10020_1_1000001 update_txnpassbook_10020_1_1000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_1_1000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_1_1000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6316 (class 2620 OID 33315474)
-- Name: txnpassbook_tbl_10020_2000001_3000001 update_txnpassbook_10020_2000001_3000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_2000001_3000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_2000001_3000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6317 (class 2620 OID 33315475)
-- Name: txnpassbook_tbl_10020_3000001_4000001 update_txnpassbook_10020_3000001_4000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_3000001_4000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_3000001_4000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6318 (class 2620 OID 33315476)
-- Name: txnpassbook_tbl_10020_4000001_5000001 update_txnpassbook_10020_4000001_5000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_4000001_5000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_4000001_5000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6319 (class 2620 OID 33315477)
-- Name: txnpassbook_tbl_10020_5000001_6000001 update_txnpassbook_10020_5000001_6000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_5000001_6000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_5000001_6000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6320 (class 2620 OID 33315478)
-- Name: txnpassbook_tbl_10020_6000001_7000001 update_txnpassbook_10020_6000001_7000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_6000001_7000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_6000001_7000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6321 (class 2620 OID 33315479)
-- Name: txnpassbook_tbl_10020_7000001_8000001 update_txnpassbook_10020_7000001_8000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_7000001_8000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_7000001_8000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6322 (class 2620 OID 33315480)
-- Name: txnpassbook_tbl_10020_8000001_9000001 update_txnpassbook_10020_8000001_9000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_8000001_9000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_8000001_9000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6323 (class 2620 OID 33315481)
-- Name: txnpassbook_tbl_10020_9000001_10000001 update_txnpassbook_10020_9000001_10000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10020_9000001_10000001 BEFORE UPDATE ON log.txnpassbook_tbl_10020_9000001_10000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6344 (class 2620 OID 33315502)
-- Name: txnpassbook_tbl_10021_10000001_11000001 update_txnpassbook_10021_10000001_11000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_10000001_11000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_10000001_11000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6335 (class 2620 OID 33315493)
-- Name: txnpassbook_tbl_10021_1000001_2000001 update_txnpassbook_10021_1000001_2000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_1000001_2000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_1000001_2000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6345 (class 2620 OID 33315503)
-- Name: txnpassbook_tbl_10021_11000001_12000001 update_txnpassbook_10021_11000001_12000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_11000001_12000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_11000001_12000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6346 (class 2620 OID 33315504)
-- Name: txnpassbook_tbl_10021_12000001_13000001 update_txnpassbook_10021_12000001_13000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_12000001_13000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_12000001_13000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6347 (class 2620 OID 33315505)
-- Name: txnpassbook_tbl_10021_13000001_14000001 update_txnpassbook_10021_13000001_14000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_13000001_14000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_13000001_14000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6348 (class 2620 OID 33315506)
-- Name: txnpassbook_tbl_10021_14000001_15000001 update_txnpassbook_10021_14000001_15000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_14000001_15000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_14000001_15000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6349 (class 2620 OID 33315507)
-- Name: txnpassbook_tbl_10021_15000001_16000001 update_txnpassbook_10021_15000001_16000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_15000001_16000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_15000001_16000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6350 (class 2620 OID 33315508)
-- Name: txnpassbook_tbl_10021_16000001_17000001 update_txnpassbook_10021_16000001_17000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_16000001_17000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_16000001_17000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6351 (class 2620 OID 33315509)
-- Name: txnpassbook_tbl_10021_17000001_18000001 update_txnpassbook_10021_17000001_18000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_17000001_18000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_17000001_18000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6352 (class 2620 OID 33315510)
-- Name: txnpassbook_tbl_10021_18000001_19000001 update_txnpassbook_10021_18000001_19000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_18000001_19000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_18000001_19000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6353 (class 2620 OID 33315511)
-- Name: txnpassbook_tbl_10021_19000001_20000001 update_txnpassbook_10021_19000001_20000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_19000001_20000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_19000001_20000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6334 (class 2620 OID 33315492)
-- Name: txnpassbook_tbl_10021_1_1000001 update_txnpassbook_10021_1_1000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_1_1000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_1_1000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6336 (class 2620 OID 33315494)
-- Name: txnpassbook_tbl_10021_2000001_3000001 update_txnpassbook_10021_2000001_3000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_2000001_3000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_2000001_3000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6337 (class 2620 OID 33315495)
-- Name: txnpassbook_tbl_10021_3000001_4000001 update_txnpassbook_10021_3000001_4000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_3000001_4000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_3000001_4000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6338 (class 2620 OID 33315496)
-- Name: txnpassbook_tbl_10021_4000001_5000001 update_txnpassbook_10021_4000001_5000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_4000001_5000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_4000001_5000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6339 (class 2620 OID 33315497)
-- Name: txnpassbook_tbl_10021_5000001_6000001 update_txnpassbook_10021_5000001_6000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_5000001_6000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_5000001_6000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6340 (class 2620 OID 33315498)
-- Name: txnpassbook_tbl_10021_6000001_7000001 update_txnpassbook_10021_6000001_7000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_6000001_7000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_6000001_7000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6341 (class 2620 OID 33315499)
-- Name: txnpassbook_tbl_10021_7000001_8000001 update_txnpassbook_10021_7000001_8000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_7000001_8000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_7000001_8000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6342 (class 2620 OID 33315500)
-- Name: txnpassbook_tbl_10021_8000001_9000001 update_txnpassbook_10021_8000001_9000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_8000001_9000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_8000001_9000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6343 (class 2620 OID 33315501)
-- Name: txnpassbook_tbl_10021_9000001_10000001 update_txnpassbook_10021_9000001_10000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10021_9000001_10000001 BEFORE UPDATE ON log.txnpassbook_tbl_10021_9000001_10000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6374 (class 2620 OID 33315532)
-- Name: txnpassbook_tbl_10022 update_txnpassbook_10022; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10022 BEFORE UPDATE ON log.txnpassbook_tbl_10022 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6375 (class 2620 OID 33315533)
-- Name: txnpassbook_tbl_10060 update_txnpassbook_10060; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10060 BEFORE UPDATE ON log.txnpassbook_tbl_10060 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6376 (class 2620 OID 33315534)
-- Name: txnpassbook_tbl_10061 update_txnpassbook_10061; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10061 BEFORE UPDATE ON log.txnpassbook_tbl_10061 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6377 (class 2620 OID 33315535)
-- Name: txnpassbook_tbl_10062 update_txnpassbook_10062; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10062 BEFORE UPDATE ON log.txnpassbook_tbl_10062 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6378 (class 2620 OID 33315536)
-- Name: txnpassbook_tbl_10065 update_txnpassbook_10065; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10065 BEFORE UPDATE ON log.txnpassbook_tbl_10065 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6382 (class 2620 OID 33315540)
-- Name: txnpassbook_tbl_10066 update_txnpassbook_10066; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10066 BEFORE UPDATE ON log.txnpassbook_tbl_10066 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6379 (class 2620 OID 33315537)
-- Name: txnpassbook_tbl_10067 update_txnpassbook_10067; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10067 BEFORE UPDATE ON log.txnpassbook_tbl_10067 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6364 (class 2620 OID 33315522)
-- Name: txnpassbook_tbl_10069_10000001_11000001 update_txnpassbook_10069_10000001_11000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_10000001_11000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_10000001_11000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6355 (class 2620 OID 33315513)
-- Name: txnpassbook_tbl_10069_1000001_2000001 update_txnpassbook_10069_1000001_2000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_1000001_2000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_1000001_2000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6365 (class 2620 OID 33315523)
-- Name: txnpassbook_tbl_10069_11000001_12000001 update_txnpassbook_10069_11000001_12000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_11000001_12000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_11000001_12000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6366 (class 2620 OID 33315524)
-- Name: txnpassbook_tbl_10069_12000001_13000001 update_txnpassbook_10069_12000001_13000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_12000001_13000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_12000001_13000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6367 (class 2620 OID 33315525)
-- Name: txnpassbook_tbl_10069_13000001_14000001 update_txnpassbook_10069_13000001_14000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_13000001_14000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_13000001_14000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6368 (class 2620 OID 33315526)
-- Name: txnpassbook_tbl_10069_14000001_15000001 update_txnpassbook_10069_14000001_15000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_14000001_15000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_14000001_15000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6369 (class 2620 OID 33315527)
-- Name: txnpassbook_tbl_10069_15000001_16000001 update_txnpassbook_10069_15000001_16000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_15000001_16000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_15000001_16000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6370 (class 2620 OID 33315528)
-- Name: txnpassbook_tbl_10069_16000001_17000001 update_txnpassbook_10069_16000001_17000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_16000001_17000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_16000001_17000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6371 (class 2620 OID 33315529)
-- Name: txnpassbook_tbl_10069_17000001_18000001 update_txnpassbook_10069_17000001_18000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_17000001_18000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_17000001_18000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6372 (class 2620 OID 33315530)
-- Name: txnpassbook_tbl_10069_18000001_19000001 update_txnpassbook_10069_18000001_19000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_18000001_19000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_18000001_19000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6373 (class 2620 OID 33315531)
-- Name: txnpassbook_tbl_10069_19000001_20000001 update_txnpassbook_10069_19000001_20000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_19000001_20000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_19000001_20000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6354 (class 2620 OID 33315512)
-- Name: txnpassbook_tbl_10069_1_1000001 update_txnpassbook_10069_1_1000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_1_1000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_1_1000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6356 (class 2620 OID 33315514)
-- Name: txnpassbook_tbl_10069_2000001_3000001 update_txnpassbook_10069_2000001_3000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_2000001_3000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_2000001_3000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6357 (class 2620 OID 33315515)
-- Name: txnpassbook_tbl_10069_3000001_4000001 update_txnpassbook_10069_3000001_4000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_3000001_4000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_3000001_4000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6358 (class 2620 OID 33315516)
-- Name: txnpassbook_tbl_10069_4000001_5000001 update_txnpassbook_10069_4000001_5000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_4000001_5000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_4000001_5000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6359 (class 2620 OID 33315517)
-- Name: txnpassbook_tbl_10069_5000001_6000001 update_txnpassbook_10069_5000001_6000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_5000001_6000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_5000001_6000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6360 (class 2620 OID 33315518)
-- Name: txnpassbook_tbl_10069_6000001_7000001 update_txnpassbook_10069_6000001_7000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_6000001_7000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_6000001_7000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6361 (class 2620 OID 33315519)
-- Name: txnpassbook_tbl_10069_7000001_8000001 update_txnpassbook_10069_7000001_8000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_7000001_8000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_7000001_8000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6362 (class 2620 OID 33315520)
-- Name: txnpassbook_tbl_10069_8000001_9000001 update_txnpassbook_10069_8000001_9000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_8000001_9000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_8000001_9000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6363 (class 2620 OID 33315521)
-- Name: txnpassbook_tbl_10069_9000001_10000001 update_txnpassbook_10069_9000001_10000001; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10069_9000001_10000001 BEFORE UPDATE ON log.txnpassbook_tbl_10069_9000001_10000001 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6383 (class 2620 OID 33315541)
-- Name: txnpassbook_tbl_10070 update_txnpassbook_10070; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10070 BEFORE UPDATE ON log.txnpassbook_tbl_10070 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6384 (class 2620 OID 33315542)
-- Name: txnpassbook_tbl_10071 update_txnpassbook_10071; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10071 BEFORE UPDATE ON log.txnpassbook_tbl_10071 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6385 (class 2620 OID 38043512)
-- Name: txnpassbook_tbl_10072 update_txnpassbook_10072; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10072 BEFORE UPDATE ON log.txnpassbook_tbl_10072 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6380 (class 2620 OID 33315538)
-- Name: txnpassbook_tbl_10073 update_txnpassbook_10073; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10073 BEFORE UPDATE ON log.txnpassbook_tbl_10073 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6386 (class 2620 OID 33315543)
-- Name: txnpassbook_tbl_10074 update_txnpassbook_10074; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10074 BEFORE UPDATE ON log.txnpassbook_tbl_10074 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6387 (class 2620 OID 33315544)
-- Name: txnpassbook_tbl_10075 update_txnpassbook_10075; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10075 BEFORE UPDATE ON log.txnpassbook_tbl_10075 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6388 (class 2620 OID 33315545)
-- Name: txnpassbook_tbl_10076 update_txnpassbook_10076; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10076 BEFORE UPDATE ON log.txnpassbook_tbl_10076 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6389 (class 2620 OID 33315546)
-- Name: txnpassbook_tbl_10077 update_txnpassbook_10077; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10077 BEFORE UPDATE ON log.txnpassbook_tbl_10077 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6390 (class 2620 OID 33315547)
-- Name: txnpassbook_tbl_10078 update_txnpassbook_10078; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10078 BEFORE UPDATE ON log.txnpassbook_tbl_10078 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6391 (class 2620 OID 33315548)
-- Name: txnpassbook_tbl_10079 update_txnpassbook_10079; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10079 BEFORE UPDATE ON log.txnpassbook_tbl_10079 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6392 (class 2620 OID 33315549)
-- Name: txnpassbook_tbl_10080 update_txnpassbook_10080; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10080 BEFORE UPDATE ON log.txnpassbook_tbl_10080 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6396 (class 2620 OID 33808094)
-- Name: txnpassbook_tbl_10081 update_txnpassbook_10081; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10081 BEFORE UPDATE ON log.txnpassbook_tbl_10081 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6393 (class 2620 OID 33315550)
-- Name: txnpassbook_tbl_10089 update_txnpassbook_10089; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10089 BEFORE UPDATE ON log.txnpassbook_tbl_10089 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6394 (class 2620 OID 33315551)
-- Name: txnpassbook_tbl_10098 update_txnpassbook_10098; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10098 BEFORE UPDATE ON log.txnpassbook_tbl_10098 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6381 (class 2620 OID 33315539)
-- Name: txnpassbook_tbl_10099 update_txnpassbook_10099; Type: TRIGGER; Schema: log; Owner: mpoint
--

CREATE TRIGGER update_txnpassbook_10099 BEFORE UPDATE ON log.txnpassbook_tbl_10099 FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6275 (class 2620 OID 17289)
-- Name: card_tbl update_card; Type: TRIGGER; Schema: system; Owner: mpoint
--

CREATE TRIGGER update_card BEFORE UPDATE ON system.card_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6277 (class 2620 OID 17290)
-- Name: cardprefix_tbl update_cardprefix; Type: TRIGGER; Schema: system; Owner: mpoint
--

CREATE TRIGGER update_cardprefix BEFORE UPDATE ON system.cardprefix_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6278 (class 2620 OID 17291)
-- Name: cardpricing_tbl update_cardpricing; Type: TRIGGER; Schema: system; Owner: mpoint
--

CREATE TRIGGER update_cardpricing BEFORE UPDATE ON system.cardpricing_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6280 (class 2620 OID 17292)
-- Name: country_tbl update_country; Type: TRIGGER; Schema: system; Owner: mpoint
--

CREATE TRIGGER update_country BEFORE UPDATE ON system.country_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6281 (class 2620 OID 17293)
-- Name: feetype_tbl update_feetype; Type: TRIGGER; Schema: system; Owner: mpoint
--

CREATE TRIGGER update_feetype BEFORE UPDATE ON system.feetype_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6282 (class 2620 OID 17294)
-- Name: flow_tbl update_flow; Type: TRIGGER; Schema: system; Owner: mpoint
--

CREATE TRIGGER update_flow BEFORE UPDATE ON system.flow_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6283 (class 2620 OID 17295)
-- Name: iinaction_tbl update_iinaction; Type: TRIGGER; Schema: system; Owner: mpoint
--

CREATE TRIGGER update_iinaction BEFORE UPDATE ON system.iinaction_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6276 (class 2620 OID 17297)
-- Name: cardchargetype_tbl update_info; Type: TRIGGER; Schema: system; Owner: mpoint
--

CREATE TRIGGER update_info BEFORE UPDATE ON system.cardchargetype_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6279 (class 2620 OID 17296)
-- Name: cardstate_tbl update_info; Type: TRIGGER; Schema: system; Owner: mpoint
--

CREATE TRIGGER update_info BEFORE UPDATE ON system.cardstate_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6284 (class 2620 OID 17298)
-- Name: pricepoint_tbl update_pricepoint; Type: TRIGGER; Schema: system; Owner: mpoint
--

CREATE TRIGGER update_pricepoint BEFORE UPDATE ON system.pricepoint_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6285 (class 2620 OID 17299)
-- Name: psp_tbl update_psp; Type: TRIGGER; Schema: system; Owner: mpoint
--

CREATE TRIGGER update_psp BEFORE UPDATE ON system.psp_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6286 (class 2620 OID 17300)
-- Name: pspcard_tbl update_pspcard; Type: TRIGGER; Schema: system; Owner: mpoint
--

CREATE TRIGGER update_pspcard BEFORE UPDATE ON system.pspcard_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6287 (class 2620 OID 17301)
-- Name: pspcurrency_tbl update_pspcurrency; Type: TRIGGER; Schema: system; Owner: mpoint
--

CREATE TRIGGER update_pspcurrency BEFORE UPDATE ON system.pspcurrency_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6288 (class 2620 OID 17302)
-- Name: shipping_tbl update_shipping; Type: TRIGGER; Schema: system; Owner: mpoint
--

CREATE TRIGGER update_shipping BEFORE UPDATE ON system.shipping_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6289 (class 2620 OID 17303)
-- Name: type_tbl update_type; Type: TRIGGER; Schema: system; Owner: mpoint
--

CREATE TRIGGER update_type BEFORE UPDATE ON system.type_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6290 (class 2620 OID 17304)
-- Name: urltype_tbl update_urltype; Type: TRIGGER; Schema: system; Owner: mpoint
--

CREATE TRIGGER update_urltype BEFORE UPDATE ON system.urltype_tbl FOR EACH ROW EXECUTE PROCEDURE public.update_table_proc();


--
-- TOC entry 6125 (class 2606 OID 17305)
-- Name: access_tbl access2client_fk; Type: FK CONSTRAINT; Schema: admin; Owner: mpoint
--

ALTER TABLE ONLY admin.access_tbl
    ADD CONSTRAINT access2client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6126 (class 2606 OID 17310)
-- Name: roleaccess_tbl roleaccess2role_fk; Type: FK CONSTRAINT; Schema: admin; Owner: mpoint
--

ALTER TABLE ONLY admin.roleaccess_tbl
    ADD CONSTRAINT roleaccess2role_fk FOREIGN KEY (roleid) REFERENCES admin.role_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6128 (class 2606 OID 17315)
-- Name: account_tbl account2client_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.account_tbl
    ADD CONSTRAINT account2client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6228 (class 2606 OID 17320)
-- Name: gatewaytrigger_tbl atriggerunit_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.gatewaytrigger_tbl
    ADD CONSTRAINT atriggerunit_fk FOREIGN KEY (aggregationtriggerunit) REFERENCES system.triggerunit_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6127 (class 2606 OID 22052469)
-- Name: account_tbl businesstype_pk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.account_tbl
    ADD CONSTRAINT businesstype_pk FOREIGN KEY (businesstype) REFERENCES system.businesstype_tbl(id);


--
-- TOC entry 6129 (class 2606 OID 33316437)
-- Name: cardaccess_tbl cardaccess2capturetype_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.cardaccess_tbl
    ADD CONSTRAINT cardaccess2capturetype_fk FOREIGN KEY (capture_type) REFERENCES system.capturetype_tbl(id);


--
-- TOC entry 6134 (class 2606 OID 17325)
-- Name: cardaccess_tbl cardaccess2card_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.cardaccess_tbl
    ADD CONSTRAINT cardaccess2card_fk FOREIGN KEY (cardid) REFERENCES system.card_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6133 (class 2606 OID 17330)
-- Name: cardaccess_tbl cardaccess2cardstate_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.cardaccess_tbl
    ADD CONSTRAINT cardaccess2cardstate_fk FOREIGN KEY (stateid) REFERENCES system.cardstate_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6132 (class 2606 OID 17335)
-- Name: cardaccess_tbl cardaccess2client_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.cardaccess_tbl
    ADD CONSTRAINT cardaccess2client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6131 (class 2606 OID 17340)
-- Name: cardaccess_tbl cardaccess2psp_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.cardaccess_tbl
    ADD CONSTRAINT cardaccess2psp_fk FOREIGN KEY (pspid) REFERENCES system.psp_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6130 (class 2606 OID 17345)
-- Name: cardaccess_tbl cardaccess_tbl_processortype_tbl_id_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.cardaccess_tbl
    ADD CONSTRAINT cardaccess_tbl_processortype_tbl_id_fk FOREIGN KEY (psp_type) REFERENCES system.processortype_tbl(id);


--
-- TOC entry 6136 (class 2606 OID 17350)
-- Name: client_tbl client2country_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.client_tbl
    ADD CONSTRAINT client2country_fk FOREIGN KEY (countryid) REFERENCES system.country_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6135 (class 2606 OID 17355)
-- Name: client_tbl client2flow_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.client_tbl
    ADD CONSTRAINT client2flow_fk FOREIGN KEY (flowid) REFERENCES system.flow_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6217 (class 2606 OID 17360)
-- Name: countrycurrency_tbl client_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.countrycurrency_tbl
    ADD CONSTRAINT client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6225 (class 2606 OID 17365)
-- Name: producttype_tbl client_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.producttype_tbl
    ADD CONSTRAINT client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id);


--
-- TOC entry 6237 (class 2606 OID 17370)
-- Name: retrial_tbl client_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.retrial_tbl
    ADD CONSTRAINT client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id);


--
-- TOC entry 6231 (class 2606 OID 17375)
-- Name: gatewaystat_tbl clientstat_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.gatewaystat_tbl
    ADD CONSTRAINT clientstat_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id);


--
-- TOC entry 6216 (class 2606 OID 17380)
-- Name: countrycurrency_tbl country_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.countrycurrency_tbl
    ADD CONSTRAINT country_fk FOREIGN KEY (countryid) REFERENCES system.country_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6215 (class 2606 OID 17385)
-- Name: countrycurrency_tbl currency_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.countrycurrency_tbl
    ADD CONSTRAINT currency_fk FOREIGN KEY (currencyid) REFERENCES system.currency_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6227 (class 2606 OID 17390)
-- Name: gatewaytrigger_tbl gateway_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.gatewaytrigger_tbl
    ADD CONSTRAINT gateway_fk FOREIGN KEY (gatewayid) REFERENCES system.psp_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6230 (class 2606 OID 17395)
-- Name: gatewaystat_tbl gatewaystat_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.gatewaystat_tbl
    ADD CONSTRAINT gatewaystat_fk FOREIGN KEY (gatewayid) REFERENCES system.psp_tbl(id);


--
-- TOC entry 6214 (class 2606 OID 17400)
-- Name: gomobileconfiguration_tbl gomobileconfiguration2client_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.gomobileconfiguration_tbl
    ADD CONSTRAINT gomobileconfiguration2client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6138 (class 2606 OID 17405)
-- Name: iinlist_tbl iinlist2client_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.iinlist_tbl
    ADD CONSTRAINT iinlist2client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6137 (class 2606 OID 17410)
-- Name: iinlist_tbl iinlist2iinaction_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.iinlist_tbl
    ADD CONSTRAINT iinlist2iinaction_fk FOREIGN KEY (iinactionid) REFERENCES system.iinaction_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6141 (class 2606 OID 17415)
-- Name: info_tbl info2client_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.info_tbl
    ADD CONSTRAINT info2client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6140 (class 2606 OID 17420)
-- Name: info_tbl info2infotype_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.info_tbl
    ADD CONSTRAINT info2infotype_fk FOREIGN KEY (infotypeid) REFERENCES client.infotype_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6139 (class 2606 OID 17425)
-- Name: info_tbl info2psp_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.info_tbl
    ADD CONSTRAINT info2psp_fk FOREIGN KEY (pspid) REFERENCES system.psp_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6142 (class 2606 OID 17430)
-- Name: ipaddress_tbl ipaccess2client_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.ipaddress_tbl
    ADD CONSTRAINT ipaccess2client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6143 (class 2606 OID 17435)
-- Name: keyword_tbl keyword2client_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.keyword_tbl
    ADD CONSTRAINT keyword2client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6145 (class 2606 OID 17440)
-- Name: merchantaccount_tbl merchantaccount2client_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.merchantaccount_tbl
    ADD CONSTRAINT merchantaccount2client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6144 (class 2606 OID 17445)
-- Name: merchantaccount_tbl merchantaccount2psp_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.merchantaccount_tbl
    ADD CONSTRAINT merchantaccount2psp_fk FOREIGN KEY (pspid) REFERENCES system.psp_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6147 (class 2606 OID 17450)
-- Name: merchantsubaccount_tbl merchantsubaccount2account_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.merchantsubaccount_tbl
    ADD CONSTRAINT merchantsubaccount2account_fk FOREIGN KEY (accountid) REFERENCES client.account_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6146 (class 2606 OID 17455)
-- Name: merchantsubaccount_tbl merchantsubaccount2psp_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.merchantsubaccount_tbl
    ADD CONSTRAINT merchantsubaccount2psp_fk FOREIGN KEY (pspid) REFERENCES system.psp_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6148 (class 2606 OID 17460)
-- Name: product_tbl product2keyword_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.product_tbl
    ADD CONSTRAINT product2keyword_fk FOREIGN KEY (keywordid) REFERENCES client.keyword_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6224 (class 2606 OID 17465)
-- Name: producttype_tbl product_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.producttype_tbl
    ADD CONSTRAINT product_fk FOREIGN KEY (productid) REFERENCES system.producttype_tbl(id);


--
-- TOC entry 6236 (class 2606 OID 17470)
-- Name: retrial_tbl retrialtype_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.retrial_tbl
    ADD CONSTRAINT retrialtype_fk FOREIGN KEY (typeid) REFERENCES system.retrialtype_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6150 (class 2606 OID 17475)
-- Name: shipping_tbl shipping2shipping_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.shipping_tbl
    ADD CONSTRAINT shipping2shipping_fk FOREIGN KEY (shippingid) REFERENCES system.shipping_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6149 (class 2606 OID 17480)
-- Name: shipping_tbl shipping2shop_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.shipping_tbl
    ADD CONSTRAINT shipping2shop_fk FOREIGN KEY (shopid) REFERENCES client.shop_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6152 (class 2606 OID 17485)
-- Name: shop_tbl shop2client_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.shop_tbl
    ADD CONSTRAINT shop2client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6151 (class 2606 OID 17490)
-- Name: shop_tbl shop2keyword_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.shop_tbl
    ADD CONSTRAINT shop2keyword_fk FOREIGN KEY (keywordid) REFERENCES client.keyword_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6229 (class 2606 OID 17495)
-- Name: gatewaystat_tbl stattype_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.gatewaystat_tbl
    ADD CONSTRAINT stattype_fk FOREIGN KEY (statetypeid) REFERENCES system.statisticstype_tbl(id);


--
-- TOC entry 6153 (class 2606 OID 17500)
-- Name: surepay_tbl surepay2client_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.surepay_tbl
    ADD CONSTRAINT surepay2client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6226 (class 2606 OID 17505)
-- Name: gatewaytrigger_tbl triggeclient_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.gatewaytrigger_tbl
    ADD CONSTRAINT triggeclient_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6155 (class 2606 OID 17510)
-- Name: url_tbl url2client_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.url_tbl
    ADD CONSTRAINT url2client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6154 (class 2606 OID 17515)
-- Name: url_tbl url2urltype_fk; Type: FK CONSTRAINT; Schema: client; Owner: mpoint
--

ALTER TABLE ONLY client.url_tbl
    ADD CONSTRAINT url2urltype_fk FOREIGN KEY (urltypeid) REFERENCES system.urltype_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6167 (class 2606 OID 17520)
-- Name: claccess_tbl access2account_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.claccess_tbl
    ADD CONSTRAINT access2account_fk FOREIGN KEY (accountid) REFERENCES enduser.account_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6166 (class 2606 OID 17525)
-- Name: claccess_tbl access2client_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.claccess_tbl
    ADD CONSTRAINT access2client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6156 (class 2606 OID 17530)
-- Name: account_tbl account2country_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.account_tbl
    ADD CONSTRAINT account2country_fk FOREIGN KEY (countryid) REFERENCES system.country_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6157 (class 2606 OID 17535)
-- Name: activation_tbl activation2account_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.activation_tbl
    ADD CONSTRAINT activation2account_fk FOREIGN KEY (accountid) REFERENCES enduser.account_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6160 (class 2606 OID 17540)
-- Name: address_tbl address2account_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.address_tbl
    ADD CONSTRAINT address2account_fk FOREIGN KEY (accountid) REFERENCES enduser.account_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6159 (class 2606 OID 17545)
-- Name: address_tbl address2card_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.address_tbl
    ADD CONSTRAINT address2card_fk FOREIGN KEY (cardid) REFERENCES enduser.card_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6158 (class 2606 OID 17550)
-- Name: address_tbl address2country_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.address_tbl
    ADD CONSTRAINT address2country_fk FOREIGN KEY (countryid) REFERENCES system.country_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6165 (class 2606 OID 17555)
-- Name: card_tbl card2account_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.card_tbl
    ADD CONSTRAINT card2account_fk FOREIGN KEY (accountid) REFERENCES enduser.account_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6164 (class 2606 OID 17560)
-- Name: card_tbl card2card_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.card_tbl
    ADD CONSTRAINT card2card_fk FOREIGN KEY (cardid) REFERENCES system.card_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6163 (class 2606 OID 17565)
-- Name: card_tbl card2cardcharge_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.card_tbl
    ADD CONSTRAINT card2cardcharge_fk FOREIGN KEY (chargetypeid) REFERENCES system.cardchargetype_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6162 (class 2606 OID 17570)
-- Name: card_tbl card2client_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.card_tbl
    ADD CONSTRAINT card2client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6161 (class 2606 OID 17575)
-- Name: card_tbl card2psp_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.card_tbl
    ADD CONSTRAINT card2psp_fk FOREIGN KEY (pspid) REFERENCES system.psp_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6173 (class 2606 OID 17580)
-- Name: transaction_tbl transaction2state_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.transaction_tbl
    ADD CONSTRAINT transaction2state_fk FOREIGN KEY (stateid) REFERENCES log.state_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6172 (class 2606 OID 17585)
-- Name: transaction_tbl txn2txn_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.transaction_tbl
    ADD CONSTRAINT txn2txn_fk FOREIGN KEY (txnid) REFERENCES log.transaction_tbl(id) ON UPDATE CASCADE;


--
-- TOC entry 6171 (class 2606 OID 17590)
-- Name: transaction_tbl txn2type_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.transaction_tbl
    ADD CONSTRAINT txn2type_fk FOREIGN KEY (typeid) REFERENCES system.type_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6170 (class 2606 OID 17595)
-- Name: transaction_tbl txnfrom2account_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.transaction_tbl
    ADD CONSTRAINT txnfrom2account_fk FOREIGN KEY (fromid) REFERENCES enduser.account_tbl(id) ON UPDATE CASCADE;


--
-- TOC entry 6169 (class 2606 OID 17600)
-- Name: transaction_tbl txnowner2account_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.transaction_tbl
    ADD CONSTRAINT txnowner2account_fk FOREIGN KEY (accountid) REFERENCES enduser.account_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6168 (class 2606 OID 17605)
-- Name: transaction_tbl txnto2account_fk; Type: FK CONSTRAINT; Schema: enduser; Owner: mpoint
--

ALTER TABLE ONLY enduser.transaction_tbl
    ADD CONSTRAINT txnto2account_fk FOREIGN KEY (toid) REFERENCES enduser.account_tbl(id) ON UPDATE CASCADE;


--
-- TOC entry 6175 (class 2606 OID 38040374)
-- Name: flight_tbl arrival_countryid_country_tbl_id_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.flight_tbl
    ADD CONSTRAINT arrival_countryid_country_tbl_id_fk FOREIGN KEY (arrival_countryid) REFERENCES system.country_tbl(id);


--
-- TOC entry 6174 (class 2606 OID 17610)
-- Name: auditlog_tbl auditlog2operation_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.auditlog_tbl
    ADD CONSTRAINT auditlog2operation_fk FOREIGN KEY (operationid) REFERENCES log.operation_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6184 (class 2606 OID 36070710)
-- Name: transaction_tbl convertedcurrency_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.transaction_tbl
    ADD CONSTRAINT convertedcurrency_fk FOREIGN KEY (convetredcurrencyid) REFERENCES system.currency_tbl(id);


--
-- TOC entry 6176 (class 2606 OID 38040369)
-- Name: flight_tbl departure_countryid_country_tbl_id_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.flight_tbl
    ADD CONSTRAINT departure_countryid_country_tbl_id_fk FOREIGN KEY (departure_countryid) REFERENCES system.country_tbl(id);


--
-- TOC entry 6239 (class 2606 OID 3504110)
-- Name: externalreference_tbl externalref2psp_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.externalreference_tbl
    ADD CONSTRAINT externalref2psp_fk FOREIGN KEY (pspid) REFERENCES system.psp_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6240 (class 2606 OID 3504105)
-- Name: externalreference_tbl externalref2txn_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.externalreference_tbl
    ADD CONSTRAINT externalref2txn_fk FOREIGN KEY (txnid) REFERENCES log.transaction_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6238 (class 2606 OID 36070705)
-- Name: externalreference_tbl externalreferencetype_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.externalreference_tbl
    ADD CONSTRAINT externalreferencetype_fk FOREIGN KEY (type) REFERENCES system.externalreferencetype_tbl(id);


--
-- TOC entry 6179 (class 2606 OID 17615)
-- Name: message_tbl msg2state_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.message_tbl
    ADD CONSTRAINT msg2state_fk FOREIGN KEY (stateid) REFERENCES log.state_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6178 (class 2606 OID 17620)
-- Name: message_tbl msg2txn_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.message_tbl
    ADD CONSTRAINT msg2txn_fk FOREIGN KEY (txnid) REFERENCES log.transaction_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6180 (class 2606 OID 17625)
-- Name: note_tbl note2transaction_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.note_tbl
    ADD CONSTRAINT note2transaction_fk FOREIGN KEY (txnid) REFERENCES enduser.transaction_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6182 (class 2606 OID 17630)
-- Name: order_tbl order2country_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.order_tbl
    ADD CONSTRAINT order2country_fk FOREIGN KEY (countryid) REFERENCES system.country_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6181 (class 2606 OID 17635)
-- Name: order_tbl order2txn_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.order_tbl
    ADD CONSTRAINT order2txn_fk FOREIGN KEY (txnid) REFERENCES log.transaction_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6177 (class 2606 OID 17640)
-- Name: flight_tbl order_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.flight_tbl
    ADD CONSTRAINT order_fk FOREIGN KEY (order_id) REFERENCES log.order_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6183 (class 2606 OID 17645)
-- Name: passenger_tbl order_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.passenger_tbl
    ADD CONSTRAINT order_fk FOREIGN KEY (order_id) REFERENCES log.order_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6245 (class 2606 OID 36070195)
-- Name: billing_summary_tbl order_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.billing_summary_tbl
    ADD CONSTRAINT order_fk FOREIGN KEY (order_id) REFERENCES log.order_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6246 (class 2606 OID 38040390)
-- Name: paymentroute_tbl pspid; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.paymentroute_tbl
    ADD CONSTRAINT pspid FOREIGN KEY (pspid) REFERENCES system.psp_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6223 (class 2606 OID 17650)
-- Name: session_tbl session_tbl_account_tbl_id_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.session_tbl
    ADD CONSTRAINT session_tbl_account_tbl_id_fk FOREIGN KEY (accountid) REFERENCES client.account_tbl(id);


--
-- TOC entry 6222 (class 2606 OID 17655)
-- Name: session_tbl session_tbl_client_tbl_id_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.session_tbl
    ADD CONSTRAINT session_tbl_client_tbl_id_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id);


--
-- TOC entry 6221 (class 2606 OID 17660)
-- Name: session_tbl session_tbl_country_tbl_id_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.session_tbl
    ADD CONSTRAINT session_tbl_country_tbl_id_fk FOREIGN KEY (countryid) REFERENCES system.country_tbl(id);


--
-- TOC entry 6220 (class 2606 OID 17665)
-- Name: session_tbl session_tbl_currency_tbl_id_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.session_tbl
    ADD CONSTRAINT session_tbl_currency_tbl_id_fk FOREIGN KEY (currencyid) REFERENCES system.currency_tbl(id);


--
-- TOC entry 6219 (class 2606 OID 17670)
-- Name: session_tbl session_tbl_sessiontype_tbl_id_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.session_tbl
    ADD CONSTRAINT session_tbl_sessiontype_tbl_id_fk FOREIGN KEY (sessiontypeid) REFERENCES system.sessiontype_tbl(id);


--
-- TOC entry 6218 (class 2606 OID 17675)
-- Name: session_tbl session_tbl_state_tbl_id_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.session_tbl
    ADD CONSTRAINT session_tbl_state_tbl_id_fk FOREIGN KEY (stateid) REFERENCES log.state_tbl(id);


--
-- TOC entry 6235 (class 2606 OID 17680)
-- Name: settlement_record_tbl settlement_record_tbl_settlement_tbl_id_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.settlement_record_tbl
    ADD CONSTRAINT settlement_record_tbl_settlement_tbl_id_fk FOREIGN KEY (settlementid) REFERENCES log.settlement_tbl(id);


--
-- TOC entry 6234 (class 2606 OID 17685)
-- Name: settlement_record_tbl settlement_record_tbl_transaction_tbl_id_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.settlement_record_tbl
    ADD CONSTRAINT settlement_record_tbl_transaction_tbl_id_fk FOREIGN KEY (transactionid) REFERENCES log.transaction_tbl(id);


--
-- TOC entry 6233 (class 2606 OID 17690)
-- Name: settlement_tbl settlement_tbl_client_tbl_id_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.settlement_tbl
    ADD CONSTRAINT settlement_tbl_client_tbl_id_fk FOREIGN KEY (client_id) REFERENCES client.client_tbl(id);


--
-- TOC entry 6232 (class 2606 OID 17695)
-- Name: settlement_tbl settlement_tbl_psp_tbl_id_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.settlement_tbl
    ADD CONSTRAINT settlement_tbl_psp_tbl_id_fk FOREIGN KEY (psp_id) REFERENCES system.psp_tbl(id);


--
-- TOC entry 6195 (class 2606 OID 17700)
-- Name: transaction_tbl transaction_tbl_producttype_tbl_id_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.transaction_tbl
    ADD CONSTRAINT transaction_tbl_producttype_tbl_id_fk FOREIGN KEY (producttype) REFERENCES system.producttype_tbl(id);


--
-- TOC entry 6194 (class 2606 OID 17705)
-- Name: transaction_tbl transaction_tbl_session_tbl_id_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.transaction_tbl
    ADD CONSTRAINT transaction_tbl_session_tbl_id_fk FOREIGN KEY (sessionid) REFERENCES log.session_tbl(id);


--
-- TOC entry 6193 (class 2606 OID 17710)
-- Name: transaction_tbl txn2account_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.transaction_tbl
    ADD CONSTRAINT txn2account_fk FOREIGN KEY (accountid) REFERENCES client.account_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6192 (class 2606 OID 17715)
-- Name: transaction_tbl txn2card_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.transaction_tbl
    ADD CONSTRAINT txn2card_fk FOREIGN KEY (cardid) REFERENCES system.card_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6191 (class 2606 OID 17720)
-- Name: transaction_tbl txn2client_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.transaction_tbl
    ADD CONSTRAINT txn2client_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6190 (class 2606 OID 17725)
-- Name: transaction_tbl txn2country_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.transaction_tbl
    ADD CONSTRAINT txn2country_fk FOREIGN KEY (countryid) REFERENCES system.country_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6189 (class 2606 OID 17730)
-- Name: transaction_tbl txn2currency_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.transaction_tbl
    ADD CONSTRAINT txn2currency_fk FOREIGN KEY (currencyid) REFERENCES system.currency_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6188 (class 2606 OID 17735)
-- Name: transaction_tbl txn2eua_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.transaction_tbl
    ADD CONSTRAINT txn2eua_fk FOREIGN KEY (euaid) REFERENCES enduser.account_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6187 (class 2606 OID 17740)
-- Name: transaction_tbl txn2keyword_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.transaction_tbl
    ADD CONSTRAINT txn2keyword_fk FOREIGN KEY (keywordid) REFERENCES client.keyword_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6186 (class 2606 OID 17745)
-- Name: transaction_tbl txn2psp_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.transaction_tbl
    ADD CONSTRAINT txn2psp_fk FOREIGN KEY (pspid) REFERENCES system.psp_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6185 (class 2606 OID 17750)
-- Name: transaction_tbl txn2type_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.transaction_tbl
    ADD CONSTRAINT txn2type_fk FOREIGN KEY (typeid) REFERENCES system.type_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6243 (class 2606 OID 8365118)
-- Name: txnpassbook_tbl_backup_20200401 txnpassbook_currency_tbl_id_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_backup_20200401
    ADD CONSTRAINT txnpassbook_currency_tbl_id_fk FOREIGN KEY (currencyid) REFERENCES system.currency_tbl(id);


--
-- TOC entry 6241 (class 2606 OID 8365128)
-- Name: txnpassbook_tbl_backup_20200401 txnpassbook_tbl_state_tbl_id_1_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_backup_20200401
    ADD CONSTRAINT txnpassbook_tbl_state_tbl_id_1_fk FOREIGN KEY (performedopt) REFERENCES log.state_tbl(id);


--
-- TOC entry 6242 (class 2606 OID 8365123)
-- Name: txnpassbook_tbl_backup_20200401 txnpassbook_tbl_state_tbl_id_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_backup_20200401
    ADD CONSTRAINT txnpassbook_tbl_state_tbl_id_fk FOREIGN KEY (requestedopt) REFERENCES log.state_tbl(id);


--
-- TOC entry 6244 (class 2606 OID 8365113)
-- Name: txnpassbook_tbl_backup_20200401 txnpassbook_transaction_tbl_id_fk; Type: FK CONSTRAINT; Schema: log; Owner: mpoint
--

ALTER TABLE ONLY log.txnpassbook_tbl_backup_20200401
    ADD CONSTRAINT txnpassbook_transaction_tbl_id_fk FOREIGN KEY (transactionid) REFERENCES log.transaction_tbl(id);


--
-- TOC entry 6196 (class 2606 OID 17755)
-- Name: card_tbl card_tbl_paymenttype_tbl_id_fk; Type: FK CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.card_tbl
    ADD CONSTRAINT card_tbl_paymenttype_tbl_id_fk FOREIGN KEY (paymenttype) REFERENCES system.paymenttype_tbl(id);


--
-- TOC entry 6197 (class 2606 OID 17760)
-- Name: cardprefix_tbl cardprefix2card_fk; Type: FK CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.cardprefix_tbl
    ADD CONSTRAINT cardprefix2card_fk FOREIGN KEY (cardid) REFERENCES system.card_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6199 (class 2606 OID 17765)
-- Name: cardpricing_tbl cardpricing2card_fk; Type: FK CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.cardpricing_tbl
    ADD CONSTRAINT cardpricing2card_fk FOREIGN KEY (cardid) REFERENCES system.card_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6198 (class 2606 OID 17770)
-- Name: cardpricing_tbl cardpricing2pricepoint_fk; Type: FK CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.cardpricing_tbl
    ADD CONSTRAINT cardpricing2pricepoint_fk FOREIGN KEY (pricepointid) REFERENCES system.pricepoint_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6200 (class 2606 OID 17775)
-- Name: country_tbl country2currency_fk; Type: FK CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.country_tbl
    ADD CONSTRAINT country2currency_fk FOREIGN KEY (currencyid) REFERENCES system.currency_tbl(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 6201 (class 2606 OID 17780)
-- Name: depositoption_tbl depositoption2country_fk; Type: FK CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.depositoption_tbl
    ADD CONSTRAINT depositoption2country_fk FOREIGN KEY (countryid) REFERENCES system.country_tbl(id) ON UPDATE CASCADE;


--
-- TOC entry 6204 (class 2606 OID 17785)
-- Name: fee_tbl fee2fromcountry_fk; Type: FK CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.fee_tbl
    ADD CONSTRAINT fee2fromcountry_fk FOREIGN KEY (fromid) REFERENCES system.country_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6203 (class 2606 OID 17790)
-- Name: fee_tbl fee2tocountry_fk; Type: FK CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.fee_tbl
    ADD CONSTRAINT fee2tocountry_fk FOREIGN KEY (toid) REFERENCES system.country_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6202 (class 2606 OID 17795)
-- Name: fee_tbl fee2type_fk; Type: FK CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.fee_tbl
    ADD CONSTRAINT fee2type_fk FOREIGN KEY (typeid) REFERENCES system.feetype_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6205 (class 2606 OID 17800)
-- Name: iprange_tbl iprange2country_fk; Type: FK CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.iprange_tbl
    ADD CONSTRAINT iprange2country_fk FOREIGN KEY (countryid) REFERENCES system.country_tbl(id) ON UPDATE CASCADE;


--
-- TOC entry 6206 (class 2606 OID 17805)
-- Name: postalcode_tbl postalcode2state_fk; Type: FK CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.postalcode_tbl
    ADD CONSTRAINT postalcode2state_fk FOREIGN KEY (stateid) REFERENCES system.state_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6207 (class 2606 OID 17810)
-- Name: pricepoint_tbl price2currency_fk; Type: FK CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.pricepoint_tbl
    ADD CONSTRAINT price2currency_fk FOREIGN KEY (currencyid) REFERENCES system.currency_tbl(id);


--
-- TOC entry 6212 (class 2606 OID 17815)
-- Name: pspcurrency_tbl psp2currency_fk; Type: FK CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.pspcurrency_tbl
    ADD CONSTRAINT psp2currency_fk FOREIGN KEY (currencyid) REFERENCES system.currency_tbl(id);


--
-- TOC entry 6210 (class 2606 OID 17820)
-- Name: pspcard_tbl pspcard2country_fk; Type: FK CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.pspcard_tbl
    ADD CONSTRAINT pspcard2country_fk FOREIGN KEY (cardid) REFERENCES system.card_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6209 (class 2606 OID 17825)
-- Name: pspcard_tbl pspcard2psp_fk; Type: FK CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.pspcard_tbl
    ADD CONSTRAINT pspcard2psp_fk FOREIGN KEY (pspid) REFERENCES system.psp_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6211 (class 2606 OID 17830)
-- Name: pspcurrency_tbl pspcurrency2psp_fk; Type: FK CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.pspcurrency_tbl
    ADD CONSTRAINT pspcurrency2psp_fk FOREIGN KEY (pspid) REFERENCES system.psp_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6208 (class 2606 OID 17835)
-- Name: psp_tbl psptoproccessingtype_fk; Type: FK CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.psp_tbl
    ADD CONSTRAINT psptoproccessingtype_fk FOREIGN KEY (system_type) REFERENCES system.processortype_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6213 (class 2606 OID 17840)
-- Name: state_tbl state2country_fk; Type: FK CONSTRAINT; Schema: system; Owner: mpoint
--

ALTER TABLE ONLY system.state_tbl
    ADD CONSTRAINT state2country_fk FOREIGN KEY (countryid) REFERENCES system.country_tbl(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6521 (class 6104 OID 4288204)
-- Name: mpoint_client_pub; Type: PUBLICATION; Schema: -; Owner: mpoint
--

CREATE PUBLICATION mpoint_client_pub WITH (publish = 'insert, update, delete');


ALTER PUBLICATION mpoint_client_pub OWNER TO mpoint;

--
-- TOC entry 6522 (class 6104 OID 4288199)
-- Name: mpoint_enduser_pub; Type: PUBLICATION; Schema: -; Owner: mpoint
--

CREATE PUBLICATION mpoint_enduser_pub WITH (publish = 'insert, update, delete');


ALTER PUBLICATION mpoint_enduser_pub OWNER TO mpoint;

--
-- TOC entry 6520 (class 6104 OID 4288208)
-- Name: mpoint_log_pub; Type: PUBLICATION; Schema: -; Owner: mpoint
--

CREATE PUBLICATION mpoint_log_pub WITH (publish = 'insert, update, delete');


ALTER PUBLICATION mpoint_log_pub OWNER TO mpoint;

--
-- TOC entry 6519 (class 6104 OID 4288213)
-- Name: mpoint_system_pub; Type: PUBLICATION; Schema: -; Owner: mpoint
--

CREATE PUBLICATION mpoint_system_pub WITH (publish = 'insert, update, delete');


ALTER PUBLICATION mpoint_system_pub OWNER TO mpoint;

--
-- TOC entry 6523 (class 6106 OID 4288205)
-- Name: mpoint_client_pub account_tbl; Type: PUBLICATION TABLE; Schema: client; Owner: -
--

ALTER PUBLICATION mpoint_client_pub ADD TABLE ONLY client.account_tbl;


--
-- TOC entry 6544 (class 6106 OID 10981106)
-- Name: mpoint_client_pub additionalproperty_tbl; Type: PUBLICATION TABLE; Schema: client; Owner: -
--

ALTER PUBLICATION mpoint_client_pub ADD TABLE ONLY client.additionalproperty_tbl;


--
-- TOC entry 6524 (class 6106 OID 10981099)
-- Name: mpoint_client_pub cardaccess_tbl; Type: PUBLICATION TABLE; Schema: client; Owner: -
--

ALTER PUBLICATION mpoint_client_pub ADD TABLE ONLY client.cardaccess_tbl;


--
-- TOC entry 6525 (class 6106 OID 4288206)
-- Name: mpoint_client_pub client_tbl; Type: PUBLICATION TABLE; Schema: client; Owner: -
--

ALTER PUBLICATION mpoint_client_pub ADD TABLE ONLY client.client_tbl;


--
-- TOC entry 6551 (class 6106 OID 10981109)
-- Name: mpoint_client_pub gatewaytrigger_tbl; Type: PUBLICATION TABLE; Schema: client; Owner: -
--

ALTER PUBLICATION mpoint_client_pub ADD TABLE ONLY client.gatewaytrigger_tbl;


--
-- TOC entry 6526 (class 6106 OID 10981104)
-- Name: mpoint_client_pub iinlist_tbl; Type: PUBLICATION TABLE; Schema: client; Owner: -
--

ALTER PUBLICATION mpoint_client_pub ADD TABLE ONLY client.iinlist_tbl;


--
-- TOC entry 6527 (class 6106 OID 4288207)
-- Name: mpoint_client_pub keyword_tbl; Type: PUBLICATION TABLE; Schema: client; Owner: -
--

ALTER PUBLICATION mpoint_client_pub ADD TABLE ONLY client.keyword_tbl;


--
-- TOC entry 6528 (class 6106 OID 10981094)
-- Name: mpoint_client_pub merchantaccount_tbl; Type: PUBLICATION TABLE; Schema: client; Owner: -
--

ALTER PUBLICATION mpoint_client_pub ADD TABLE ONLY client.merchantaccount_tbl;


--
-- TOC entry 6529 (class 6106 OID 10981114)
-- Name: mpoint_client_pub product_tbl; Type: PUBLICATION TABLE; Schema: client; Owner: -
--

ALTER PUBLICATION mpoint_client_pub ADD TABLE ONLY client.product_tbl;


--
-- TOC entry 6530 (class 6106 OID 10981117)
-- Name: mpoint_client_pub url_tbl; Type: PUBLICATION TABLE; Schema: client; Owner: -
--

ALTER PUBLICATION mpoint_client_pub ADD TABLE ONLY client.url_tbl;


--
-- TOC entry 6531 (class 6106 OID 4288200)
-- Name: mpoint_enduser_pub account_tbl; Type: PUBLICATION TABLE; Schema: enduser; Owner: -
--

ALTER PUBLICATION mpoint_enduser_pub ADD TABLE ONLY enduser.account_tbl;


--
-- TOC entry 6554 (class 6106 OID 36071928)
-- Name: mpoint_log_pub externalreference_tbl; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.externalreference_tbl;


--
-- TOC entry 6532 (class 6106 OID 4288209)
-- Name: mpoint_log_pub message_tbl; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.message_tbl;


--
-- TOC entry 6548 (class 6106 OID 4288212)
-- Name: mpoint_log_pub session_tbl; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.session_tbl;


--
-- TOC entry 6553 (class 6106 OID 18982285)
-- Name: mpoint_log_pub settlement_record_tbl; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.settlement_record_tbl;


--
-- TOC entry 6552 (class 6106 OID 18982279)
-- Name: mpoint_log_pub settlement_tbl; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.settlement_tbl;


--
-- TOC entry 6533 (class 6106 OID 4288211)
-- Name: mpoint_log_pub state_tbl; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.state_tbl;


--
-- TOC entry 6534 (class 6106 OID 4288210)
-- Name: mpoint_log_pub transaction_tbl; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.transaction_tbl;


--
-- TOC entry 6566 (class 6106 OID 33320855)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_10000001_11000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_10000001_11000001;


--
-- TOC entry 6557 (class 6106 OID 33320846)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_1000001_2000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_1000001_2000001;


--
-- TOC entry 6567 (class 6106 OID 33320856)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_11000001_12000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_11000001_12000001;


--
-- TOC entry 6568 (class 6106 OID 33320857)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_12000001_13000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_12000001_13000001;


--
-- TOC entry 6569 (class 6106 OID 33320858)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_13000001_14000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_13000001_14000001;


--
-- TOC entry 6570 (class 6106 OID 33320859)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_14000001_15000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_14000001_15000001;


--
-- TOC entry 6571 (class 6106 OID 33320860)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_15000001_16000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_15000001_16000001;


--
-- TOC entry 6572 (class 6106 OID 33320861)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_16000001_17000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_16000001_17000001;


--
-- TOC entry 6573 (class 6106 OID 33320862)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_17000001_18000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_17000001_18000001;


--
-- TOC entry 6574 (class 6106 OID 33320863)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_18000001_19000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_18000001_19000001;


--
-- TOC entry 6575 (class 6106 OID 33320864)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_19000001_20000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_19000001_20000001;


--
-- TOC entry 6556 (class 6106 OID 33320845)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_1_1000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_1_1000001;


--
-- TOC entry 6558 (class 6106 OID 33320847)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_2000001_3000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_2000001_3000001;


--
-- TOC entry 6559 (class 6106 OID 33320848)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_3000001_4000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_3000001_4000001;


--
-- TOC entry 6560 (class 6106 OID 33320849)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_4000001_5000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_4000001_5000001;


--
-- TOC entry 6561 (class 6106 OID 33320850)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_5000001_6000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_5000001_6000001;


--
-- TOC entry 6562 (class 6106 OID 33320851)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_6000001_7000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_6000001_7000001;


--
-- TOC entry 6563 (class 6106 OID 33320852)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_7000001_8000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_7000001_8000001;


--
-- TOC entry 6564 (class 6106 OID 33320853)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_8000001_9000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_8000001_9000001;


--
-- TOC entry 6565 (class 6106 OID 33320854)
-- Name: mpoint_log_pub txnpassbook_tbl_10018_9000001_10000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10018_9000001_10000001;


--
-- TOC entry 6586 (class 6106 OID 33320875)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_10000001_11000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_10000001_11000001;


--
-- TOC entry 6577 (class 6106 OID 33320866)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_1000001_2000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_1000001_2000001;


--
-- TOC entry 6587 (class 6106 OID 33320876)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_11000001_12000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_11000001_12000001;


--
-- TOC entry 6588 (class 6106 OID 33320877)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_12000001_13000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_12000001_13000001;


--
-- TOC entry 6589 (class 6106 OID 33320878)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_13000001_14000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_13000001_14000001;


--
-- TOC entry 6590 (class 6106 OID 33320879)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_14000001_15000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_14000001_15000001;


--
-- TOC entry 6591 (class 6106 OID 33320880)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_15000001_16000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_15000001_16000001;


--
-- TOC entry 6592 (class 6106 OID 33320881)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_16000001_17000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_16000001_17000001;


--
-- TOC entry 6593 (class 6106 OID 33320882)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_17000001_18000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_17000001_18000001;


--
-- TOC entry 6594 (class 6106 OID 33320883)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_18000001_19000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_18000001_19000001;


--
-- TOC entry 6595 (class 6106 OID 33320884)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_19000001_20000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_19000001_20000001;


--
-- TOC entry 6576 (class 6106 OID 33320865)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_1_1000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_1_1000001;


--
-- TOC entry 6578 (class 6106 OID 33320867)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_2000001_3000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_2000001_3000001;


--
-- TOC entry 6579 (class 6106 OID 33320868)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_3000001_4000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_3000001_4000001;


--
-- TOC entry 6580 (class 6106 OID 33320869)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_4000001_5000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_4000001_5000001;


--
-- TOC entry 6581 (class 6106 OID 33320870)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_5000001_6000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_5000001_6000001;


--
-- TOC entry 6582 (class 6106 OID 33320871)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_6000001_7000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_6000001_7000001;


--
-- TOC entry 6583 (class 6106 OID 33320872)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_7000001_8000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_7000001_8000001;


--
-- TOC entry 6584 (class 6106 OID 33320873)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_8000001_9000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_8000001_9000001;


--
-- TOC entry 6585 (class 6106 OID 33320874)
-- Name: mpoint_log_pub txnpassbook_tbl_10020_9000001_10000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10020_9000001_10000001;


--
-- TOC entry 6606 (class 6106 OID 33320895)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_10000001_11000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_10000001_11000001;


--
-- TOC entry 6597 (class 6106 OID 33320886)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_1000001_2000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_1000001_2000001;


--
-- TOC entry 6607 (class 6106 OID 33320896)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_11000001_12000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_11000001_12000001;


--
-- TOC entry 6608 (class 6106 OID 33320897)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_12000001_13000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_12000001_13000001;


--
-- TOC entry 6609 (class 6106 OID 33320898)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_13000001_14000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_13000001_14000001;


--
-- TOC entry 6610 (class 6106 OID 33320899)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_14000001_15000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_14000001_15000001;


--
-- TOC entry 6611 (class 6106 OID 33320900)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_15000001_16000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_15000001_16000001;


--
-- TOC entry 6612 (class 6106 OID 33320901)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_16000001_17000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_16000001_17000001;


--
-- TOC entry 6613 (class 6106 OID 33320902)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_17000001_18000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_17000001_18000001;


--
-- TOC entry 6614 (class 6106 OID 33320903)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_18000001_19000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_18000001_19000001;


--
-- TOC entry 6615 (class 6106 OID 33320904)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_19000001_20000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_19000001_20000001;


--
-- TOC entry 6596 (class 6106 OID 33320885)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_1_1000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_1_1000001;


--
-- TOC entry 6598 (class 6106 OID 33320887)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_2000001_3000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_2000001_3000001;


--
-- TOC entry 6599 (class 6106 OID 33320888)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_3000001_4000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_3000001_4000001;


--
-- TOC entry 6600 (class 6106 OID 33320889)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_4000001_5000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_4000001_5000001;


--
-- TOC entry 6601 (class 6106 OID 33320890)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_5000001_6000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_5000001_6000001;


--
-- TOC entry 6602 (class 6106 OID 33320891)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_6000001_7000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_6000001_7000001;


--
-- TOC entry 6603 (class 6106 OID 33320892)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_7000001_8000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_7000001_8000001;


--
-- TOC entry 6604 (class 6106 OID 33320893)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_8000001_9000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_8000001_9000001;


--
-- TOC entry 6605 (class 6106 OID 33320894)
-- Name: mpoint_log_pub txnpassbook_tbl_10021_9000001_10000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10021_9000001_10000001;


--
-- TOC entry 6636 (class 6106 OID 33320925)
-- Name: mpoint_log_pub txnpassbook_tbl_10022; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10022;


--
-- TOC entry 6637 (class 6106 OID 33320926)
-- Name: mpoint_log_pub txnpassbook_tbl_10060; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10060;


--
-- TOC entry 6638 (class 6106 OID 33320927)
-- Name: mpoint_log_pub txnpassbook_tbl_10061; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10061;


--
-- TOC entry 6639 (class 6106 OID 33320928)
-- Name: mpoint_log_pub txnpassbook_tbl_10062; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10062;


--
-- TOC entry 6640 (class 6106 OID 33320929)
-- Name: mpoint_log_pub txnpassbook_tbl_10065; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10065;


--
-- TOC entry 6644 (class 6106 OID 33320933)
-- Name: mpoint_log_pub txnpassbook_tbl_10066; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10066;


--
-- TOC entry 6641 (class 6106 OID 33320930)
-- Name: mpoint_log_pub txnpassbook_tbl_10067; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10067;


--
-- TOC entry 6626 (class 6106 OID 33320915)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_10000001_11000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_10000001_11000001;


--
-- TOC entry 6617 (class 6106 OID 33320906)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_1000001_2000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_1000001_2000001;


--
-- TOC entry 6627 (class 6106 OID 33320916)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_11000001_12000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_11000001_12000001;


--
-- TOC entry 6628 (class 6106 OID 33320917)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_12000001_13000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_12000001_13000001;


--
-- TOC entry 6629 (class 6106 OID 33320918)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_13000001_14000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_13000001_14000001;


--
-- TOC entry 6630 (class 6106 OID 33320919)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_14000001_15000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_14000001_15000001;


--
-- TOC entry 6631 (class 6106 OID 33320920)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_15000001_16000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_15000001_16000001;


--
-- TOC entry 6632 (class 6106 OID 33320921)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_16000001_17000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_16000001_17000001;


--
-- TOC entry 6633 (class 6106 OID 33320922)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_17000001_18000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_17000001_18000001;


--
-- TOC entry 6634 (class 6106 OID 33320923)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_18000001_19000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_18000001_19000001;


--
-- TOC entry 6635 (class 6106 OID 33320924)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_19000001_20000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_19000001_20000001;


--
-- TOC entry 6616 (class 6106 OID 33320905)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_1_1000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_1_1000001;


--
-- TOC entry 6618 (class 6106 OID 33320907)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_2000001_3000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_2000001_3000001;


--
-- TOC entry 6619 (class 6106 OID 33320908)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_3000001_4000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_3000001_4000001;


--
-- TOC entry 6620 (class 6106 OID 33320909)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_4000001_5000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_4000001_5000001;


--
-- TOC entry 6621 (class 6106 OID 33320910)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_5000001_6000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_5000001_6000001;


--
-- TOC entry 6622 (class 6106 OID 33320911)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_6000001_7000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_6000001_7000001;


--
-- TOC entry 6623 (class 6106 OID 33320912)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_7000001_8000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_7000001_8000001;


--
-- TOC entry 6624 (class 6106 OID 33320913)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_8000001_9000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_8000001_9000001;


--
-- TOC entry 6625 (class 6106 OID 33320914)
-- Name: mpoint_log_pub txnpassbook_tbl_10069_9000001_10000001; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10069_9000001_10000001;


--
-- TOC entry 6645 (class 6106 OID 33320934)
-- Name: mpoint_log_pub txnpassbook_tbl_10070; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10070;


--
-- TOC entry 6646 (class 6106 OID 33320935)
-- Name: mpoint_log_pub txnpassbook_tbl_10071; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10071;


--
-- TOC entry 6642 (class 6106 OID 33320931)
-- Name: mpoint_log_pub txnpassbook_tbl_10073; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10073;


--
-- TOC entry 6647 (class 6106 OID 33320936)
-- Name: mpoint_log_pub txnpassbook_tbl_10074; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10074;


--
-- TOC entry 6648 (class 6106 OID 33320937)
-- Name: mpoint_log_pub txnpassbook_tbl_10075; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10075;


--
-- TOC entry 6649 (class 6106 OID 33320938)
-- Name: mpoint_log_pub txnpassbook_tbl_10076; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10076;


--
-- TOC entry 6650 (class 6106 OID 33320939)
-- Name: mpoint_log_pub txnpassbook_tbl_10077; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10077;


--
-- TOC entry 6651 (class 6106 OID 33320940)
-- Name: mpoint_log_pub txnpassbook_tbl_10078; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10078;


--
-- TOC entry 6652 (class 6106 OID 33320941)
-- Name: mpoint_log_pub txnpassbook_tbl_10079; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10079;


--
-- TOC entry 6653 (class 6106 OID 33320942)
-- Name: mpoint_log_pub txnpassbook_tbl_10080; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10080;


--
-- TOC entry 6654 (class 6106 OID 33320943)
-- Name: mpoint_log_pub txnpassbook_tbl_10089; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10089;


--
-- TOC entry 6655 (class 6106 OID 33320944)
-- Name: mpoint_log_pub txnpassbook_tbl_10098; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10098;


--
-- TOC entry 6643 (class 6106 OID 33320932)
-- Name: mpoint_log_pub txnpassbook_tbl_10099; Type: PUBLICATION TABLE; Schema: log; Owner: -
--

ALTER PUBLICATION mpoint_log_pub ADD TABLE ONLY log.txnpassbook_tbl_10099;


--
-- TOC entry 6555 (class 6106 OID 22062090)
-- Name: mpoint_system_pub businesstype_tbl; Type: PUBLICATION TABLE; Schema: system; Owner: -
--

ALTER PUBLICATION mpoint_system_pub ADD TABLE ONLY system.businesstype_tbl;


--
-- TOC entry 6535 (class 6106 OID 4288222)
-- Name: mpoint_system_pub card_tbl; Type: PUBLICATION TABLE; Schema: system; Owner: -
--

ALTER PUBLICATION mpoint_system_pub ADD TABLE ONLY system.card_tbl;


--
-- TOC entry 6536 (class 6106 OID 10981127)
-- Name: mpoint_system_pub cardstate_tbl; Type: PUBLICATION TABLE; Schema: system; Owner: -
--

ALTER PUBLICATION mpoint_system_pub ADD TABLE ONLY system.cardstate_tbl;


--
-- TOC entry 6537 (class 6106 OID 4288215)
-- Name: mpoint_system_pub country_tbl; Type: PUBLICATION TABLE; Schema: system; Owner: -
--

ALTER PUBLICATION mpoint_system_pub ADD TABLE ONLY system.country_tbl;


--
-- TOC entry 6546 (class 6106 OID 4288216)
-- Name: mpoint_system_pub currency_tbl; Type: PUBLICATION TABLE; Schema: system; Owner: -
--

ALTER PUBLICATION mpoint_system_pub ADD TABLE ONLY system.currency_tbl;


--
-- TOC entry 6656 (class 6106 OID 36071925)
-- Name: mpoint_system_pub externalreferencetype_tbl; Type: PUBLICATION TABLE; Schema: system; Owner: -
--

ALTER PUBLICATION mpoint_system_pub ADD TABLE ONLY system.externalreferencetype_tbl;


--
-- TOC entry 6538 (class 6106 OID 4288217)
-- Name: mpoint_system_pub flow_tbl; Type: PUBLICATION TABLE; Schema: system; Owner: -
--

ALTER PUBLICATION mpoint_system_pub ADD TABLE ONLY system.flow_tbl;


--
-- TOC entry 6539 (class 6106 OID 10981139)
-- Name: mpoint_system_pub iinaction_tbl; Type: PUBLICATION TABLE; Schema: system; Owner: -
--

ALTER PUBLICATION mpoint_system_pub ADD TABLE ONLY system.iinaction_tbl;


--
-- TOC entry 6545 (class 6106 OID 4288223)
-- Name: mpoint_system_pub paymenttype_tbl; Type: PUBLICATION TABLE; Schema: system; Owner: -
--

ALTER PUBLICATION mpoint_system_pub ADD TABLE ONLY system.paymenttype_tbl;


--
-- TOC entry 6540 (class 6106 OID 4288220)
-- Name: mpoint_system_pub processortype_tbl; Type: PUBLICATION TABLE; Schema: system; Owner: -
--

ALTER PUBLICATION mpoint_system_pub ADD TABLE ONLY system.processortype_tbl;


--
-- TOC entry 6549 (class 6106 OID 4288214)
-- Name: mpoint_system_pub producttype_tbl; Type: PUBLICATION TABLE; Schema: system; Owner: -
--

ALTER PUBLICATION mpoint_system_pub ADD TABLE ONLY system.producttype_tbl;


--
-- TOC entry 6541 (class 6106 OID 4288219)
-- Name: mpoint_system_pub psp_tbl; Type: PUBLICATION TABLE; Schema: system; Owner: -
--

ALTER PUBLICATION mpoint_system_pub ADD TABLE ONLY system.psp_tbl;


--
-- TOC entry 6547 (class 6106 OID 4288218)
-- Name: mpoint_system_pub sessiontype_tbl; Type: PUBLICATION TABLE; Schema: system; Owner: -
--

ALTER PUBLICATION mpoint_system_pub ADD TABLE ONLY system.sessiontype_tbl;


--
-- TOC entry 6550 (class 6106 OID 10981125)
-- Name: mpoint_system_pub triggerunit_tbl; Type: PUBLICATION TABLE; Schema: system; Owner: -
--

ALTER PUBLICATION mpoint_system_pub ADD TABLE ONLY system.triggerunit_tbl;


--
-- TOC entry 6542 (class 6106 OID 4288221)
-- Name: mpoint_system_pub type_tbl; Type: PUBLICATION TABLE; Schema: system; Owner: -
--

ALTER PUBLICATION mpoint_system_pub ADD TABLE ONLY system.type_tbl;


--
-- TOC entry 6543 (class 6106 OID 10981148)
-- Name: mpoint_system_pub urltype_tbl; Type: PUBLICATION TABLE; Schema: system; Owner: -
--

ALTER PUBLICATION mpoint_system_pub ADD TABLE ONLY system.urltype_tbl;

--
-- TOC entry 4755 (class 3466 OID 36072367)
-- Name: no_ddl_allowed; Type: EVENT TRIGGER; Schema: -; Owner: rdsadmin
--

CREATE EVENT TRIGGER no_ddl_allowed ON ddl_command_end
         WHEN TAG IN ('ALTER TABLE')
   EXECUTE FUNCTION public.no_ddl();


ALTER EVENT TRIGGER no_ddl_allowed OWNER TO postgres;

-- Completed on 2020-09-21 12:14:11 UTC

--
-- PostgreSQL database dump complete
--