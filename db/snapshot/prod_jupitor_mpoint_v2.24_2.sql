--Table Name : Client.CardAccess_Tbl
--REF - CTECH-4242

INSERT INTO client.cardaccess_tbl (id, clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) 
SELECT (select max(id)+1 from client.cardaccess_tbl), 10077, 7, true, 64, NULL, 1, NULL, false, 10, 0, 0, 1, NULL, true;

INSERT INTO client.cardaccess_tbl (clientid, cardid, enabled, pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) 
SELECT (select max(id)+1 from client.cardaccess_tbl), 10077, 8, true, 64, NULL, 1, NULL, false, 10, 0, 0, 1, NULL, true;

INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, username, passwd, enabled, stored_card) 
SELECT (select max(id)+1 from client.MerchantAccount_Tbl), 64, 'CyberSourceFraud', 'cebupacific' ,
'+h47/kx47rCpe64cFG24WJw/MKiWLpNzodO1DAJx0uWDsHrif7w0tzlv9CfChqEzHwGJLK52J+H2kcWMj0eE0W6a5oZmv9ep8uSNsrwnTWKw8JpWdn0CjZLLOcmABvnVEG/hKX8lZiVdbB8p3ccDoapRfxboVGuzXOEoAsQ++nsIhTa/cpROp7rbrX9FsT+60YtYAOfixGsRfrTenYvoq4XgkF/h70e3ODO5IWOKsCjDTtTPTgP3SeqctmYsLG9r1Yrm+Ho8ZMrKlmwC1FjaQVuF9ZOGQGwdSvyELT4Ioa2hGRL5W3G0xRHunwZB7UwFCZuiUjCAFd8x6UBjWabv6A==', 
true, null;

INSERT INTO Client.MerchantSubAccount_Tbl (id, accountid, pspid, name, enabled) 
SELECT (select max(id)+1 from client.MerchantSubAccount_Tbl), 100770, 64, '-1', true;
