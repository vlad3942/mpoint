-- State for passbook functionality -
INSERT INTO log.state_tbl (id, name, module, func, enabled) VALUES (5010, 'Authorize Operation Requested', 'Passbook', null, true);
INSERT INTO log.state_tbl (id, name, module, func, enabled) VALUES (5012, 'Cancel Operation Requested', 'Passbook', null, true);
INSERT INTO log.state_tbl (id, name, module, func, enabled) VALUES (5013, 'Refund Operation Requested', 'Passbook', null, true);
INSERT INTO log.state_tbl (id, name, module, func, enabled) VALUES (5011, 'Capture Operation Requested', 'Passbook', null, true);
INSERT INTO log.state_tbl (id, name, module, func, enabled) VALUES (5014, 'Initialize Operation Requested', 'Passbook', null, true);
INSERT INTO log.state_tbl (id, name, module, func, enabled) VALUES (5015, 'Void Operation Requested', 'Passbook', null, true);
INSERT INTO log.state_tbl (id, name, module, func, enabled) VALUES (6100, 'Invalid Passbook Operation', 'Passbook', null, true);
INSERT INTO log.state_tbl (id, name, module, func, enabled) VALUES (6200, 'Operation Not Allowed ', 'Passbook', null, true);
INSERT INTO log.state_tbl (id, name, module, func, enabled) VALUES (6201, 'Amount is Higher', 'Passbook', null, true);

create table log.txnpassbook_tbl
(
    id               serial                  not null
        constraint txnpassbook_pk
            primary key,
    transactionid    integer                 not null
        constraint txnpassbook_transaction_tbl_id_fk
            references log.transaction_tbl,
    amount           integer                 not null,
    currencyid       integer                 not null
        constraint txnpassbook_currency_tbl_id_fk
            references system.currency_tbl,
    requestedopt     integer
        constraint txnpassbook_tbl_state_tbl_id_fk
            references log.state_tbl,
    performedopt     integer
        constraint txnpassbook_tbl_state_tbl_id_1_fk
            references log.state_tbl,
    status           varchar(20)             not null,
    extref           varchar(50),
    extrefidentifier varchar(100),
    enabled          boolean   default true,
    created          timestamp without time zone default now(),
    modified         timestamp without time zone default now()
);

comment on column log.txnpassbook_tbl.transactionid is 'Primary Key of log.transaction_tbl';

comment on column log.txnpassbook_tbl.amount is 'Amount used for the operation';

comment on column log.txnpassbook_tbl.currencyid is 'Current used for the operation
primary key of system.currency_tbl';

comment on column log.txnpassbook_tbl.requestedopt is 'Request operation
·         Initialize
·         Authorize
·         Cancel
·         Capture
·         Refund';

comment on column log.txnpassbook_tbl.performedopt is 'Based on requested operations which are not performed or pending, next for performing operation will decide.
Entry will contain either requested or performed operation';

comment on column log.txnpassbook_tbl.extref is 'Capture, refund and cancel may be related to order, line time, ticket or full txn
This contains the primary id of repective table to fetch all necessary in the callback';

comment on column log.txnpassbook_tbl.extrefidentifier is 'Table or entity from which external reference is used';

alter table log.txnpassbook_tbl
    owner to mpoint;


CREATE TRIGGER Update_TxnPassbook
    BEFORE UPDATE
    ON Log.txnpassbook_tbl
    FOR EACH ROW
EXECUTE PROCEDURE Public.Update_Table_Proc();


alter table system.psp_tbl
	add SupportedPartialOperations integer default 0 not null;

comment on column system.psp_tbl.SupportedPartialOperations is 'Merchant''s Supported Partial Operations
and PSP''s supported Partial Operations
2 - Partial Capture
3 - Partial Refund
5 - Partial Cancel
Possible values % (constants)
30 % (2 || 3 || 5)   = Capture and Cancel and Refund
15 % (3 || 5)	= Refund and Cancel
10 % (2 || 5)	= Capture and Cancel
6 % (2 || 3)	 = Capture and Refund
5 % 5		= Cancel
3 % 3		= Refund
2 % 2		= Capture';




alter table client.merchantaccount_tbl
	add SupportedPartialOperations integer default 0 not null;

comment on column system.psp_tbl.SupportedPartialOperations is 'Merchant''s Supported Partial Operations
and PSP''s supported Partial Operations
2 - Partial Capture
3 - Partial Refund
5 - Partial Cancel
Possible values % (constants)
30 % (2 || 3 || 5)   = Capture and Cancel and Refund
15 % (3 || 5)	= Refund and Cancel
10 % (2 || 5)	= Capture and Cancel
6 % (2 || 3)	 = Capture and Refund
5 % 5		= Cancel
3 % 3		= Refund
2 % 2		= Capture';


INSERT INTO client.additionalproperty_tbl (key, value, type, enabled, scope, externalid) VALUES ('preferredvoidoperation', 'false', 'client', true, 0, 10067);
INSERT INTO client.additionalproperty_tbl (key, value, type, enabled, scope, externalid) VALUES ('cumulativesettlement', 'true', 'client', true, 0, 10067);
INSERT INTO client.additionalproperty_tbl (key, value, type, enabled, scope, externalid) VALUES ('ismutualexclusive', 'true', 'client', true, 0, 10067);