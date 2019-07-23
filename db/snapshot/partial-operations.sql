-- State for passbook functionality -
INSERT INTO log.state_tbl (id, name, module, func, enabled) VALUES (5010, 'Authorize Operation Requested', 'Passbook', null, true);
INSERT INTO log.state_tbl (id, name, module, func, enabled) VALUES (5012, 'Cancel Operation Requested', 'Passbook', null, true);
INSERT INTO log.state_tbl (id, name, module, func, enabled) VALUES (5013, 'Refund Operation Requested', 'Passbook', null, true);
INSERT INTO log.state_tbl (id, name, module, func, enabled) VALUES (5011, 'Capture Operation Requested', 'Passbook', null, true);
INSERT INTO log.state_tbl (id, name, module, func, enabled) VALUES (5014, 'Initialize Operation Requested', 'Passbook', null, true);
INSERT INTO log.state_tbl (id, name, module, func, enabled) VALUES (6100, 'Invalid Passbook Operation', 'Passbook', null, true);
INSERT INTO log.state_tbl (id, name, module, func, enabled) VALUES (6200, 'Operation Not Allowed ', 'Passbook', null, true);
INSERT INTO log.state_tbl (id, name, module, func, enabled) VALUES (6201, 'Amount is Higher', 'Passbook', null, true);