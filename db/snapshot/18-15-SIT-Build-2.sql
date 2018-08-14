/*=================== Adding new states for tokenization used for UATP SUVTP generation : START =======================*/
INSERT INTO log.state_tbl (id, name, module, enabled) VALUES (2020 , 'Tokenization complete - Virtual card created', 'Authorization', true);
INSERT INTO log.state_tbl (id, name, module, enabled) VALUES (2021 , 'Tokenization Failed', 'Authorization', true);
/*=================== Adding new states for tokenization used for UATP SUVTP generation : END =======================*/
ALTER TYPE LOG.ADDITIONAL_DATA_REF ADD VALUE 'Transaction';