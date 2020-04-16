--select * from log.fn_add_txnpassbook_triggers('log.txnpassbook_tbl',10018,'Y',1,10000000,1000000);
--select * from log.fn_add_txnpassbook_triggers('log.txnpassbook_tbl,10022,'N');
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
$function$
;
