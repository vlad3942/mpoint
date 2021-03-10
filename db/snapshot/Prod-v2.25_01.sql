-- mPoint Scripts

-- CPMRM-5962

update client.client_tbl
set cssurl = 'https://cpd-hpp2-prodassests.s3.eu-central-1.amazonaws.com/10077'
where id=10077;

update client.url_tbl set url = 'https://cpd-hpp2-prodassests.s3.eu-central-1.amazonaws.com/10077' where clientid = 10077 and urltypeid = 14;

update client.client_tbl
set logourl = 'https://cpd-hpp2-prodassests.s3.eu-central-1.amazonaws.com/10077/logo.svg'
where id=10077;

