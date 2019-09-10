--CMP-3128
alter table enduser.account_tbl
    add mProfileId varchar(50);

comment on column enduser.account_tbl.mProfileId is 'mProfile id related to the enduser profile';
