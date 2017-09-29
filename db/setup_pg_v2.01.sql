-- INSERT MESB URL for Taxa Client.
-- Note: Check taxa clientid on deploying environment
-- Note: Check that SSL proxy is working on deploying environment
-- MUST BE HTTPS (TLS/SSL)
INSERT INTO Client.URL_Tbl
  (clientid, urltypeid, url)
VALUES
  (10028, 4, 'https://taxa.mesb.cellpointmobile.com');
