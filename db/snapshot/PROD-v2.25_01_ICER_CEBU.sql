-- Cebu Icer : Set dcc enabled to true for PayPal
UPDATE client.cardaccess_tbl set dccenabled = '1' where clientid = 10077 and cardid = 28 ;

-- Cebu Icer : Add sale currency and presentment currency configuration for PayPal

INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (28,10077,156,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (28,10077,360,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (28,10077,901,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (28,10077,36,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (28,10077,96,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (28,10077,446,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (28,10077,784,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (28,10077,410,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (28,10077,764,608,'true','true');



-- Cebu Icer : Set dcc enabled to true for Gcash
UPDATE client.cardaccess_tbl set dccenabled = '1' where clientid = 10077 and cardid = 93 ;

-- Cebu Icer : Add sale currency and presentment currency configuration for Gcash

INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,156,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,360,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,901,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,36,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,96,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,446,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,784,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,410,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,764,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,344,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,458,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,702,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,392,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (93,10077,840,608,'true','true');

-- Cebu Icer : Set dcc enabled to true for GrabPay
UPDATE client.cardaccess_tbl set dccenabled = '1' where clientid = 10077 and cardid = 94 ;

-- Cebu Icer : Add sale currency and presentment currency configuration for GrabPay

INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,156,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,360,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,901,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,36,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,96,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,446,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,784,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,410,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,764,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,344,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,458,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,702,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,392,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (94,10077,840,608,'true','true');





-- Cebu Icer : Set dcc enabled to true for paymaya
UPDATE client.cardaccess_tbl set dccenabled = '1' where clientid = 10077 and cardid = 95 ;

-- Cebu Icer : Add sale currency and presentment currency configuration for paymaya

INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,156,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,360,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,901,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,36,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,96,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,446,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,784,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,410,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,764,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,344,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,458,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,702,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,392,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (95,10077,840,608,'true','true');






-- Cebu Icer : Set dcc enabled to true for payment center
UPDATE client.cardaccess_tbl set dccenabled = '1' where clientid = 10077 and cardid = 96 ;

-- Cebu Icer : Add sale currency and presentment currency configuration for payment center
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,36,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,36,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,96,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,96,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,156,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,156,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,410,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,410,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,446,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,446,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,764,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,764,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,901,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,901,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,344,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,344,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,458,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,458,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,702,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,702,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,392,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,392,784,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,840,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,840,784,'true','true');

INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,784,608,'true','true');
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,608,784,'true','true');