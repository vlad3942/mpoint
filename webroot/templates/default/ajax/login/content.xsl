<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/content">
	
	<div id="login">
		<h1><xsl:value-of select="headline" /></h1>
		<form id="send-login" action="/login/sys/auth.php" method="post">
			<p>
				<table align="center">
				<tr>
					<td>
						<label for="countryid" accesskey="C"><xsl:value-of select="labels/country" /></label>
					</td>
					<td>
					<xsl:choose>
					<xsl:when test="count(countries/item) &gt; 1">
						<select id="countryid" name="countryid" onchange="javascript:obj_Client.clear(this); obj_Client.sendInputData(document.getElementById('send-login'), this);" tabindex="1">
							<option value="0"><xsl:value-of select="labels/select" /></option>
							
							<xsl:for-each select="countries/item">
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
						<xsl:value-of select="countries/item/name" />
						<input type="hidden" id="countryid" name="countryid" value="{countries/item/@id}" />
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
					<td>
						<label for="password" accesskey="P"><xsl:value-of select="labels/password" /></label>
						<br /><br />
					</td>
					<td>
						<input type="password" id="password" name="password" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendInputData(document.getElementById('send-login'), this);" tabindex="3" title="password" />
						<br />
						<a href="#" onclick="javascript:obj_Client.changePage('/login/forgot_password.php');" tabindex="5" title="forgot-password"><xsl:value-of select="labels/forgot-password" /></a>
					</td>
					<td><img class="hidden" name="password_img" id="password_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
				</tr>
				<tr>
					<td class="submit" colspan="2"><button type="button" class="button" onclick="javascript:obj_Client.sendFormData(document.getElementById('send-login') );" tabindex="4" title="login"><xsl:value-of select="labels/submit" /></button></td>
				</tr>
				</table>
			</p>
		</form>
	</div>
	<div id="sign-up">
		<h2>
			<a href="#" onclick="javascript:obj_Client.changePage('/new/step1.php');" tabindex="6" title="sign-up">
				<xsl:value-of select="labels/sign-up" />
			</a>
		</h2>
	</div>
</xsl:template>
</xsl:stylesheet>