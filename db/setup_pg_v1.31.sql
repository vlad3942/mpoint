UPDATE Log.State_Tbl SET name = 'Undefined Mobile Number' WHERE id = 31;
UPDATE Log.State_Tbl SET name = 'Mobile Number is too short' WHERE id = 32;
UPDATE Log.State_Tbl SET name = 'Mobile Number is too long' WHERE id = 33;
UPDATE Log.State_Tbl SET name = 'Amount is too small' WHERE id = 52;
UPDATE Log.State_Tbl SET name = 'Amount is too great' WHERE id = 53;

INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (151, 'Undefined flag for Auto Store Card', 'Validate', 'valBoolean');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (152, 'Invalid flag for Auto Store Card', 'Validate', 'valBoolean');

INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (161, 'Undefined Icon URL', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (162, 'Icon URL is too short', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (163, 'Icon URL is too long', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (164, 'Icon URL is malformed', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (165, 'Icon URL is Invalid, no Protocol specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (166, 'Icon URL is Invalid, no Host specified', 'Validate', 'valURL');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (167, 'Icon URL is Invalid, no Path specified', 'Validate', 'valURL');

INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (171, 'Undefined mPoint ID', 'Validate', 'valmPointID');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (172, 'Invalid mPoint ID', 'Validate', 'valmPointID');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (173, 'Transaction not found for mPoint ID', 'Validate', 'valmPointID');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (174, 'Transaction for mPoint ID has been disabled', 'Validate', 'valmPointID');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (175, 'Payment Rejected for Transaction', 'Validate', 'valmPointID');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (176, 'Payment already Captured for Transaction', 'Validate', 'valmPointID');

INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (181, 'Undefined Order ID', 'Validate', 'valOrderID');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (182, 'Transaction not found', 'Validate', 'valOrderID');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (183, 'Order ID doesn''t match Transaction', 'Validate', 'valOrderID');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (184, 'Transaction Disabled', 'Validate', 'valOrderID');

INSERT INTO Admin.User_Tbl (countryid, firstname, lastname, mobile, email, username, passwd) VALUES (100, 'Jonatan Evald', 'Buus', '28882861', 'jonatan.buus@cellpointmobile.com', 'jona', 'oisJona');
INSERT INTO Admin.Access_Tbl (userid, clientid) SELECT Max(U.id), CL.id FROM Admin.User_Tbl U, Client.Client_Tbl CL GROUP BY CL.id;
INSERT INTO Admin.User_Tbl (countryid, firstname, lastname, email, username, passwd) VALUES (100, 'Bjarn Virum', 'Madsen', 'bvm@dsb.dk', 'DSB', 'hdfy28abd1');
INSERT INTO Admin.Access_Tbl (userid, clientid) SELECT Max(id), 10005 FROM Admin.User_Tbl;