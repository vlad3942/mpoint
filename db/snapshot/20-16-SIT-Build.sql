--- CMP-4296 ---
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('invoiceidrule_PAYPAL_CEBU', 'invoiceid ::= (psp-config/@id)=="24"=(transaction.@id)', true, 10077, 'client', 0);

--------------------------------------------------------------------------------
----  Update fraud state descriptions
--------------------------------------------------------------------------------
UPDATE log.state_tbl SET name='Pre Auth Initiated' WHERE id=3010;
UPDATE log.state_tbl SET name='Pre Auth Success' WHERE id=3011;
UPDATE log.state_tbl SET name='Pre Auth Unavbl' WHERE id=3012;
UPDATE log.state_tbl SET name='Pre Auth Unknown' WHERE id=3013;
UPDATE log.state_tbl SET name='Pre Auth Review' WHERE id=3014;
UPDATE log.state_tbl SET name='Pre Auth Fail' WHERE id=3015;
UPDATE log.state_tbl SET name='Pre Auth Conx Fail' WHERE id=3016;

UPDATE log.state_tbl SET name='Post Auth Initiated' WHERE id=3110;
UPDATE log.state_tbl SET name='Post Auth Success' WHERE id=3111;
UPDATE log.state_tbl SET name='Post Auth Unavbl' WHERE id=3112;
UPDATE log.state_tbl SET name='Post Auth Unknown' WHERE id=3113;
UPDATE log.state_tbl SET name='Post Auth Review' WHERE id=3114;
UPDATE log.state_tbl SET name='Post Auth Fail' WHERE id=3115;
UPDATE log.state_tbl SET name='Post Auth Conx Fail' WHERE id=3116;
