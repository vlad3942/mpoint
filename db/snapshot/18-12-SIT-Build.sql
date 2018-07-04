-------- TXN Time Out -------------

UPDATE log.state_tbl SET name = 'Issuer timed out' WHERE id = 20109;
INSERT INTO log.state_tbl (id, name, module) VALUES (20108, 'PSP timed out', 'Payment');

-------- TXN Time Out -------------

-------- CMP-2426: PCI Password expose --------
DROP TABLE admin.user_tbl CASCADE;
-------- CMP-2426: PCI Password expose --------