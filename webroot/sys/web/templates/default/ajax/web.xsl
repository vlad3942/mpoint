<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">

<xsl:template match="messages">
	<xsl:if test="count(item) &gt; 0">
		<div class="status">
			<xsl:choose>
			<xsl:when test="count(item) = 1">
				<xsl:copy-of select="item" />
			</xsl:when>
			<xsl:otherwise>
				<ul>
				<xsl:for-each select="item">
					<li><xsl:copy-of select="." /></li>
				</xsl:for-each>
				</ul>
			</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:if>
</xsl:template>

<xsl:template name="headline">
	<xsl:param name="headline" />
	
	<table cellpadding="0" cellspacing="0">
	<tr>
		<td width="50%" style="padding-top:12px;"><hr /></td>
		<td><h2><xsl:value-of select="$headline" /></h2></td>
		<td width="50%" style="padding-top:12px;"><hr /></td>
	</tr>
	</table>
</xsl:template>

<xsl:template match="/root/session">
</xsl:template>

<xsl:template name="dropdown" match="item">
	<xsl:param name="form" />
	<xsl:param name="name" />
	<xsl:param name="child" />
	<xsl:param name="data" />
	<xsl:param name="select" />
	<xsl:param name="session" />
	
	<xsl:choose>
	<xsl:when test="count(item) &gt; 1">
		<select name="{$name}" onchange="javascript:populateChild(document.getElementById('{$form}').{$child}, {$data}, this.value, 0)">
			<option value="0"><xsl:value-of select="$select" /></option>
			
			<xsl:for-each select="item">
				<xsl:choose>
				<xsl:when test="@id = $session">
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
		<xsl:value-of select="item/name" />
		<input type="hidden" name="{$name}" value="{item/@id}" />
		<script type="text/javascript">
			populateChild(document.getElementById('<xsl:value-of select="$form" />').<xsl:value-of select="$child" />, <xsl:value-of select="$data" />, <xsl:value-of select="item/@id" />, 0);
		</script>
	</xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>