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

-- PSP config for SDK integration
--app id
INSERT INTO client.merchantsubaccount_tbl(
  accountid, pspid, name, enabled)
VALUES (100071, 43, '2016080600179506', true);


INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) SELECT 'key.app', 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDAtoXgD2JA0COk6Aa18Bhg5+tdKlq4Fs/5QJ4SrGXkElNIuHok1lKeIGfUc2eJK7IfVz5Lrl3GEAm4HyX+tytegvUjH68Cxria8ZKu9SzBVMpUc7RDKsfVNr9YmITWfMM59QWp4BaJAGPi9u4GXnlv8hiiJYaENPkTZFTCY7FX2ylCjhkfB7OLp8DVU6dqZm8DpeAuScpLCHfT2q7XCG5pd78GDYDUxXG8kQEWvSXqYMYHlSxRQUrD13rtgLdJQL2TfP1rROoR0GO6tw5683idx+l9jDvVNWbQ34mKWW33Vz4R0qDwQn0gkql96yAN9XuREUpiXsaddL5HT6PWUxd7AgMBAAECggEAP5X55kwtJyWGHUtRq4ZlBNSBHGR1OniMdrmTbqXjmLVTNZNo+e6do/8dQ0QwzVnVk/G9ZEtMNaXlDxN3/euCK9UZ/VTe8hOPpdA/jernsYLAn8ztlZvwA7HkwN7SNdNEt0LZc4u04891JdZEA2X4u68t4ZJwJ/8yj+ty7BDo2wuqKMJs4SMsqtW8mhUdto9C4AROul3GebHTC5/4dVfciCQ5ATZNkNqU8pLHCfQxNyM2P9gawQaycNrqhx5MJ2pSG0GpYxieo3xxN5k4L99DkON3WjExJerUJ9MQbYHmDM0ceh/5niLKxUOTttxYaeEsSRX7Sc+0owIoyQV/Neq2AQKBgQDxB+yjUKvZBhc2rlA/KENjO2VvPw+MJbNJqFlbTLmSoqsVnid0CP3ZpqICvVEzXNoMga+T2CWW3mfnIoisPSwoeiASRgTBrL3iiXTxCXmHGrTbJ61aw2/EKAxjCK1IFou+8wjNbkya8zj4G6BrXn6OoVNVgq2RIRry1y84U8h5GwKBgQDMrmf/2bkrJKXzYHtP3906Y00NaRC//7q/Y9JZca4E+aZKcUh3u61itAHSWRlq++e8/blV4PzJTk+GgmlfLSbwJMc48MH77WroYMYMSbRu59tdKVnFzbk0Mlo0CxGKMOG8+YPKoJgcO8QKSZJo2BOAj958Hxy5AVZXGNMiWo0hIQKBgAkme91XWq7KhGcXBwTeynAh+R/YDQcNB1lsgrfsmb7vXf9cGbNWBA0XPl9MQKDqjXycD8ZVFlg76UXlEbs4N0zyFfWbouKXZD4NadscuPhgEy2eu/4OHVgdDRtVYP6znGqLX3ItFctsIGWK5vQsijFv/nHonB4+W3+Mm8ZPp/SxAoGAOzSTtqk266jdK+oToUYjCvmgVym2A6OoVCY+uUqtyJiiJlRgXun1vGBPSpYlSRH2tW87BgFffadeT403h6Va5wnsaqcRpZrGWtNrVjCXtaDxjiAg7JuWX+fUucsd1rhPA8e0/I65kSkkisk/RX6DHaP/+i1RtJ4TaHwwznYc7qECgYEA43aTdt0OIlwfb/VsuIIOQxabAmUq4wro7DTkmlIN1HHB1U4qU3qL2ScRViTpHlkIycOaknB27NrzNTd4JM3+ahdHzYCrRyVrd1vLEKMecM0cgWWr8RQz4RK2jO+tUG4v6V4Q5sJJHJfFxNGyaSMM9G4Tk8BYAcFs5ZwjhNYkMh8=', id, 'merchant' FROM client.merchantaccount_tbl WHERE clientid=10007 AND pspid=43 ;


INSERT INTO client.additionalproperty_tbl (key, value, externalid, type) select 'key.html', 'MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAIBQnVnYfs/oHSZJZhE01Z9ukKRmEU1OrDFGggD9F9YLYQP+kPLSg2DcVVC1Xl4Yyjp2RhfSODXmQD+io2Pt+HUZ+3CMlkI0e1qiQhnfLNbNEIjq+RVsIFZNqPlo3Lg/hBqlPhqk4YfqOuoagMthyuSBZJZ3UwXsRHgdzfBAzyI/AgMBAAECgYB/uL6HefnwVOj+/Tx9kAu7YMDVA0vhmZfIjJhHB6Y8RqNQ6Im7SlO/jFHXvlCqdR6GxsfKWlPdQs1dCjR8+Zi+/jEPaGDmvYa7p4kNXgJ+6zY4rSMt1MC0Py5fVZ4J+75HdfSwmbcm3u8LkREidRBn0EKbwQ0SwQOZqb/T6scr8QJBALlJxw7Xh0Mx6Gs+L55j2iO+m5mhvrBZGt0nmCcz+HFoSo3oO5rHeBGUJF1eGrbbValC0j2wL0n1Wa7fVK0xKrsCQQCxSK4Lrdvrw3T3/t1kjVFXgkZ3JhWpFKPBroX/AsBhANcRVKMG5oh43dw5jMJFgQmWQ6QsKh5q52dnqoueL1hNAkBDjoHUiILZ3h2G1IKaNn/3nmyvREj5lVN1JRWV3Z4NA2CDgxQQaAAAMMpdfI0y9J+z+hgbw9xKE/niB62hBBc3AkACDDnebqqspXxTZQE/qRY4cYvI0orLgi6GDTMFCA4a0LyrOZQMf1syMjXaAFM6JExtDOj3jaD+UR/zpZepQxi9AkEAimPiAgmD9DQVSWXLyi2DLvJ8flOV6PFx3Fq0hx9P0VbScKdJy2ETSH4gTnm+CIfE/5VJP16+jfSVV3BlODhdug==', id, 'merchant' from client.merchantaccount_tbl WHERE clientid=10007 AND pspid=43 ;
/* ========== Global Configuration for AliPay Chinese ENDS ========== */