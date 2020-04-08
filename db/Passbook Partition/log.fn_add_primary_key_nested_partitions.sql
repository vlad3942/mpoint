--select * from log.fn_add_primary_key_nested_partitions('log.txnpassbook_tbl',10018,1,20000000,1000000);
--select * from log.fn_add_primary_key_nested_partitions('log.txnpassbook_tbl',10061,1,20000000,1000000);
CREATE OR REPLACE FUNCTION log.fn_add_primary_key_nested_partitions(p_ods_table character varying, p_client_id integer, p_dim_from bigint, p_dim_to bigint,p_size bigint)
RETURNS void 
LANGUAGE plpgsql
AS
$function$
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
$function$
;
