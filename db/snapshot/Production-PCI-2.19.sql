

INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid)  SELECT 10020, PC.cardid, PC.pspid, '154' FROM System.PSPCard_Tbl PC, Client.Client_Tbl Cl WHERE PC.cardid IN (67,40) AND PC.pspid ='40' GROUP BY PC.cardid, PC.pspid;

-- Hpp flag
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, enabled, scope) VALUES ('isAutoRedirect', 'true', <clientid>, 'client', true, 2);

/* Update callback url for UATP client Ref Jira : CMP-3000
 * Replace host url as per env while running the query
 * DevPune - http://mpoint.dev2.cellpointmobile.com/uatp/callback.php
 * SITLondon - http://mpoint.sit.cellpointmobile.com/uatp/callback.php
 * SITPune - http://mpoint.sit2.cellpointmobile.com/uatp/callback.php
 * UATAWS - http://mpoint.uat-01.cellpointmobile.net/uatp/callback.php
 * PROD - http://mpoint.cellpointmobile.net/uatp/callback.php
*/
update client.client_tbl set callbackurl = '<mpoint-host>/uatp/callback.php' where id = 10069;


