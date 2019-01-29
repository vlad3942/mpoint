<?php
function getEmailData($passengerData = "", $flightData = "")
{
    return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Payment Notification</title>
    <style>
        .ReadMsgBody {
            width: 100%;
        }
        .ExternalClass {
            width: 100%;
        }
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
            line-height: 100%;
        }
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        td ul li {
            font-size: 16px;
        }
        body {
            margin: 0;
            padding: 0;
        }
        img {
            max-width: 100%;
            border: 0;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        table {
            border-collapse: collapse !important;
        }
        .content {
            width: 100%;
            max-width: 600px;
        }
        .content img {
            height: auto;
            min-height: 1px;
        }
        #bodyTable {
            margin: 0;
            padding: 0;
            width: 100% !important;
        }
        #bodyCell {
            margin: 0;
            padding: 0;
        }
        #bodyCellFooter {
            margin: 0;
            padding: 0;
            width: 100% !important;
            padding-bottom: 15px;
            background-color: #113754
        }
        body {
            margin: 0;
            padding: 0;
            min-width: 100% !important;
        }
        #templateContainerFootBrd {
            border-bottom: 1px solid #e2e2e2;
            border-left: 1px solid #e2e2e2;
            border-right: 1px solid #e2e2e2;
            border-radius: 0 0 4px 4px;
            background-clip: padding-box;
            border-spacing: 0;
            height: 10px;
            width: 100% !important;
        }
        #templateContainer {
            border-top: 1px solid #e2e2e2;
            border-left: 1px solid #e2e2e2;
            border-right: 1px solid #e2e2e2;
            border-radius: 4px 4px 0 0;
            background-clip: padding-box;
            border-spacing: 0;
        }
        #templateContainerMiddle {
            border-left: 1px solid #e2e2e2;
            border-right: 1px solid #e2e2e2;
        }
        #templateContainerMiddleBtm {
            border-left: 1px solid #e2e2e2;
            border-right: 1px solid #e2e2e2;
            border-bottom: 1px solid #e2e2e2;
            border-radius: 0 0 4px 4px;
            background-clip: padding-box;
            border-spacing: 0;
        }
        h2 {
            color: #2e2e2e;
            display: block;
            font-family: Helvetica;
            font-size: 22px;
            line-height: 1.455em;
            font-style: normal;
            font-weight: normal;
            letter-spacing: normal;
            margin-top: 0;
            margin-right: 0;
            margin-bottom: 15px;
            margin-left: 0;
            text-align: center;
        }
        h5 {
            color: #545454;
            display: block;
            font-family: Helvetica;
            font-size: 13px;
            line-height: 1.538em;
            font-style: normal;
            font-weight: normal;
            letter-spacing: normal;
            margin-top: 0;
            margin-right: 0;
            margin-bottom: 15px;
            margin-left: 0;
            text-align: left;
        }
        h6 {
            color: #545454;
            display: block;
            font-family: Helvetica;
            font-size: 12px;
            line-height: 2.000em;
            font-style: normal;
            font-weight: normal;
            letter-spacing: normal;
            margin-top: 0;
            margin-right: 0;
            margin-bottom: 15px;
            margin-left: 0;
            text-align: left;
        }
        p {
            color: #545454;
            display: block;
            font-family: Helvetica;
            font-size: 16px;
            line-height: 1.500em;
            font-style: normal;
            font-weight: normal;
            letter-spacing: normal;
            margin-top: 0;
            margin-right: 0;
            margin-bottom: 15px;
            margin-left: 0;
            text-align: center;
        }
        .unSubContent h6 {
            color: #ffffff;
            font-size: 12px;
            line-height: 1.5em;
            margin-bottom: 0;
        }
        .bodyContent {
            color: #505050;
            font-family: Helvetica;
            font-size: 14px;
            line-height: 150%;
            padding-top: 3.143em;
            padding-right: 3.5em;
            padding-left: 3.5em;
            padding-bottom: 0.714em;
            text-align: center;
        }
        .bodyContentImage {
            color: #505050;
            font-family: Helvetica;
            font-size: 14px;
            line-height: 150%;
            padding-top: 0;
            padding-right: 3.571em;
            padding-left: 3.571em;
            padding-bottom: 2em;
            text-align: left;
        }

        .bodyContentImage h5 {
            color: #828282;
            font-size: 12px;
            line-height: 1.667em;
            margin-bottom: 0;
        }
        a:visited, a:focus, a:hover, a:link {
            color: #3386e4;
            text-decoration: none;
        }
        .bodyContent img {
            height: auto;
            max-width: 498px;
        }
        .footerContent {
            color: #808080;
            font-family: Helvetica;
            font-size: 10px;
            line-height: 150%;
            padding-top: 2.000em;
            padding-right: 2.000em;
            padding-bottom: 2.000em;
            padding-left: 2.000em;
            text-align: left;
        }
        .footerContent a:link, .footerContent a:visited, /* Yahoo! Mail Override */
        .footerContent a .yshortcuts, .footerContent a span /* Yahoo! Mail Override */
        {
            color: #606060;
            font-weight: normal;
            text-decoration: underline;
        }
        #templateContainerImageFull {
            border-left: 1px solid #e2e2e2;
            border-right: 1px solid #e2e2e2;
        }
        .bodyContentImageFull p {
            font-size: 0 !important;
            margin-bottom: 0 !important;
        }
        .brdBottomPadd-lg {
            border-bottom: 1px solid #f0f0f0;
        }
        .brdBottomPadd-lg .bodyContent {
            padding-bottom: 2.286em;
        }
        .brdBottomPadd {
            border-bottom: 1px solid #f0f0f0;
        }
        .brdBottomPadd .bodyContent {
            padding-bottom: 0em;
        }
        a.blue-btn {
            background: #113754;
            display: inline-block;
            color: #FFFFFF;
            border-top: 10px solid #113754;
            border-bottom: 10px solid #113754;
            border-left: 20px solid #113754;
            border-right: 20px solid #113754;
            text-decoration: none;
            font-size: 14px;
            margin-top: 1.0em;
            border-radius: 3px 3px 3px 3px;
            background-clip: padding-box;
            padding: 0 20px
        }
        .bodyContentNewsLetterDate {
            color: #505050;
            font-family: Helvetica;
            font-size: 14px;
            line-height: 150%;
            padding-top: 1.571em;
            padding-right: 1.714em;
            padding-left: 1.714em;
            padding-bottom: 0;
        }
        .bodyContentNewsLetter {
            color: #505050;
            font-family: Helvetica;
            font-size: 14px;
            line-height: 150%;
            padding-top: 0em;
            padding-right: 1.714em;
            padding-left: 1.714em;
            padding-bottom: 0.714em;
            text-align: left;
        }

        @media only screen and (max-width: 480px), screen and (max-device-width: 480px) {
            h2 {
                font-size: 30px !important;
            }
            h5 {
                font-size: 16px !important;
            }
            h6 {
                font-size: 14px !important;
            }
            p {
                font-size: 18px !important;
            }
            .brdBottomPadd .bodyContent {
                padding-bottom: 0em !important;
            }
            .brdBottomPadd-lg .bodyContent {
                padding-bottom: 2.286em !important;
            }
            .bodyContent {
                padding: 6% 5% 1% 6% !important;
            }
            .bodyContentNewsLetter {
                padding: 6% 5% 2% 6% !important;
            }
            .bodyContent img {
                max-width: 100% !important;
            }
            .bodyContentImage {
                padding: 3% 6% 6% 6% !important;
            }
            .bodyContentImage img {
                max-width: 100% !important;
            }
            .bodyContentImage h5 {
                font-size: 15px !important;
                margin-top: 0;
            }
            table.column {
                width: 100% !important;
                max-width: 100% !important;
                margin-bottom: 2em !important;
            }
            table.column img {
                width: 100%;
                height: auto;
            }
            table.columnspace {
                height: 0em !important;
            }
            .bodyContentNewsLetterDate h5 {
                font-size: 14px !important;
            }
            .hide {
                display: none !important;
            }
        }
        .ii a[href] {
            color: inherit !important;
        }
        span > a, span > a[href] {
            color: inherit !important;
        }
        a > span, .ii a[href] > span {
            text-decoration: inherit !important;
        }
        #templateContainerFooter h6 a {
            color: #ffffff;
        }
    </style>
</head>
<body bgcolor="#F2F3F2">
<table width="100%" bgcolor="#F2F3F2" border="0" cellpadding="10" cellspacing="0">
    <tr>
        <td>
            <table bgcolor="#ffffff" class="content" align="center" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td align="center" valign="top">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateContainer">
                            <tr>

                                <td valign="top" class="bodyContentNewsLetter">
                                    <p style="text-align:right;margin:0;padding:0;">
                                        <img src="{LOGO IMAGE}"/>
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
                                             style="display:block; margin:0; padding:0; border:0; max-width: 100%; width:100%;"/>
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
                                    <h6 style="text-align:center;margin-top: 9px;">1111 Brickell Avenue, 11th Floor</h6>
                                    <h6 style="text-align:center;">Miami FL 33131</h6>
                                    <h6 style="text-align:center;">Phone: +1 (305) 913-7115</h6>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>';
}
?>