<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" media-type="text/html" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="../mobile.xsl" />

<xsl:template match="/root">
	<div id="progress" class="mPoint_Info">
		<xsl:if test="string-length(transaction/cancel-url) &gt; 0">
			<form action="{transaction/cancel-url}" method="post">
				<!-- Standard mPoint Variables -->
				<input type="hidden" name="mpoint-id" value="{transaction/@id}" />
				<input type="hidden" name="orderid" value="{transaction/orderid}" />
				<input type="hidden" name="amount" value="{transaction/amount}" />
				<input type="hidden" name="currency" value="{transaction/amount/@currency}" />
				<input type="hidden" name="mobile" value="{transaction/mobile}" />
				<input type="hidden" name="operator" value="{transaction/operator}" />
				<!-- Custom Client Variables -->
				<xsl:for-each select="accept/client-vars/item">
					<input type="hidden" name="{name}" value="{value}" />
				</xsl:for-each>
					
				<input name="cancel-payment" id="cancel-payment" type="submit" class="mPoint_Button" value="{labels/cancel}" />
			</form>
		</xsl:if>
		<br /><br />
	</div>
	
	<div id="store-card">
		<div id="outer-border">
			<div class="mPoint_Label"><xsl:value-of select="labels/info" /></div>
			<div id="inner-border">		
				<xsl:choose>
					<!-- WorldPay -->
					<xsl:when test="psp/@id = 4">
						<xsl:apply-templates select="psp" mode="worldpay" />
					</xsl:when>
					<!-- Error -->
					<xsl:otherwise>
						
					</xsl:otherwise>
				</xsl:choose>
			</div>
			<div class="mPoint_Help"><xsl:copy-of select="labels/disclaimer" /></div>
		</div>
	</div>
</xsl:template>

<xsl:template match="psp" mode="worldpay">
	<table>
	<tr>
		<td>
		<form action="{func:appendQueryString('/worldpay/sys/rxml.php') }" method="post">
			<div>
				<!-- WorldPay data -->
				<input type="hidden" name="cardid" value="{@card-id}" />
				<input type="hidden" name="merchant-code" value="{account}" />
				<input type="hidden" name="installation-id" value="{sub-account}" />
				<input type="hidden" name="currency" value="{currency}" />
				<input type="hidden" name="store-card" value="true" />
				
				<input type="submit" value="{//labels/yes}" class="mPoint_Button" />
			</div>
		</form>
		</td>
		<td>
		<form action="{func:appendQueryString('/worldpay/sys/rxml.php') }" method="post">
			<div>
				<!-- WorldPay data -->
				<input type="hidden" name="cardid" value="{@card-id}" />
				<input type="hidden" name="merchant-code" value="{account}" />
				<input type="hidden" name="installation-id" value="{sub-account}" />
				<input type="hidden" name="currency" value="{currency}" />
				<input type="hidden" name="store-card" value="false" />
				
				<input type="submit" value="{//labels/no}" class="mPoint_Button" />
			</div>
		</form>
		</td>
	</tr>
	</table>
</xsl:template>

</xsl:stylesheet>