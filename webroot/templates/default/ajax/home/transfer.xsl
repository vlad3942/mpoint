<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/content">
	<div id="transfer">
		<h1><xsl:value-of select="headline" /></h1>
		<div id="overview">
			<xsl:value-of select="overview" />
		</div>
		<br />
		<form id="make-transfer" action="/home/sys/make_transfer.php" method="post">
			<div>
				<table align="center">
				<tr>
					<td>
						<label><xsl:value-of select="labels/balance" /></label>
					</td>
					<td class="data"><xsl:value-of select="account/funds" /></td>
					<td></td>
				</tr>
				<tr>
					<td>
						<label for="countryid" accesskey="C"><xsl:value-of select="labels/country" /></label>
					</td>
					<td class="data">
					<xsl:choose>
					<xsl:when test="count(//countries/item) &gt; 1">
						<select id="countryid" name="countryid" onchange="javascript:obj_Client.clear(this); obj_Client.sendInputData(document.getElementById('make-transfer'), this);" tabindex="1">
							<option value="0"><xsl:value-of select="labels/select" /></option>
							
							<xsl:for-each select="//countries/item">
								<xsl:choose>
								<xsl:when test="@id = //session/countryid">
									<option value="{@id}" selected="selected"><xsl:value-of select="name" /></option>
								</xsl:when>
								<xsl:otherwise>
									<option value="{@id}"><xsl:value-of select="name" /></option>
								</xsl:otherwise>
								</xsl:choose>
							</xsl:for-each>
						</select>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="//countries/item/name" />
						<input type="hidden" id="countryid" name="countryid" value="{//countries/item/@id}" />
					</xsl:otherwise>
					</xsl:choose>
					</td>
					<td><img class="hidden" name="countryid_img" id="countryid_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
				</tr>
				<tr>
					<td>
						<label for="recipient" accesskey="R"><xsl:value-of select="labels/recipient/label" /></label>
						<div class="info">(<xsl:value-of select="labels/recipient/help" />)</div>
					</td>
					<td class="data"><input type="text" id="recipient" name="recipient" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendLinkedData(document.getElementById('make-transfer'), new Array(this, document.getElementById('make-transfer').countryid) );" tabindex="2" title="recipient" value="" maxlength="50" /></td>
					<td><img class="hidden" name="recipient_img" id="recipient_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
				</tr>
				<tr>
					<td>
						<label for="amount" accesskey="A"><xsl:value-of select="labels/amount/label" /></label>
						<div class="info">(<xsl:value-of select="labels/amount/help" />)</div>
					</td>
					<td class="data"><input type="text" id="amount" name="amount" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendInputData(document.getElementById('make-transfer'), this);" tabindex="3" title="amount" value="" maxlength="4" /></td>
					<td><img class="hidden" name="amount_img" id="amount_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
				</tr>
				<tr>
					<td class="submit" colspan="2">
						<input type="button" value="{labels/submit}" class="button" onclick="javascript:obj_Client.sendFormData(document.getElementById('make-transfer') );" tabindex="4" title="make-transfer" />
					</td>
				</tr>
				</table>
			</div>
		</form>
	</div>
</xsl:template>
</xsl:stylesheet>