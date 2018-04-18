ALTER TABLE client.gatewaystat_tbl ALTER COLUMN statvalue TYPE numeric ;

/*=================== Moving triggers to BRE =================== */
ALTER TABLE client.gatewaytrigger_tbl DROP COLUMN healthtriggerunit ;
ALTER TABLE client.gatewaytrigger_tbl DROP COLUMN healthtriggervalue ;
ALTER TABLE client.gatewaytrigger_tbl DROP COLUMN resetthresholdunit ;
ALTER TABLE client.gatewaytrigger_tbl DROP COLUMN resetthresholdvalue ;
/*=================== Moving triggers to BRE =================== */

ALTER TABLE Client.gatewaytrigger_tbl ADD COLUMN lastrun timestamp without time zone ;