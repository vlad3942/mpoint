<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />


<xsl:template match="/root/content">
	
	<div id="forgot-password">
		<h1><xsl:value-of select="headline" /></h1>
		
		<form name="send-password" id="send-password" action="/login/sys/send_password.php">
			<p>
				<table align="center">
				<tr>
					<td>
						<label for="countryid" accesskey="C"><xsl:value-of select="labels/country" /></label>
					</td>
					<td>
					<xsl:choose>
					<xsl:when test="count(country-configs/config) &gt; 1">
						<select id="countryid" name="countryid" onchange="javascript:obj_Client.clear(this); obj_Client.sendInputData(document.getElementById('send-login'), this);" tabindex="1">
							<option value="0"><xsl:value-of select="labels/select" /></option>
							
							<xsl:for-each select="country-configs/config">
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
						<xsl:value-of select="country-configs/config/name" />
						<input type="hidden" id="countryid" name="countryid" value="{country-configs/config/@id}" />
					</xsl:otherwise>
					</xsl:choose>
					</td>
					<td><img class="hidden" name="countryid_img" id="countryid_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
				</tr>
				<tr>
					<td>
						<label for="username" accesskey="U"><xsl:value-of select="labels/username" /></label>
					</td>
					<td><input type="text" id="username" name="username" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendLinkedData(document.getElementById('send-login'), new Array(this, document.getElementById('send-login').countryid) );" tabindex="2" title="username" value="{session/username}" /></td>
					<td><img class="hidden" name="username_img" id="username_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
				</tr>
				<tr>
					<td class="submit" colspan="2">
						<button type="button" onclick="javascript:obj_Client.sendFormData(document.getElementById('send-password') );" tabindex="2" title="login"><xsl:value-of select="labels/submit" /></button>
					</td>
				</tr>
				</table>
			</p>
		</form>
	</div>
</xsl:template>
</xsl:stylesheet>