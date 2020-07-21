
/* ========== CONFIGURE RMFSS START========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (65, 'CEBU-RMFSS',9);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (1, 65);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (5, 65);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (7, 65);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (8, 65);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (22, 65);

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,65,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (608,65,'PHP');


INSERT INTO system.cardprefix_tbl (cardid, min, max, enabled) VALUES (36, 0, 0, true);

----Fraud Integration
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3010, 'Pre Fraud Check Initiated', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3011, 'Pre-screening Result - Accepted', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3012, 'Pre-screening Fraud Service Unavailable', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3013, 'Pre-screening Result - Unknown', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3014, 'Pre-screening Result - Review', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3015, 'Pre-screening Result - Rejected', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3016, 'Pre-screening Connection Failed - Rejected', 'Fraud', '');

INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3110, 'Post Fraud Check Initiated', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3111, 'Post-screening Result - Accepted', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3112, 'Post-screening Fraud Service Unavailable', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3113, 'Post-screening Result - Unknown', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3114, 'Post-screening Result - Review', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3115, 'Post-screening Result - Rejected', 'Fraud', '');
INSERT INTO log.state_tbl(id, "name", "module", func)VALUES(3116, 'Post-screening Connection Failed', 'Fraud', '');

INSERT INTO "system".processortype_tbl (id, "name") VALUES(10, 'Post Auth Fraud Gateway');


/* ========== CONFIGURE Cyber Fraud GateWay START========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (64, 'CyberSource Fraud Gateway',9);
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (<cardid>, 64);


INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (1, 64, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (5, 64, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (7, 64, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (8, 64, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (22, 64, true);
INSERT INTO system.pspcard_tbl (cardid, pspid, enabled) VALUES (0, 64, true);

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (840,64,'USD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (124,64,'CAD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (36,64,'AUD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (344,64,'HKD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (392,64,'JPY');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (710,64,'ZAR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (826,64,'GBP');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (978,64,'EUR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (554,64,'NZD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (752,64,'SEK');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (901,64,'TWD');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (643,64,'RUB');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (356,64,'INR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (360,64,'IDR');
INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (608,64,'PHP');

TODO ADD OTHER CURRENCIES FOR CYBS

INSERT INTO system.pspcurrency_tbl (currencyid, pspid, name) VALUES (901,63,'TWD');

/* ========== END CONFIGURE Cyber Fraud GateWay START========== */

insert into system.cardpricing_tbl (pricepointid ,cardid ) select pricepointid,0 from system.cardpricing_tbl where cardid = 8
 ON conflict ON CONSTRAINT cardpricing_uq DO NOTHING;
 UPDATE SYSTEM.CARD_TBL set enabled=true where id=0;

-- Card prefix range for master card --
INSERT INTO "system".cardprefix_tbl (cardid, min, max, enabled) VALUES(7, 222100, 272099, true);