<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/new-card">
	<!-- Window Bar -->
	<div class="window-bar" onmousedown="javascript:obj_Window.moveWindow(this.parentNode);" onmouseover="this.className = 'window-bar iehover';" onmouseout="this.className = 'window-bar';">
		<img onclick="javascript:obj_Window.closeWindow('new-card');" src="/img/x.gif" width="16" height="16" alt="- Close -" />
	</div>
	
	<!-- Window Page -->
	<div class="window-content">
		<h2><xsl:value-of select="headline" /></h2>
		<div class="info"><xsl:value-of select="guide" /></div>
		<br />
		<form id="add-new-card" name="add-new-card" action="/buy/topup.php" method="post" target="Add-Card">
			<div>
				<!-- mPoint client parameters -->
				<input type="hidden" name="clientid" value="{//client-config/@id}" />
				<xsl:if test="//account-config/@id &gt; 0">
					<input type="hidden" name="account" value="{//account-config/@id}" />
				</xsl:if>
				<!-- mPoint transaction parameters -->
				<input type="hidden" name="amount" value="{//country-config/add-card-amount}" />
				<!-- mPoint end-user parameters -->
				<input type="hidden" name="mobile" value="{//account/mobile}" />
				<xsl:if test="//account/operator &gt; 0">
					<input type="hidden" name="operator" value="{//account/operator}" />
				</xsl:if>
				<xsl:if test="string-length(//account/email) &gt; 0">
					<input type="hidden" name="email" value="{//account/email}" />	
				</xsl:if>
				<input type="hidden" name="auto-store-card" value="true" />
				<!-- mPoint customization parameters -->
				<input type="hidden" name="logo-url" value="{//client-config/logo-url}" />
				<input type="hidden" name="css-url" value="{//client-config/css-url}" />
				<!-- <input type="hidden" name="language" value="{/root/system/language}" />  -->
				<input type="hidden" name="mode" value="{//client-config/@mode}" />
			</div>
			<div class="submit">
				<button type="button" onclick="javascript:window.open('', 'Add-Card', 'width=450,height=600,status=yes,resizable=no,scrollbars=no'); document.getElementById('add-new-card').submit(); obj_Window.closeWindow('new-card');"><xsl:value-of select="labels/submit" /></button>
			</div>
		</form>
	</div>
</xsl:template>
</xsl:stylesheet>