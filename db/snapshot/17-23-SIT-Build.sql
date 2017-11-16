CREATE TABLE system.paymenttype_tbl
(
    id SERIAL PRIMARY KEY NOT NULL,
    name VARCHAR(50) NOT NULL
);
CREATE UNIQUE INDEX paymenttype_tbl_name_uindex ON system.paymenttype_tbl (name);

INSERT INTO system.paymenttype_tbl (name) VALUES ('Card');
INSERT INTO system.paymenttype_tbl (name) VALUES ('Voucher');
INSERT INTO system.paymenttype_tbl (name) VALUES ('Wallet');
INSERT INTO system.paymenttype_tbl (name) VALUES ('APM');
INSERT INTO system.paymenttype_tbl (name) VALUES ('Card Token');


ALTER TABLE system.card_tbl ADD paymenttype INTEGER DEFAULT 1 NOT NULL;

ALTER TABLE system.card_tbl
ADD CONSTRAINT card_tbl_paymenttype_tbl_id_fk
FOREIGN KEY (paymenttype) REFERENCES system.paymenttype_tbl (id);

UPDATE system.card_tbl SET paymenttype = 2 WHERE id = 24;
UPDATE system.card_tbl SET paymenttype = 2 WHERE id = 26;
UPDATE system.card_tbl SET paymenttype = 3 WHERE id = 16;
UPDATE system.card_tbl SET paymenttype = 3 WHERE id = 17;
UPDATE system.card_tbl SET paymenttype = 3 WHERE id = 27;
UPDATE system.card_tbl SET paymenttype = 3 WHERE id = 25;
UPDATE system.card_tbl SET paymenttype = 3 WHERE id = 23;
UPDATE system.card_tbl SET paymenttype = 3 WHERE id = 15;
UPDATE system.card_tbl SET paymenttype = 4 WHERE id = 33;
UPDATE system.card_tbl SET paymenttype = 4 WHERE id = 28;
UPDATE system.card_tbl SET paymenttype = 4 WHERE id = 31;
UPDATE system.card_tbl SET paymenttype = 4 WHERE id = 32;
UPDATE system.card_tbl SET paymenttype = 4 WHERE id = 34;
UPDATE system.card_tbl SET paymenttype = 4 WHERE id = 36;
UPDATE system.card_tbl SET paymenttype = 5 WHERE id = 35;
UPDATE system.card_tbl SET paymenttype = 5 WHERE id = 11;

/*Base URL*/
INSERT INTO System.URLType_Tbl (id, name) VALUES (14, 'Base URL for Images');

INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (14, 10007, 'https://hpp-dev2.cellpointmobile.com/img/');

/*Base URL*/
