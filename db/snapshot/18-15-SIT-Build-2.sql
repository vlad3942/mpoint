/*=================== Adding new states for tokenization used for UATP SUVTP generation : START =======================*/
INSERT INTO log.state_tbl (id, name, module, enabled) VALUES (2030 , 'Tokenization complete - Virtual card created', 'Authorization', true);
INSERT INTO log.state_tbl (id, name, module, enabled) VALUES (2031 , 'Tokenization Failed', 'Authorization', true);
/*=================== Adding new states for tokenization used for UATP SUVTP generation : END =======================*/

INSERT INTO client.additionalproperty_tbl(key, value,externalid, type)
    VALUES ('webSessionTimeout', 2000, 10007, 'client');