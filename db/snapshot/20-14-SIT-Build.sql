-- State for Payment retried using DR
INSERT INTO log.state_tbl (id, name, module, func) VALUES (7010, 'Payment retried using dynamic routing', 'General', 'authWithAlternateRoute');