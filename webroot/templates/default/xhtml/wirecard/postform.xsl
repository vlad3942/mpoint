<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions"
	extension-element-prefixes="func">
	<xsl:output method="html" indent="no" media-type="text/html"
		doctype-public="HTML" omit-xml-declaration="yes" standalone="yes" />

	<xsl:template match="/root">
	
		<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=ISO-8859-15" />
				<meta http-equiv="Cache-Control" content="max-age=86400" />
				<meta http-equiv="Content-Style-Type" content="text/css" />
				<title>
					<xsl:value-of select="/root/title" />
				</title>
				<link href="{/root/transaction/css-url}" type="text/css" rel="stylesheet" />
			</head>
			<body>
				<!-- Display Client Logo using the provided URL -->
				<xsl:if test="string-length(/root/transaction/logo/url) &gt; 0">
					<div id="logo">
						<img
							src="/img/{/root/transaction/logo/width}x{/root/transaction/logo/height}_client_{/root/system/session/@id}.png"
							width="{/root/transaction/logo/width}" height="{/root/transaction/logo/height}"
							alt="- {/root/client-config/name} -" />
					</div>
				</xsl:if>
				
				<xsl:variable name="card-number">
					<xsl:if test="transaction/@mode &gt; 0">
						4012000300001003
					</xsl:if>
				</xsl:variable>
				<xsl:variable name="expiry-month">
					<xsl:if test="transaction/@mode &gt; 0">
						01
					</xsl:if>
				</xsl:variable>
				<xsl:variable name="expiry-year">
					<xsl:if test="transaction/@mode &gt; 0">
						2019
					</xsl:if>
				</xsl:variable>
				<xsl:variable name="cvc">
					<xsl:if test="transaction/@mode &gt; 0">
						003
					</xsl:if>
				</xsl:variable>

				<xsl:variable name="card-url"
					select="concat(system/protocol, '://', system/host, '/img/', card/width, 'x', card/height, '_card_', card/@id, '_', system/session/@id, '.png')" />

				<style type="text/css">

					body {
					margin: 0;
					}
					body, table {
					background-color: #8fbfd5;
					text-align: left;
					}
					body, table, div {
					color: #000;
					font-family: "Trebuchet MS",arial,sans-serif;
					font-size: 1em;
					}
					div#content {
					margin: 10px 0 10px 10px;
					}
					table, table * div, table * .mPoint_Info {
					color: #000;
					}
					div#status table, div#status table * div, div#status table * .mPoint_Info {
					color: #000;
					}
					h1 {
					color: #000;
					font-size: 130%;
					text-align: center;
					white-space: nowrap;
					}
					form, table {
					margin: 0;
					padding: 0;
					text-align: left;
					}
					a {
					color: blue;
					text-decoration: none;
					}
					a:hover {
					text-decoration: underline;
					}
					input.number {
					}
					.mPoint_Label {
					font-weight: bold;
					padding-right: 0.5em;
					padding-top: 0.3em;
					text-align: left;
					white-space: nowrap;
					}
					.mPoint_Info {
					color: #000;
					font-style: italic;
					font-weight: normal;
					margin: 0;
					padding: 0;
					text-align: left;
					}
					.mPoint_Status {
					color: red;
					font-style: italic;
					padding-bottom: 0.5em;
					padding-top: 0.5em;
					}
					.mPoint_Help {
					padding-bottom: 5px;
					text-align: center;
					}
					td.mPoint_Number {
					padding-right: 0.5em;
					text-align: right;
					vertical-align: bottom;
					}
					tr.mPoint_Even {
					background-color: #8fbfd5;
					}
					tr.mPoint_Uneven {
					}
					input.mPoint_Button {
					color: #000;
					font-size: 1em;
					font-weight: bold;
					}
					input.mPoint_Card_Button {
					background-color: #fff;
					border-style: none;
					color: blue;
					margin: 0;
					padding: 0 0 2px;
					text-decoration: underline;
					}
					table#products tr td {
					padding-left: 3px;
					padding-top: 3px;
					}
					table#receipt {
					background-color: #8fbfd5;
					margin-bottom: 0.5em;
					margin-top: 0.5em;
					}
					div#logo, div#progress, div#mPoint, div#status {
					text-align: center;
					}
					div#status table tr td {
					padding-left: 0.5em;
					vertical-align: middle;
					}
					div#terms {
					white-space: pre;
					}
					div#delivery, table#shipping, div#email, div#cardinfo, div#accountinfo {
					background-color: #8fbfd5;
					color: #000;
					padding-bottom: 3px;
					padding-left: 3px;
					}
					div#cardinfo .mPoint_Info, div#cardinfo * .mPoint_Info, div#cardinfo
					.mPoint_Label {
					color: #000;
					}
					div#price {
					padding-bottom: 0.5em;
					}
					div#cardinfo {
					background-color: #8fbfd5;
					border: 2px solid #000;
					border-radius: 10px;
					color: #000;
					margin-right: 10px;
					padding-bottom: 5px;
					padding-left: 10px;
					padding-top: 2px;
					}
					div#submit {
					text-align: center;
					}
					span#loader {
					background-color: #fff;
					border: 2px solid #000;
					border-radius: 10px;
					color: #000;
					font-size: 10px;
					height: 50px;
					left: 40%;
					padding: 5px;
					position: absolute;
					text-align: center;
					top: 50%;
					visibility: hidden;
					width: 50px;
					z-index: 30;
					}

				</style>


				<div id="progress" class="mPoint_Info">
					<xsl:value-of select="labels/progress" />
					<br />
					<br />
				</div>

				<div id="wrapper">
					<div id="content">
						<form id="wirecardform" action="authorisewirecard.php"
							method="POST">
							<div>
								<!-- wirecard Required Data -->
								<input type="hidden" id="merchant_account_id" name="merchant_account_id"
									value="{wirecard/@merchant-account}" />
								<input type="hidden" id="request_id" name="request_id"
									value="{wirecard/hidden-fields/request_id}" />
								<input type="hidden" id="request_time_stamp" name="request_time_stamp"
									value="{wirecard/request_time_stamp}" />
								<input type="hidden" id="transaction_type" name="transaction_type"
									value="{wirecard/hidden-fields/transaction_type}" />
								<input type="hidden" id="requested_amount" name="requested_amount"
									value="{wirecard/hidden-fields/requested_amount}" />
								<input type="hidden" id="requested_amount_currency" name="requested_amount_currency"
									value="{wirecard/hidden-fields/requested_amount_currency}" />
								<input type="hidden" id="payment_ip_address" name="payment_ip_address"
									value="{wirecard/hidden-fields/payment_ip_address}" />
								<input type="hidden" id="email" name="email"
									value="{wirecard/hidden-fields/email}" />
								<input type="hidden" id="phone" name="phone"
									value="{wirecard/hidden-fields/phone}" />
								<input type="hidden" id="field_name_1" name="field_name_1"
									value="{wirecard/hidden-fields/field_name_1}" />
								<input type="hidden" id="field_name_2" name="field_name_2"
									value="{wirecard/hidden-fields/field_name_2}" />
								<input type="hidden" id="field_name_3" name="field_name_3"
									value="{wirecard/hidden-fields/field_name_3}" />
								<input type="hidden" id="field_value_3" name="field_value_3"
									value="{wirecard/hidden-fields/field_value_3}" />
								<input type="hidden" id="card_type" name="card_type"
									value="{wirecard/hidden-fields/card_type}" />
								<input type="hidden" id="notification_url_1" name="notification_url_1"
									value="{wirecard/hidden-fields/notification_url_1}" />
							</div>

							<div>
								<span class="mPoint_Label">
									<xsl:value-of select="labels/selected-card" />
								</span>
								<table>
									<tbody>
										<tr>
											<td>
												<img src="{$card-url}" width="{card/width}" height="{card/height}"
													alt="- {card/name} -" />
											</td>
											<td class="status" colspan="3">
												<xsl:value-of select="concat(' ', card/name)" />
											</td>
										</tr>
									</tbody>
								</table>
							</div>

							<div id="price">
								<span class="mPoint_Label">
									<xsl:value-of select="labels/price" />
								</span>
								<xsl:value-of select="transaction/price" />
							</div>

							<div class="mPoint_Help">Enter your card information below</div>

							<div id="cardinfo">
								<table cellpadding="0" cellspacing="0" class="grouped">
									<tr class="first-row">
										<th class="left-column">
											<xsl:value-of select="labels/first-name" />
										</th>
										<td class="right-column stretch" colspan="2">
											<input type="text" name="first_name" class="text" />
										</td>
									</tr>
									<tr class="first-row">
										<th class="left-column">
											<xsl:value-of select="labels/last-name" />
										</th>
										<td class="right-column stretch" colspan="2">
											<input type="text" name="last_name" class="text" />
										</td>
									</tr>
									<tr class="first-row">
										<th class="left-column">
											<xsl:value-of select="labels/card-number" />
										</th>
										<td class="right-column stretch" colspan="2">
											<input type="tel" name="card-number" pattern="[0-9]*"
												value="{$card-number}" maxlength="19" class="text" />
										</td>
									</tr>
									<tr class="row">
										<th class="left-column">
											<xsl:value-of select="labels/expiry-date" />
											:
											<div class="mPoint_Info">
												(
												<xsl:value-of select="labels/expiry-month" />
												/
												<xsl:value-of select="labels/expiry-year" />
												)
											</div>
										</th>
										<td class="right-column stretch" colspan="2">
											<input type="tel" id="expiry-month" name="emonth"
												maxlength="2" value="{$expiry-month}" size="3" pattern="[0-9]*" />
											<xsl:value-of select="concat(' ', '/', ' ')" />
											<input type="tel" id="expiry-year" name="eyear"
												minlength="4" maxlength="4" value="{$expiry-year}" size="4"
												pattern="[0-9]*" />
										</td>
									</tr>
									<tr class="row">
										<th class="left-column">
											<xsl:value-of select="labels/cvc" />
										</th>
										<td class="right-column " colspan="2">
											<xsl:choose>
												<!-- American Express -->
												<xsl:when test="card/@id = 1">
													<input type="tel" name="cvc" maxlength="4" value="{$cvc}"
														size="5" pattern="[0-9]*" />
												</xsl:when>
												<xsl:otherwise>
													<input type="tel" name="cvc" maxlength="3" value="{$cvc}"
														size="4" pattern="[0-9]*" />
												</xsl:otherwise>
											</xsl:choose>
										</td>
									</tr>
									<tr class="last-row combined-row">
										<td class="left-column right-column info stretch" colspan="3">
											<xsl:choose>
												<!-- American Express -->
												<xsl:when test="card/@id = 1">
													<xsl:value-of select="labels/cvc-4-help" />
												</xsl:when>
												<xsl:otherwise>
													<xsl:value-of select="labels/cvc-3-help" />
												</xsl:otherwise>
											</xsl:choose>
										</td>
									</tr>
								</table>
							</div>



							<div id="submit">
								<input type="submit" id="wirecardSubmit" name="wirecardSubmit"
									value="{labels/submit}" />
							</div>
						</form>
					</div>
				</div>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>