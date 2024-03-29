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
							  <xsl:when test="@id = '16' or @id = '23' or @id = '28'">
							  	<xsl:apply-templates select="." mode="other-wallet" />
							  </xsl:when>
							  <xsl:when test="@id = '31'">
							  	<xsl:apply-templates select="." mode="sadad" />
							  </xsl:when>
						   </xsl:choose>
						</xsl:for-each>
						
						<!-- Display payment form for normal payment cards -->
						<xsl:if test="cards/item/@id = 1 or cards/item/@id = 2 or cards/item/@id = 3 or cards/item/@id = 5 or cards/item/@id = 6 or cards/item/@id = 7 or cards/item/@id = 8 or cards/item/@id = 9">
							<xsl:apply-templates select="cards" mode="cpm" />
						</xsl:if>
					</div>
					<div class="back-button">
						&#10229; <xsl:value-of select="/root/labels/back-button" />
					</div>
				</div>		
		</div>
	</div>
	<script type="text/javascript">
		parent.postMessage('mpoint-list-cards,<xsl:value-of select="system/session/@id" />', '*');
		
		jQuery(function($)
		{
			// Display loading screen on submit
			$('form.card-form').submit(function()
			{
				$('body').addClass('loading');
			});
			
			// Enable wallet button
			$('.card.wallet').on('click', function(event) {
				$('.card-logo', this).find('img').first().click();
			});
			$(".card.wallet .card-logo img").click(function(event) {
				// A click that triggers a click on itself, better stop propagation:
				event.stopPropagation();
			});
			
			// Enable all other buttons
			$('.card').not('.wallet').click(function(event)
			{
				// Use this code for showing the payment form in a second step
				var $this = $(this);
				if($this.hasClass('delete-selected') === false &amp;&amp; $this.hasClass('selected') === false)
				{
					$('.mpoint-status').remove();
					
					$('.card').each(function(i)
					{
					
						$(this).delay(50*i).animate({
							right: '-=1000',
							opacity: 0
						}, 400, 'easeOutCubic', function()
						{
							$('.card').hide();
							$('.paypal-card').hide();
							if(event.target.className === 'delete-card-icon')
							{
								$this.addClass('delete-selected');
								$($this).find('.deletion-form').fadeIn();
							}
							else
							{
								if($this.hasClass('stored'))
								{
									$this.addClass('selected');
								}
								else
								{
									$this.next().fadeIn();
								}
							}
							
							$('.back-button').delay(200).slideDown('fast');
						});
					});
					var replace = ($('.progress').text()).replace('1', '2');
					$('.progress').text(replace);
				}
			});
			
			// Enable back button
			$('.back-button').click(function()
			{
				$(this).fadeOut('fast');
				$('.card').each(function(i)
				{
					$(this).animate({
						right: '0',
						opacity: 1
					}, 0, 'easeOutCubic', function() {
						$('.card').show();
						$('.paypal-card').show();
						if($(this).hasClass('stored'))
						{
							$(this).removeClass('selected');
							$(this).removeClass('delete-selected');
						}
						else
						{
							$('.payment-form').hide();
						}
					});
				});
				var replace = ($('.progress').text()).replace('2', '1');
				$('.progress').text(replace);
			});

			// Toggle card name and password fields
			$('.checkbox input[name="store-card"]').change(function()
			{
				var paymentClass = $(this).closest('form').parent().prop('class');
				
				if(paymentClass.indexOf(" ") > 0)
				{
					paymentClass = "div."+paymentClass.split(" ")[0];
				}
				else
				{
					paymentClass = "div."+paymentClass;
				}
				
				if(this.checked)
				{
					
					$(paymentClass+' .save-card').addClass('active');
					
					if($(paymentClass+' .save-card #new-password').length > 0)
					{
						$(paymentClass+' .save-card #cardname').attr("required", "required");
						$(paymentClass+' .save-card #new-password').attr("required", "required");
						$(paymentClass+' .save-card #repeat-password').attr("required", "required");
					}
					
 				} else {
 					$(paymentClass+' .save-card').removeClass('active');

					if($(paymentClass+' .save-card #new-password').length > 0)
					{
						$(paymentClass+' .save-card #cardname').removeAttr("required");
						$(paymentClass+' .save-card #new-password').removeAttr("required");
						$(paymentClass+' .save-card #repeat-password').removeAttr("required");
					}
				}
			});
			
			// Stored card deletion form show
			$('.delete-card-icon').hover(function()
			{
				$(this).parent().addClass('ignore-hover');
			}, function()
			{
				$(this).parent().removeClass('ignore-hover');
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
		<form class="new-card-form" action="{func:constLink('/pay/sys/authorize.php') }" method="post" autocomplete="on">
			<input type="hidden" name="cardtype" value="" />
			<input type="hidden" name="pspid" value="{@pspid}" />
			<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
			<label for="cardnumber"><xsl:value-of select="/root/labels/cardnumber" /></label>
			<div class="cardnumber">
				<div class="cc-card-type card-logo">
					<div class="icon" style="background-image: url({/root/system/protocol}://{/root/system/host}/img/card_payment.png)" />
				</div>
				<input type="tel" name="cardnumber" class="cc-number" autocomplete="cc-number" maxlength="23" required="required" placeholder="1111 2222 3333 4444" />
				<div class="card-logo enabled-cards">
					<xsl:for-each select="/root/cards/item">
						<xsl:if test="@id = 1 or @id = 2 or @id = 3 or @id = 5 or @id = 6 or @id = 7 or @id = 8 or @id = 9">
							<div class="icon card-{@id}" style="background-image: url({/root/system/protocol}://{/root/system/host}/img/card_payment.png)"></div>
						</xsl:if>
					</xsl:for-each>
				</div>
			</div>
			
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
						<input type="password" class="new-password" name="new-password" maxlength="20" placeholder="{/root/labels/new-password}" title="new-password" />
						<input type="password" class="repeat-password" name="repeat-password" maxlength="20" placeholder="{/root/labels/repeat-password}" title="repeat-password" />
					</div>
				</xsl:otherwise>
			</xsl:choose>
			
			<input type="submit" value="{/root/labels/button}" />
		</form>
	</div>

	<script type="text/javascript">
		var cards = [
		<xsl:for-each select="/root/cards/item">
			<xsl:if test="@id = 1 or @id = 2 or @id = 3 or @id = 5 or @id = 6 or @id = 7 or @id = 8 or @id = 9">
			{
				type: 'card-<xsl:value-of select="@id" />',
				patterns: [
					<xsl:for-each select="prefixes/prefix">
						[<xsl:value-of select="min" />, <xsl:value-of select="max" />],
					</xsl:for-each>
				],
				format: 
				<xsl:choose>
					<xsl:when test="@id = 1 or @id = 3">
						/(\d{1,4})(\d{1,6})?(\d{1,4})?/
					</xsl:when>
					<xsl:otherwise>
						/(\d{1,4})/g
					</xsl:otherwise>
				</xsl:choose>,
				length: [<xsl:value-of select="@min-length" />, <xsl:value-of select="@max-length" />],
				cvcLength: [<xsl:value-of select="@cvc-length" />],
				luhn: true
			},
			</xsl:if>
		</xsl:for-each>
		];
		
		for(i = 0; i &lt; cards.length; i++)
		{
			// Convert patterns to a non-nested array:
			var new_pattern = [];
			for(x = 0; x &lt; cards[i].patterns.length; x++)
			{
				if(cards[i].patterns[x][0] == cards[i].patterns[x][1])
				{
					new_pattern.push(cards[i].patterns[x][0]);
				}
				else if (cards[i].patterns[x][0] &lt; cards[i].patterns[x][1])
				{
					for(y = cards[i].patterns[x][0]; y &lt;= cards[i].patterns[x][1]; y++)
					{
						new_pattern.push(y);
					}
				}
			}
			cards[i].patterns = new_pattern;
			
			// Provide one length if min and max are the same, otherwise all possible lengths:
			if(cards[i].length[0] == cards[i].length[1])
			{
				cards[i].length.pop(cards[i].length[1]);
			}
			else
			{
				var new_length = [];
				for(z = cards[i].length[0]; z &lt;= cards[i].length[1]; z++)
				{
					new_length.push(z);
				}
				cards[i].length = new_length;
			}
		}

		jQuery(function($)
		{
			var hasError = true;
			$.payment.cards = cards;
			
			// Format input fields
			$('input.cc-number').payment('formatCardNumber');
			$('input.cc-cvv').payment('formatCardCVC');
			$('input.cc-month').payment('restrictNumeric');
			$('input.cc-year').payment('restrictNumeric');
			
			// Toggle error class for input fields
			$.fn.toggleInputError = function(erred)
			{
				$(this).toggleClass('has-error', erred);
				return erred;
			};
			
			// Validate the card input fields
			function validateInput()
			{
				var cardType = $.payment.cardType($('.cc-number').val());
				var cardError = $('.cc-number').toggleInputError(!$.payment.validateCardNumber($('.cc-number').val()));
				var expiryError = $('.cc-month').toggleInputError(!$.payment.validateCardExpiry($('.cc-month').val(), $('.cc-year').val()));
				var expiryError = $('.cc-year').toggleInputError(!$.payment.validateCardExpiry($('.cc-month').val(), $('.cc-year').val()));
				var cvcError = $('.cc-cvv').toggleInputError(!$.payment.validateCardCVC($('.cc-cvv').val(), cardType));
				$('.cc-card-type div').attr('class', 'icon ' + cardType);
				if(Boolean(cardType) != false)
				{
					$('input[name="cardtype"]').val(cardType.replace('card-', ''));
				}
				
				if(cardError == true || expiryError == true || cvcError == true)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			
			$('.cc-number, .cc-cvv, .cc-month, .cc-year').on('change paste keyup input', function()
			{
				validateInput();
			});
			
			// Prevent form submission if input does not validate
			$('form.new-card-form').submit(function(e)
			{
				hasError = validateInput();
				
				if(hasError)
				{
					e.preventDefault();
				}
				else
				{
					// Display loading screen
					$('body').addClass('loading');
				}
			});
			
			$('#walletform_28').submit(function (e) 
			{
				// Display loading screen
				$('body').addClass('loading');
			});
		});
	</script>
</xsl:template>

<xsl:template match="item"  mode="other-wallet">

	<xsl:choose>
		<xsl:when test="@id = '28'">
			<div class="paypal-card wallet card-{@id}">
				<div class="payment-paypal-form payment-form">
					<form action="{func:constLink('/pay/sys/paypal.php') }" method="POST" name="walletform_{@id}" id="walletform_{@id}">
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
									<input type="text" name="cardname" id="cardname" placeholder="{/root/labels/name}" />
								</div>
							</xsl:when>
							<xsl:otherwise>
								<div class="save-card">
									<label for="cardname"><xsl:value-of select="/root/labels/name" /></label>
									<input type="text" name="cardname" id="cardname" placeholder="{/root/labels/name}" />
									<label for="new-password"><xsl:value-of select="/root/labels/password" /></label>
									<input type="password" class="new-password" name="new-password" id="new-password" maxlength="20" placeholder="{/root/labels/new-password}" title="new-password"/>
									<input type="password" class="repeat-password" name="repeat-password" id="repeat-password" maxlength="20" placeholder="{/root/labels/repeat-password}" title="repeat-password" />
								</div>
							</xsl:otherwise>
						</xsl:choose>
						<input type="hidden" name="transactionid" value="{/root/transaction/@id}" /> 
						<input type="hidden" name="cardtype" value="{@id}" /> 
						
						<div class="card wallet card-{@id}">
							<div id="card-{@id}" class="card-logo">
								<!-- <img src="{/root/system/protocol}://{/root/system/host}/img/card_{@id}.gif" alt="" onclick="submitForm();" class="paypal-image"/> -->
								<input type="submit" id="paypal" name="paypal" alt="paypal" value="" style="background-image:url({/root/system/protocol}://{/root/system/host}/img/card_28.gif);"/>
							</div>
							<div class="card-name">
								<div class="card-button"><xsl:value-of select="name" /></div>
							</div>
							<div class="card-arrow">&#10095;</div>
						</div>
					</form>
					<script type="text/javascript">
						var id = <xsl:value-of select="@id"/>;
						function submitForm(e)
						{
							hasError = validateInput();
				
							if(hasError)
							{
								e.preventDefault();
							}
							else
							{
								// Display loading screen
								$('body').addClass('loading');
								
								document.getElementById("walletform_"+id).submit();
							}							
						}
					</script>
				</div>
			</div>
		</xsl:when>
		<xsl:otherwise>
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
		  </xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="item"  mode="cpm-wallet">
	<div class="stored-cards-wrapper">
		<xsl:for-each select="/root/stored-cards/card">
			<xsl:if test="client/@id = /root/client-config/@id">
				<div class="card stored card-{type/@id}">
					<div class="delete-card-icon" title="{/root/labels/delete-card}">&#10006;</div>
					<div class="card-logo">
						<div class="icon card-{type/@id}" style="background-image: url({/root/system/protocol}://{/root/system/host}/img/card_payment.png)">
							<div class="hover" style="background-image: url({/root/system/protocol}://{/root/system/host}/img/card_payment.png)" />
						</div>
					</div>
					<div class="card-name">
						<div class="card-button"><xsl:value-of select="name" /></div>
					</div>

					<div class="deletion-form">
						<div class="card-info">
							<div class="card-logo">
								<div class="icon card-{type/@id}" style="background-image: url({/root/system/protocol}://{/root/system/host}/img/card_payment.png)">
									<div class="hover" style="background-image: url({/root/system/protocol}://{/root/system/host}/img/card_payment.png)" />
								</div>
							</div>
							<div class="card-name">
								<div class="card-button"><xsl:value-of select="name" /></div>
							</div>
							<div class="card-mask">
								<div class="card-button"><xsl:value-of select="mask" /></div>
							</div>
							<div class="card-expiry">
								<div class="card-button"><xsl:value-of select="expiry" /></div>
							</div>							
						</div>
						
						<form id="delete-card" class="card-form" action="{func:constLink('/cpm/sys/del_card.php') }" method="post">
							<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
							<input type="hidden" name="cardtype" value="11" />
							<input type="hidden" name="prepaid" value="false" />
							<input type="hidden" id="cardid" name="cardid" value="{@id}" />
							<input type="password" class="password" name="pwd" value="" required="required" placeholder="{/root/labels/password}" />
							<input type="submit" value="{/root/labels/delete-card}" />
						</form>
					</div>

					<div class="payment-form">
						<form id="pay-account" class="card-form" action="{func:constLink('/pay/sys/authorize.php') }" method="post">
							<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
							<input type="hidden" name="cardtype" value="{@type-id}" />
							<input type="hidden" name="prepaid" value="false" />
							<input type="hidden" id="cardid" name="cardid" value="{@id}" />
							<input type="hidden" name="storedcard" value="true" />
							
							
							<xsl:if test="@type-id != 28">
								<label for="cvv"><xsl:value-of select="/root/labels/cvv" /></label>
								<input type="tel" name="cvv" autocomplete="off" maxlength="4" required="required" placeholder="CVV" />
							</xsl:if>
							
							<label for="password"><xsl:value-of select="/root/labels/password" /></label>
							<input type="password" name="pwd" value="" required="required" />
							
							<input type="submit" value="{/root/labels/submit}" />
						</form>
					</div>
				</div>
			</xsl:if>
		</xsl:for-each>
	</div>
</xsl:template>
<xsl:template match="item"  mode="sadad">
	<div class="card card-{@id}">
		<div class="card-logo" id="card-{@id}">
			<!-- <img src="{/root/system/protocol}://{/root/system/host}/img/card_{@id}.png" alt="" /> -->
			<div class="icon card-{type/@id} hover sadad-card" style="background-image: url({/root/system/protocol}://{/root/system/host}/img/card_31.png)" />
		</div>
		<div class="card-name">
			<div class="card-button sadad-card-button"><xsl:value-of select="name" /></div>
		</div>
		<div class="card-arrow sadad-card-arrow">&#10095;</div>
	</div>
	<div class="payment-form">
		<form class="card-form" action="{func:constLink('/pay/sys/sadad.php') }" method="post" autocomplete="on">
			<input type="hidden" name="pspid" value="{@pspid}" />
			<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
			<label for="cardnumber"><xsl:value-of select="/root/labels/sadad_payment_id" /></label>
			<div class="cardnumber">
				<input type="tel" name="sadad_payment_id" maxlength="23" required="required" placeholder="SADAD Payment Id" />
			</div>
			<input type="submit" value="{/root/labels/button}" />
		</form>
	</div>
	
	<script type="text/javascript">
		jQuery(function($)
		{
			// Prevent form submission if input does not validate
			$('form.card-form').submit(function(e)
			{
				// Display loading screen
				$('body').addClass('loading');
			});
		});
	</script>
</xsl:template>
</xsl:stylesheet>