-- CMP-2807
alter table client.additionalproperty_tbl
	add scope int default 0;

comment on column client.additionalproperty_tbl.scope is 'Scope of properties
0 - Internal
1 - Private
2 - Public';

update table client.additionalproperty_tbl set scope = 2;
