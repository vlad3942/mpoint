/* ========== Global Configuration for DragonPay Offline = STARTS ========== */

INSERT INTO system.card_tbl (id, name, logo, enabled, position, minlength, maxlength, cvclength, paymenttype) VALUES (88, 'DRAGONPAYOFFLINE', null, true, 23, -1, -1, -1, 4);

/* ========== Global Common Configuration for DragonPay Offline/DragonPay Online = STARTS ========== */
INSERT INTO System.PSP_Tbl (id, name,system_type) VALUES (61, 'DragonPay',1);

INSERT INTO System.PSPCurrency_Tbl (currencyid, pspid, name) VALUES (608,61,'PHP');


INSERT INTO system.cardpricing_tbl (pricepointid, cardid, enabled) VALUES (-608, 88, true);

/*
* Dragon pay offline card with Dragon Pay aggregator 
*/

INSERT INTO System.PSPCard_Tbl (cardid, pspid) VALUES (88, 61);

/* ========== Global Common Configuration for DragonPay Offline/DragonPay Online = STARTS ========== */

INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (<clientid>, 61, 'DragonPay', <DragonPay_merchatid>, <DragonPay_MerchatAuthKey>);
INSERT INTO Client.MerchantAccount_Tbl (clientid, pspid, name, username, passwd) VALUES (10018, 61, 'DragonPay', 'CPM', '3GJ8LubyWVUMgqY');

INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (<ClientID>, <countryid>, <CurrencyID>,true)
INSERT INTO client.countrycurrency_tbl (clientid,countryid,currencyid,enabled)VALUES (10018,640,608,true)


INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (<accountid>, 61, '-1');
INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100094, 61, '-1');

-- Route DragonPay Offline Card to DragonPayAggregator with country Japan
INSERT INTO Client.CardAccess_Tbl(clientid,cardid,enabled,pspid,countryid,stateid,psp_type) values (<clientid>,88,true,61,<countryid>,1,1);
-- Route DragonPay Offline  Card to DragonPayAggregator with country Japan
INSERT INTO Client.CardAccess_Tbl(clientid,cardid,enabled,pspid,countryid,stateid,psp_type) values (10018,88,true,61,640,1,1);

--set timezone for CEBU
INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('TIMEZONE', 'Asia/Kuala_Lumpur', true, 10077, 'client', 2);

--currency symbols SQL update query
update system.currency_tbl set symbol='L'  where id='8'  and code='ALL';
update system.currency_tbl set symbol='DA'  where id='12'  and code='DZD';
update system.currency_tbl set symbol='$'  where id='32'  and code='ARS';
update system.currency_tbl set symbol='A$'  where id='36'  and code='AUD';
update system.currency_tbl set symbol='B$'  where id='44'  and code='BSD';
update system.currency_tbl set symbol='BD'  where id='48'  and code='BHD';
update system.currency_tbl set symbol='৳'  where id='50'  and code='BDT';
update system.currency_tbl set symbol='֏'  where id='51'  and code='AMD';
update system.currency_tbl set symbol='Bds$'  where id='52'  and code='BBD';
update system.currency_tbl set symbol='BD$'  where id='60'  and code='BMD';
update system.currency_tbl set symbol='Nu.'  where id='64'  and code='BTN';
update system.currency_tbl set symbol='Bs'  where id='68'  and code='BOB';
update system.currency_tbl set symbol='P'  where id='72'  and code='BWP';
update system.currency_tbl set symbol='BZ$'  where id='84'  and code='BZD';
update system.currency_tbl set symbol='Si$'  where id='90'  and code='SBD';
update system.currency_tbl set symbol='B$'  where id='96'  and code='BND';
update system.currency_tbl set symbol='K'  where id='104'  and code='MMK';
update system.currency_tbl set symbol='FBu'  where id='108'  and code='BIF';
update system.currency_tbl set symbol='៛'  where id='116'  and code='KHR';
update system.currency_tbl set symbol='C$'  where id='124'  and code='CAD';
update system.currency_tbl set symbol='Esc'  where id='132'  and code='CVE';
update system.currency_tbl set symbol='CI$'  where id='136'  and code='KYD';
update system.currency_tbl set symbol='Rs'  where id='144'  and code='LKR';
update system.currency_tbl set symbol='$'  where id='152'  and code='CLP';
update system.currency_tbl set symbol='¥'  where id='156'  and code='CNY';
update system.currency_tbl set symbol='$'  where id='170'  and code='COP';
update system.currency_tbl set symbol='CF'  where id='174'  and code='KMF';
update system.currency_tbl set symbol='₡'  where id='188'  and code='CRC';
update system.currency_tbl set symbol='kn'  where id='191'  and code='HRK';
update system.currency_tbl set symbol='₱'  where id='192'  and code='CUP';
update system.currency_tbl set symbol='Kč'  where id='203'  and code='CZK';
update system.currency_tbl set symbol='RD$'  where id='214'  and code='DOP';
update system.currency_tbl set symbol='₡'  where id='222'  and code='SVC';
update system.currency_tbl set symbol='Br'  where id='230'  and code='ETB';
update system.currency_tbl set symbol='Nfk'  where id='232'  and code='ERN';
update system.currency_tbl set symbol='FK£'  where id='238'  and code='FKP';
update system.currency_tbl set symbol='FJ$'  where id='242'  and code='FJD';
update system.currency_tbl set symbol='Fdj'  where id='262'  and code='DJF';
update system.currency_tbl set symbol='D'  where id='270'  and code='GMD';
update system.currency_tbl set symbol='£'  where id='292'  and code='GIP';
update system.currency_tbl set symbol='Q'  where id='320'  and code='GTQ';
update system.currency_tbl set symbol='FG'  where id='324'  and code='GNF';
update system.currency_tbl set symbol='G$'  where id='328'  and code='GYD';
update system.currency_tbl set symbol='G'  where id='332'  and code='HTG';
update system.currency_tbl set symbol='L'  where id='340'  and code='HNL';
update system.currency_tbl set symbol='HK$'  where id='344'  and code='HKD';
update system.currency_tbl set symbol='Ft'  where id='348'  and code='HUF';
update system.currency_tbl set symbol='kr'  where id='352'  and code='ISK';
update system.currency_tbl set symbol='₹'  where id='356'  and code='INR';
update system.currency_tbl set symbol='Rp'  where id='360'  and code='IDR';
update system.currency_tbl set symbol='﷼'  where id='364'  and code='IRR';
update system.currency_tbl set symbol='ع.د'  where id='368'  and code='IQD';
update system.currency_tbl set symbol='₪'  where id='376'  and code='ILS';
update system.currency_tbl set symbol='J$'  where id='388'  and code='JMD';
update system.currency_tbl set symbol='¥'  where id='392'  and code='JPY';
update system.currency_tbl set symbol='₸'  where id='398'  and code='KZT';
update system.currency_tbl set symbol='د.ا'  where id='400'  and code='JOD';
update system.currency_tbl set symbol='KSh'  where id='404'  and code='KES';
update system.currency_tbl set symbol='₩'  where id='408'  and code='KPW';
update system.currency_tbl set symbol='₩'  where id='410'  and code='KRW';
update system.currency_tbl set symbol='KD'  where id='414'  and code='KWD';
update system.currency_tbl set symbol='Лв'  where id='417'  and code='KGS';
update system.currency_tbl set symbol='₭'  where id='418'  and code='LAK';
update system.currency_tbl set symbol='LL‎'  where id='422'  and code='LBP';
update system.currency_tbl set symbol='L'  where id='426'  and code='LSL';
update system.currency_tbl set symbol='L$'  where id='430'  and code='LRD';
update system.currency_tbl set symbol='LD'  where id='434'  and code='LYD';
update system.currency_tbl set symbol='MOP$'  where id='446'  and code='MOP';
update system.currency_tbl set symbol='MK'  where id='454'  and code='MWK';
update system.currency_tbl set symbol='RM'  where id='458'  and code='MYR';
update system.currency_tbl set symbol='Rf'  where id='462'  and code='MVR';
update system.currency_tbl set symbol='UM'  where id='478'  and code='MRO';
update system.currency_tbl set symbol='Rs'  where id='480'  and code='MUR';
update system.currency_tbl set symbol='Mex$'  where id='484'  and code='MXN';
update system.currency_tbl set symbol='₮'  where id='496'  and code='MNT';
update system.currency_tbl set symbol='L'  where id='498'  and code='MDL';
update system.currency_tbl set symbol='DH'  where id='504'  and code='MAD';
update system.currency_tbl set symbol='RO'  where id='512'  and code='OMR';
update system.currency_tbl set symbol='N$'  where id='516'  and code='NAD';
update system.currency_tbl set symbol='रू'  where id='524'  and code='NPR';
update system.currency_tbl set symbol='NAf'  where id='532'  and code='ANG';
update system.currency_tbl set symbol='ƒ'  where id='533'  and code='AWG';
update system.currency_tbl set symbol='VT'  where id='548'  and code='VUV';
update system.currency_tbl set symbol='NZ$'  where id='554'  and code='NZD';
update system.currency_tbl set symbol='C$'  where id='558'  and code='NIO';
update system.currency_tbl set symbol='₦'  where id='566'  and code='NGN';
update system.currency_tbl set symbol='kr'  where id='578'  and code='NOK';
update system.currency_tbl set symbol='Rs'  where id='586'  and code='PKR';
update system.currency_tbl set symbol='B/.'  where id='590'  and code='PAB';
update system.currency_tbl set symbol='K'  where id='598'  and code='PGK';
update system.currency_tbl set symbol='₲'  where id='600'  and code='PYG';
update system.currency_tbl set symbol='S/'  where id='604'  and code='PEN';
update system.currency_tbl set symbol='₱'  where id='608'  and code='PHP';
update system.currency_tbl set symbol='QR'  where id='634'  and code='QAR';
update system.currency_tbl set symbol='₽'  where id='643'  and code='RUB';
update system.currency_tbl set symbol='FRw'  where id='646'  and code='RWF';
update system.currency_tbl set symbol='£'  where id='654'  and code='SHP';
update system.currency_tbl set symbol='Db'  where id='678'  and code='STD';
update system.currency_tbl set symbol='SAR'  where id='682'  and code='SAR';
update system.currency_tbl set symbol='SR'  where id='690'  and code='SCR';
update system.currency_tbl set symbol='Le'  where id='694'  and code='SLL';
update system.currency_tbl set symbol='S$'  where id='702'  and code='SGD';
update system.currency_tbl set symbol='₫'  where id='704'  and code='VND';
update system.currency_tbl set symbol='Sh.so.'  where id='706'  and code='SOS';
update system.currency_tbl set symbol='R'  where id='710'  and code='ZAR';
update system.currency_tbl set symbol='SS£'  where id='728'  and code='SSP';
update system.currency_tbl set symbol='E'  where id='748'  and code='SZL';
update system.currency_tbl set symbol='kr'  where id='752'  and code='SEK';
update system.currency_tbl set symbol='Fr'  where id='756'  and code='CHF';
update system.currency_tbl set symbol='LS'  where id='760'  and code='SYP';
update system.currency_tbl set symbol='฿'  where id='764'  and code='THB';
update system.currency_tbl set symbol='T$'  where id='776'  and code='TOP';
update system.currency_tbl set symbol='TT$'  where id='780'  and code='TTD';
update system.currency_tbl set symbol='د.إ'  where id='784'  and code='AED';
update system.currency_tbl set symbol='DT'  where id='788'  and code='TND';
update system.currency_tbl set symbol='USh'  where id='800'  and code='UGX';
update system.currency_tbl set symbol='den'  where id='807'  and code='MKD';
update system.currency_tbl set symbol='E£'  where id='818'  and code='EGP';
update system.currency_tbl set symbol='TSh'  where id='834'  and code='TZS';
update system.currency_tbl set symbol='$U'  where id='858'  and code='UYU';
update system.currency_tbl set symbol='soʻm'  where id='860'  and code='UZS';
update system.currency_tbl set symbol='WS$'  where id='882'  and code='WST';
update system.currency_tbl set symbol='﷼'  where id='886'  and code='YER';
update system.currency_tbl set symbol='NT$'  where id='901'  and code='TWD';
update system.currency_tbl set symbol='CUC$'  where id='931'  and code='CUC';
update system.currency_tbl set symbol='Z$'  where id='932'  and code='ZWL';
update system.currency_tbl set symbol='Br'  where id='933'  and code='BYN';
update system.currency_tbl set symbol='T'  where id='934'  and code='TMT';
update system.currency_tbl set symbol='GH₵'  where id='936'  and code='GHS';
update system.currency_tbl set symbol='Bs.'  where id='937'  and code='VEF';
update system.currency_tbl set symbol='SDG'  where id='938'  and code='SDG';
update system.currency_tbl set symbol='din'  where id='941'  and code='RSD';
update system.currency_tbl set symbol='MT'  where id='943'  and code='MZN';
update system.currency_tbl set symbol='₼'  where id='944'  and code='AZN';
update system.currency_tbl set symbol='lei'  where id='946'  and code='RON';
update system.currency_tbl set symbol='₺'  where id='949'  and code='TRY';
update system.currency_tbl set symbol='FCFA'  where id='950'  and code='XAF';
update system.currency_tbl set symbol='EC$'  where id='951'  and code='XCD';
update system.currency_tbl set symbol='CFA'  where id='952'  and code='XOF';
update system.currency_tbl set symbol='₣'  where id='953'  and code='XPF';
update system.currency_tbl set symbol='SDR'  where id='960'  and code='XDR';
update system.currency_tbl set symbol='ZK'  where id='967'  and code='ZMW';
update system.currency_tbl set symbol='$'  where id='968'  and code='SRD';
update system.currency_tbl set symbol='Ar'  where id='969'  and code='MGA';
update system.currency_tbl set symbol='؋'  where id='971'  and code='AFN';
update system.currency_tbl set symbol='SM'  where id='972'  and code='TJS';
update system.currency_tbl set symbol='Kz'  where id='973'  and code='AOA';
update system.currency_tbl set symbol='Лв.'  where id='975'  and code='BGN';
update system.currency_tbl set symbol='FC'  where id='976'  and code='CDF';
update system.currency_tbl set symbol='KM'  where id='977'  and code='BAM';
update system.currency_tbl set symbol='₴'  where id='980'  and code='UAH';
update system.currency_tbl set symbol='GEL'  where id='981'  and code='GEL';
update system.currency_tbl set symbol='zł'  where id='985'  and code='PLN';
update system.currency_tbl set symbol='R$'  where id='986'  and code='BRL';
update system.currency_tbl set symbol='UF'  where id='990'  and code='CLF';
update system.currency_tbl set symbol='S/.'  where id='994'  and code='XSU';
update system.currency_tbl set symbol='$'  where id='997'  and code='USN';