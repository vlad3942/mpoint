/* ========== Global Configuration for AliPay Chinese - Payment Method : START========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength,paymenttype) VALUES (40, 'AliPay Chinese', 23, -1, -1, -1,4);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (40, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 40, id FROM System.PricePoint_Tbl WHERE amount = -1 AND currencyid = 156;


INSERT INTO System.PSP_Tbl (id, name, system_type) VALUES (43, 'AliPay - Chinese',4);
INSERT INTO System.PSPCurrency_Tbl (pspid, name,currencyid) VALUES (43,'CNY',156);


INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (40, 43);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 43, 'AliPay Chinese', '2088102135220161', 'MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAIBQnVnYfs/oHSZJZhE01Z9ukKRmEU1OrDFGggD9F9YLYQP+kPLSg2DcVVC1Xl4Yyjp2RhfSODXmQD+io2Pt+HUZ+3CMlkI0e1qiQhnfLNbNEIjq+RVsIFZNqPlo3Lg/hBqlPhqk4YfqOuoagMthyuSBZJZ3UwXsRHgdzfBAzyI/AgMBAAECgYB/uL6HefnwVOj+/Tx9kAu7YMDVA0vhmZfIjJhHB6Y8RqNQ6Im7SlO/jFHXvlCqdR6GxsfKWlPdQs1dCjR8+Zi+/jEPaGDmvYa7p4kNXgJ+6zY4rSMt1MC0Py5fVZ4J+75HdfSwmbcm3u8LkREidRBn0EKbwQ0SwQOZqb/T6scr8QJBALlJxw7Xh0Mx6Gs+L55j2iO+m5mhvrBZGt0nmCcz+HFoSo3oO5rHeBGUJF1eGrbbValC0j2wL0n1Wa7fVK0xKrsCQQCxSK4Lrdvrw3T3/t1kjVFXgkZ3JhWpFKPBroX/AsBhANcRVKMG5oh43dw5jMJFgQmWQ6QsKh5q52dnqoueL1hNAkBDjoHUiILZ3h2G1IKaNn/3nmyvREj5lVN1JRWV3Z4NA2CDgxQQaAAAMMpdfI0y9J+z+hgbw9xKE/niB62hBBc3AkACDDnebqqspXxTZQE/qRY4cYvI0orLgi6GDTMFCA4a0LyrOZQMf1syMjXaAFM6JExtDOj3jaD+UR/zpZepQxi9AkEAimPiAgmD9DQVSWXLyi2DLvJ8flOV6PFx3Fq0hx9P0VbScKdJy2ETSH4gTnm+CIfE/5VJP16+jfSVV3BlODhdug==');

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 43, '-1');

-- Route AliPay Card to AliPay with country CHN
INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, countryid) VALUES (10001, 40, 43, true, 609);

/* ========== Global Configuration for AliPay Chinese ENDS ========== */