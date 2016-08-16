<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="html" indent="no" media-type="text/html" doctype-public="HTML" omit-xml-declaration="yes" standalone="yes" />
<xsl:include href="../mobile.xsl" />

<xsl:template match="/root">
	<div class="wrapper">
		<div class="content">
			<div class="progress mPoint_Info">
				<xsl:value-of select="labels/progress" />
			</div>
			
			<!-- Display Status Messages -->
			<xsl:apply-templates select="messages" />
			<div class="card-wrapper">
				<div class="mpoint-help"><xsl:value-of select="labels/info" /></div>
				<div class="cards">
					<xsl:for-each select="cards/item">
						<xsl:choose>
						  <xsl:when test="@id = '11'">
						  	<xsl:apply-templates select="." mode="cpm-wallet" />
						  </xsl:when>
						  <xsl:when test="@id = '16' or @id = '23' or @id = '25'">
						  	<xsl:apply-templates select="." mode="other-wallet" />
						  </xsl:when>
					   </xsl:choose>
					</xsl:for-each>
					
					<!-- Display payment form for normal payment cards -->
					<xsl:if test="cards/item/@id = 1 or cards/item/@id = 2 or cards/item/@id = 3 or cards/item/@id = 5 or cards/item/@id = 6 or cards/item/@id = 7 or cards/item/@id = 8 or cards/item/@id = 9">
						<xsl:apply-templates select="cards" mode="cpm" />
					</xsl:if>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		parent.postMessage('mpoint-list-cards,<xsl:value-of select="system/session/@id" />', '*');
		
		jQuery(document).ready(function($) {
			// Display loading screen on submit
			jQuery('form.card-form').submit(function() {
				jQuery('.loader-screen').css({'opacity': 1, 'z-index': 20});
			});
			
			jQuery('.card').not('.wallet').click(function() {
				// Use this code for showing the payment form in a second step
				var $this = jQuery(this);
				jQuery('.card').each(function(i) {
					j(this).delay(50*i).animate({
						right: '-=1000',
						opacity: 0
					}, 400, 'easeOutCubic', function() {
						jQuery('.card').hide();
						if($this.hasClass('stored')) {
							$this.addClass('selected');
						} else {
							$this.next().fadeIn();
						}
					});
				});
				var replace = (jQuery('.progress').text()).replace('1', '2');
				jQuery('.progress').text(replace);
				
				/*
				// Use this code for showing the payment form inline.
				if($(this).hasClass('hover') === false) {
					$('.card').removeClass('hover');
					$(this).addClass('hover');
					$('.payment-form').slideUp('fast', 'easeOutCubic');
					$(this).next().fadeIn();
				}
				*/
			});
			
			// Toggle card name and password fields
			$('.checkbox input[name="store-card"]').change(function() {
				if(this.checked) {
					$('.payment-form .save-card').addClass('active');
				} else {
					$('.payment-form .save-card').removeClass('active');
				}
			});
		});
	</script>
</xsl:template>

<xsl:template match="cards" mode="cpm">
	<div class="card">
		<div class="card-logo">
			<div class="icon" style="background-image: url({/root/system/protocol}://{/root/system/host}/img/card_payment.png)" />
		</div>
		<div class="card-name">
			<div class="card-button"><xsl:value-of select="/root/labels/paymentcard" /></div>
		</div>
		<div class="card-arrow">&#10095;</div>
	</div>
	<div class="payment-form">
		<form class="card-form" action="{func:constLink('/pay/authorize.php') }" method="post" autocomplete="on">
			<input type="hidden" name="cardtype" value="" />
			<input type="hidden" name="pspid" value="{@pspid}" />
			<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
			<label for="cardnumber"><xsl:value-of select="/root/labels/cardnumber" /></label>
			<input type="tel" name="cardnumber" class="cc-number" autocomplete="cc-number" maxlength="19" required="required" placeholder="1111 2222 3333 4444" />
			
			<div class="additional">
				<div class="expiry">
					<label for="expiry-month"><xsl:value-of select="/root/labels/expiry" /></label>
					<input type="tel" name="expiry-month" class="cc-month" autocomplete="cc-month" maxlength="2" required="required" placeholder="MM" />
					<span>/</span>
					<input type="tel" name="expiry-year" class="cc-year" autocomplete="cc-year" maxlength="2" required="required" placeholder="YY" />
				</div>
				<div class="cvv">
					<div class="tooltip"></div>
					<label for="cvv"><xsl:value-of select="/root/labels/cvv" /></label>
					<input type="tel" name="cvv" class="cc-cvv" autocomplete="off" maxlength="4" required="required" placeholder="CVV" />
				</div>
			</div>
			
			<label for="cardholdername"><xsl:value-of select="/root/labels/cardholdername" /></label>
			<input type="text" name="cardholdername" class="cc-cardholder" autocomplete="off" required="required" maxlength="50" placeholder="{/root/labels/cardholdername}" />
			
			<div class="checkbox">
				<label>
					<input type="checkbox" name="store-card" />
					<xsl:value-of select="/root/labels/savecard" />
				</label>
			</div>
			
			<xsl:choose>
				<xsl:when test="/root/cards/@accountid > 0">
					<div class="save-card">
						<label for="cardname"><xsl:value-of select="/root/labels/name" /></label>
						<input type="text" name="cardname" placeholder="{/root/labels/name}" />
					</div>
				</xsl:when>
				<xsl:otherwise>
					<div class="save-card">
						<label for="cardname"><xsl:value-of select="/root/labels/name" /></label>
						<input type="text" name="cardname" placeholder="{/root/labels/name}" />
						<label for="new-password"><xsl:value-of select="/root/labels/password" /></label>
						<input type="password" class="new-password" name="new-password" maxlength="20" required="required" placeholder="{/root/labels/new-password}" title="new-password" />
						<input type="password" class="repeat-password" name="repeat-password" maxlength="20" required="required" placeholder="{/root/labels/repeat-password}" title="repeat-password" />
					</div>
				</xsl:otherwise>
			</xsl:choose>
			
			<input type="submit" value="{/root/labels/button}" />
		</form>
	</div>
</xsl:template>

<xsl:template match="item"  mode="other-wallet">
	<div class="card wallet card-{@id}">
		<div class="card-logo" id="card-{@id}">
			<!-- <img src="{/root/system/protocol}://{/root/system/host}/img/card_{@id}.png" alt="" /> -->
		</div>
		<div class="card-name">
			<div class="card-button"><xsl:value-of select="name" /></div>
		</div>
		<div class="card-arrow">&#10095;</div>
	</div>
	<form name="walletform_{@id}" id="walletform_{@id}" action="{func:constLink('/pay/sys/authorize.php') }" method="post">
		<input type="hidden" name="cardtype" value="{@id}" />
		<input type="hidden" name="pspid" value="{@pspid}" />
		<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
		<input type="hidden" name="token" id="token" value=""/>
		<input type="hidden" name="verifier" id="verifier" value="" />
		<input type="hidden" name="checkouturl" id="checkouturl" value="" />
	</form>
					
	<script type="text/javascript">
		var id = <xsl:value-of select="@id"/>;

		jQuery("head").append("<xsl:value-of select="head"/>");
						
		jQuery("#card-"+id).html('<xsl:value-of select="body"/>');
	</script>
</xsl:template>

<xsl:template match="item"  mode="cpm-wallet">
	<div class="stored-cards-wrapper">
		<xsl:for-each select="/root/stored-cards/card">
			<xsl:if test="client/@id = /root/client-config/@id">
				<div class="card stored card-{type/@id}">
					<div class="card-logo">
						<div class="icon card-{type/@id}" style="background-image: url({/root/system/protocol}://{/root/system/host}/img/card_payment.png)">
							<div class="hover" style="background-image: url({/root/system/protocol}://{/root/system/host}/img/card_payment.png)" />
						</div>
					</div>
					<div class="card-name">
						<div class="card-button"><xsl:value-of select="name" /></div>
					</div>
					<div class="card-arrow">&#10095;</div>
					<div class="payment-form">
						<form id="pay-account" class="card-form" action="{func:constLink('/cpm/pay_account.php') }" method="post">
							<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
							<input type="hidden" name="cardtype" value="11" />
							<input type="hidden" name="prepaid" value="false" />
							<input type="hidden" id="cardid" name="cardid" value="{@id}" />

							<label for="password"><xsl:value-of select="/root/labels/password" /></label>
							<input type="password" name="pwd" value="" />
							
							<input type="submit" value="{/root/labels/submit}" />
						</form>
					</div>
				</div>
			</xsl:if>
		</xsl:for-each>
	</div>
</xsl:template>
</xsl:stylesheet>