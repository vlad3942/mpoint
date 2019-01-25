-- Adding new card payment type id 7 for FPX and other online banking payment types --
INSERT INTO system.paymenttype_tbl (id, name) VALUES (7, 'OnlineBanking');
Update System.Card_Tbl set paymenttype=7 where id=73;
-- end--