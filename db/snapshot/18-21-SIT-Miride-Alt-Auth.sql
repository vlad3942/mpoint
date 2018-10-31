-- add the authentication url (url type id = 2) to enable profile validation using the external miride profile system
--Please edit before executing on the env.
-- 1. client id if different for miRide on the env.
-- 2. MESB-HOST-URL as per the env
INSERT INTO client.url_tbl
 (urltypeid, clientid, url)
VALUES
 (2, 10062, '<protocol>://<MESB-HOST-URL>/mprofile/miride/validate-profile');