UPDATE Client.Client_Tbl SET salt = 'Fh17_8sFgd' WHERE id = 10005;
UPDATE Client.Client_Tbl SET salt = 'Fh17_8sFgd' WHERE id = 10014;
UPDATE Client.Client_Tbl SET salt = '8sFgd_Fh17' WHERE id = 10019;


/**
 * CMP-1146 Support "Payment Settled" state for mConsole Search transaction API
 */ 
INSERT INTO Log.State_Tbl (id, name) VALUES (2020, 'Payment Settled');


/* ========== Global Configuration for MobilePay Online- Payment Method : START========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (30, 'MobilePay Online', 23, -1, -1, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (30, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 30, id FROM System.PricePoint_Tbl WHERE amount = -1 AND countryid = 103;

INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (30, 24);
/* ========== Global Configuration for MobilePay Online- Payment Method : END========== */

/* ============= Configure / Update PayFort accounts per environment (UAT/SIT/DEV/POC) =============== */
-- UAT
Update Client.merchantaccount_tbl SET passwd = 'thE78UJRWmnGyxPSQGAT' WHERE username = 'CTjbJcSI' AND pspid = 23;
-- SIT
Update Client.merchantaccount_tbl SET passwd = 'DhZyZO6VP6A1z325jphn' WHERE username = 'CTjbJcSI' AND pspid = 23;
-- DEV PUNE
Update Client.merchantaccount_tbl SET passwd = 'BMMVFHwUGyfjDZk2PzMc' WHERE username = 'CTjbJcSI' AND pspid = 23;
-- POC
Update Client.merchantaccount_tbl SET passwd = 'rYBDTQunZRTgG2cVmMJZ' WHERE username = 'CTjbJcSI' AND pspid = 23;



/* ========== Global Configuration for SADAD - Payment Method : START========== */
INSERT INTO System.Card_Tbl (id, name, position, minlength, maxlength, cvclength) VALUES (31, 'SADAD', 23, -1, -1, -1);
INSERT INTO System.CardPrefix_Tbl (cardid, min, max) VALUES (31, 0, 0);
INSERT INTO System.CardPricing_Tbl (cardid, pricepointid) SELECT 31, id FROM System.PricePoint_Tbl WHERE amount = -1 AND countryid = 608;

INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (31, 23);


/**
 * CMP-1276 Support "Payment Settled" state for mConsole Search transaction API
 */ 
INSERT INTO Log.State_Tbl (id, name) VALUES (1998, 'Account Validated');
INSERT INTO Log.State_Tbl (id, name) VALUES (19980, 'Account Validated and Cancelled');
INSERT INTO Log.State_Tbl (id, name) VALUES (1997, 'Account Validation Failed');


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

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10001, 26, '8JkZO3BrCSwSvQsvkAa6raNlCX6KCMDoIolcU6zXE6TKG+E67TKz8RoA7BkXLA9tE22cdVNqeSGCcSbzR26MpN7ECOX7tI3j3mPaDlBf1+48c8noTUQTYWmFoAOkZZWhfXqKazoPpW6ruIHk972Bf+v+DdUJzW59I3g7FPdOTwl7LtlcEiQXJQ3YZevUurARGWQH3gJUWfKJglhNzcd8CsjLTLJlvUocGVnubhCXUDtx3x3+UEuxhL/BCpSoJ5soQw/UJaG5Chd0TIcD3reERIRHWkirMeozsXkb3UB+GVFWNnA0GWWResta7HU0N6jfREi0QYdmHj3EX2zQRc4LFg5hdDiUDAzy+9aOTZqczSd3Qz5xRRXqKwWyXgNsyFl3F0ib1RILePfgXdStu7q0fiirZdBPoIUH6aw7RbqDu52Q0kOKGZqwJJZreMS7MlrkACG0tw7uCjrCU8kxa8+P/4Z93bB7FSAvaALYiBdRiT2llZvFWV2MHEIWCfeZDOoj2IzKe4mHWvFcymW6oYWt8CbaNRBnEvpeNjivsaH/RxYTywyJ6h9NiZvi+pwkOhGmNZcL4MrquIaikOgt3z2NCh6Df08Z0M7tvTPmpG0dYscajavepnpu3GnpDH2cWu7C02qp/VwVl1tD6sQeHO8V/0y70Y5XmrO5HPpdA2goUv3mpo+lnZ0PiYhZBqkW8q1URTCoS+FVFk5ydwruDjzcbYf0GN1pM2H1WMUCBSguvb+E5y958ErcbcfavwHe5BOWtRL7k52evbHPRvj1LCoQskcHRZd+koxuOc+Pum1SW06XfSfu5KFS3Yc5Dv/Vy4H654ThyeEFpVKfJ1sQSUUl3ayLShlsYaOfafzynwz7I/LHybaouBkRiJ9+4iObKY8ZW5ii7d0aOic23lFcNge7k6Jq7geUnTqW7GhghmAfEcU27of5nkNUx1jo1+ra1U+1Wn9EgmexDJuDZPkWiWwqkOdVGBkQT+m/whn34+TPlqee/PkEWZLTwJzltWyq/rvWT3k42hzKNT3HJHLh7gXoVCxFZWasPRKlm/upSrl4YYB+jwM50KPyBXOg8hWLpLCPTV+wU4Pyll5VGA+g0d4BuNAoFJyE0zRTIUw7nAH1XGjCmqEqxK5Sh4zMJWZipqtkRVYKTpr/7Q4bRtRnnANDlucb8g4tJpqkkp+3hc7DkQV6HfVVTUHLxlxhCfLj2u/dCDVY7IOK+cmN23KBnSCHi0eEtaBy7Pa+rgT8h2EOLqTwCqRCBuhlme1huvqk0Q4A8dlOB+4KSVIw4Sy0L7C9d9MXKo+gV+y5L6PB4/pr/TF3uInNG9cIHO4ARMAQDxM2NDrljBJCfkbXOHp4tXpl+tCUGIy1aOpIpn0yig8obNb6iFiNaeTr8NhGvnylKtM80EipgcMqWJb8Ifa4wRCrZXxICumymp9WcQLNn6IpBe8pqNEkYS1z9rTIGA/WOKFkd5sLNyz0SU+Fx4VoKJ7S8p3zJcHnw7cokD4Z1umP5p/dFJ0gXv68q6tKqsfSjIEq9lkI+AFPYssHHl6i2hOJkfyIyfBTqzQkunmNSH5CB3fnf0PJe7r7rw==', '764764000000278', 'gXPRPPam3j58');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100001, 26, '-1');

-- Route VISA Card to 2C2P with country Thailand
UPDATE Client.CardAccess_Tbl SET pspid = 26, countryid = 644 WHERE clientid = 10001 AND cardid = 8;
/* ========== Global Configuration for 2C2P = ENDS ========== */


/*Myanmar*/
INSERT INTO System.Country_Tbl (id, name, currency, minmob, maxmob, channel, priceformat, decimals, addr_lookup) VALUES (652, 'Myanmar', 'MMK', '1000000', '99999999', '123', '', 0, false);
INSERT INTO System.PricePoint_Tbl (id, countryid, amount) VALUES (-652, 652, -1);
INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT -652, id FROM System.Card_Tbl WHERE id > 0;
INSERT INTO System.PSPCurrency_Tbl (countryid, pspid, name) VALUES (652, 9, 'MMK');