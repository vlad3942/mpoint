UPDATE Client.Account_Tbl SET markup = 'xhtml';

INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (191, 'Undefined Markup Language', 'Validate', 'valMarkupLanguage');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (192, 'Markup Language not found in Template', 'Validate', 'valMarkupLanguage');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2008, 'Payment with account', 'Payment', '');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2011, 'Payment declined', 'Payment', 'capture');
INSERT INTO Log.State_Tbl (id, name, module, func) VALUES (2019, 'Payment duplicated', 'Payment', 'completeTransaction');