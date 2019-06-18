--CMP-3015 default value set to empty
alter table client.merchantaccount_tbl alter column username set default 'empty';
alter table client.merchantaccount_tbl alter column passwd set default 'empty';