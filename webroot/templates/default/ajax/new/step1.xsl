<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/content">
	<script type="text/javascript">
		aCountries = new Array();
		 
		<xsl:for-each select="countries/item">
			aCountries[<xsl:value-of select="@id" />] = <xsl:value-of select="string-length(maxmobile)" />
		</xsl:for-each>
	</script>
	
	<div id="create-account">
		<h1><xsl:value-of select="headline" /></h1>
		<div id="progress"><xsl:value-of select="labels/progress" /></div>
		<br />
		<div class="info"><xsl:value-of select="guide" /></div>
		
		<xsl:apply-templates select="labels" />
	</div>
	
	<xsl:if test="//session/countryid &gt; 0">
		<script type="text/javascript">
			document.getElementById('mobile').maxLength = aCountries[<xsl:value-of select="//session/countryid" />];
		</script>
	</xsl:if>
</xsl:template>

<xsl:template match="/root/content/labels">
	<form id="new-account" action="/new/sys/create_account.php" method="post">
		<div>
			<table align="center">
			<tr>
				<td>
					<label for="countryid" accesskey="C"><xsl:value-of select="country" /></label>
				</td>
				<td>
				<xsl:choose>
				<xsl:when test="count(//countries/item) &gt; 1">
					<select id="countryid" name="countryid" onchange="javascript:document.getElementById('mobile').maxLength=aCountries[this.value]; obj_Client.clear(this); obj_Client.sendInputData(document.getElementById('new-account'), this);" tabindex="1">
						<option value="0"><xsl:value-of select="select" /></option>
						
						<xsl:for-each select="//countries/item">
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
					<xsl:value-of select="//countries/item/name" />
					<input type="hidden" id="countryid" name="countryid" value="{//countries/item/@id}" />
				</xsl:otherwise>
				</xsl:choose>
				</td>
				<td><img class="hidden" name="countryid_img" id="countryid_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
			</tr>
			<tr>
				<td>
					<label for="firstname" accesskey="F"><xsl:value-of select="firstname" /></label>
				</td>
				<td><input type="text" id="firstname" name="firstname" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendInputData(document.getElementById('new-account'), this);" tabindex="2" title="firstname" value="{//session/firstname}" /></td>
				<td><img class="hidden" name="firstname_img" id="firstname_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
			</tr>
			<tr>
				<td>
					<label for="lastname" accesskey="L"><xsl:value-of select="lastname" /></label>
				</td>
				<td><input type="text" id="lastname" name="lastname" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendInputData(document.getElementById('new-account'), this);" tabindex="3" title="lastname" value="{//session/lastname}" /></td>
				<td><img class="hidden" name="lastname_img" id="lastname_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
			</tr>
			<tr>
				<td colspan="2"><hr /></td>
			</tr>
			<tr>
				<td>
					<label for="password" accesskey="P"><xsl:value-of select="password" /></label>
				</td>
				<td><input type="password" id="password" name="password" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendInputData(document.getElementById('new-account'), this);" tabindex="4" title="password" value="{//session/password}" /></td>
				<td><img class="hidden" name="password_img" id="password_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
			</tr>
			<tr>
				<td>
					<label for="repeat-password" accesskey="R"><xsl:value-of select="repeat-password" /></label>
				</td>
				<td><input type="password" id="repeatpassword" name="repeatpassword" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendInputData(document.getElementById('new-account'), this);" tabindex="5" title="repeat-password" value="{//session/repeat-password}" /></td>
				<td><img class="hidden" name="repeatpassword_img" id="repeatpassword_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
			</tr>
			<tr>
				<td colspan="2"><hr /></td>
			</tr>
			<tr>
				<td>
					<label for="mobile" accesskey="M"><xsl:value-of select="mobile" /></label>
				</td>
				<td><input type="text" id="mobile" name="mobile" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendLinkedData(document.getElementById('new-account'), new Array(this, document.getElementById('new-account').countryid, document.getElementById('new-account').checksum) );" tabindex="6" title="mobile" value="{//session/mobile}" /></td>
				<td><img class="hidden" name="mobile_img" id="mobile_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
			</tr>
			<tr>
				<td>
					<label for="email" accesskey="E"><xsl:value-of select="email" /></label>
				</td>
				<td><input type="text" id="email" name="email" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendLinkedData(document.getElementById('new-account'), new Array(this, document.getElementById('new-account').countryid, document.getElementById('new-account').checksum) );" tabindex="7" title="email" value="{//session/email}" maxlength="50" /></td>
				<td><img class="hidden" name="email_img" id="email_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
			</tr>
			<tr>
				<td>
					<label for="checksum" accesskey="T"><xsl:value-of select="code" /></label>
				</td>
				<td><input type="text" id="checksum" name="checksum" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendInputData(document.getElementById('new-account'), this);" tabindex="8" title="checksum" value="{//session/checksum}" /></td>
				<td><img class="hidden" name="checksum_img" id="checksum_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
			</tr>
			<tr>
				<td colspan="2"></td>
			</tr>
			<tr>
				<td class="submit" colspan="2">
					<input type="button" value="{submit}" class="button" onclick="javascript:obj_Client.sendFormData(document.getElementById('new-account') );" tabindex="9" title="save" />
				</td>
			</tr>
			</table>
		</div>
	</form>
</xsl:template>
</xsl:stylesheet>