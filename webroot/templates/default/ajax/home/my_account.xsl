<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/content">
	
	<span id="data-source" class="hidden-data">
		<!-- Hidden Data Fields Start -->
		<span id="account-info-data">
			<br />
			<form id="edit-info" action="/home/sys/save_info.php" method="post">
				<div>
					<xsl:apply-templates select="account" />
				</div>
			</form>
		</span>
		
		<span id="stored-card-data">
			<table cellpadding="0" cellspacing="0">
			<xsl:choose>
			<xsl:when test="count(stored-cards/card) = 0">
				<td colspan="2" class="info"><xsl:value-of select="help" /></td> 
			</xsl:when>
			<xsl:otherwise>
				<tr>
					<td id="clients">
					<select onchange="javascript:document.getElementById('card-data').innerHTML = document.getElementById('stored-card-data-'+ this.value).innerHTML;">
					<xsl:for-each select="stored-cards/card">
						<xsl:variable name="current" select="position()" />
						
						<xsl:if test="$current = 1 or //stored-cards/card[position() = $current - 1]/client/@id != client/@id">
							<xsl:apply-templates select="client" />
						</xsl:if>
					</xsl:for-each>
					</select>
					</td>
				</tr>
				<tr>
					<td>
						<div id="card-data">
							<form id="edit-card" action="/home/sys/manage_card.php" method="post">
								<div>
									<input type="hidden" id="command" name="command" value="" />
								</div>
								<table cellpadding="0" cellspacing="0">
								<!-- Show Preferred Card -->
								<tr>
									<td colspan="4" class="label"><xsl:value-of select="labels/preferred" /></td>
								</tr>
								<xsl:apply-templates select="stored-cards/card[//stored-cards/card[position() = 1]/client/@id = client/@id and @preferred = 'true']" />
								<!-- List Other Cards -->
								<tr>
									<td colspan="4" class="label"><xsl:value-of select="labels/other" /></td>
								</tr>
								<xsl:apply-templates select="stored-cards/card[//stored-cards/card[position() = 1]/client/@id = client/@id and @preferred = 'false']" />
								</table>
							</form>
						</div>
					</td>
				</tr>
			</xsl:otherwise>
			</xsl:choose>
			<tr>
				<td colspan="2">
					<br />
					<button type="button" onclick="javascript:obj_Window.openWindow('new-card', 'my-account', '/home/new_card.php', 'new-card', new Array(obj_Client, obj_Client.changePage) );"><xsl:value-of select="commands/new" /></button>
					<xsl:if test="count(stored-cards/card) &gt; 0">
						<button type="button" class="button" onclick="javascript:document.getElementById('command').value=this.value; obj_Client.sendFormData(document.getElementById('edit-card') );" value="preferred"><xsl:value-of select="commands/preferred" /></button>
						<button type="button" class="button" onclick="javascript:document.getElementById('command').value=this.value; obj_Client.sendFormData(document.getElementById('edit-card') );" value="delete"><xsl:value-of select="commands/delete" /></button>
					</xsl:if>
				</td>
			</tr>
			</table>
		</span>
		
		<xsl:for-each select="stored-cards/card">
			<xsl:variable name="current" select="position()" />
			
			<xsl:if test="$current = 1 or //stored-cards/card[position() = $current - 1]/client/@id != client/@id">
				<span id="stored-card-data-{client/@id}">
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="4">
							<h2><xsl:value-of select="client" /></h2>
						</td>
					</tr>
					<!-- Show Preferred Card -->
					<tr>
						<td colspan="4" class="label"><xsl:value-of select="//labels/preferred" /></td>
					</tr>
					<xsl:apply-templates select="//stored-cards/card[//stored-cards/card[position() = $current]/client/@id = client/@id and @preferred = 'true']" />
					<!-- List Other Cards -->
					<tr>
						<td colspan="4" class="label"><xsl:value-of select="//labels/other" /></td>
					</tr>
					<xsl:apply-templates select="//stored-cards/card[//stored-cards/card[position() = $current]/client/@id = client/@id and @preferred = 'false']" />
					</table>
				</span>
			</xsl:if>
		</xsl:for-each>
		<!-- Hidden Data Fields End -->
	</span>
	
	<div id="my-account">
		<h1><xsl:value-of select="headline" /></h1>
		<br />
		<table align="center">
		<tr>
			<td class="folder">
			<ul class="menu">
				<li>
					<a class="current" href="#" onclick="javascript:selectMenu(this, 'current'); changeFolder(document.getElementById('data-source'), document.getElementById('account-data'), document.getElementById('account-info-data') );">
						<div>
							<span><xsl:value-of select="labels/account-info" /></span>
							<img src="/img/folder.png" width="20" height="20" alt="" border="0" />
						</div>
					</a>
				</li>
				<li>
					<a href="#" onclick="javascript:selectMenu(this, 'current'); changeFolder(document.getElementById('data-source'), document.getElementById('account-data'), document.getElementById('stored-card-data') );">
						<div>
							<span><xsl:value-of select="labels/multiple-stored-cards" /></span>
							<img src="/img/folder.png" width="20" height="20" alt="" border="0" />
						</div>
					</a>
				</li>
			</ul>
			</td>
		</tr>
		<tr>
			<td><div id="account-data"><!-- Completed dynamically by JavaScript --></div></td>
		</tr>
		</table>
		
		<script type="text/javascript">
			changeFolder(document.getElementById('data-source'), document.getElementById('account-data'), document.getElementById('account-info-data') );
		</script>
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
		<td><a href="#" onclick="javascript:obj_Window.openWindow('edit-password', 'my-account', '/home/edit_password.php', 'edit-password', new Array(obj_Client, obj_Client.changePage) );" tabindex="1" title="password"><xsl:value-of select="//commands/edit" /></a></td>
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
		<td><a href="#" onclick="javascript:obj_Window.openWindow('edit-mobile', 'my-account', '/home/edit_mobile.php', 'edit-mobile', new Array(obj_Client, obj_Client.changePage) );" tabindex="4" title="mobile"><xsl:value-of select="//commands/edit" /></a></td>
	</tr>
	<tr>
		<td>
			<label for="email" accesskey="E"><xsl:value-of select="//labels/email" /></label>
		</td>
		<td><xsl:value-of select="email" /></td>
		<td><a href="#" onclick="javascript:obj_Window.openWindow('edit-email', 'my-account', '/home/edit_email.php', 'edit-email', new Array(obj_Client, obj_Client.changePage) );" tabindex="5" title="email"><xsl:value-of select="//commands/edit" /></a></td>
	</tr>
	<tr>
		<td class="submit" colspan="2">
			<button type="button" class="button" onclick="javascript:obj_Client.sendFormData(document.getElementById('edit-info') );" tabindex="6" title="save"><xsl:value-of select="//commands/save" /></button>
		</td>
	</tr>
	</table>
</xsl:template>

<xsl:template match="card">
	<xsl:variable name="css">
		<xsl:choose>
			<!-- Even row -->
			<xsl:when test="position() mod 2 = 0">
				<xsl:text>mPoint_Even</xsl:text>
			</xsl:when>
			<!-- Odd row -->
			<xsl:otherwise>
				<xsl:text>mPoint_Uneven</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
			
	<tr class="{$css}">
		<td><img src="/img/31x20_card_{type/@id}.png" width="31" height="20" alt="- {type} -" /></td>
		<td><xsl:value-of select="mask" /></td>
		<td class="info">(<xsl:value-of select="expiry" />)<xsl:value-of select="position()" /></td>
		<td>
			<xsl:choose>
			<xsl:when test="position() = 1">
				<input type="radio" name="cardid" value="{@id}" checked="true" />
			</xsl:when>
			<xsl:otherwise>
				<input type="radio" name="cardid" value="{@id}" />
			</xsl:otherwise>
			</xsl:choose>
		</td>
	</tr>
</xsl:template>

<xsl:template match="client">
	<option value="{@id}"><xsl:value-of select="." /></option>
</xsl:template>

</xsl:stylesheet>