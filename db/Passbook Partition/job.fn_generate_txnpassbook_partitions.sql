--select * from log.fn_generate_txnpassbook_partitions('log.txnpassbook_tbl_part',10018,'Y',1,10000000,1000000);
--select * from log.fn_generate_txnpassbook_partitions('log.txnpassbook_tbl_part',10022,'N');
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
		Date		  : 2020-03-09
		Purpose 	  : Generates Partitions(Basic/Nested) for the configurations
		Author	      : CPM (SWE/Sarvesh)
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
declare
create_query text;
index_query text;

v_p_ods_table varchar(50):=p_ods_table;
v_p_orig_table varchar(50):=substring(v_p_ods_table,1,length(v_p_ods_table)-5);
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
			||v_p_ods_table||'  FOR VALUES IN ('||v_Client_id||')';

			RAISE NOTICE 'create_query : %',create_query ;

			EXECUTE create_query;

ELSIF  p_nested='Y' THEN

	create_query := 
		    'CREATE TABLE '
			||v_p_orig_table||'_'||v_Client_id
			||' PARTITION OF '
			||v_p_ods_table||'  FOR VALUES IN ('||v_Client_id||') PARTITION BY RANGE  (transactionid)';

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
$function$
;
