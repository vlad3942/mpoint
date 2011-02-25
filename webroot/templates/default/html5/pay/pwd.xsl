<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="html" indent="no" media-type="text/html" doctype-public="HTML" omit-xml-declaration="yes" standalone="yes" />
<xsl:include href="../mobile.xsl" />

<xsl:template match="/root">
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
	
	<div id="my-account">
		<form action="{func:constLink('/pay/sys/save_pwd.php') }" method="post">
			<div class="mPoint_Help"><xsl:value-of select="labels/info" /></div>
			<div id="accountinfo">
				<!-- Password Info -->
				<div class="mPoint_Label">
					<xsl:value-of select="labels/password" />:<br />
					<input type="password" name="pwd" value="" />
				</div>
				<!-- Repeat Password -->
				<div class="mPoint_Label">
					<xsl:value-of select="labels/repeat-password" />:<br />
					<input type="password" name="rpt" value="" />
				</div>
				<!-- Card Name -->
				<div class="mPoint_Label">
					<xsl:value-of select="labels/card-name" />:<br />
					<input type="text" name="name" value="" maxlength="50" />
				</div>
				<div class="mPoint_Info"><xsl:value-of select="labels/card-name-help" /></div>
			</div>
			<div>
				<input type="submit" value="{labels/submit}" class="mPoint_Button" />
			</div>
		</form>
	</div>
</xsl:template>

</xsl:stylesheet>