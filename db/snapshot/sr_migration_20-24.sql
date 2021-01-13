-- Migration of existing merchant route details
INSERT into client.route_tbl (id, clientid, providerid)
SELECT id, clientid, pspid FROM client.merchantaccount_tbl WHERE clientid=10018;

-- Migration of existing merchant configuration
INSERT into client.routeconfig_tbl (routeid, name, capturetype, mid, countryid, currencyid, username, password, enabled, created, modified)
SELECT ROT.id, SP.name, CA.capture_type, MA.name, CA.countryid, null, MA.username, MA.passwd, MA.enabled, MA.created, MA.modified  FROM Client.MerchantAccount_Tbl MA
LEFT JOIN Client.CardAccess_tbl CA ON MA.pspid = CA.pspid AND MA.clientid = CA.clientid
INNER JOIN Client.Route_Tbl ROT ON ROT.id = MA.id AND ROT.clientid = MA.clientid
INNER JOIN System.Psp_Tbl SP ON SP.id = MA.pspid
WHERE MA.clientid = 10018

========= Migration of existing additionalproperty_tbl  ==========================

------- Maybank

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (27, 10018, 'Maybank_AMEX', 2, '02701700290875100472', null, null, null, '6sjhPN9X');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (27, 10018, 'Maybank_Mastercard', 2, '02700770202075001284', null, null, null, '4GkR2Hkk');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (27, 10018, 'Maybank_VISA', 2, '02700770202075001284', null, null, null, '4GkR2Hkk');


------- Publicbank

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_Mastercard_MYR', 2, '5500003631', null, 458, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_Mastercard_HKD', 2, '5500003798', null, 344, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_Mastercard_SGD', 2, '5500003658', null, 702, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_Mastercard_AUD', 2, '5500003771', null, 36, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_Mastercard_LKR', 2, '5500003895', null, 144, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_Mastercard_CNY', 2, '5500003909', null, 156, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_Mastercard_THB', 2, '5500003887', null, 764, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_Mastercard_TWD', 2, '5500004077', null, 901, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_Mastercard_SAR', 2, '5500004492', null, 682, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_Mastercard_USD', 2, '5500003666', null, 840, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_Mastercard_IDR', 2, '5500004239', null, 360, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_VISA_MYR', 2, '3300004667', null, 458, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_VISA_HKD', 2, '3300004802', null, 344, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_VISA_SGD', 2, '3300004675', null, 702, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_VISA_AUD', 2, '3300004799', null, 36, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_VISA_LKR', 2, '3300004918', null, 144, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_VISA_CNY', 2, '3300004942', null, 156, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_VISA_THB', 2, '3300004896', null, 764, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_VISA_TWD', 2, '3300005116', null, 901, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_VISA_SAR', 2, '3300005574', null, 682, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_VISA_USD', 2, '3300004683', null, 840, 'sandbox', 'APPLE001');

INSERT INTO client.routeconfig_tbl( routeid, clientid, name, capturetype, mid, countryid, currencyid, username, password)
VALUES (28, 10018, 'PUBLICBANK_VISA_IDR', 2, '3300005302', null, 360, 'sandbox', 'APPLE001');


-- Add route feature for malindo
INSERT INTO client.routefeature_tbl( clientid, routeconfigid, featureid)
VALUES (10018, <routeconfigid>, 9);

INSERT INTO client.routefeature_tbl( clientid, routeconfigid, featureid)
VALUES (10018, <routeconfigid>, 11);
