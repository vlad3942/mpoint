--- mPoint Client Schema - for SR Migration

------------------------------------------------------------------------------
-- Enable client to use SR flow i.e non legacy flow:

INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('IS_LEGACY', 'false',10077, 'client', 2);

-- Migration of existing merchant route details
INSERT into client.route_tbl (id, clientid, providerid, enabled)
SELECT id, clientid, pspid, enabled FROM client.merchantaccount_tbl WHERE clientid=10077;

-- 2C2P-alc - USD
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'UIYSTHY0O4WLKZMG', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 40;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='CebuPacific_USD';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'CebuPacific_USD';


-- 2C2P-alc - MCC

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, '2c2p-alc_Master_VISA_PHP', 2, 'CebuPacific_MCC', 'CELLPM', 'UIYSTHY0O4WLKZMG', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 40;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='CebuPacific_MCC';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on
        r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'CebuPacific_MCC';


-- PAYPAL PHP : need to check currency for Paypal PHP / USD
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'Paypal_PHP', 2, 'As0CsasLOsIIjn6r.ieY7gG7r2vXA.oZ86a8wBLg-p5pF0Ov9-y.kQ0F', 'phcebpaypal_api1.cebupacificair.com', 'XS96A2ADR2RJDEPR', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 24;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='As0CsasLOsIIjn6r.ieY7gG7r2vXA.oZ86a8wBLg-p5pF0Ov9-y.kQ0F';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'As0CsasLOsIIjn6r.ieY7gG7r2vXA.oZ86a8wBLg-p5pF0Ov9-y.kQ0F';


-- PAYPAL SGD
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'Paypal_SGD', 2, 'AFcWxV21C7fd0v3bYYYRCpSSRl31AMSGek.A5c24IFvwr1oJEmnyY2zs', 'sgcebpaypal_api1.cebupacificair.com', 'JWJT3PSUP57DM2CW', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 24;

INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='AFcWxV21C7fd0v3bYYYRCpSSRl31AMSGek.A5c24IFvwr1oJEmnyY2zs';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'AFcWxV21C7fd0v3bYYYRCpSSRl31AMSGek.A5c24IFvwr1oJEmnyY2zs';


-- PAYPAL HKD
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'Paypal_HKD', 2, 'Ai1PaghZh5FmBLCDCTQpwG8jB264Ar.yoWfzAZAtGXi7ElVkreZTUDJj', 'hkcebpaypal_api1.cebupacificair.com', 'MB3UHZHE2SYEBWWH', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 24;
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='Ai1PaghZh5FmBLCDCTQpwG8jB264Ar.yoWfzAZAtGXi7ElVkreZTUDJj';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'Ai1PaghZh5FmBLCDCTQpwG8jB264Ar.yoWfzAZAtGXi7ElVkreZTUDJj';


-- PAYPAL MYR
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'Paypal_MYR', 2, 'AeQHpsR6oLP4vEUw.mB5Zx9anAkmAy0QSXSXnOevSEwQ5LJSCNeL7S3n', 'mycebpaypal_api1.cebupacificair.com', '38LQ49R939JK737V', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 24;

INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='AeQHpsR6oLP4vEUw.mB5Zx9anAkmAy0QSXSXnOevSEwQ5LJSCNeL7S3n';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'AeQHpsR6oLP4vEUw.mB5Zx9anAkmAy0QSXSXnOevSEwQ5LJSCNeL7S3n';


-- PAYPAL USD

INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'Paypal_USD', 2, 'AICx5s75qo3cvpG9DkpKA8hj8IYyAP9SasTn3mUrSZ2u2UYAGGg4qMs6', 'uscebpaypal_api1.cebupacificair.com', 'ND6QB4VLYYJ46QZK', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 24;

INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='AICx5s75qo3cvpG9DkpKA8hj8IYyAP9SasTn3mUrSZ2u2UYAGGg4qMs6';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'AICx5s75qo3cvpG9DkpKA8hj8IYyAP9SasTn3mUrSZ2u2UYAGGg4qMs6';


-- MODIRUM API
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'MODIRUM-MPI', 2, 'MODIRUM MPI', '9449005362', '-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDGaf1esiccugzC
UcI1/CVNIbnrsDb4F2ctLpw2i4d65U2NtP+wpHRnCzm9KbIJ6gEJkeOPjb7WLyaK
DLaGbvhGW/DnKa0fa3KkvgvSaJTrji2ssDcr2lBRulOgNRTyilbNWMdfwosUgveN
dY5DPNp/f0nHpq+HajsdcSL/POT+slWy7ZXKfS4CqIB39ClPm2AOD5htzQhEBr5Z
174lm69R3QHh6CPg1bdS/1qKpr7RuXyvOGlsrvc00gJWhEbF3OGANieyeOWrxOFq
cs4B/ubvqkikcTPw42VKWPTRfxNr+8ANF0g+1bmok/X/UUUGbD9YXXjWNTKcJ68k
FDqPen8fAgMBAAECggEADB5/NmCFWRDYJKpfxXJgSOTNeWLrCJ5NVAoryn0dSlll
Mkmi8IQrA/xAi5hXYpmjdJUvpB4RUP3SSc5a+70ddxa4kTYPeALVHtDo4fI3Xmfx
zEF7LTeJfmR2JSv42pOul55blzLH0fnp6v8KMHswEWeR9xrsT8YiVDsL2zE5/4CD
dxEzO5eCgdp8QnqJPZR9dErrFwP2DVm+IvGDKb5WZAztRL3EwZnGzh4+3aA4NKHy
D9OQJQ4pHApx3nXDedxBg0GKT+Ecr6u4vQW8Gc3wM9NqgYHvWQeIsPX8gGzoKxVa
x6GV8PpRXNe3dcun/q+lIdmTnUX9+gczHH0VPZ8/qQKBgQDyK2OQ1ABXkYJhuuKG
WYSQ1gJKIYrn++SyUAvpTAFeUSDYD303v5YDhDvXRVn9xufcjb0NRZuI3S2jPi1x
EGi7e68jC+I3QhypVlNdsJQFhPPiVJYysngPlHXJFxbpeuPKy/MWeyGQBvcxADPB
Z0KkXVQcE4NlfSuAbz/xnTEn2wKBgQDRvuEJ9uNIUTzAeb2OvrlPM2xxJ+4nGs3a
4RTLPafdm8xjBCuDm8+RiT6fGRp/oPfzy20qGkQWhfjLPnplbt4oOSgTt3HfafNm
hi7X4scpsp51229BQxhoKTapn5W5Qkd7rrtXI+9cRWNy+i78iHuKISusxixRPqFM
NUvkfyQ7DQKBgQCCSo3bEfTNKGB5rE7L8cW9FydMMxfFEGVO+nouHtJtqEB/fnXk
VJOleLOpcoqkWyvMIgYg6d5wmG9BcOaJ+kYe+MCVnoMrL8qz4NohgithfNKqZtAY
nqSx3TIx6tZM7+024tv6sGyyTM0Z8/3khGX6gKwMHwOGyv1osHI60FPuGQKBgHJx
kzqrooIACYUAKCTt/hCv/1iSsAhYQMBQFdd7kc+CRfg0+0U5S/2eBDQtL186RnCY
q7zQThx4BzNmqMQVxVPvM+XmL1T2658iUgbrrz4aPwoRrFfQs02KR1AwSjKmbniW
85NtgRo4pjXDOsYB9l59EOaZzu6ZnMsHPZy0nuE1AoGBAKrvh0cFyo+S4+s9HcGp
S3s+a4P+Yru/PKWeVs5l3fbbBF5jrX4PcjFk+jLwT2VTQ+el0ipWIm/6Hc2IKmG+
vGfRURP08SpuvR6SJeXPjayZ34sOcWDHigXAUGBw5Jz5i5lNTjeiqEVUYRMN0DzQ
iR1ggsJPAmoGDC2J7+mwMflk
-----END PRIVATE KEY-----', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 47;

INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='MODIRUM MPI';
INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='MODIRUM MPI';

-- FIRST DATA
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'first-data', 2, '6160800539', 'WS6160800539._.1', 'Q58*jkS[CX', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 62;

INSERT INTO client.routefeature_tbl( clientid, routeconfigid, featureid) SELECT 10077, rc.id, 9 FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and  mid ='6160800539';
INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='6160800539';
INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='6160800539';

-- WorldPay
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'worldpay_JPY', 2, 'CEBUAIRECJPY', 'CEBUAIRECJPY', 'CkeQG3k9mP', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 4;

INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='CEBUAIRECJPY';
INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='CEBUAIRECJPY';

-- GrabPay
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'GrabPay', 2, 'c636da2c-cd58-46e0-9821-8277e2b9bbde', '14db420a-dd9e-4ffb-aa8d-ed63c73dad3b', 'dOFP-WHyKlePySZ5', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 67;

INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='c636da2c-cd58-46e0-9821-8277e2b9bbde';
INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='c636da2c-cd58-46e0-9821-8277e2b9bbde';


-- Stored Card:
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'Stored Card', 2, 'mVault', 'Blank', 'Blank', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 36;

INSERT INTO client.routecountry_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mVault';
INSERT INTO client.routecurrency_tbl(routeconfigid) SELECT rc.id FROM client.routeconfig_tbl rc inner join client.route_tbl r on r.id = rc.routeid WHERE r.clientid = 10077 and mid ='mVault';


-- CyberSourceAMEX
INSERT INTO client.routeconfig_tbl( routeid, name, capturetype, mid, username, password, enabled)
SELECT id, 'CyberSourceAMEX', 2, 'CyberSourceAMEX', 'cebupacificamex', 'VmZzDRg2odfkF64FVY1MFo+rT7vS6Fb3YC16h1bcmScuCW//j08C0oE8Z3lDMDm6ShxkKZmuftifmUfnulPxaucAQapVV5n5wjhjWwg5Mr9CyOKTQ6RbCuSeIJdAXHewYB4jNVK5h3Bk728IivhwDyyrk4vXULJGQqVToocvO6+bXNVLtTNOHBGbSEts3DM26Rx/GZ1HYtWaauFV3g39cG/x6Ao4NXjg9UoZ59g6FYOgCgsmHAB/XpK7kxjbI+pxBzqhiRDeX79NxAfhUIXWPFYqaBH83YsSGganHUxzkNg2jTzxmwSV2+JXZKUoq37TUgHCygl2pT9Gs+mMieTuBQ==', enabled FROM client.route_tbl WHERE clientid = 10077 AND providerid = 63;

INSERT INTO client.routecountry_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'CyberSourceAMEX';
INSERT INTO client.routecurrency_tbl(routeconfigid) select rc.id from client.routeconfig_tbl rc INNER JOIN client.route_tbl r on r.id = rc.routeid where r.clientid = 10077 and rc.mid = 'CyberSourceAMEX';

--- Client Country currency mapping

INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,630,36,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,601,48,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,501,96,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,202,124,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,634,144,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,609,156,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,100,208,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,614,344,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,603,356,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,505,360,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,616,392,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,632,410,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,604,414,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,636,446,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,638,458,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,502,554,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,416,578,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,640,608,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,606,634,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,608,682,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,642,702,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,649,704,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,101,752,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,136,756,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,644,764,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,602,784,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,422,826,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,200,840,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,646,901,true);
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10077,409,978,true);