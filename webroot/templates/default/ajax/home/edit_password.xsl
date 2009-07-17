<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/edit-password">
	<!-- Window Bar -->
	<div class="window-bar" onmousedown="javascript:obj_Window.moveWindow(this.parentNode);" onmouseover="this.className = 'window-bar iehover';" onmouseout="this.className = 'window-bar';">
		<img onclick="javascript:obj_Window.closeWindow('edit-password');" src="/img/x.gif" width="16" height="16" alt="- Close -" />
	</div>
	
	<!-- Window Page -->
	<div class="window-content">
		<h2><xsl:value-of select="headline" /></h2>
	
		<form id="edit-pwd" action="/home/sys/save_password.php" method="post">
			<div>
				<table>	
				<tr>
					<td>
						<label for="old-password" accesskey="O"><xsl:value-of select="//labels/old-password" /></label>
					</td>
					<td><input type="password" id="oldpassword" name="oldpassword" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendInputData(document.getElementById('edit-pwd'), this);" tabindex="11" title="old-password" value="" /></td>
					<td><img class="hidden" name="oldpassword_img" id="oldpassword_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
				</tr>
				<tr>
					<td>
						<label for="new-password" accesskey="N"><xsl:value-of select="//labels/new-password" /></label>
					</td>
					<td><input type="password" id="newpassword" name="newpassword" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendInputData(document.getElementById('edit-pwd'), this);" tabindex="12" title="new-password" value="" /></td>
					<td><img class="hidden" name="newpassword_img" id="newpassword_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
				</tr>
				<tr>
					<td>
						<label for="repeat-password" accesskey="R"><xsl:value-of select="//labels/repeat-password" /></label>
					</td>
					<td><input type="password" id="repeatpassword" name="repeatpassword" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendInputData(document.getElementById('edit-pwd'), this);" tabindex="13" title="repeat-password" value="" /></td>
					<td><img class="hidden" name="repeatpassword_img" id="repeatpassword_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
				</tr>
				<tr>
					<td class="submit" colspan="2">
						<input type="button" value="{labels/submit}" class="button" onclick="javascript:obj_Client.sendFormData(document.getElementById('edit-pwd') );" tabindex="14" title="save" />
					</td>
				</tr>
				</table>
			</div>
		</form>
	</div>
</xsl:template>
</xsl:stylesheet>