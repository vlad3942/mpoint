<?php
function getEmailData($passengerData = "", $flightData = "")
{
    return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Payment Notification</title>
    <!--[if (gte mso 9)|(IE)]>
    <style type="text/css">
        .bodyContentTwoColumn {
            padding: 0 !important;
        }
    </style>
    <![endif]-->
    <link href="{CSS URL}" rel="stylesheet"/>
</head>
<body bgcolor="#F2F3F2">
<table width="100%" bgcolor="#F2F3F2" border="0" cellpadding="10" cellspacing="0">
    <tr>
        <td>
            <!--[if (gte mso 9)|(IE)]>
            <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td>
            <![endif]-->
            <table bgcolor="#ffffff" class="content" align="center" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td align="center" valign="top">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateContainer"
                               bgcolor="#FFFFFF">
                            <tr>
                                <td valign="top" class="bodyContentNewsLetter">
                                    <p style="text-align:center;margin:0;padding:0;">
                                        <img src="{LOGO IMAGE}" style="width:20%; height: auto"/>
                                    </p>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateContainerImageFull"
                               style="min-height:15px;">
                            <tr>
                                <td valign="top" class="bodyContentImageFull">
                                    <p style="text-align:center;margin:0;padding:0;">
                                        <img src="{BANNER IMAGE}"
                                             style="display:block; margin:0; padding:0; border:0; max-width: 100%;"/>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateContainerMiddle"
                               class="brdBottomPadd-lg">
                            <tr>
                                <td valign="top" class="bodyContent">
                                    <p>{MESSAGE TEXT}</p>
                                    <a class="blue-btn" href="{PAY NOW URL}"><strong>Pay now</strong></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top" id="bodyCellFooter" class="unSubContent">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" id="templateContainerFooter">
                            <tr>
                                <td valign="top" width="100%">
                                    <h6 style="text-align:center;margin-top: 9px;">reservations@sunriseairways.net</h6>
                                    <h6 style="text-align:center;">Phone numbers (Haiti): +(509)37012390</h6>
                                    <h6 style="text-align:center;">Phone numbers (Dominican Republic):+ 1(849)
                                        916-6666</h6>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>
            <!--[if (gte mso 9)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </td>
    </tr>
</table>
</body>
</html>';
}
?>