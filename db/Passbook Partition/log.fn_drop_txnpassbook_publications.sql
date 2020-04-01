--select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl',10018,'Y',1,10000000,1000000);
--select * from log.fn_drop_txnpassbook_publications('log.txnpassbook_tbl,10022,'N');
CREATE OR REPLACE FUNCTION log.fn_drop_txnpassbook_publications
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
		Procedure : log.fn_drop_txnpassbook_publications()
		Version	  : v1.1
		Date		  : 2020-03-09
		Purpose 	  : Drops Publications(for Basic/Nested Partitions) for the configurations
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
		    'ALTER PUBLICATION mpoint_log_pub DROP TABLE '
			||v_p_ods_table||'_'||v_Client_id;

			RAISE NOTICE 'publication_query : %',publication_query ;

			EXECUTE publication_query;

ELSIF  p_nested='Y' THEN

	FOR publication_query IN SELECT
			'ALTER PUBLICATION mpoint_log_pub DROP TABLE '
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

				RAISE WARNING '[log.fn_drop_txnpassbook_publications] - UDP ERROR [OTHER] - SQLSTATE: %, SQLERRM: %', SQLSTATE, SQLERRM;

	
END;
$function$
;
