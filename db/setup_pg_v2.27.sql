--Update timezone for CEBU
UPDATE client.additionalproperty_tbl SET "key"='TIMEZONE', value='Asia/Kuala_Lumpur' where id = (select id from client.additionalproperty_tbl where "key"='TIMEZONE' and externalid=10077)





