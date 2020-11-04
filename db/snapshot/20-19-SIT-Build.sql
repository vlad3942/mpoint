

-- CMP-4456 --
UPDATE "system".endpoint_tbl SET timeout=150 WHERE id = (SELECT id FROM "system".endpoint_tbl where "path" like '%/mpoint/initialize-payment%');
