/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		Procedure : stabilize_stats_mpoint.sql()
		Version	  : v1.1
		Date		  : 2022-03-01
		Purpose   : Gather stats on mPoint for Optimal executions
		Author	  : CPD (SWE/Sarvesh)
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

SET MAINTENANCE_WORK_MEM='500MB';

VACUUM ANALYZE log.additional_data_tbl;
VACUUM ANALYZE log.address_tbl;
VACUUM ANALYZE log.auditlog_tbl;
VACUUM ANALYZE log.billing_summary_tbl;
VACUUM ANALYZE log.externalreference_tbl;
VACUUM ANALYZE log.flight_tbl;
VACUUM ANALYZE log.message_tbl;
VACUUM ANALYZE log.note_tbl;
VACUUM ANALYZE log.operation_tbl;
VACUUM ANALYZE log.order_tbl;
VACUUM ANALYZE log.passenger_tbl;
VACUUM ANALYZE log.paymentroute_tbl;
VACUUM ANALYZE log.paymentsecureinfo_tbl;
VACUUM ANALYZE log.session_tbl;
VACUUM ANALYZE log.settlement_record_tbl;
VACUUM ANALYZE log.settlement_tbl;
VACUUM ANALYZE log.state_tbl;
VACUUM ANALYZE log.transaction_tbl;
VACUUM ANALYZE log.txnpassbook_tbl_10077_DEFAULT;
VACUUM ANALYZE log.txnpassbook_tbl_10101_DEFAULT;

SET MAINTENANCE_WORK_MEM=default;

--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------