-- Deleting entry made by error in last version--
DELETE from client.card_currency_mapping_tbl where client_id = 10077 AND card_id = 96
and sale_currency_id = 392 AND settlement_currency_id = 392;

--Adding JPY to KRW mapping --
INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (96,10077,392,410,'true','true');