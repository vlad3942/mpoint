
-- 2c2p JCB card - ROLLBACK

DELETE FROM client.additionalproperty_tbl 
WHERE key = 'MID.5'
AND value = <MID> 
AND externalid = (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = <client id> and pspid = <pspid>)
AND type = 'merchant';



-- MID selection based on card id - Rollback

DELETE FROM client.additionalproperty_tbl 
WHERE key = 'mechantaccountrule'
AND value = 'merchantaccount ::= (property[@name=''<midpath>''])
midpath ::= "MID."(@card-id)'
AND externalid = (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = 10007 and pspid = 18)
AND type = 'merchant';


