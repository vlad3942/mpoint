<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" media-type="application/xhtml+xml" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="../mobile.xsl"/>

<xsl:template match="/root">
	<xsl:variable name="cardid" select="session/cardid" />
	
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
	
	<div id="my-account">
		<div id="selected-card">
			<div class="mPoint_Label"><xsl:value-of select="labels/selected-card" />:</div>
			<img src="{/root/system/protocol}://{/root/system/host}/img/{cards/item[@id = $cardid]/logo-width}x{cards/item[@id = $cardid]/logo-height}_card_{$cardid}_{/root/system/session/@id}.png" width="{cards/item[@id = $cardid]/logo-width}" height="{cards/item[@id = $cardid]/logo-height}" alt="" />
			<xsl:value-of select="concat(' ', cards/item[@id = $cardid]/name)" />
		</div>
		
		<form action="{func:constLink('/pay/sys/save_pwd.php') }" method="post">
			<div>
				<input type="hidden" name="cardid" value="{session/cardid}" />
			</div>
			
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
					<input type="text" name="name" value="{session/name}" maxlength="50" />
				</div>
				<div class="mPoint_Info"><xsl:value-of select="labels/card-name-help" /></div>
			</div>
			<div id="submit">
				<input type="submit" value="{labels/submit}" class="mPoint_Button" />
			</div>
		</form>
	</div>
</xsl:template>

</xsl:stylesheet>