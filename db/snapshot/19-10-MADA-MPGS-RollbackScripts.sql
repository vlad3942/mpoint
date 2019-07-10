--//********MADA-MPGS Rollback scripts*******************//
--Run these scripts on SIT-LONDON only. To revert earlier configuration done MADA card.

--//**********system.card_tbl************//
update  system.card_tbl set minlength = -1, maxlength = -1,cvclength = -1 where id = 71;


--//**********client.cardaccess_tbl************//
delete  from client.cardaccess_tbl where clientid = <clientid> and cardid = 71 and pspid = 57;


--//**********system.cardprefix_tbl Bin range************//
delete  from system.cardprefix_tbl where cardid = 71 and min != 0 and max != 0 ;

//********END OF MADA-MPGS*******************//