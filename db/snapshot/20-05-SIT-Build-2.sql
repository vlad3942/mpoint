-- UATP batch cut-off-time for CMP-3527 --
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('BATCH-CUT-OFF-TIME', '02:00', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <ClientID> and pspid = 50), 'merchant',1);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('TICKET-START-RANGE', '526016', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <ClientID> and pspid = 50), 'merchant',1);
INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('TICKET-END-RANGE', '526019', (SELECT id FROM Client.MerchantAccount_Tbl WHERE clientid = <ClientID> and pspid = 50), 'merchant',1);

-- CMP-3484 Wallet Based Routing --
ALTER TABLE client.cardaccess_tbl ADD walletid int4;
drop index cardaccess_card_country_uq;
CREATE UNIQUE INDEX cardaccess_card_country_uq ON client.cardaccess_tbl USING btree (clientid, cardid, pspid, countryid, psp_type,walletid) WHERE (enabled = true);
