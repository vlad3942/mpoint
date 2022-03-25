-- APIKEY property for Adyen Provider
INSERT INTO client.psp_property_tbl (clientid,propertyid,value,enabled) VALUES (<client-id>,(SELECT id FROM system.psp_property_tbl WHERE pspid  = 12 AND name = 'APIKEY'),'<api-key>',true);

-- Add 3DS Property for Adyen Provider
INSERT INTO client.routefeature_tbl (routeconfigid,clientid,featureid) SELECT id, <client-id>, 9 FROM client.routeconfig_tbl WHERE routeid IN (SELECT id FROM client.route_tbl WHERE clientid = <client-id> AND providerid IN (12));

-- Add MPI property for Adyen Provider
INSERT INTO client.routefeature_tbl (routeconfigid,clientid,featureid) SELECT id, <client-id> ,20 FROM client.routeconfig_tbl WHERE routeid IN (SELECT id FROM client.route_tbl WHERE clientid = <client-id> AND providerid IN (12));