CREATE OR REPLACE PROCEDURE public.sp_stabilize_db_connections  --stabilize_pg_v1.1.sql
(
p_age numeric,
p_retain numeric
)
LANGUAGE plpgsql
SECURITY DEFINER
AS $procedure$
/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure : public.sp_stabilize_db_connections()
		Version	  : v1.1
		Date		  : 2022-02-22
		Purpose 	  : Terminates the Idle connections based on the connection age & retention
		Author	      : CPD (SWE/Sarvesh)
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
DECLARE

create_query text;
index_query text;
barray boolean[];

v_bool boolean;

  conn_sql text;
  rec record;

BEGIN

  conn_sql = '
WITH idle_connections 
AS 
(
    SELECT
        pid,
        rank() over (partition by client_addr order by backend_start ASC) as rank
    FROM 
        pg_stat_activity
    WHERE
        pid <> pg_backend_pid( )
    AND
        application_name !~ ''(?:psql)|(?:pgAdmin.+)''
    AND
        datname = current_database() 
    AND
        state in (''idle'', ''idle in transaction'', ''idle in transaction (aborted)'', ''disabled'') 
    AND
        current_timestamp - state_change > interval '''||p_age|| ' minutes'''
||')
SELECT
    pid
FROM
    idle_connections 
WHERE     rank > '||p_retain||'
'
;

--    RAISE NOTICE 'conn_sql %', conn_sql;

  FOR r IN EXECUTE conn_sql
  LOOP
--  PERFORM pg_stat_clear_snapshot();
	
    PERFORM pg_terminate_backend(r.pid);
	  -- RAISE NOTICE 'Removed pid %', r.pid;

  END LOOP;
 -- PERFORM pg_sleep(1);

END;
$procedure$
;
