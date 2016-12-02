<?php
header('HTTP/1.0 200 OK');
/* echo '<?xml version="1.0" encoding="UTF-8"?>
<root>
     <status code="2005">3d verification required</status>
    <parsed-challenge>'.
		htmlentities('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><Meta HTTP-EQUIV="Cache-Control" CONTENT="no-cache" ><Meta HTTP-EQUIV="Pragma" CONTENT="no-cache"><Meta HTTP-EQUIV="Expires" CONTENT="0"><title>Connecting to Payment Gateway</title><SCRIPT LANGUAGE = JavaScript><!--isNN = document.layers ? 1 : 0;function noContext(){return false;}function noClick(e){if(isNN){if(e.which > 1) {return false;}} else {if(event.button > 1){return false;}}}if(isNN){document.captureEvents(Event.MOUSEDOWN);}document.oncontextmenu = noContext;document.onmousedown   = noClick;document.onmouseup     = noClick;//--></SCRIPT><script language="javascript">window.history.forward(); function noBack() { window.history.forward(); } function SubmitMe(){ document.getElementById("submit").style.visibility=\'hidden\';document.getElementById("submit").click(); }</script><style type="text/css"> body { font-family: Arial, Helvetica, sans-serif; font-weight:normal; color:#474747; font-size: 14px; margin:15% auto 0 auto; } #intermediatepage { background-image:url(\'/images/ccavenue_logo.gif\'); background-position:center top; background-repeat:no-repeat; padding:10px 20px 0 20px;} #intermediatepage div.process { ; background-position:center 35px; background-repeat:no-repeat;}</style> </head><body oncontextmenu="return false;" onLoad="noBack();SubmitMe();"><form name="MalltoEpay" method="POST" action="https://test.ccavenue.com/bnk/servlet/processCCardReq?gtwID=AVN&requestType=PAYMENT" ><input type=hidden name="MD" value="P"><input type=hidden name="PID" value="AVN0003 USD"><input type=hidden name="PRN" value="305002945747"><input type=hidden name="ITC" value="AVN0003 USD"><input type=hidden name="AMT" value="10.25"><input type=hidden name="CRN" value="USD"><input type=hidden name="RU" value="https://test.ccavenue.com/receive/102/servlet/BankRespReceive"><input type=hidden name="CG" value="Y"><input type=hidden name="TYPE" value="POST"><input type=hidden name="RESPONSE" value="AUTO"><input type=hidden name="ResponseType" value=""><input type="submit" id="submit" value="Continue" style="display:none;"></form><div id="intermediatepage" align="center"><div class="process"><br /><br /><br /><br /><br /><br /><span class="content-text" style="font-size:24px; font-weight:bold;"></span><br style="line-height:35px;" /><span></span><br /><br /></div></div></body></html>')
		.'</parsed-challenge>
</root>'; */

echo '<?xml version="1.0" encoding="UTF-8"?>
<root>
    <status code="2005">3d Secure Verification Required</status>
    <parsed-challenge>
        <action type-id="10">
            <url content-type="application/x-www-form-urlencoded" method="POST" type-id="1">https://ubimpi.electracard.com/electraSECURE/vbv/MPIEntry.jsp</url>
            <hidden-fields>
                <merchantID>458791000002339</merchantID>
                <pan>5577632311994646</pan>
                <expiry>2105</expiry>
                <purchase_amount>100</purchase_amount>
                <shoppingContext>105148986085</shoppingContext>
                <currencyVal>356</currencyVal>
                <callBackURL>https://secure.ccavenue.com/receive/121/servlet/BankRespReceive</callBackURL>
                <deviceCategory>0</deviceCategory>
            </hidden-fields>
        </action>
    </parsed-challenge>
</root>';

exit;
?>