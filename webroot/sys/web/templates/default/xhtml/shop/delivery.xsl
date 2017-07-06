<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" media-type="application/xhtml+xml" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="../mobile.xsl" />

<xsl:template match="/root">
	<div class="mPoint_Info">
		<br />
		<xsl:value-of select="labels/info" />
	</div>
	
	<xsl:if test="country-config/address-lookup = 'true'">
		<form action="{func:constLink('sys/get_delivery_info.php')}" method="post">
			<div class="mPoint_Label">
				<xsl:value-of select="labels/phone-no" /><br />
				<input name="mobile" value="{session/mobile}" size="{string-length(country-config/max-mobile)}" maxlength="{string-length(country-config/max-mobile) }" style="-wap-input-format:'*N';" />
				<input type="submit" value="{labels/find}" class="mPoint_Button" />
			</div>
		</form>
	</xsl:if>
	
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
	
	<form action="{func:constLink('sys/save_delivery_info.php')}" method="post">
		<div id="delivery">
			<div class="mPoint_Label">
				<xsl:value-of select="labels/name" /><br />
				<input name="name" value="{session/name}" />
			</div>
			<div class="mPoint_Label">
				<xsl:value-of select="labels/company" /><br />
				<input name="company" value="{session/company}" />
			</div>
			<div class="mPoint_Label">
				<xsl:value-of select="labels/street" /><br />
				<input name="street" value="{session/street}" />
			</div>
			<div class="mPoint_Label">
				<xsl:value-of select="labels/zipcode" /> &amp; <xsl:value-of select="labels/city" /><br />
				<!-- Construct Zip Code Input -->
				<xsl:choose>
					<xsl:when test="country-config/@id = 100">
						<input name="zipcode" value="{session/zipcode}" size="4" maxlength="4" style="-wap-input-format:'*N';" />
					</xsl:when>
					<xsl:when test="country-config/@id = 101">
						<input name="zipcode" value="{session/zipcode}" size="4" maxlength="5" style="-wap-input-format:'*N';" />
					</xsl:when>
					<xsl:when test="country-config/@id = 200">
						<input name="zipcode" value="{session/zipcode}" size="5" maxlength="5" style="-wap-input-format:'*N';" />
					</xsl:when>
					<xsl:otherwise>
						<input name="zipcode" value="{session/zipcode}" size="6" maxlength="6" />
					</xsl:otherwise>
				</xsl:choose>
				<input name="city" value="{session/city}" size="15" />
			</div>
			<xsl:if test="shop-config/delivery-date = 'true'">
				<div class="mPoint_Label">
					<xsl:value-of select="labels/delivery-date/label" />
					<span class="mPoint_Info">
						(<xsl:value-of select="labels/delivery-date/year" />-<xsl:value-of select="labels/delivery-date/month" />-<xsl:value-of select="labels/delivery-date/day" />)
					</span>
					<br />
					<input name="year" value="{session/year}" size="4" maxlength="4" style="-wap-input-format:'*N';" />-
					<input name="month" value="{session/month}" size="2" maxlength="2" style="-wap-input-format:'*N';" />-
					<input name="day" value="{session/day}" size="2" maxlength="2" style="-wap-input-format:'*N';" />
				</div>
			</xsl:if>
		</div>
		<div>
			<input type="submit" value="{labels/next}" class="mPoint_Button" />
		</div>
	</form>
</xsl:template>
		
</xsl:stylesheet>