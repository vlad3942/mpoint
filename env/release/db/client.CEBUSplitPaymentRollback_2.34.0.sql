-----Rollback SQL to disable Split Payment (TF + e-Wallet)-----
UPDATE client.split_configuration_tbl SET enabled = false WHERE id = 2;
-------------------