-- CMP-6317
insert into client.routefeature_tbl (clientid, routeconfigid, featureid)
select mt.clientid, rt2.id, 4
from client.merchantaccount_tbl mt
         join client.route_tbl rt on rt.clientid = mt.clientid and rt.providerid = mt.pspid
         join client.routeconfig_tbl rt2 on rt2.routeid = rt.id
where  supportedpartialoperations > 0 and supportedpartialoperations%2 = 0 and mt.clientid  in (10077,10101) and mt.enabled = true and rt.enabled = true and rt2.enabled = true
union
select mt.clientid, rt2.id, 6
from client.merchantaccount_tbl mt
         join client.route_tbl rt on rt.clientid = mt.clientid and rt.providerid = mt.pspid
         join client.routeconfig_tbl rt2 on rt2.routeid = rt.id
where supportedpartialoperations > 0 and  supportedpartialoperations%3 = 0 and mt.clientid  in (10077,10101) and mt.enabled = true and rt.enabled = true and rt2.enabled = true
union
select mt.clientid, rt2.id, 19
from client.merchantaccount_tbl mt
         join client.route_tbl rt on rt.clientid = mt.clientid and rt.providerid = mt.pspid
         join client.routeconfig_tbl rt2 on rt2.routeid = rt.id
where supportedpartialoperations > 0 and supportedpartialoperations%5 = 0 and mt.clientid  in (10077,10101) and mt.enabled = true and rt.enabled = true and rt2.enabled = true;

-- Client propert fingerprint enchancment --
UPDATE client.client_property_tbl SET value = '9ozphlqx' where propertyid = (select id from system.client_property_tbl where name = 'CYBS_DM_ORGID') and clientid = 10101;
--START: Create New Client 10106--
INSERT INTO Client.Client_Tbl (Id, Countryid, Flowid, Name, Username, Passwd, Logourl, Cssurl, Callbackurl, Accepturl,
                               Cancelurl, Maxamount, Lang, Smsrcpt, Emailrcpt, Method, Terms, Enabled, Mode, Send_Pspid,
                               Store_Card, Iconurl, Show_All_Cards, Max_Cards, Identification, Transaction_Ttl,
                               Num_Masked_Digits, Declineurl, Salt, Secretkey, Communicationchannels, Installment,
                               Max_Installments, Installment_Frequency, Enable_Cvv)
VALUES (10106, 103, 1, 'Virgin Holidays', 'virginholidays', 'WZ9yT/FQ',
        'https://cpd-hpp2-prodassests.s3.eu-central-1.amazonaws.com/10106/logo.png',
        'https://cpd-hpp2-prodassests.s3.eu-central-1.amazonaws.com/10106', '', '', '', 99999999999, 'gb', FALSE,
        FALSE, 'mPoint', NULL, TRUE, 1, TRUE, 0, NULL, FALSE, -1, 7, 0, 4, '', 'ju3ki2lo1hy', NULL, 5, 0, 0, 0,
        FALSE);

--Create Storefronts--
INSERT INTO Client.Account_Tbl (Clientid, Name, Mobile, Enabled, Markup, Businesstype)
VALUES (101060, 10106, 'Virgin Holidays', NULL, TRUE, 'spa', 0);

--Create Keyword--
INSERT INTO Client.Keyword_Tbl (Clientid, Name, Standard, Enabled)
VALUES (10106, 'CPM', TRUE, TRUE);

--URL entries--
INSERT INTO Client.Url_Tbl (Urltypeid, Clientid, Url, Enabled)
VALUES (4, 10106, 'https://vh.velocity.cellpointmobile.net:443', TRUE);
INSERT INTO Client.Url_Tbl (Urltypeid, Clientid, Url, Enabled)
VALUES (14, 10106, 'https://cpd-hpp2-prodassests.s3.eu-central-1.amazonaws.com/10106', TRUE);
INSERT INTO Client.Url_Tbl (Urltypeid, Clientid, Url, Enabled)
VALUES (17, 10106, 'payment.webdev.vholsinternal.co.uk', FALSE);

--Country-Currency Mapping--
INSERT INTO Client.Countrycurrency_Tbl (Id, Clientid, Countryid, Currencyid, Enabled)
VALUES (10106, 103, 826, TRUE); --GBP
INSERT INTO Client.Countrycurrency_Tbl (Id, Clientid, Countryid, Currencyid, Enabled)
VALUES (10106, 103, 840, TRUE); --USD
INSERT INTO Client.Countrycurrency_Tbl (Id, Clientid, Countryid, Currencyid, Enabled)
VALUES (10106, 103, 978, TRUE);
--EURO--

--Entry in Additional Property--
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('mvault', 'true', TRUE, 10106, 'client', 0);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('IS_STORE_BILLING_ADDRS', 'true', TRUE, 10106, 'client', 0);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('mandateBillingDetails', 'true', TRUE, 10106, 'client', 2);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('IS_LEGACY', 'false', TRUE, 10106, 'client', 2);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('IS_LEGACY_CALLBACK_FLOW', 'false', TRUE, 10106, 'client', 2);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('isAutoRedirect', 'true', TRUE, 10106, 'client', 2);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('showBillingDetails', 'true', TRUE, 10106, 'client', 2);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('enableHppAuthentication', 'true', TRUE, 10106, 'client', 0);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('webSessionTimeout', '15', TRUE, 10106, 'client', 2);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('max_session_retry_count', '1000', TRUE, 10106, 'client', 0);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('maxPollingInterval', '60', TRUE, 10106, 'client', 2);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('minPollingInterval', '5', TRUE, 10106, 'client', 2);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('binsearch_required', 'true', TRUE, 10106, 'client', 2);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('CARRIER_NAME', 'Virgin Atlantic Holidays Ltd', TRUE, 10106, 'client', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('TICKET_ISSUE_CITY', 'Crawley', TRUE, 10106, 'client', 1);

INSERT INTO Client.Services_Tbl (Clientid, Dcc_Enabled, Mcp_Enabled, Pcc_Enabled, Fraud_Enabled, Tokenization_Enabled,
                                 Splitpayment_Enabled, Callback_Enabled, Void_Enabled, Mpi_Enabled, Legacy_Flow_Enabled,
                                 Enabled)
VALUES (10106, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, TRUE, FALSE, TRUE);

INSERT INTO Client.Pm_Tbl (Clientid, Pmid, Enabled)
VALUES (10106, 8, TRUE);
INSERT INTO Client.Pm_Tbl (Clientid, Pmid, Enabled)
VALUES (10106, 7, TRUE);
INSERT INTO Client.Pm_Tbl (Clientid, Pmid, Enabled)
VALUES (10106, 1, TRUE);

--END: Create New Client 10106--

--START: Create New Client 10107--
INSERT INTO Client.Client_Tbl (Id, Countryid, Flowid, Name, Username, Passwd, Logourl, Cssurl, Callbackurl, Accepturl,
                               Cancelurl, Maxamount, Lang, Smsrcpt, Emailrcpt, Method, Terms, Enabled, Mode, Send_Pspid,
                               Store_Card, Iconurl, Show_All_Cards, Max_Cards, Identification, Transaction_Ttl,
                               Num_Masked_Digits, Declineurl, Salt, Secretkey, Communicationchannels, Installment,
                               Max_Installments, Installment_Frequency, Enable_Cvv)
VALUES (10107, 103, 1, 'Virgin Holidays Call Center', 'vhcallcenter', 'R5ur{5MY',
        'https://cpd-hpp2-prodassests.s3.eu-central-1.amazonaws.com/10106/logo.png',
        'https://cpd-hpp2-prodassests.s3.eu-central-1.amazonaws.com/10106', '', '', '', 99999999999, 'gb', FALSE,
        FALSE, 'mPoint', NULL, TRUE, 1, TRUE, 0, NULL, FALSE, -1, 7, 0, 4, '', 'aq4sw5de6fr', NULL, 5, 0, 0, 0,
        FALSE);

--Create Storefronts--
INSERT INTO Client.Account_Tbl (Clientid, Name, Mobile, Enabled, Markup, Businesstype)
VALUES (101070, 10107, 'Virgin Holidays Call Center', NULL, TRUE, 'spa', 0);

--Create Keyword--
INSERT INTO Client.Keyword_Tbl (Clientid, Name, Standard, Enabled)
VALUES (10107, 'CPM', TRUE, TRUE);

--URL entries--
INSERT INTO Client.Url_Tbl (Urltypeid, Clientid, Url, Enabled)
VALUES (4, 10107, 'https://vhcc.velocity.cellpointmobile.net:443', TRUE);
INSERT INTO Client.Url_Tbl (Urltypeid, Clientid, Url, Enabled)
VALUES (14, 10107, 'https://cpd-hpp2-prodassests.s3.eu-central-1.amazonaws.com/10106', TRUE);

--Country-Currency Mapping--
INSERT INTO Client.Countrycurrency_Tbl (Id, Clientid, Countryid, Currencyid, Enabled)
VALUES (10107, 103, 826, TRUE); --GBP
INSERT INTO Client.Countrycurrency_Tbl (Id, Clientid, Countryid, Currencyid, Enabled)
VALUES (10107, 103, 840, TRUE); --USD
INSERT INTO Client.Countrycurrency_Tbl (Id, Clientid, Countryid, Currencyid, Enabled)
VALUES (10107, 103, 978, TRUE);
--EURO--


--Entry in Additional Property--
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('IS_STORE_BILLING_ADDRS', 'true', TRUE, 10107, 'client', 0);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('mandateBillingDetails', 'true', TRUE, 10107, 'client', 2);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('IS_LEGACY', 'false', TRUE, 10107, 'client', 2);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('IS_LEGACY_CALLBACK_FLOW', 'false', TRUE, 10107, 'client', 2);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('isAutoRedirect', 'true', TRUE, 10107, 'client', 2);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('showBillingDetails', 'false', TRUE, 10107, 'client', 2);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('enableHppAuthentication', 'true', TRUE, 10107, 'client', 0);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('webSessionTimeout', '15', TRUE, 10107, 'client', 2);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('max_session_retry_count', '1000', TRUE, 10107, 'client', 0);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('maxPollingInterval', '60', TRUE, 10107, 'client', 2);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('minPollingInterval', '5', TRUE, 10107, 'client', 2);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('binsearch_required', 'true', TRUE, 10107, 'client', 2);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('CARRIER_NAME', 'Virgin Atlantic Holidays Ltd', TRUE, 10107, 'client', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('TICKET_ISSUE_CITY', 'Crawley', TRUE, 10107, 'client', 1);


INSERT INTO Client.Services_Tbl (Clientid, Dcc_Enabled, Mcp_Enabled, Pcc_Enabled, Fraud_Enabled, Tokenization_Enabled,
                                 Splitpayment_Enabled, Callback_Enabled, Void_Enabled, Mpi_Enabled, Legacy_Flow_Enabled,
                                 Enabled)
VALUES (10107, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, TRUE);

INSERT INTO Client.Pm_Tbl (Clientid, Pmid, Enabled)
VALUES (10107, 8, TRUE);
INSERT INTO Client.Pm_Tbl (Clientid, Pmid, Enabled)
VALUES (10107, 7, TRUE);
INSERT INTO Client.Pm_Tbl (Clientid, Pmid, Enabled)
VALUES (10107, 1, TRUE);

--END: Create New Client 10107--

--START: Entry in route tables for First Data 10106--
INSERT INTO Client.Route_Tbl (Clientid, Providerid, Enabled)
VALUES (10106, 62, TRUE);

INSERT INTO Client.Routeconfig_Tbl(Routeid, Name, Capturetype, Mid, Username, Password, Enabled)
SELECT Id, 'FIRST DATA', 1, '520334509971560', 'WST837504011._.1', 'aPg.(7T7gg', Enabled
FROM Client.Route_Tbl
WHERE Clientid = 10106
  AND Providerid = 62;

INSERT INTO Client.Routecountry_Tbl(Routeconfigid)
SELECT Rc.Id
FROM Client.Routeconfig_Tbl Rc
         INNER JOIN Client.Route_Tbl R ON R.Id = Rc.Routeid
WHERE R.Clientid = 10106
  AND Mid = '520334509971560'
  AND Capturetype = 1;

INSERT INTO Client.Routecurrency_Tbl(Routeconfigid)
SELECT Rc.Id
FROM Client.Routeconfig_Tbl Rc
         INNER JOIN Client.Route_Tbl R ON R.Id = Rc.Routeid
WHERE R.Clientid = 10106
  AND Mid = '520334509971560'
  AND Capturetype = 1;

--Entry in Merchant Account--
INSERT INTO Client.Merchantaccount_Tbl (Clientid, Pspid, Name, Enabled, Username, Passwd, Stored_Card,
                                        Supportedpartialoperations)
VALUES (10106, 47, 'MODIRUM MPI', TRUE, '9449005362', '-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDGaf1esiccugzC
UcI1/CVNIbnrsDb4F2ctLpw2i4d65U2NtP+wpHRnCzm9KbIJ6gEJkeOPjb7WLyaK
DLaGbvhGW/DnKa0fa3KkvgvSaJTrji2ssDcr2lBRulOgNRTyilbNWMdfwosUgveN
dY5DPNp/f0nHpq+HajsdcSL/POT+slWy7ZXKfS4CqIB39ClPm2AOD5htzQhEBr5Z
174lm69R3QHh6CPg1bdS/1qKpr7RuXyvOGlsrvc00gJWhEbF3OGANieyeOWrxOFq
cs4B/ubvqkikcTPw42VKWPTRfxNr+8ANF0g+1bmok/X/UUUGbD9YXXjWNTKcJ68k
FDqPen8fAgMBAAECggEADB5/NmCFWRDYJKpfxXJgSOTNeWLrCJ5NVAoryn0dSlll
Mkmi8IQrA/xAi5hXYpmjdJUvpB4RUP3SSc5a+70ddxa4kTYPeALVHtDo4fI3Xmfx
zEF7LTeJfmR2JSv42pOul55blzLH0fnp6v8KMHswEWeR9xrsT8YiVDsL2zE5/4CD
dxEzO5eCgdp8QnqJPZR9dErrFwP2DVm+IvGDKb5WZAztRL3EwZnGzh4+3aA4NKHy
D9OQJQ4pHApx3nXDedxBg0GKT+Ecr6u4vQW8Gc3wM9NqgYHvWQeIsPX8gGzoKxVa
x6GV8PpRXNe3dcun/q+lIdmTnUX9+gczHH0VPZ8/qQKBgQDyK2OQ1ABXkYJhuuKG
WYSQ1gJKIYrn++SyUAvpTAFeUSDYD303v5YDhDvXRVn9xufcjb0NRZuI3S2jPi1x
EGi7e68jC+I3QhypVlNdsJQFhPPiVJYysngPlHXJFxbpeuPKy/MWeyGQBvcxADPB
Z0KkXVQcE4NlfSuAbz/xnTEn2wKBgQDRvuEJ9uNIUTzAeb2OvrlPM2xxJ+4nGs3a
4RTLPafdm8xjBCuDm8+RiT6fGRp/oPfzy20qGkQWhfjLPnplbt4oOSgTt3HfafNm
hi7X4scpsp51229BQxhoKTapn5W5Qkd7rrtXI+9cRWNy+i78iHuKISusxixRPqFM
NUvkfyQ7DQKBgQCCSo3bEfTNKGB5rE7L8cW9FydMMxfFEGVO+nouHtJtqEB/fnXk
VJOleLOpcoqkWyvMIgYg6d5wmG9BcOaJ+kYe+MCVnoMrL8qz4NohgithfNKqZtAY
nqSx3TIx6tZM7+024tv6sGyyTM0Z8/3khGX6gKwMHwOGyv1osHI60FPuGQKBgHJx
kzqrooIACYUAKCTt/hCv/1iSsAhYQMBQFdd7kc+CRfg0+0U5S/2eBDQtL186RnCY
q7zQThx4BzNmqMQVxVPvM+XmL1T2658iUgbrrz4aPwoRrFfQs02KR1AwSjKmbniW
85NtgRo4pjXDOsYB9l59EOaZzu6ZnMsHPZy0nuE1AoGBAKrvh0cFyo+S4+s9HcGp
S3s+a4P+Yru/PKWeVs5l3fbbBF5jrX4PcjFk+jLwT2VTQ+el0ipWIm/6Hc2IKmG+
vGfRURP08SpuvR6SJeXPjayZ34sOcWDHigXAUGBw5Jz5i5lNTjeiqEVUYRMN0DzQ
iR1ggsJPAmoGDC2J7+mwMflk
-----END PRIVATE KEY-----', NULL, 0);

INSERT INTO Client.Merchantaccount_Tbl (Clientid, Pspid, Name, Username, Passwd)
VALUES (10106, 36, 'mVault', 'blank', 'blank');

---Configuration of MPI--
INSERT INTO Client.Mpi_Config_Tbl (Clientid, Pmid, Providerid)
VALUES (10106, 1, 47);
INSERT INTO Client.Mpi_Config_Tbl (Clientid, Pmid, Providerid)
VALUES (10106, 7, 47);
INSERT INTO Client.Mpi_Config_Tbl (Clientid, Pmid, Providerid)
VALUES (10106, 8, 47);
INSERT INTO Client.Mpi_Property_Tbl (Clientid, Version)
VALUES (10106, '2.0');
INSERT INTO Client.Routefeature_Tbl (Routeconfigid, Clientid, Featureid)
SELECT Id, 10106, 20
FROM Client.Routeconfig_Tbl
WHERE Routeid IN (SELECT Id FROM Client.Route_Tbl WHERE Clientid = 10106 AND Providerid IN (62))

--Account Mapping with First Data + Modirum +Mvault--
    INSERT INTO Client.Merchantsubaccount_Tbl (Accountid, Pspid, Name, Enabled)
VALUES (101060, 62, '-1', TRUE);--FIRST DATA
INSERT INTO Client.Merchantsubaccount_Tbl (Accountid, Pspid, Name, Enabled)
VALUES (101060, 36, '-1', TRUE);--mvault
INSERT INTO Client.Merchantsubaccount_Tbl (Accountid, Pspid, Name, Enabled)
VALUES (101060, 47, '-1', TRUE);--Modirum

INSERT INTO Client.Providerpm_Tbl (Pmid, Routeid, Enabled)
VALUES (7, (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 62 AND Clientid = 10106), TRUE);

INSERT INTO Client.Providerpm_Tbl (Pmid, Routeid, Enabled)
VALUES (8, (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 62 AND Clientid = 10106), TRUE);

--END: Entry in route tables for First Data 10106--

--START: Entry in route tables for First Data 10107--
INSERT INTO Client.Route_Tbl (Clientid, Providerid, Enabled)
VALUES (10107, 62, TRUE);

INSERT INTO Client.Routeconfig_Tbl(Routeid, Name, Capturetype, Mid, Username, Password, Enabled)
SELECT Id, 'FIRST DATA', 1, '520334509971578', 'WST837504011._.1', 'aPg.(7T7gg', Enabled
FROM Client.Route_Tbl
WHERE Clientid = 10107
  AND Providerid = 62;

INSERT INTO Client.Routecountry_Tbl(Routeconfigid)
SELECT Rc.Id
FROM Client.Routeconfig_Tbl Rc
         INNER JOIN Client.Route_Tbl R ON R.Id = Rc.Routeid
WHERE R.Clientid = 10107
  AND Mid = '520334509971578'
  AND Capturetype = 1;

INSERT INTO Client.Routecurrency_Tbl(Routeconfigid)
SELECT Rc.Id
FROM Client.Routeconfig_Tbl Rc
         INNER JOIN Client.Route_Tbl R ON R.Id = Rc.Routeid
WHERE R.Clientid = 10107
  AND Mid = '520334509971578'
  AND Capturetype = 1;

INSERT INTO Client.Merchantsubaccount_Tbl (Accountid, Pspid, Name, Enabled)
VALUES (101070, 62, '-1', TRUE);--FIRST DATA

INSERT INTO Client.Providerpm_Tbl (Pmid, Routeid, Enabled)
VALUES (7, (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 62 AND Clientid = 10107), TRUE);

INSERT INTO Client.Providerpm_Tbl (Pmid, Routeid, Enabled)
VALUES (8, (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 62 AND Clientid = 10107), TRUE);
--END: Entry in route tables for First Data 10107--

ALTER SEQUENCE Client.Route_Tbl_Id_Seq RESTART WITH 520;
ALTER SEQUENCE Client.Merchantaccount_Tbl_Id_Seq RESTART WITH 520;

--START: Entry in route tables for Amex 10106
INSERT INTO Client.Merchantaccount_Tbl (Clientid, Pspid, Name, Username, Passwd)
VALUES (10106, 45, 'AMEX', 'AMEX', 'dummy');

--Entry in route tables for AMEX 10106--
INSERT INTO Client.Route_Tbl (Clientid, Providerid)
VALUES (10106, 45);

INSERT INTO Client.Routeconfig_Tbl(Routeid, Name, Capturetype, Mid, Username, Password, Enabled)
SELECT Id, 'AMEX', 1, 'AMEX', 'AMEX', 'dummy', Enabled
FROM Client.Route_Tbl
WHERE Clientid = 10106
  AND Providerid = 45;

INSERT INTO Client.Routecountry_Tbl(Routeconfigid)
SELECT Rc.Id
FROM Client.Routeconfig_Tbl Rc
         INNER JOIN Client.Route_Tbl R ON R.Id = Rc.Routeid
WHERE R.Clientid = 10106
  AND Mid = 'AMEX'
  AND Capturetype = 1;

INSERT INTO Client.Routecurrency_Tbl(Routeconfigid)
SELECT Rc.Id
FROM Client.Routeconfig_Tbl Rc
         INNER JOIN Client.Route_Tbl R ON R.Id = Rc.Routeid
WHERE R.Clientid = 10106
  AND Mid = 'AMEX'
  AND Capturetype = 1;


INSERT INTO Client.Providerpm_Tbl (Pmid, Routeid, Enabled)
VALUES (1, (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), TRUE);
--END: Entry in route tables for Amex 10106--

--START: Entry in route tables for Amex 10107--
INSERT INTO Client.Merchantaccount_Tbl (Clientid, Pspid, Name, Username, Passwd)
VALUES (10107, 45, 'AMEX', 'AMEX', 'dummy');

--START: Entry in route tables for AMEX 10107--
INSERT INTO Client.Route_Tbl (Clientid, Providerid)
VALUES (10107, 45);

INSERT INTO Client.Routeconfig_Tbl(Routeid, Name, Capturetype, Mid, Username, Password, Enabled)
SELECT Id, 'AMEX', 1, 'AMEX', 'AMEX', 'dummy', Enabled
FROM Client.Route_Tbl
WHERE Clientid = 10107
  AND Providerid = 45;

INSERT INTO Client.Routecountry_Tbl(Routeconfigid)
SELECT Rc.Id
FROM Client.Routeconfig_Tbl Rc
         INNER JOIN Client.Route_Tbl R ON R.Id = Rc.Routeid
WHERE R.Clientid = 10107
  AND Mid = 'AMEX'
  AND Capturetype = 1;

INSERT INTO Client.Routecurrency_Tbl(Routeconfigid)
SELECT Rc.Id
FROM Client.Routeconfig_Tbl Rc
         INNER JOIN Client.Route_Tbl R ON R.Id = Rc.Routeid
WHERE R.Clientid = 10107
  AND Mid = 'AMEX'
  AND Capturetype = 1;

INSERT INTO Client.Providerpm_Tbl (Pmid, Routeid, Enabled)
VALUES (1, (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), TRUE);
--END: Entry in route tables for Amex 10107--

---START: Account Mapping with AMEX 10106--
INSERT INTO Client.Merchantsubaccount_Tbl (Accountid, Pspid, Name, Enabled)
VALUES (101060, 45, '-1', TRUE);

--PSP Additonal Properties--
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_COUNTRY_CODE', '826', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_CARD_ACCEPTOR_CITY', 'Crawley, West Sussex', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_CARD_ACCEPTOR_ADDRESS', 'The VHQ, Fleming Way', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_CARD_ACCEPTOR_NAME', 'Virgin Atlantic Holidays Ltd', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_CARD_ACCEPTOR_COUNTRY', 'GB', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_CARD_ACCEPTOR_IDENTIFICATION_CODE', '9421667635', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_SFTP_USERNAME', 'CPMPROD', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_SFTP_PASSWORD', 'C%1Pm05*!vwk97x', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_ORIGIN', 'Cellpoint Mobile', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_MESSAGE_TYPE', 'ISO GCAG', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_CARD_ACCEPTOR_BUSINESS_CODE', '4722', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_CARD_ACCEPTOR_TERMINAL_ID', '208752', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_ROUTING_INDICATOR', '000', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_SUBMITTER_ID', 'CPMVHOLS', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_SFTP_HOST', 'https://fsgateway.americanexpress.com', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_SFTP_FILENAME', 'CPMPROD.SETTLEMENT.CPMVHOLS', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_REGION', 'EMEA', TRUE, (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106),
        'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_MESSAGE_REASON_CODE', '1900', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_CARD_ACCEPTOR_REGION', 'GB', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_MERCHANT_NUMBER', '9421667635', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_CARD_ACCEPTOR_ZIP', 'RH10 9DF  ', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10106), 'merchant', 1);
--Client Additonal Properties--

---END: Account Mapping with AMEX 10106--


---START: Account Mapping with AMEX 10107--
---Account Mapping with AMEX--
INSERT INTO Client.Merchantsubaccount_Tbl (Accountid, Pspid, Name, Enabled)
VALUES (101070, 45, '-1', TRUE);

--PSP Additonal Properties--
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_COUNTRY_CODE', '826', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_CARD_ACCEPTOR_CITY', 'Crawley, West Sussex', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_CARD_ACCEPTOR_ADDRESS', 'The VHQ, Fleming Way', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_CARD_ACCEPTOR_NAME', 'Virgin Atlantic Holidays Ltd', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_CARD_ACCEPTOR_COUNTRY', 'GB', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_CARD_ACCEPTOR_IDENTIFICATION_CODE', '9421668054', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_SFTP_USERNAME', 'CPMPROD', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_SFTP_PASSWORD', 'C%1Pm05*!vwk97x', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_ORIGIN', 'Cellpoint Mobile', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_MESSAGE_TYPE', 'ISO GCAG', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_CARD_ACCEPTOR_BUSINESS_CODE', '4722', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_CARD_ACCEPTOR_TERMINAL_ID', '208752', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_ROUTING_INDICATOR', '000', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_SUBMITTER_ID', 'CPMVHOLS', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_SFTP_HOST', 'https://fsgateway.americanexpress.com', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_SFTP_FILENAME', 'CPMPROD.SETTLEMENT.CPMVHOLS', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_REGION', 'EMEA', TRUE, (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107),
        'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_MESSAGE_REASON_CODE', '1900', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_CARD_ACCEPTOR_REGION', 'GB', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_MERCHANT_NUMBER', '9421668054', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
INSERT INTO Client.Additionalproperty_Tbl (Key, Value, Enabled, Externalid, Type, Scope)
VALUES ('AMEX_CARD_ACCEPTOR_ZIP', 'RH10 9DF  ', TRUE,
        (SELECT Id FROM Client.Route_Tbl WHERE Providerid = 45 AND Clientid = 10107), 'merchant', 1);
--Client Additonal Properties--

---END: Account Mapping with AMEX 10107--


-- START: Property Migration - 10106--
-- Client Config--
INSERT INTO Client.Client_Property_Tbl (Propertyid, Value, Clientid)
SELECT DISTINCT Sp.Id, Ap.Value, Ap.Externalid
FROM Client.Additionalproperty_Tbl Ap
         INNER JOIN System.Client_Property_Tbl Sp ON Ap.Key = Sp.Name
WHERE Ap.Externalid = 10106
  AND Ap.Type = 'client'
    ON CONFLICT (Propertyid, Clientid) DO NOTHING;

-- PSP Config--
INSERT INTO Client.Psp_Property_Tbl (Propertyid, Value, Clientid)
SELECT DISTINCT Sp.Id, Ap.Value, Rt.Clientid
FROM Client.Route_Tbl Rt
         INNER JOIN Client.Additionalproperty_Tbl Ap ON Ap.Externalid = Rt.Id
         INNER JOIN System.Psp_Property_Tbl Sp ON Ap.Key = Sp.Name AND Rt.Providerid = Sp.Pspid
WHERE Ap.Type = 'merchant'
  AND Rt.Clientid = 10106
  AND Ap.Enabled = TRUE
    ON CONFLICT (Propertyid, Clientid) DO NOTHING;

-- Route Config--
INSERT INTO Client.Route_Property_Tbl (Propertyid, Value, Routeconfigid)
SELECT DISTINCT Sp.Id, Ap.Value, Rc.Id
FROM Client.Route_Tbl Rt
         INNER JOIN Client.Additionalproperty_Tbl Ap ON Ap.Externalid = Rt.Id
         INNER JOIN System.Route_Property_Tbl Sp ON Ap.Key = Sp.Name AND Rt.Providerid = Sp.Pspid
         INNER JOIN Client.Routeconfig_Tbl Rc ON Rt.Id = Rc.Routeid
WHERE Ap.Type = 'merchant'
  AND Rt.Clientid = 10106
  AND Ap.Enabled = TRUE
    ON CONFLICT (Propertyid, Routeconfigid) DO NOTHING;


-- END: Property Migration - 10106--

-- START: Property Migration - 10107--
-- Client Config--
INSERT INTO Client.Client_Property_Tbl (Propertyid, Value, Clientid)
SELECT DISTINCT Sp.Id, Ap.Value, Ap.Externalid
FROM Client.Additionalproperty_Tbl Ap
         INNER JOIN System.Client_Property_Tbl Sp ON Ap.Key = Sp.Name
WHERE Ap.Externalid = 10107
  AND Ap.Type = 'client'
    ON CONFLICT (Propertyid, Clientid) DO NOTHING;

-- PSP Config--
INSERT INTO Client.Psp_Property_Tbl (Propertyid, Value, Clientid)
SELECT DISTINCT Sp.Id, Ap.Value, Rt.Clientid
FROM Client.Route_Tbl Rt
         INNER JOIN Client.Additionalproperty_Tbl Ap ON Ap.Externalid = Rt.Id
         INNER JOIN System.Psp_Property_Tbl Sp ON Ap.Key = Sp.Name AND Rt.Providerid = Sp.Pspid
WHERE Ap.Type = 'merchant'
  AND Rt.Clientid = 10107
  AND Ap.Enabled = TRUE
    ON CONFLICT (Propertyid, Clientid) DO NOTHING;

-- Route Config--
INSERT INTO Client.Route_Property_Tbl (Propertyid, Value, Routeconfigid)
SELECT DISTINCT Sp.Id, Ap.Value, Rc.Id
FROM Client.Route_Tbl Rt
         INNER JOIN Client.Additionalproperty_Tbl Ap ON Ap.Externalid = Rt.Id
         INNER JOIN System.Route_Property_Tbl Sp ON Ap.Key = Sp.Name AND Rt.Providerid = Sp.Pspid
         INNER JOIN Client.Routeconfig_Tbl Rc ON Rt.Id = Rc.Routeid
WHERE Ap.Type = 'merchant'
  AND Rt.Clientid = 10107
  AND Ap.Enabled = TRUE
    ON CONFLICT (Propertyid, Routeconfigid) DO NOTHING;

-- END: Property Migration - 10107--
