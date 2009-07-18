<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/content">
	<div id="my-account">
		<h1><xsl:value-of select="headline" /></h1>
		
		<table align="center">
		<tr>
			<td id="account-info">
				<h2><xsl:value-of select="labels/account-info" /></h2>
				<form id="edit-info" action="/home/sys/save_info.php" method="post">
					<div>
						<xsl:apply-templates select="account" />
					</div>
				</form>
			</td>
			<td id="stored-cards">
				<h2><xsl:value-of select="labels/multiple-stored-cards" /></h2>
				<table cellpadding="0" cellspacing="0">
					<!-- Show Preferred Card -->
					<tr>
						<td colspan="4" class="label"><xsl:value-of select="labels/preferred" /></td>
					</tr>
					<xsl:apply-templates select="stored-cards/card[@preferred = 'true']" />
					<!-- List Other Cards -->
					<tr>
						<td colspan="4" class="label"><xsl:value-of select="labels/other" /></td>
					</tr>
					<xsl:apply-templates select="stored-cards/card[@preferred = 'false']" />
				</table>
			</td>
		</tr>
		</table>
	</div>
</xsl:template>

<xsl:template match="account">
	<table>
	<tr>
		<td>
			<label for="id" accesskey="C"><xsl:value-of select="//labels/id" /></label>
		</td>
		<td><xsl:value-of select="@id" /></td>
		<td><img class="hidden" name="id_img" id="id_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
	</tr>
	<tr>
		<td>
			<label for="countryid" accesskey="C"><xsl:value-of select="//labels/country" /></label>
		</td>
		<td>
			<xsl:value-of select="//country-config/name" />
			<input type="hidden" id="countryid" name="countryid" value="{@countryid}" />
		</td>
		<td><img class="hidden" name="countryid_img" id="countryid_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
	</tr>
	<tr>
		<td>
			<label for="password" accesskey="P"><xsl:value-of select="//labels/password" /></label>
		</td>
		<td><xsl:value-of select="password/@mask" /></td>
		<td><a href="#" onclick="javascript:obj_Window.openWindow('edit-password', 'my-account', '/home/edit_password.php', 'edit-password', new Array(obj_Client, obj_Client.changePage) );" tabindex="1" title="password"><xsl:value-of select="//labels/edit" /></a></td>
	</tr>
	<tr>
		<td>
			<label for="firstname" accesskey="F"><xsl:value-of select="//labels/firstname" /></label>
		</td>
		<td><input type="text" id="firstname" name="firstname" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendInputData(document.getElementById('edit-info'), this);" tabindex="2" title="firstname" value="{firstname}" /></td>
		<td><img class="hidden" name="firstname_img" id="firstname_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
	</tr>
	<tr>
		<td>
			<label for="lastname" accesskey="L"><xsl:value-of select="//labels/lastname" /></label>
		</td>
		<td><input type="text" id="lastname" name="lastname" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendInputData(document.getElementById('edit-info'), this);" tabindex="3" title="lastname" value="{lastname}" /></td>
		<td><img class="hidden" name="lastname_img" id="lastname_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
	</tr>
	<tr>
		<td>
			<label for="mobile" accesskey="M"><xsl:value-of select="//labels/mobile" /></label>
		</td>
		<td><xsl:value-of select="mobile" /></td>
		<td><a href="#" onclick="javascript:obj_Window.openWindow('edit-mobile', 'my-account', '/home/edit_mobile.php', 'edit-mobile', new Array(obj_Client, obj_Client.changePage) );" tabindex="4" title="mobile"><xsl:value-of select="//labels/edit" /></a></td>
	</tr>
	<tr>
		<td>
			<label for="email" accesskey="E"><xsl:value-of select="//labels/email" /></label>
		</td>
		<td><xsl:value-of select="email" /></td>
		<td><a href="#" onclick="javascript:obj_Window.openWindow('edit-email', 'my-account', '/home/edit_email.php', 'edit-email', new Array(obj_Client, obj_Client.changePage) );" tabindex="5" title="email"><xsl:value-of select="//labels/edit" /></a></td>
	</tr>
	<tr>
		<td class="submit" colspan="2">
			<input type="button" value="{//labels/submit}" class="button" onclick="javascript:obj_Client.sendFormData(document.getElementById('edit-info') );" tabindex="6" title="save" />
		</td>
	</tr>
	</table>
</xsl:template>

<xsl:template match="card">
	<xsl:variable name="css">
		<xsl:choose>
			<!-- Even row -->
			<xsl:when test="position()+1 mod 2 = 0">
				<xsl:text>mPoint_Even</xsl:text>
			</xsl:when>
			<!-- Odd row -->
			<xsl:otherwise>
				<xsl:text>mPoint_Uneven</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
			
	<tr class="{$css}">
		<td><input type="hidden" name="id" value="{@id}" /></td>
		<td><img src="/img/card_{type/@id}.png" width="31" height="20" alt="- {type} -" /></td>
		<td><xsl:value-of select="mask" /></td>
		<td class="info">(<xsl:value-of select="expiry" />)</td>
	</tr>
</xsl:template>

</xsl:stylesheet>