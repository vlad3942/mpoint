<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="ISO-8859-15" indent="yes" media-type="application/xhtml+xml" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="../mobile.xsl" />

<xsl:template match="/root">
	<div class="mPoint_Info">
		<br />
		<xsl:value-of select="labels/info" />
	</div>
	
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
	
	<div id="top-up">
		<!-- List Deposit Options -->
		<xsl:for-each select="deposits/option[amount + //account/balance &lt;= //country-config/max-balance]">
			<xsl:variable name="css">
				<xsl:choose>
					<!-- Even row -->
					<xsl:when test="position() mod 2 = 0">
						<xsl:text>Even</xsl:text>
					</xsl:when>
					<!-- Uneven row -->
					<xsl:otherwise>
						<xsl:text>Uneven</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
				
			<form action="{/root/system/protocol}://{/root/system/host}/buy/topup.php" method="post">
				<div>
					<!-- mPoint client parameters -->
					<input type="hidden" name="clientid" value="{//client-config/@id}" />
					<xsl:if test="//account-config/@id &gt; 0">
						<input type="hidden" name="account" value="{//account-config/@id}" />
					</xsl:if>
					<!-- mPoint transaction parameters -->
					<input type="hidden" name="amount" value="{amount}" />
					<!-- mPoint end-user parameters -->
					<input type="hidden" name="mobile" value="{//transaction/mobile}" />
					<input type="hidden" name="operator" value="{//transaction/operator}" />
					<xsl:if test="string-length(//transaction/email) &gt; 0">
						<input type="hidden" name="email" value="{//transaction/email}" />	
					</xsl:if>
					<!-- mPoint customization parameters -->
					<input type="hidden" name="logo-url" value="{//transaction/logo/url}" />
					<input type="hidden" name="css-url" value="{//transaction/css-url}" />
					<input type="hidden" name="callback-url" value="{//transaction/callback-url}" />
					<input type="hidden" name="accept-url" value="{//transaction/accept-url}" />
					<input type="hidden" name="cancel-url" value="{//transaction/cancel-url}" />
					<input type="hidden" name="language" value="{/root/system/language}" />
					<input type="hidden" name="mode" value="{//transaction/@mode}" />
					
					<!--
					  - The colspan attribute in the table below ensures that the page is rendered correctly on the Nokia 6230.
					  - Nokia 6230 assigns the same width to all table columns but by using the colspan attribute (eventhough it really isn't needed)
					  - the phone will assign 25% of the screen width to the card logo and 75% of the screen width to the card name.
					  -->
					<table cellpadding="0" cellspacing="0" style="width:100%;">
					<tr class="{concat('mPoint_', $css) }">
						<td style="width:40px; padding-right:5px;"><img src="{/root/system/protocol}://{/root/system/host}/img/topup.png" width="40" height="40" alt="" /></td>
						<td colspan="3"><input type="submit" value="{price}" class="{concat('mPoint_', $css, '_Card_Button') }" /></td>
					</tr>
					</table>
				</div>
			</form>
		</xsl:for-each>
	</div>
</xsl:template>
</xsl:stylesheet>