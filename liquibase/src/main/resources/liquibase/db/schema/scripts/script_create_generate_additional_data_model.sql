
CREATE TABLE log.stg_additional_data_tbl (
                                             id serial4 NOT NULL,
                                             name varchar(50) NULL,
                                             value text NULL,
                                             type varchar(100) ,
                                             created timestamp NULL DEFAULT now(),
                                             modified timestamp NULL DEFAULT now(),
                                             externalid int4 NULL,
                                             CONSTRAINT temp_additional_data_pk PRIMARY KEY (type, created, id)
)
    PARTITION BY LIST (type);

SELECT setval('log.stg_additional_data_tbl_id_seq', (SELECT MAX(id) FROM log.additional_data_tbl)+500);

CREATE INDEX stg_additional_data_tbl_externalid_type_index ON log.stg_additional_data_tbl USING btree (type,created,externalid);