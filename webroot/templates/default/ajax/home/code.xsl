<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/edit-mobile">
	<!-- Window Bar -->
	<div class="window-bar" onmousedown="javascript:obj_Window.moveWindow(this.parentNode);" onmouseover="this.className = 'window-bar iehover';" onmouseout="this.className = 'window-bar';">
		<img onclick="javascript:obj_Window.closeWindow('edit-mobile');" src="/img/x.gif" width="16" height="16" alt="- Close -" />
	</div>
	
	<!-- Window Page -->
	<div class="window-content">
		<h2><xsl:value-of select="headline" /></h2>
		<div id="progress"><xsl:value-of select="labels/progress" /></div>
		<br />
		<div class="info"><xsl:value-of select="guide" /></div>
	
		<form id="edit-mob" action="/home/sys/save_mobile.php" method="post">
			<div>
				<table>	
				<tr>
					<td>
						<label for="activation-code" accesskey="C"><xsl:value-of select="labels/activation-code" /></label>
					</td>
					<td><input type="code" id="code" name="code" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendInputData(document.getElementById('edit-mob'), this);" tabindex="11" title="new-code" value="" maxlength="6" /></td>
					<td><img class="hidden" name="code_img" id="code_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
				</tr>
				<tr>
					<td class="submit" colspan="2">
						<input type="button" value="{labels/submit}" class="button" onclick="javascript:obj_Client.sendFormData(document.getElementById('edit-mob') );" tabindex="14" title="save" />
					</td>
				</tr>
				</table>
			</div>
		</form>
	</div>
</xsl:template>
</xsl:stylesheet>