--Update External ID with routeid
--Ingenico
UPDATE client.additionalproperty_tbl
SET externalid=36
WHERE id IN(604,605) and "key"='INGENICO_AUTH_MODE';
​
--Worldpay
UPDATE client.additionalproperty_tbl
SET externalid=37
WHERE id IN (606,607,608,609,610,611,612,613,614,615,616,617,618,619,620,621,622,623,624,625,626,627,628,629,630,631,632,633,634,635,636,637);
​
--Safetypay
UPDATE client.additionalproperty_tbl
SET externalid=38
WHERE id IN (638,639,640,641,642,643,644,645,646,647);
​
​
--Update scope 
--(Ingenico)
UPDATE client.additionalproperty_tbl
SET "scope"=1
WHERE id IN(604,605) and "key"='INGENICO_AUTH_MODE';
​
--(Worldpay)
UPDATE client.additionalproperty_tbl
SET "scope"=1
WHERE id IN (606,607,608,609,610,611,612,613,614,615,616,617,618,619,620,621,622,623,624,625,626,627,628,629,630,631,632,633,634,635,636,637);
​
--(Safetypay)
UPDATE client.additionalproperty_tbl
SET "scope"=1
WHERE id IN (638,639,640,641,642,643,644,645,646,647);
​
​
--Fraud DM Credentials
Update client.merchantaccount_tbl
set passwd='IGIWa+ER+i5lLn47/uG67gVI7jIjEjMuUfnGzfshoy3B7h8TfCRyHTcKGdOp+415RnuOhtX5c5ql6ja7k74LurZjHsURsMR38V+Elq4vtotMsKMFuL07PjoXZQgPHS6BZLJNXG7uRYkF7ZUfqiz3V+VyjnMFvmUrPiFa3hqkKkzmSzLlPbE5Ku5+a/CMLIjicB+qnsfCb+W68GD9eEIwVfwKMJn2MzEn1gw9AL/AgR86CYnXC2Bk1SJGy0vEyjAipiMZZ0xgpQEtSdfGoE2TdDB4lsu7h7/QYVWCeAtwf11GhsHFBUc3xQ/QDtG16ZS0GyvAIU0/i6SQTnAVFMPbQQ=='
where clientid=10101 and pspid=64 and username='avianca_master';