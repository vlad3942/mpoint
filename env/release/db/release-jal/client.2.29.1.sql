-- CEBU

INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth, enabled) VALUES (1, 10077, 'Card+Voucher', true, true);
INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth, enabled) VALUES (2, 10077, 'Wallet+Voucher', true, true);
INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth, enabled) VALUES (3, 10077, 'APM+Voucher', true, true);


INSERT INTO client.split_combination_tbl (id, split_config_id, payment_type, sequence_no) VALUES (1, 1, 1, 1);
INSERT INTO client.split_combination_tbl (id, split_config_id, payment_type, sequence_no) VALUES (2, 1, 2, 2);
INSERT INTO client.split_combination_tbl (id, split_config_id, payment_type, sequence_no) VALUES (3, 2, 3, 1);
INSERT INTO client.split_combination_tbl (id, split_config_id, payment_type, sequence_no) VALUES (4, 2, 2, 2);
INSERT INTO client.split_combination_tbl (id, split_config_id, payment_type, sequence_no) VALUES (5, 3, 4, 1);
INSERT INTO client.split_combination_tbl (id, split_config_id, payment_type, sequence_no) VALUES (6, 3, 2, 2);

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('IS_REOFFER', 'false', true, 10077, 'client', 0);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('IS_MANUAL_REFUND', 'false', true, 10077, 'client', 0);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('sessiontype', '2', true, 10077, 'client', 0);

--AV

INSERT INTO client.split_configuration_tbl (id, client_id, name, is_one_step_auth, enabled) VALUES (4, 10101, 'Card+Card', false, true);

INSERT INTO client.split_combination_tbl (id, split_config_id, payment_type, sequence_no) VALUES (7, 4, 1, 1);
INSERT INTO client.split_combination_tbl (id, split_config_id, payment_type, sequence_no) VALUES (8, 4, 1, 2);

INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('IS_REOFFER', 'true', true, 10101, 'client', 0);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('IS_MANUAL_REFUND', 'true', true, 10101, 'client', 0);
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('sessiontype', '2', true, 10101, 'client', 0);

