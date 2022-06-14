-- AVPOP-945 - Add missing client.providerpm_tbl for NMI
DELETE FROM client.providerpm_tbl  WHERE routeid = 466 and pmid=7; --Master Card
DELETE FROM client.providerpm_tbl  WHERE routeid = 466 and pmid=8; --VISA