
/* ========== Global Configuration for 2C2P = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name) VALUES (26, '2C2P');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (644,1,'THB');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (644,26,'THB');

/*Amex*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (1, 26);
/*MasterCard*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (7, 26);
/*VISA*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (8, 26);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 26, 'gXPRPPam3j58', '764764000000278', '-----BEGIN CERTIFICATE-----
MIIFJzCCBA+gAwIBAgIKHoPmwQAAAAAADDANBgkqhkiG9w0BAQUFADAWMRQwEgYD
VQQDEwtTaW5hcHRJUSBDQTAeFw0xMTA4MTkwMzA2MDFaFw0xNjA4MTkwMzE2MDFa
MHgxCzAJBgNVBAYTAlNHMRIwEAYDVQQIEwlTaW5nYXBvcmUxEjAQBgNVBAcTCVNp
bmdhcG9yZTEWMBQGA1UEChMNMkMyUCBQdGUgTHRkLjEQMA4GA1UECxMHMkMyUCBJ
VDEXMBUGA1UEAxMOZGVtbzIuMmMycC5jb20wggEiMA0GCSqGSIb3DQEBAQUAA4IB
DwAwggEKAoIBAQDPA/vOQADihqx7hKIQOuCt/1K0inz56m+HkwQ9CWhMQoF2tbKQ
gcvl4KUlWQ2V6jBGKRO/ouc11gz6OvC5NYfE33eoPyIoQcWQylQntCANCVsOupF/
wqNCcCFGmKivBSmE+vuvpm/BLI4PjzTNSfcE98ps0TRsQj5ey0nv8La9hkjqvUt0
McIC+X2J/yjMuF0rr7inpbZiQ8eXPgYWL2v/+XUFxjzC+xJVFBXAOJGypkjuWWe/
cWH+aSdXmZ44v9h2MkomOSzf0r8CGO6KpoCUXcsxkXFM1KqFZsn610SSpG5YrN5N
rTmeE4PF78bZVfAry9uIHh8pNUutpMDLc4nRAgMBAAGjggITMIICDzAOBgNVHQ8B
Af8EBAMCBPAwEwYDVR0lBAwwCgYIKwYBBQUHAwEweAYJKoZIhvcNAQkPBGswaTAO
BggqhkiG9w0DAgICAIAwDgYIKoZIhvcNAwQCAgCAMAsGCWCGSAFlAwQBKjALBglg
hkgBZQMEAS0wCwYJYIZIAWUDBAECMAsGCWCGSAFlAwQBBTAHBgUrDgMCBzAKBggq
hkiG9w0DBzAdBgNVHQ4EFgQU4glatjPrSNy1IxesBZi+iJCScJEwHwYDVR0jBBgw
FoAUV8ha5F0AzG5urok9LcTApE02DZAweQYDVR0fBHIwcDBuoGygaoYzaHR0cDov
L3cyazNzdHcxMDMzbzExZy9DZXJ0RW5yb2xsL1NpbmFwdElRJTIwQ0EuY3JshjNm
aWxlOi8vXFxXMkszU1RXMTAzM08xMUdcQ2VydEVucm9sbFxTaW5hcHRJUSBDQS5j
cmwwgbIGCCsGAQUFBwEBBIGlMIGiME8GCCsGAQUFBzAChkNodHRwOi8vdzJrM3N0
dzEwMzNvMTFnL0NlcnRFbnJvbGwvVzJLM1NUVzEwMzNPMTFHX1NpbmFwdElRJTIw
Q0EuY3J0ME8GCCsGAQUFBzAChkNmaWxlOi8vXFxXMkszU1RXMTAzM08xMUdcQ2Vy
dEVucm9sbFxXMkszU1RXMTAzM08xMUdfU2luYXB0SVEgQ0EuY3J0MA0GCSqGSIb3
DQEBBQUAA4IBAQAKrBRCdb/6eGrw5iK+VJ+YDTS1X+6p3ios0lTHO0OnHoSLa3q6
mSSCdGMIQQiizBkFXU+NgqpGPs8Ef3OUA3Y4NfH9q7f3jiAt/Us+KCTkmVnnHCA7
z+/Teo3irXFgY32RgDe8AwbENPZkkQpG99YuzntIScX9nReJU5k4oGBwV+RJReb3
lk0NCLRX9NIg5oDYrhrnoqnOVguGRulVv8/l3zcE1G+FHrA9suixvAs+KDgyTIaS
AwkGxB7OQomWYE/YQMbQSgzwoJ9axbclJOLvzSoigEdMy/kApMGu2KuPVYxfD9qz
ZcwraxkBhlV1WYGe0lK7QwwCRddSyLVRmx/r
-----END CERTIFICATE-----');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 26, '-1');

-- Route VISA Card to 2C2P with country Thailand
UPDATE Client.CardAccess_Tbl SET pspid = 26, countryid = 644 WHERE clientid = 10001 AND cardid = 8;
/* ========== Global Configuration for 2C2P = ENDS ========== */


/*Myanmar*/
INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (652, 'Myanmar', 'MMK', '1000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-652, 652, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -652, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (652, 9, 'MMK');


/* ========== Global Configuration for MayBank = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name) VALUES (27, 'MayBank');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (638,1,'MYR');
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (638,27,'MYR');

/*Amex*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (1, 27);
/*MasterCard*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (7, 27);
/*VISA*/
INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (8, 27);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 27, 'MayBank', '02700770202075001284', '4GkR2Hkk');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 27, '-1');

-- Route VISA Card to MayBank with country Malaysia
UPDATE Client.CardAccess_Tbl SET pspid = 27, countryid = 638 WHERE clientid = 10001 AND cardid = 8;
-- Route Master Card to MayBank with country Malaysia
UPDATE Client.CardAccess_Tbl SET pspid = 27, countryid = 638 WHERE clientid = 10001 AND cardid = 7;
-- Route AMEX Card to MayBank with country Malaysia
UPDATE Client.CardAccess_Tbl SET pspid = 27, countryid = 638 WHERE clientid = 10001 AND cardid = 1;

/* ========== Global Configuration for MayBank = ENDS ========== */
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (652, 9,'MMK');
