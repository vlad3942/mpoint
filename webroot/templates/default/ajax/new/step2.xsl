<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/content">
	<div id="create-account">
		<h1><xsl:value-of select="headline" /></h1>
		<div id="progress"><xsl:value-of select="labels/progress" /></div>
		<br />
		<div class="info"><xsl:value-of select="guide" /></div>
	
		<form id="activate-mobile" action="/new/sys/save_mobile.php" method="post">
			<div>
				<table align="center">
				<tr>
					<td>
						<label for="account-id" accesskey="A"><xsl:value-of select="labels/account-id" /></label>
					</td>
					<td><xsl:value-of select="session/accountid" /><input type="hidden" name="accountid" value="{session/accountid}" title="account-id" /></td>
					<td><img class="hidden" name="accountid_img" id="accountid_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
				</tr>
				<tr>
					<td>
						<label for="activation-code" accesskey="C"><xsl:value-of select="labels/activation-code" /></label>
					</td>
					<td><input type="code" id="code" name="code" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendInputData(document.getElementById('activate-mobile'), this);" tabindex="11" title="new-code" value="" maxlength="6" /></td>
					<td><img class="hidden" name="code_img" id="code_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
				</tr>
				<tr>
					<td class="submit" colspan="2">
						<input type="button" value="{labels/submit}" class="button" onclick="javascript:obj_Client.sendFormData(document.getElementById('activate-mobile') );" tabindex="14" title="save" />
					</td>
				</tr>
				</table>
			</div>
		</form>
	</div>
</xsl:template>

</xsl:stylesheet>