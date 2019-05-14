
-- 2c2p JCB card - ROLLBACK

DELETE FROM client.additionalproperty_tbl 
WHERE value = <MID> 
AND externalid = (SELECT ID FROM client.merchantaccount_tbl WHERE clientid = <client id> and pspid = <pspid>)
AND type = 'merchant';


