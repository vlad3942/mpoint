<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/confirm-transfer">
	<!-- Window Bar -->
	<div class="window-bar" onmousedown="javascript:obj_Window.moveWindow(this.parentNode);" onmouseover="this.className = 'window-bar iehover';" onmouseout="this.className = 'window-bar';">
		<img onclick="javascript:obj_Window.closeWindow('confirm-transfer');" src="/img/x.gif" width="16" height="16" alt="- Close -" />
	</div>
	
	<!-- Window Page -->
	<div class="window-content">
		<h2><xsl:value-of select="headline" /></h2>
		<div id="progress"><xsl:value-of select="labels/progress" /></div>
		<br />
		<div class="info">
			<xsl:choose>
			<xsl:when test="string-length(account/mobile) &gt; 0">
				<xsl:value-of select="guide/confirmation-code" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="guide/password-only" />
			</xsl:otherwise>
			</xsl:choose>
		</div>
		<br />
		<form id="make-transfer" action="/home/sys/make_transfer.php" method="post">
			<div>
				<input type="hidden" name="countryid" value="" />
				<input type="hidden" name="recipient" value="" />
				<input type="hidden" name="amount" value="" />
			</div>
			<div>
				<table>
				<tr>
					<td>
						<label><xsl:value-of select="labels/recipient" /></label>
					</td>
					<td id="confirm-recipient"><!-- Completed dynamically by JavaScript --></td>
					<td></td>
				</tr>
				<tr>
					<td>
						<label><xsl:value-of select="labels/total" /></label>
					</td>
					<td id="confirm-total"><!-- Completed dynamically by JavaScript --></td>
					<td></td>
				</tr>
				<tr>
					<td>
						<label for="password" accesskey="P"><xsl:value-of select="labels/password" /></label>
					</td>
					<td><input type="password" id="password" name="password" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendInputData(document.getElementById('make-transfer'), this);" tabindex="11" title="password" value="" maxlength="50" /></td>
					<td><img class="hidden" name="password_img" id="password_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
				</tr>
				<xsl:if test="string-length(account/mobile) &gt; 0">
					<tr>
						<td>
							<label for="code" accesskey="C"><xsl:value-of select="labels/confirmation-code" /></label>
						</td>
						<td><input type="password" id="code" name="code" class="text" onfocus="javascript:obj_Client.clear(this);" onblur="javascript:obj_Client.sendInputData(document.getElementById('make-transfer'), this);" tabindex="12" title="code" value="" maxlength="6" /></td>
						<td><img class="hidden" name="code_img" id="code_img" src="/img/rederrorarrow.gif" width="13" height="10" alt="" border="0" /></td>
					</tr>
				</xsl:if>
				<tr>
					<td class="submit" colspan="2">
						<input type="button" value="{labels/submit}" class="button" onclick="javascript:obj_Client.sendFormData(document.getElementById('make-transfer') );" tabindex="13" title="make-transfer" />
					</td>
				</tr>
				</table>
			</div>
		</form>
		
		<script type="text/javascript">
			// Transfer transfer data from initial transfer form
			document.getElementById('make-transfer').countryid.value = document.getElementById('init-transfer').countryid.value;
			document.getElementById('make-transfer').recipient.value = document.getElementById('init-transfer').recipient.value;
			document.getElementById('make-transfer').amount.value = document.getElementById('init-transfer').amount.value;
			
			document.getElementById('confirm-recipient').innerHTML = document.getElementById('init-transfer').recipient.value;
			document.getElementById('confirm-total').innerHTML = document.getElementById('total').innerHTML;
		</script>
	</div>
</xsl:template>
</xsl:stylesheet>