/*  ===========  START : Adding New Processor Type  ==================  */
INSERT INTO system.processortype_tbl (id, name) VALUES (6, 'Merchant Plug-in');
/*  ===========  END : Adding New Processor Type  ==================  */

INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (42, 'NETS MPI',6);
INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (840,42,'USD');
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10007, 42, 'NETS MPI', '', '');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100007, 42, '-1');
INSERT INTO client.cardaccess_tbl ( clientid, cardid, enabled, pspid, countryid, stateid, position) VALUES (10007, 8, true, 42, 200, 1, null);

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) VALUES ('NETS_3DVERIFICATION', 'true', 10007, 'client');

ALTER TABLE client.cardaccess_tbl ADD psp_type INT DEFAULT 1 NOT NULL;
ALTER TABLE client.cardaccess_tbl
  ADD CONSTRAINT cardaccess_tbl_processortype_tbl_id_fk
FOREIGN KEY (psp_type) REFERENCES system.processortype_tbl (id);
DROP INDEX client.cardaccess_card_country_uq RESTRICT;
UPDATE client.cardaccess_tbl
SET psp_type = psp_tbl.system_type
FROM system.psp_tbl
WHERE psp_tbl.id = cardaccess_tbl.pspid;
CREATE UNIQUE INDEX cardaccess_card_country_uq ON client.cardaccess_tbl (clientid, cardid, countryid, psp_type);