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
		<form action="{func:constLink('/new/sys/save_pwd.php') }" method="post">
			<div><xsl:value-of select="labels/info" /></div>
			<div id="accountinfo">
				<!-- Country -->
				<div>
					<div class="mPoint_Label"><xsl:value-of select="labels/country" />:</div>
					<xsl:choose>
					<xsl:when test="//user-info/mobile/@countryid &gt;= 100">
						<xsl:value-of select="country-configs/config[@id = //user-info/mobile/@countryid]/name" />
					</xsl:when>
					<xsl:when test="count(//country-configs/config) &gt; 1">
						<select id="countryid" name="countryid">
							<option value="0"><xsl:value-of select="labels/select" /></option>
							
							<xsl:for-each select="//country-configs/config">
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
						<xsl:value-of select="//country-configs/config/name" />
						<input type="hidden" id="countryid" name="countryid" value="{//country-configs/config/@id}" />
					</xsl:otherwise>
					</xsl:choose>
				</div>
				<!-- Mobile -->
				<div>
					<div class="mPoint_Label"><xsl:value-of select="labels/mobile" />:</div>
					<xsl:choose>
					<xsl:when test="//user-info/mobile &gt; 10000">
						<xsl:value-of select="//user-info/mobile" />
					</xsl:when>
					<xsl:otherwise>
						<input type="text" id="mobile" name="mobile" class="mPoint_Number" value="{//session/mobile}" />
					</xsl:otherwise>
					</xsl:choose>
				</div>
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
				<!-- Transfer Code -->
				<div>
					<div class="mPoint_Label"><xsl:value-of select="labels/code" />:</div>
					<input type="text" name="checksum" value="{session/checksum}" />
				</div>
			</div>		
			<div>
				<input type="submit" value="{labels/submit}" class="mPoint_Button" />
			</div>
		</form>
	</div>
</xsl:template>

</xsl:stylesheet>