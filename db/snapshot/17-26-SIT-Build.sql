/*  ===========  START : Adding column attempts to Log.Transaction_Tbl  ==================  */
ALTER TABLE Log.Transaction_Tbl ADD COLUMN attempt integer DEFAULT 1;
/*  ===========  END : Adding column attempts to Log.Transaction_Tbl  ==================  */

/*  ===========  START : Adding column preferred to Client.CardAccess_Tbl  ==================  */
ALTER TABLE Client.CardAccess_Tbl ADD COLUMN preferred boolean DEFAULT false;
/*  ===========  END : Adding column preferred to Client.CardAccess_Tbl  ==================  */


INSERT INTO log.state_tbl (id, name, module) VALUES (4001, 'Session Created', 'Payment');
INSERT INTO log.state_tbl (id, name, module) VALUES (4010, 'Session Expire', 'Payment');
INSERT INTO log.state_tbl (id, name, module) VALUES (4020, 'Session Decline (fail)', 'Payment');
INSERT INTO log.state_tbl (id, name, module) VALUES (4030, 'Session Complete', 'Payment');


CREATE TABLE system.SessionType_tbl
(
    id SERIAL PRIMARY KEY,
    name VARCHAR(50),
    enable BOOLEAN DEFAULT TRUE
);
COMMENT ON TABLE system.SessionType_tbl IS 'Contains all session type like normal session, split session and etc';

INSERT INTO system.sessiontype_tbl (id, name) VALUES (1, 'Normal Session');
INSERT INTO system.sessiontype_tbl (id, name) VALUES (2, 'Split Payment Session');

CREATE TABLE log.Session_tbl
(
    id SERIAL PRIMARY KEY,
    clientid INTEGER,
    accountid INTEGER,
    currencyid INTEGER,
    countryid INTEGER,
    stateid INTEGER,
    orderid VARCHAR(128) NOT NULL,
    amount DECIMAL NOT NULL,
    mobile NUMERIC NOT NULL,
    deviceid VARCHAR(128),
    ipaddress VARCHAR(15),
    externalid INTEGER,
    sessiontypeid INTEGER,
    expire TIMESTAMP(6) DEFAULT current_timestamp,
    created TIMESTAMP(6) DEFAULT current_timestamp,
    modified TIMESTAMP(6) DEFAULT current_timestamp,
    CONSTRAINT Session_tbl_client_tbl_id_fk FOREIGN KEY (clientid) REFERENCES client.client_tbl (id),
    CONSTRAINT Session_tbl_account_tbl_id_fk FOREIGN KEY (accountid) REFERENCES client.account_tbl (id),
    CONSTRAINT Session_tbl_currency_tbl_id_fk FOREIGN KEY (currencyid) REFERENCES system.currency_tbl (id),
    CONSTRAINT Session_tbl_country_tbl_id_fk FOREIGN KEY (countryid) REFERENCES system.country_tbl (id),
    CONSTRAINT Session_tbl_state_tbl_id_fk FOREIGN KEY (stateid) REFERENCES log.state_tbl (id),
    CONSTRAINT Session_tbl_sessiontype_tbl_id_fk FOREIGN KEY (sessiontypeid) REFERENCES system.SessionType_tbl (id)
);
COMMENT ON COLUMN log.Session_tbl.clientid IS 'Merchant Id';
COMMENT ON COLUMN log.Session_tbl.accountid IS 'Storefront Id';
COMMENT ON COLUMN log.Session_tbl.currencyid IS 'Currency of transaction';
COMMENT ON COLUMN log.Session_tbl.countryid IS 'Country of transaction';
COMMENT ON COLUMN log.Session_tbl.stateid IS 'State of session';
COMMENT ON COLUMN log.Session_tbl.amount IS 'Total amount for payment';
COMMENT ON COLUMN log.Session_tbl.externalid IS 'Profile id';
COMMENT ON COLUMN log.Session_tbl.sessiontypeid IS 'Session Type id';
COMMENT ON TABLE log.Session_tbl IS 'Session table act as master table for transaction. Split transactions will track by Session id';

ALTER TABLE log.transaction_tbl ADD sessionid INTEGER NULL;
ALTER TABLE log.transaction_tbl
    ADD CONSTRAINT transaction_tbl_session_tbl_id_fk
FOREIGN KEY (sessionid) REFERENCES log.session_tbl (id);
/*  ===========  START : Adding communicationchannels to Client.Client_Tbl  ==================  */
ALTER TABLE client.client_tbl ADD COLUMN communicationchannels integer DEFAULT 0;
/*  ===========  END : Adding communicationchannels to Client.Client_Tbl  ==================  */
