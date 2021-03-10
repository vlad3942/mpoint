--Table Name : Client.CardAccess_Tbl
--REF - CTECH-4242

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) 
values (10077, 7, true, 64, NULL, 1, NULL, false, 10, 0, 0, 1, NULL, true);

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) 
values (10077, 8, true, 64, NULL, 1, NULL, false, 10, 0, 0, 1, NULL, true);

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd, enabled, stored_card) 
values (10077, 64, 'CyberSourceFraud', 'cebupacific' ,
'+h47/kx47rCpe64cFG24WJw/MKiWLpNzodO1DAJx0uWDsHrif7w0tzlv9CfChqEzHwGJLK52J+H2kcWMj0eE0W6a5oZmv9ep8uSNsrwnTWKw8JpWdn0CjZLLOcmABvnVEG/hKX8lZiVdbB8p3ccDoapRfxboVGuzXOEoAsQ++nsIhTa/cpROp7rbrX9FsT+60YtYAOfixGsRfrTenYvoq4XgkF/h70e3ODO5IWOKsCjDTtTPTgP3SeqctmYsLG9r1Yrm+Ho8ZMrKlmwC1FjaQVuF9ZOGQGwdSvyELT4Ioa2hGRL5W3G0xRHunwZB7UwFCZuiUjCAFd8x6UBjWabv6A==', 
true, null);

INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name, enabled) 
values (100770, 64, '-1', true);

-- Ref: CTECH-4286
--mPoint DB Script

--Table Name : Client.CardAccess_Tbl

--Australia - AUD
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 15, true, 56, 500, 1, NULL, false, 3, 0, 0, 2, NULL, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 7, true, 56, 500, 1, NULL, false, 1, 0, 0, 2, 14, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 8, true, 56, 500, 1, NULL, false, 1, 0, 0, 2, 14, false);

--Newzealand  - NZD
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 15, true, 56, 513, 1, NULL, false, 3, 0, 0, 2, NULL, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 7, true, 56, 513, 1, NULL, false, 1, 0, 0, 2, 14, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 8, true, 56, 513, 1, NULL, false, 1, 0, 0, 2, 14, false);

--China - CNY
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 15, true, 56, 609, 1, NULL, false, 3, 0, 0, 2, NULL, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 7, true, 56, 609, 1, NULL, false, 1, 0, 0, 2, 14, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 8, true, 56, 609, 1, NULL, false, 1, 0, 0, 2, 14, false);

--Hongkong. - HKD
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 15, true, 56, 614, 1, NULL, false, 3, 0, 0, 2, NULL, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 7, true, 56, 614, 1, NULL, false, 1, 0, 0, 2, 14, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 8, true, 56, 614, 1, NULL, false, 1, 0, 0, 2, 14, false);

--Taiwan - TWD
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 15, true, 56, 646, 1, NULL, false, 3, 0, 0, 2, NULL, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 7, true, 56, 646, 1, NULL, false, 1, 0, 0, 2, 14, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 8, true, 56, 646, 1, NULL, false, 1, 0, 0, 2, 14, false);

--Macau - MOP
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 15, true, 56, 636, 1, NULL, false, 3, 0, 0, 2, NULL, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 7, true, 56, 636, 1, NULL, false, 1, 0, 0, 2, 14, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 8, true, 56, 636, 1, NULL, false, 1, 0, 0, 2, 14, false);

--UK - GBP
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 15, true, 56, 103, 1, NULL, false, 3, 0, 0, 2, NULL, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 7, true, 56, 103, 1, NULL, false, 1, 0, 0, 2, 14, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 8, true, 56, 103, 1, NULL, false, 1, 0, 0, 2, 14, false);

--UAE - AED
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 15, true, 56, 647, 1, NULL, false, 3, 0, 0, 2, NULL, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 7, true, 56, 647, 1, NULL, false, 1, 0, 0, 2, 14, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 8, true, 56, 647, 1, NULL, false, 1, 0, 0, 2, 14, false);

--Saudi - SAR
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 15, true, 56, 608, 1, NULL, false, 3, 0, 0, 2, NULL, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 7, true, 56, 608, 1, NULL, false, 1, 0, 0, 2, 14, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 8, true, 56, 608, 1, NULL, false, 1, 0, 0, 2, 14, false);

--US - USD
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 15, true, 56, 200, 1, NULL, false, 3, 0, 0, 2, NULL, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 7, true, 56, 200, 1, NULL, false, 1, 0, 0, 2, 14, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 8, true, 56, 200, 1, NULL, false, 1, 0, 0, 2, 14, false);

--Canada - CAD 
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 15, true, 56, 202, 1, NULL, false, 3, 0, 0, 2, NULL, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 7, true, 56, 202, 1, NULL, false, 1, 0, 0, 2, 14, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 8, true, 56, 202, 1, NULL, false, 1, 0, 0, 2, 14, false);

--Japan - JPY 
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 15, true, 56, 616, 1, NULL, false, 3, 0, 0, 2, NULL, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 7, true, 56, 616, 1, NULL, false, 1, 0, 0, 2, 14, false);
INSERT INTO client.cardaccess_tbl (id,clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES((SELECT max(id)+1 from client.cardaccess_tbl), 10020, 8, true, 56, 616, 1, NULL, false, 1, 0, 0, 2, 14, false);
