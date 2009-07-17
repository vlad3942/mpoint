<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/edit-email">
	<!-- Window Bar -->
	<div class="window-bar" onmousedown="javascript:obj_Window.moveWindow(this.parentNode);" onmouseover="this.className = 'window-bar iehover';" onmouseout="this.className = 'window-bar';">
		<img onclick="javascript:obj_Window.closeWindow('edit-email');" src="/img/x.gif" width="16" height="16" alt="- Close -" />
	</div>
	
	<!-- Window Page -->
	<div class="window-content">
		<h2><xsl:value-of select="headline" /></h2>
		<div id="progress"><xsl:value-of select="labels/progress" /></div>
		<br />
		<div class="info"><xsl:value-of select="guide" /></div>
		<br />
		<form id="edit-mail" action="/home/sys/send_link.php" method="post">
			<div>
				<table>	
				<tr>
					<td>
						<label for="old-email" accesskey="O"><xsl:value-of select="labels/old-email" /></label>
					</td>
					<td><xsl:value-of select="account/email" /></td>
					<td></td>
				</tr>
				<tr>
					<td>
						<label for="new-email" accesskey="N"><xsl:value-of select="labels/new-email" /></label>
					</td>
					<td><input type="text" id="email" name="email" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendInputData(document.getElementById('edit-mail'), this);" tabindex="11" title="new-mail" value="" maxlength="50" /></td>
					<td><img class="hidden" name="email_img" id="email_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
				</tr>
				<tr>
					<td class="submit" colspan="2">
						<input type="button" value="{labels/submit}" class="button" onclick="javascript:obj_Client.sendFormData(document.getElementById('edit-mail') );" tabindex="12" title="save" />
					</td>
				</tr>
				</table>
			</div>
		</form>
	</div>
</xsl:template>
</xsl:stylesheet>