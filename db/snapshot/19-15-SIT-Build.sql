-- Diasabling Non 3D authentication for JCB MID
INSERT INTO client.additionalproperty_tbl( key, value, externalid, type, scope )
SELECT 'SUPRESS_3DS_FLOW', 'JPY.5', id, 'merchant', 2 FROM client.merchantaccount_tbl WHERE pspid=26 AND clientid = 10018;


---  JCB MID Script
update client.additionalproperty_tbl set value= '764764000002012',scope=2 where externalid = 408 and key = 'username.5' 
and value = '1258A795EF5A37B064AEDE936DFA452E41D34F5C93F5973F209F722919AD61BA'

update client.additionalproperty_tbl set value= '1258A795EF5A37B064AEDE936DFA452E41D34F5C93F5973F209F722919AD61BA',scope=2
where externalid = 408 and key = 'MID.5' and value = '764764000002012'

---rollback Scripts :

update client.additionalproperty_tbl set value= '1258A795EF5A37B064AEDE936DFA452E41D34F5C93F5973F209F722919AD61BA' 
where externalid = 408 and key = 'username.5' and value = '764764000002012'

update client.additionalproperty_tbl set value= '764764000002012' 
where externalid = 408 and key = 'MID.5' and value = '1258A795EF5A37B064AEDE936DFA452E41D34F5C93F5973F209F722919AD61BA'
