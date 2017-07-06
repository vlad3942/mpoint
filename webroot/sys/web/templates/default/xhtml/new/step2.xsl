<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" media-type="application/xhtml+xml" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="../admin.xsl"/>

<xsl:template match="/root/content">
	<xsl:if test="string-length(//user-info/mobile/@countryid) &lt; 3">
		<div id="progress" class="mPoint_Info">
			<xsl:value-of select="labels/progress" />
		</div>
	</xsl:if>
	<br /><br />
	
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
	
	<div id="my-account">
		<form action="{func:constLink('/new/sys/save_mob.php') }" method="post">
			<div><xsl:value-of select="labels/info" /></div>
			<div id="accountinfo">
				<!-- Accoint ID -->
				<div>
					<span class="mPoint_Label"><xsl:value-of select="labels/account-id" />:</span>
					<xsl:value-of select="session/accountid" />
				</div>
				<!-- Activation Code -->
				<div>
					<div class="mPoint_Label"><xsl:value-of select="labels/activation-code" />:</div>
					<input type="text" name="code" value="{session/code}" maxlength="6" />
				</div>
			</div>		
			<div>
				<input type="submit" value="{labels/submit}" class="mPoint_Button" />
			</div>
		</form>
	</div>
</xsl:template>

</xsl:stylesheet>