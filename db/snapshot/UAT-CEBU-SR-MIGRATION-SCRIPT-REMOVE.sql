--- mPoint Client Schema - for SR Migration

------------------------------------------------------------------------------
-- Remove Currency Specific route currency
DELETE FROM client.routecurrency_tbl where currencyid is not null;

-- Remove Country Specific route country
DELETE FROM client.routecountry_tbl where countryid is not null;