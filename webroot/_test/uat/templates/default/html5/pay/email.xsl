<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="html" indent="no" media-type="text/html" doctype-public="HTML" omit-xml-declaration="yes" standalone="yes" />
<xsl:include href="../mobile.xsl" />

<xsl:template match="/root">
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
	
	<div id="outer-border">
		<div class="mPoint_Help">
			<xsl:value-of select="labels/info" />
		</div>
		<form id="send-email" action="{func:constLink('sys/send_email.php')}" method="post">
			<div id="email">
				<table cellpadding="0" cellspacing="0" class="grouped">
				<tr class="first-row last-row">
					<th class="left-column mPoint_Label"><xsl:value-of select="labels/email" /></th>
					<td class="right-column stretch"><input type="email" name="email" value="{session/email}" maxlength="50" /></td>
				</tr>
				</table>
			</div>
			<div id="submit">
				<a id="send" class="submit-button" onclick="javascript:this.className='submit-button-clicked'; document.getElementById('loader').style.visibility='visible'; this.disabled=true; document.getElementById('send-email').submit();">
					<h2><xsl:value-of select="labels/submit" /></h2>
				</a>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		parent.postMessage('mpoint-send-email,<xsl:value-of select="system/session/@id" />', '*');
	</script>
</xsl:template>

</xsl:stylesheet>