<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/missing-data">
	<!-- Window Bar -->
	<div class="window-bar" onmousedown="javascript:obj_Window.moveWindow(this.parentNode);" onmouseover="this.className = 'window-bar iehover';" onmouseout="this.className = 'window-bar';">
		<img onclick="javascript:obj_Window.closeWindow('missing-data');" src="/img/x.gif" width="16" height="16" alt="- Close -" />
	</div>
	
	<!-- Window Page -->
	<div class="window-content">
		<h2><xsl:value-of select="headline" /></h2>
		<br />
		<div class="info">
			<xsl:choose>
			<xsl:when test="guide/@text = 'mobile'">
				<xsl:value-of select="guide/missing-mobile" />
			</xsl:when>
			<xsl:when test="guide/@text = 'email'">
				<xsl:value-of select="guide/missing-email" />
			</xsl:when>
			<xsl:when test="guide/@text = 'info'">
				<xsl:value-of select="guide/missing-info" />
			</xsl:when>
			</xsl:choose>
		</div>
		<br />
		<div class="submit">
			<button type="button" onclick="javascript:obj_Window.closeWindow('missing-data');" tabindex="1" title="close"><xsl:value-of select="labels/close" /></button>
			<button type="button" onclick="javascript:obj_Client.changePage('/home/my_account.php'); obj_Window.closeWindow('missing-data');" tabindex="2" title="my-account"><xsl:value-of select="labels/my-account" /></button>
		</div>
	</div>
</xsl:template>
</xsl:stylesheet>