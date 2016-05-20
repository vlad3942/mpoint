UPDATE  Client.Client_Tbl 
SET accepturl= 'https://1415.mesb.cellpointmobile.com:10443/mpoint/dibs/accept',
 	cancelurl = 'https://1415.mesb.cellpointmobile.com:10443/mpoint/dibs/cancel',
 	declineurl = 'https://1415.mesb.cellpointmobile.com:10443/mpoint/dibs/decline' 
WHERE id = 10019;