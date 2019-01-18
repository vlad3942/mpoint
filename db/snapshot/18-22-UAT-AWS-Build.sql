--Master sql 2.02 and 2.03 consolidated
ALTER TABLE log.transaction_tbl
  ADD approval_action_code varchar(40) NULL;
COMMENT ON COLUMN log.transaction_tbl.approval_action_code
IS 'This field contains an action code and approval code
"approval code":"action code"';

-- Settlement Improvement
ALTER TABLE system.psp_tbl ADD capture_method int DEFAULT 0;
COMMENT ON COLUMN system.psp_tbl.capture_method IS '0 - manual
2 - bulk capture
3 - bulk refund
6 - bulk capture + bulk  refund';

CREATE TABLE log.settlement_tbl
(
  id serial PRIMARY KEY,
  record_number int NOT NULL,
  file_reference_number varchar(10) NOT NULL,
  file_sequence_number int NOT NULL,
  created timestamp DEFAULT now(),
  client_id int NOT NULL,
  psp_id int NOT NULL,
  record_tracking_number varchar(20),
  record_type varchar(20),
  description varchar(100),
  status varchar(10) DEFAULT 'active' NOT NULL,
  CONSTRAINT settlement_tbl_client_tbl_id_fk FOREIGN KEY (client_id) REFERENCES client.client_tbl (id),
  CONSTRAINT settlement_tbl_psp_tbl_id_fk FOREIGN KEY (psp_id) REFERENCES system.psp_tbl (id)
)
WITH (
OIDS=FALSE
);
ALTER TABLE log.settlement_tbl
  OWNER TO mpoint;

CREATE TABLE log.settlement_record_tbl
(
  id            serial PRIMARY KEY,
  settlementid  int,
  transactionid int,
  description varchar(100),
  CONSTRAINT settlement_record_tbl_settlement_tbl_id_fk FOREIGN KEY (settlementid) REFERENCES log.settlement_tbl (id),
  CONSTRAINT settlement_record_tbl_transaction_tbl_id_fk FOREIGN KEY (transactionid) REFERENCES log.transaction_tbl (id)
)
WITH (
OIDS=FALSE
);
ALTER TABLE log.settlement_record_tbl
  OWNER TO mpoint;

-------- CMP-2426: PCI Password expose --------
DROP TABLE admin.user_tbl CASCADE;
-------- CMP-2426: PCI Password expose --------

DROP INDEX IF EXISTS log.session_tbl_orderid_uindex;

UPDATE system.psp_tbl SET name = 'ApplePay' WHERE name = 'Apple Pay';
UPDATE system.psp_tbl SET name = 'VisaCheckout' WHERE name = 'VISA Checkout';
UPDATE system.psp_tbl SET name = 'MasterPass' WHERE name = 'Master Pass';
UPDATE system.psp_tbl SET name = 'AMEXExpressCheckout' WHERE name = 'AMEX Express Checkout';
UPDATE system.psp_tbl SET name = 'AndroidPay' WHERE name = 'Android Pay';
UPDATE system.psp_tbl SET name = 'GooglePay' WHERE name = 'Google Pay';
UPDATE system.psp_tbl SET name = 'MVault' WHERE name = 'mVault';

-- Adding Virtual Token for Saving SUVTP in mPoint schema
ALTER TABLE Log.Transaction_Tbl ADD COLUMN virtualtoken character varying(512);

ALTER TABLE log.settlement_tbl ALTER COLUMN status TYPE varchar(100) USING status::varchar(100);

ALTER TYPE LOG.ADDITIONAL_DATA_REF ADD VALUE 'Transaction';
DROP INDEX client.cardaccess_card_country_uq RESTRICT;
CREATE UNIQUE INDEX cardaccess_card_country_uq ON client.cardaccess_tbl (clientid, cardid, countryid, psp_type) WHERE enabled='true';

-- Drop orderId unique constraint --
ALTER TABLE log.session_tbl DROP CONSTRAINT constraint_name;

-- country calling code
ALTER TABLE system.country_tbl ADD country_calling_code INTEGER NULL;

-- URL where the customer may be redirected if txn fails.
ALTER TABLE Log.Transaction_Tbl ADD declineurl VARCHAR(255);


----------
--setup sql 2.03
update system.country_tbl set country_calling_code=297 where alpha3code='ABW';
update system.country_tbl set country_calling_code=93 where alpha3code='AFG';
update system.country_tbl set country_calling_code=244 where alpha3code='AGO';
update system.country_tbl set country_calling_code=355 where alpha3code='ALB';
update system.country_tbl set country_calling_code=376 where alpha3code='AND';
update system.country_tbl set country_calling_code=971 where alpha3code='ARE';
update system.country_tbl set country_calling_code=54 where alpha3code='ARG';
update system.country_tbl set country_calling_code=374 where alpha3code='ARM';
update system.country_tbl set country_calling_code=61 where alpha3code='AUS';
update system.country_tbl set country_calling_code=43 where alpha3code='AUT';
update system.country_tbl set country_calling_code=994 where alpha3code='AZE';
update system.country_tbl set country_calling_code=257 where alpha3code='BDI';
update system.country_tbl set country_calling_code=32 where alpha3code='BEL';
update system.country_tbl set country_calling_code=229 where alpha3code='BEN';
update system.country_tbl set country_calling_code=226 where alpha3code='BFA';
update system.country_tbl set country_calling_code=880 where alpha3code='BGD';
update system.country_tbl set country_calling_code=359 where alpha3code='BGR';
update system.country_tbl set country_calling_code=973 where alpha3code='BHR';
update system.country_tbl set country_calling_code=387 where alpha3code='BIH';
update system.country_tbl set country_calling_code=375 where alpha3code='BLR';
update system.country_tbl set country_calling_code=501 where alpha3code='BLZ';
update system.country_tbl set country_calling_code=591 where alpha3code='BOL';
update system.country_tbl set country_calling_code=55 where alpha3code='BRA';
update system.country_tbl set country_calling_code=673 where alpha3code='BRN';
update system.country_tbl set country_calling_code=975 where alpha3code='BTN';
update system.country_tbl set country_calling_code=267 where alpha3code='BWA';
update system.country_tbl set country_calling_code=236 where alpha3code='CAF';
update system.country_tbl set country_calling_code=41 where alpha3code='CHE';
update system.country_tbl set country_calling_code=56 where alpha3code='CHL';
update system.country_tbl set country_calling_code=86 where alpha3code='CHN';
update system.country_tbl set country_calling_code=225 where alpha3code='CIV';
update system.country_tbl set country_calling_code=237 where alpha3code='CMR';
update system.country_tbl set country_calling_code=243 where alpha3code='COD';
update system.country_tbl set country_calling_code=242 where alpha3code='COG';
update system.country_tbl set country_calling_code=682 where alpha3code='COK';
update system.country_tbl set country_calling_code=57 where alpha3code='COL';
update system.country_tbl set country_calling_code=269 where alpha3code='COM';
update system.country_tbl set country_calling_code=238 where alpha3code='CPV';
update system.country_tbl set country_calling_code=506 where alpha3code='CRI';
update system.country_tbl set country_calling_code=53 where alpha3code='CUB';
update system.country_tbl set country_calling_code=996 where alpha3code='CYM';
update system.country_tbl set country_calling_code=357 where alpha3code='CYP';
update system.country_tbl set country_calling_code=420 where alpha3code='CZE';
update system.country_tbl set country_calling_code=49 where alpha3code='DEU';
update system.country_tbl set country_calling_code=253 where alpha3code='DJI';
update system.country_tbl set country_calling_code=45 where alpha3code='DNK';
update system.country_tbl set country_calling_code=213 where alpha3code='DZA';
update system.country_tbl set country_calling_code=593 where alpha3code='ECU';
update system.country_tbl set country_calling_code=20 where alpha3code='EGY';
update system.country_tbl set country_calling_code=291 where alpha3code='ERI';
update system.country_tbl set country_calling_code=34 where alpha3code='ESP';
update system.country_tbl set country_calling_code=372 where alpha3code='EST';
update system.country_tbl set country_calling_code=251 where alpha3code='ETH';
update system.country_tbl set country_calling_code=358 where alpha3code='FIN';
update system.country_tbl set country_calling_code=679 where alpha3code='FJI';
update system.country_tbl set country_calling_code=500 where alpha3code='FLK';
update system.country_tbl set country_calling_code=33 where alpha3code='FRA';
update system.country_tbl set country_calling_code=298 where alpha3code='FRO';
update system.country_tbl set country_calling_code=691 where alpha3code='FSM';
update system.country_tbl set country_calling_code=241 where alpha3code='GAB';
update system.country_tbl set country_calling_code=995 where alpha3code='GEO';
update system.country_tbl set country_calling_code=233 where alpha3code='GHA';
update system.country_tbl set country_calling_code=350 where alpha3code='GIB';
update system.country_tbl set country_calling_code=224 where alpha3code='GIN';
update system.country_tbl set country_calling_code=590 where alpha3code='GLP';
update system.country_tbl set country_calling_code=220 where alpha3code='GMB';
update system.country_tbl set country_calling_code=245 where alpha3code='GNB';
update system.country_tbl set country_calling_code=240 where alpha3code='GNQ';
update system.country_tbl set country_calling_code=30 where alpha3code='GRC';
update system.country_tbl set country_calling_code=299 where alpha3code='GRL';
update system.country_tbl set country_calling_code=502 where alpha3code='GTM';
update system.country_tbl set country_calling_code=594 where alpha3code='GUF';
update system.country_tbl set country_calling_code=592 where alpha3code='GUY';
update system.country_tbl set country_calling_code=852 where alpha3code='HKG';
update system.country_tbl set country_calling_code=504 where alpha3code='HND';
update system.country_tbl set country_calling_code=385 where alpha3code='HRV';
update system.country_tbl set country_calling_code=509 where alpha3code='HTI';
update system.country_tbl set country_calling_code=36 where alpha3code='HUN';
update system.country_tbl set country_calling_code=62 where alpha3code='IDN';
update system.country_tbl set country_calling_code=91 where alpha3code='IND';
update system.country_tbl set country_calling_code=353 where alpha3code='IRL';
update system.country_tbl set country_calling_code=98 where alpha3code='IRN';
update system.country_tbl set country_calling_code=964 where alpha3code='IRQ';
update system.country_tbl set country_calling_code=354 where alpha3code='ISL';
update system.country_tbl set country_calling_code=972 where alpha3code='ISR';
update system.country_tbl set country_calling_code=39 where alpha3code='ITA';
update system.country_tbl set country_calling_code=962 where alpha3code='JOR';
update system.country_tbl set country_calling_code=81 where alpha3code='JPN';
update system.country_tbl set country_calling_code=254 where alpha3code='KEN';
update system.country_tbl set country_calling_code=855 where alpha3code='KHM';
update system.country_tbl set country_calling_code=686 where alpha3code='KIR';
update system.country_tbl set country_calling_code=965 where alpha3code='KWT';
update system.country_tbl set country_calling_code=856 where alpha3code='LAO';
update system.country_tbl set country_calling_code=961 where alpha3code='LBN';
update system.country_tbl set country_calling_code=231 where alpha3code='LBR';
update system.country_tbl set country_calling_code=423 where alpha3code='LIE';
update system.country_tbl set country_calling_code=94 where alpha3code='LKA';
update system.country_tbl set country_calling_code=266 where alpha3code='LSO';
update system.country_tbl set country_calling_code=370 where alpha3code='LTU';
update system.country_tbl set country_calling_code=352 where alpha3code='LUX';
update system.country_tbl set country_calling_code=371 where alpha3code='LVA';
update system.country_tbl set country_calling_code=853 where alpha3code='MAC';
update system.country_tbl set country_calling_code=853 where alpha3code='MAC';
update system.country_tbl set country_calling_code=212 where alpha3code='MAR';
update system.country_tbl set country_calling_code=377 where alpha3code='MCO';
update system.country_tbl set country_calling_code=373 where alpha3code='MDA';
update system.country_tbl set country_calling_code=261 where alpha3code='MDG';
update system.country_tbl set country_calling_code=960 where alpha3code='MDV';
update system.country_tbl set country_calling_code=52 where alpha3code='MEX';
update system.country_tbl set country_calling_code=692 where alpha3code='MHL';
update system.country_tbl set country_calling_code=389 where alpha3code='MKD';
update system.country_tbl set country_calling_code=223 where alpha3code='MLI';
update system.country_tbl set country_calling_code=356 where alpha3code='MLT';
update system.country_tbl set country_calling_code=382 where alpha3code='MNE';
update system.country_tbl set country_calling_code=976 where alpha3code='MNG';
update system.country_tbl set country_calling_code=258 where alpha3code='MOZ';
update system.country_tbl set country_calling_code=222 where alpha3code='MRT';
update system.country_tbl set country_calling_code=596 where alpha3code='MTQ';
update system.country_tbl set country_calling_code=230 where alpha3code='MUS';
update system.country_tbl set country_calling_code=265 where alpha3code='MWI';
update system.country_tbl set country_calling_code=60 where alpha3code='MYS';
update system.country_tbl set country_calling_code=262 where alpha3code='MYT';
update system.country_tbl set country_calling_code=264 where alpha3code='NAM';
update system.country_tbl set country_calling_code=687 where alpha3code='NCL';
update system.country_tbl set country_calling_code=227 where alpha3code='NER';
update system.country_tbl set country_calling_code=234 where alpha3code='NGA';
update system.country_tbl set country_calling_code=505 where alpha3code='NIC';
update system.country_tbl set country_calling_code=683 where alpha3code='NIU';
update system.country_tbl set country_calling_code=31 where alpha3code='NLD';
update system.country_tbl set country_calling_code=47 where alpha3code='NOR';
update system.country_tbl set country_calling_code=977 where alpha3code='NPL';
update system.country_tbl set country_calling_code=674 where alpha3code='NRU';
update system.country_tbl set country_calling_code=64 where alpha3code='NZL';
update system.country_tbl set country_calling_code=968 where alpha3code='OMN';
update system.country_tbl set country_calling_code=92 where alpha3code='PAK';
update system.country_tbl set country_calling_code=507 where alpha3code='PAN';
update system.country_tbl set country_calling_code=51 where alpha3code='PER';
update system.country_tbl set country_calling_code=63 where alpha3code='PHL';
update system.country_tbl set country_calling_code=680 where alpha3code='PLW';
update system.country_tbl set country_calling_code=48 where alpha3code='POL';
update system.country_tbl set country_calling_code=351 where alpha3code='PRT';
update system.country_tbl set country_calling_code=595 where alpha3code='PRY';
update system.country_tbl set country_calling_code=970 where alpha3code='PSE';
update system.country_tbl set country_calling_code=689 where alpha3code='PYF';
update system.country_tbl set country_calling_code=974 where alpha3code='QAT';
update system.country_tbl set country_calling_code=262 where alpha3code='REU';
update system.country_tbl set country_calling_code=40 where alpha3code='ROU';
update system.country_tbl set country_calling_code=7 where alpha3code='RUS';
update system.country_tbl set country_calling_code=250 where alpha3code='RWA';
update system.country_tbl set country_calling_code=966 where alpha3code='SAU';
update system.country_tbl set country_calling_code=249 where alpha3code='SDN';
update system.country_tbl set country_calling_code=221 where alpha3code='SEN';
update system.country_tbl set country_calling_code=65 where alpha3code='SGP';
update system.country_tbl set country_calling_code=290 where alpha3code='SHN';
update system.country_tbl set country_calling_code=290 where alpha3code='SHN';
update system.country_tbl set country_calling_code=677 where alpha3code='SLB';
update system.country_tbl set country_calling_code=232 where alpha3code='SLE';
update system.country_tbl set country_calling_code=503 where alpha3code='SLV';
update system.country_tbl set country_calling_code=378 where alpha3code='SMR';
update system.country_tbl set country_calling_code=252 where alpha3code='SOM';
update system.country_tbl set country_calling_code=508 where alpha3code='SPM';
update system.country_tbl set country_calling_code=381 where alpha3code='SRB';
update system.country_tbl set country_calling_code=239 where alpha3code='STP';
update system.country_tbl set country_calling_code=597 where alpha3code='SUR';
update system.country_tbl set country_calling_code=421 where alpha3code='SVK';
update system.country_tbl set country_calling_code=386 where alpha3code='SVN';
update system.country_tbl set country_calling_code=46 where alpha3code='SWE';
update system.country_tbl set country_calling_code=268 where alpha3code='SWZ';
update system.country_tbl set country_calling_code=248 where alpha3code='SYC';
update system.country_tbl set country_calling_code=963 where alpha3code='SYR';
update system.country_tbl set country_calling_code=235 where alpha3code='TCD';
update system.country_tbl set country_calling_code=228 where alpha3code='TGO';
update system.country_tbl set country_calling_code=66 where alpha3code='THA';
update system.country_tbl set country_calling_code=993 where alpha3code='TKM';
update system.country_tbl set country_calling_code=676 where alpha3code='TON';
update system.country_tbl set country_calling_code=216 where alpha3code='TUN';
update system.country_tbl set country_calling_code=90 where alpha3code='TUR';
update system.country_tbl set country_calling_code=688 where alpha3code='TUV';
update system.country_tbl set country_calling_code=255 where alpha3code='TZA';
update system.country_tbl set country_calling_code=256 where alpha3code='UGA';
update system.country_tbl set country_calling_code=380 where alpha3code='UKR';
update system.country_tbl set country_calling_code=598 where alpha3code='URY';
update system.country_tbl set country_calling_code=1 where alpha3code='USA';
update system.country_tbl set country_calling_code=998 where alpha3code='UZB';
update system.country_tbl set country_calling_code=379 where alpha3code='VAT';
update system.country_tbl set country_calling_code=58 where alpha3code='VEN';
update system.country_tbl set country_calling_code=678 where alpha3code='VUT';
update system.country_tbl set country_calling_code=681 where alpha3code='WLF';
update system.country_tbl set country_calling_code=685 where alpha3code='WSM';
update system.country_tbl set country_calling_code=967 where alpha3code='YEM';
update system.country_tbl set country_calling_code=27 where alpha3code='ZAF';
update system.country_tbl set country_calling_code=260 where alpha3code='ZMB';
update system.country_tbl set country_calling_code=263 where alpha3code='ZWE';

--Transaction under review for fraud, payment rejected sub state.
INSERT INTO log.state_tbl (id, name, module) VALUES (20104, 'Payment rejected. Transaction under review.', 'Payment');

--CMP-2558
DROP INDEX client.cardaccess_card_country_uq RESTRICT;
CREATE UNIQUE INDEX cardaccess_card_country_uq ON client.cardaccess_tbl (clientid, cardid, pspid, countryid, psp_type) WHERE enabled='true';

