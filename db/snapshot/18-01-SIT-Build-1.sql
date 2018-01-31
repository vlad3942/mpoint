/*  ===========  START : Adding Default value to   ==================  */
UPDATE Client.Client_Tbl SET communicationchannels = 5;
UPDATE client.merchantsubaccount_tbl SET name='Default' where pspid=13
