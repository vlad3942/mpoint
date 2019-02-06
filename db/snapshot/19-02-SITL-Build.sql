--ODMBE-5192
ALTER TABLE client.client_tbl ALTER COLUMN maxamount TYPE BIGINT USING maxamount::BIGINT;

update client.client_tbl cl set maxamount=947483647 where maxamount=-1 and id=100018;

--CMESB-3488

UPDATE System.Country_Tbl set id = 653, currencyid = 104, alpha2code = 'MM', alpha3code = 'MMR', code = 104 WHERE id = 653;
--Inserting the entry for Malindo to work with Myanmar with USD currency.
INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid) VALUES (10018,653,840);