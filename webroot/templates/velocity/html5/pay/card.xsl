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
					<!-- Display alternative payment methods -->
					<xsl:for-each select="cards/item">
						<xsl:apply-templates select="." mode="cpm-wallet" />
					</xsl:for-each>
					
					<!-- Display payment form for normal payment cards -->
					<xsl:if test="contains(cards/item/@id, 1) or contains(cards/item/@id, 2) or contains(cards/item/@id, 3) or contains(cards/item/@id, 5) or contains(cards/item/@id, 6) or contains(cards/item/@id, 7) or contains(cards/item/@id, 8) or contains(cards/item/@id, 9)">
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
							<form class="card-form" action="{func:constLink('/cpm/payment.php') }" method="post" autocomplete="on">
								<input type="hidden" name="cardtype" value="" />
								<input type="hidden" name="pspid" value="{@pspid}" />
								<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
								<label for="cardholdername"><xsl:value-of select="/root/labels/cardholdername" /></label>
								<input type="text" name="cardholdername" class="cc-number" autocomplete="cc-number" maxlength="50" />
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
								
								<label for="cardholder"><xsl:value-of select="/root/labels/cardholder" /></label>
								<input type="text" name="cardholder" class="cc-cardholder" autocomplete="off" required="required" placeholder="{/root/labels/cardholder}" />
								
								<div class="checkbox">
									<label>
										<input type="checkbox" name="store-card" />
										<xsl:value-of select="/root/labels/savecard" />
									</label>
								</div>
								
								<input type="submit" value="{/root/labels/button}" />
							</form>
						</div>
					</xsl:if>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		parent.postMessage('mpoint-list-cards,<xsl:value-of select="system/session/@id" />', '*');
		
		// Display loading screen on submit
		$('form.card-form').submit(function() {
			$('.loader-screen').css({'opacity': 1, 'z-index': 1});
		});
		
		$('.card').not('.wallet').click(function() {
			// Use this code for showing the payment form in a second step
			var $this = $(this);
			$('.card').each(function(i) {
				$(this).delay(50*i).animate({
					right: '-=1000',
					opacity: 0
				}, 400, 'easeOutCubic', function() {
					$('.card').hide();
					$this.next().fadeIn();
				});
			});
			var replace = ($('.progress').text()).replace('1', '2');
			$('.progress').text(replace);
			
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
	</script>
</xsl:template>

<xsl:template match="item" mode="cpm-wallet">
	<!--
	  - Only display option if:
	  - Card shouldn't be "auto stored" AND
	  - "Card" is NOT "My Account" OR
	  - "Cards" stored for Merchant OR
	  - E-Money based Prepaid Account is available AND Transaction is not an Account Top-Up
	  -->
	<xsl:if test="/root/transaction/auto-store-card = 'false' and (@id != 11 or count(/root/stored-cards/card[client/@id = /root/client-config/@id]) &gt; 0 or (floor(/root/client-config/store-card div 1) mod 2 != 1 and (/root/transaction/@type &lt; 100 or /root/transaction/@type &gt; 109) ) )">
		<div class="card wallet card-{@id}" onclick="jQuery(this).next().find('form').submit();">
			<div class="card-logo">
				<img src="{/root/system/protocol}://{/root/system/host}/img/card_{@id}.png" alt="" />
			</div>
			<div class="card-name">
				<div class="card-button"><xsl:value-of select="name" /></div>
			</div>
			<div class="card-arrow">&#10095;</div>
		</div>
		<div class="payment-form card-{@id}">
			<form id="card-{@id}" class="card-form" action="{func:constLink('/cpm/payment.php') }" method="post">
				<input type="hidden" name="cardtype" value="{@id}" />
				<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
			</form>
		</div>
	</xsl:if>
</xsl:template>
</xsl:stylesheet>