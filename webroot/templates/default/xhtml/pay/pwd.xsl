<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="ISO-8859-15" indent="yes" media-type="application/xhtml+xml" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="../mobile.xsl"/>

<xsl:template match="/root">
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
	
	<div id="my-account">
		<form action="{func:constLink('/pay/sys/save_pwd.php') }" method="post">
			<div><xsl:value-of select="labels/info" /></div>
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
			</div>		
			<div>
				<input type="submit" value="{labels/submit}" class="mPoint_Button" />
			</div>
		</form>
	</div>
</xsl:template>

</xsl:stylesheet>