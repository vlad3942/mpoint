INSERT INTO client.merchantaccount_tbl (clientid, pspid, "name", enabled, username, passwd, stored_card, supportedpartialoperations) VALUES(10101, 64, 'CYBS Fraud',true, 'avianca_master', 'c0JN8dpRw0b+PVidvKGPhfMEEcjQpAVXZxSJBo3iPKqF7hgRUuPU5HNRDVqCeC3aOgQsYjK2v32uKYUUGrZiypRzJud0BEVc7xOl5Y+KGBef5PuEHUGx2FBVeyoD5Y+IDaPRRfkkudr5PhZJDVgPJS6x81eYQ1WXKCGawma6jp+XeuIheoptIsJ2RjmVQgP4/I3zFcRc+lxev7uMNFzlfAsZ+6XoeRNfmPHpfR9xln1NxMV0y7faIVLXEXg2J7c0mZNRJuXZmBlNeu5W68LS0Pkg4ERTYaiuCNonwAXxeifsTy4HgNDqiALQRsuBX1Hufo4wQ44KYzMp2QoYe7EStg==', NULL, 0);

INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES( 101010, 64, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES( 101012, 64, '-1', true);
INSERT INTO client.merchantsubaccount_tbl (accountid, pspid, "name", enabled) VALUES( 101011, 64, '-1', true);
INSERT INTO client.cardaccess_tbl (clientid, cardid, created,pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10101, 8, true, 64, NULL, 1, NULL, false, 9, 0, 0, 1, NULL, false);
INSERT INTO client.cardaccess_tbl (clientid, cardid, created,pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10101, 7, true, 64, NULL, 1, NULL, false, 9, 0, 0, 1, NULL, false);
INSERT INTO client.cardaccess_tbl (clientid, cardid, created,pspid, countryid, stateid, "position", preferred, psp_type, installment, capture_method, capture_type, walletid, dccenabled) VALUES(10101, <cardid>, true, 64, NULL, 1, NULL, false, 9, 0, 0, 1, NULL, false);

