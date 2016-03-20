UPDATE Client.Client_Tbl SET num_masked_digits = 2 WHERE id = 10005;	-- DSB App PRODUCTION
UPDATE Client.Client_Tbl SET num_masked_digits = 2 WHERE id = 10014;	-- DSB App Test
UPDATE Client.Client_Tbl SET num_masked_digits = 2 WHERE id = 10019;	-- Mobile Travel Card

-- Setting maxamount for DSB clients to DKK 50.000,- since new and more expensive products have been introduced
UPDATE Client.Client_Tbl SET maxamount = 5000000 WHERE id = 10005; -- DSB App PRODUCTION
UPDATE Client.Client_Tbl SET maxamount = 5000000 WHERE id = 10014; -- DSB App Test
