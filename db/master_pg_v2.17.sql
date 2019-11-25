--CMP-3128
ALTER TABLE log.transaction_tbl ADD profileid int8 NULL;

comment on column log.transaction_tbl.profileid is 'mProfile id associated with the txn';

ALTER TABLE enduser.account_tbl ADD profileid int8 NULL;

comment on column enduser.account_tbl.profileid is 'mProfile id associated with the registered enduser';


/* Ticket level transaction - Add new column fees in log.order_tbl */
ALTER TABLE Log.order_tbl ADD COLUMN fees integer DEFAULT 0;

-- Create new table system.businesstype_tbl to store businesstype for each client
CREATE TABLE system.businesstype_tbl
(
  id serial NOT NULL,
  name character varying(50),
  enabled boolean DEFAULT true,
  CONSTRAINT businesstype_pk PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);

ALTER TABLE system.businesstype_tbl OWNER TO mpoint;
GRANT ALL ON TABLE system.businesstype_tbl TO mpoint;
GRANT ALL ON TABLE system.businesstype_tbl TO jona;

-- Added new column and foreign key constraint
ALTER TABLE client.account_tbl
	ADD COLUMN businessType integer DEFAULT 0,
	ADD CONSTRAINT businessType_pk FOREIGN KEY (businessType) REFERENCES system.businesstype_tbl (id);


CREATE INDEX account_tbl_businessType_index ON client.account_tbl (businessType);
CREATE INDEX order_tbl_orderref_index ON Log.Order_Tbl (orderref);comment on column enduser.account_tbl.profileid is 'mProfile id associated with the registered enduser';

--CMP-3295
CREATE INDEX eu_account_tbl_profileid_index ON enduser.account_tbl (profileid);