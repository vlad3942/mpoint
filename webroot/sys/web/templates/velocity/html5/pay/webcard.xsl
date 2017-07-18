<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions"
	extension-element-prefixes="func">
	<xsl:output method="html" indent="no" media-type="text/html"
		doctype-public="HTML" omit-xml-declaration="yes" standalone="yes" />
	<xsl:include href="../responsive.xsl" />

	<xsl:template match="/root">

		<div class="row panel-main">
			<div class="col-md-10 col-md-offset-1">
				<!-- Start: Tab section for various payment methods -->
				<section>
					<div class="pay-options">
						<div class="content-wrap">
							<section id="section-1">
								<div class="row">
									<div class="col-md-12">
										<div class="panel-group" id="accordion1" role="tablist" aria-multiselectable="true">
											<xsl:for-each select="cards/item">
											<xsl:choose>
												<xsl:when test="@id = '11'">
													<xsl:apply-templates select="." mode="cpm-wallet" />
												</xsl:when>
											</xsl:choose>
											</xsl:for-each>
											<!-- Display payment form for other payment methods -->
											<xsl:if test="cards/item/@id = '16' or cards/item/@id = '23' or cards/item/@id = '28' or cards/item/@id = '30' or cards/item/@id = '31' or cards/item/@id = '32' or cards/item/@id = '33'">
												<xsl:apply-templates select="cards" mode="other-wallet" />
											</xsl:if>								
											<!-- Display payment form for normal payment cards -->
											<xsl:if test="cards/item/@id = 1 or cards/item/@id = 2 or cards/item/@id = 3 or cards/item/@id = 5 or cards/item/@id = 6 or cards/item/@id = 7 or cards/item/@id = 8 or cards/item/@id = 9">
												<xsl:apply-templates select="cards" mode="cpm" />
											</xsl:if>
												<!-- Display error messages template -->
											<xsl:if test="messages/item">
												<xsl:apply-templates select="messages" mode="error" >
													<xsl:with-param name="errorvalue" select="messages/item" />
												</xsl:apply-templates>
											</xsl:if>
											<!-- Display error messages template -->
											<xsl:if test="messages/item">
												<xsl:apply-templates select="messages" mode="error" >
													<xsl:with-param name="errorvalue" select="messages/item" />
												</xsl:apply-templates>
											</xsl:if>
										</div>


										<div class="col-md-12">
											<p class="remove-card text-center">
												<a href="#" data-toggle="modal" data-target="#modal-cancel"
													class="red remove-alert66469">
													<span class="glyphicon glyphicon-chevron-left" title="Delete Card"></span>
													<big>
														<xsl:value-of select="/root/labels/back-button" />
													</big>
												</a>
											</p>
										</div>

										<div id="modal-cancel" href="https://www.google.co.in"
											class="modal fade" data-backdrop="static" data-keyboard="false"
											tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
											aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="modal-body">
														<button type="button" class="bootbox-close-button close"
															data-dismiss="modal" aria-hidden="true" style="margin-top: -10px;"> Ã— </button>
														<div class="bootbox-body " align="center">
															<h3 class="text-warning"> Warning!!!</h3>
														</div>
													</div>
													<div class="modal-footer">
														<div class="row">
															<div class="col-md-12">
																<div class="col-md-12" align="center">
																	<h4>Are you sure you want to cancel the Transaction?
																	</h4>
																</div>

															</div>
														</div>
														<br />
														<xsl:variable name="returnurl" select="/root/labels/returnurl" />
														<a href="{$returnurl}" class="btn btn-success btn-sm">Yes</a>
														<button id="no" data-dismiss="modal"
															data-bb-handler="No" type="button" class="btn btn-danger btn-sm">No</button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</section>
						</div><!-- /content -->
					</div><!-- /tabs -->
				</section>
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

			});
		</script>
	</xsl:template>
<xsl:template match="messages" mode="error">
<xsl:param name="errorvalue" />
<script type="text/javascript">
$(document).ready(function() {
  $('#modalerror').modal('show'); 
  
    $("#ok").click(function(){
    var Qurl = window.location.href;
    var url = Qurl.substr(0, Qurl.indexOf("&amp;msg"));
	window.location.href = url;
    });
});
</script>
	<div id="modalerror" href="http://www.google.co.in" class="modal fade" data-backdrop="static" data-keyboard="false"
											tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
											aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="modal-body">
														<button type="button" class="bootbox-close-button close"
															data-dismiss="modal" aria-hidden="true" style="margin-top: -10px;"> Ã— </button>
														<div class="bootbox-body " align="center">
															<h3 class="text-warning"> Oops something went wrong!!!</h3>
														</div>
													</div>
													<div class="modal-footer">
														<div class="row">
															<div class="col-md-12">
																<div class="col-md-12" align="center">
																	<h4><xsl:value-of select="$errorvalue" />
																	</h4>
																</div>

															</div>
														</div>
														<br />								
														<button id="ok" data-dismiss="modal"
															data-bb-handler="Ok" type="button" class="btn btn-danger btn-sm">Ok</button>
													</div>
												</div>
											</div>
										</div>
								</xsl:template>	
								
									
	<xsl:template match="item" mode="cpm-wallet">
		<xsl:variable name="storedcards" select="count(/root/stored-cards/card)" />
		<xsl:if test="count(/root/stored-cards/card) > 0">

			<div class="panel panel-default add-card">
				<div class="panel-heading" role="tab" id="headingOne">
					<a role="button" data-toggle="collapse" data-parent="#accordion1"
						href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
						<h4 class="panel-title red">
							<i class="more-less glyphicon glyphicon-minus"></i>
							Stored Cards
						</h4>
					</a>
				</div>
				<div id="collapseOne" class="panel-collapse collapse in" role="tabpanel"
					aria-labelledby="headingOne">
					<div class="panel-body">
						<div class="row">
							<!-- Start: Single saved card -->
							<xsl:for-each select="/root/stored-cards/card">
								<xsl:if test="client/@id = /root/client-config/@id">
									<div class="saved-card col-md-12  " style="border:none;box-shadow:none;">
										<div id="modalshow{@id}" class="col-md-12 saved-card">
											<img src="/css/swag/img/card_{@type-id}.png" class="card-type"
												alt="Visa" />
											<h4 class="red">
												<xsl:value-of select="name" />
											</h4>
											<p>
												<xsl:value-of select="mask" />
											</p>
											<p>
												<small>
													Expiry:
													<xsl:value-of select="expiry" />
												</small>
											</p>
										</div>
										<div class="col-md-12">
											<p class="remove-card">
												<a class="red remove-alert{@id}">
													<small>Remove </small>
													<span class="glyphicon glyphicon-trash" title="{/root/labels/delete-card}"></span>
												</a>
											</p>
										</div>
									</div>
									<!-- Start: Single saved card -->
									<!-- Start: Saved card remove alert -->
									<div id="remove-card-alert{@id}" class="alert fade in alert-danger"
										role="alert" style="display:none">
										<div class="col-md-12">
											<div class="col-md-4">Are you sure you want to remove this card?
											</div>

											<div class="col-md-6">
												<form id="delete-card" class="classy-form"
													action="{func:constLink('/cpm/sys/del_card.php') }" method="post" autocomplete="off">
													<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
													<input type="hidden" name="cardtype" value="11" />
													<input type="hidden" name="prepaid" value="false" />
													<input type="hidden" id="cardid" name="cardid" value="{@id}" />
													<input type="password" class="form-control"
														style="display: inline;width:100px;padding: none;font-size: inherit;"
														name="pwd" value="" required="required" placeholder="{/root/labels/password}" />
													<button type="submit" class="btn pull-right">Yes, remove it.</button>
												</form>
											</div>
											<div class="col-md-2">
												<button type="" class="btn btn-sm cancel">Cancel</button>
											</div>
										</div>
									</div>
									<!-- End: Saved card remove alert -->
									<!-- Start: Enter password modal -->
									<div id="modal{@id}" class="modal fade cvv-password"
										tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel{@id}">
										<div class="modal-dialog" role="document">
											<div class="modal-content text-center">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal{@id}"
														aria-label="Close">
														<span aria-hidden="true"></span>
													</button>
													<h4 class="modal-title">Enter details to pay with this card</h4>
												</div>
												<form class="form-inline classy-form" id="pay-account"
													action="{func:constLink('/pay/sys/authorize.php') }"
													method="post"  autocomplete="off">
													<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
													<input type="hidden" name="cardtype" value="{@type-id}" />
													<input type="hidden" name="prepaid" value="false" />
													<input type="hidden" id="cardid" name="cardid" value="{@id}" />
													<input type="hidden" name="storedcard" value="true" />
													<div class="form-group">
														<label class="sr-only" for="cvv">
															<xsl:value-of select="/root/labels/cvv" />
														</label>
														<div class="input-group">
															<div class="input-group-addon">CVV</div>
															<input name="cvv" type="password" class="form-control"
																id="exampleInputAmount" />
														</div>
													</div>
													<div class="form-group">
														<label class="sr-only" for="password">
															<xsl:value-of select="/root/labels/password" />
														</label>
														<div class="input-group">
															<div class="input-group-addon">Password</div>
															<input type="password" name="pwd" class="form-control"
																id="exampleInputAmount" />
														</div>
													</div>

													<button type="submit" class="btn">
														<xsl:value-of select="/root/labels/submit" />
													</button>
												</form>
											</div>
										</div>
									</div>
									<script type="text/javascript">
										$('#modalshow<xsl:value-of select="@id" />').on('click', function(e) {
										$('#modal<xsl:value-of select="@id" />').modal('show');
										});
										$('.alert .cancel').on('click', function(e) {
										$('#remove-card-alert<xsl:value-of select="@id" />').hide(100);
										$('.remove-alert<xsl:value-of select="@id" />').parents(':eq(1)').removeClass('disabled-card');
										});
										$('.remove-alert<xsl:value-of select="@id" />').on('click', function(e) {
										$('#modalshow<xsl:value-of select="@id" />').off('click');
										$('#remove-card-alert<xsl:value-of select="@id" />').show(100);
										$('.remove-alert<xsl:value-of select="@id" />').parents(':eq(1)').addClass('disabled-card');
										});

									</script>
									<!-- end: Enter password modal -->

								</xsl:if>
							</xsl:for-each>

							<!-- End: Single saved card -->
						</div>
					</div>
				</div>
			</div>
		</xsl:if>

	</xsl:template>

	<xsl:template match="cards" mode="cpm">

		<div class="panel panel-default add-card">
			<div class="panel-heading" role="tab" id="headingThree">
				<a role="button" data-toggle="collapse" data-parent="#accordion1"
					href="#collapseThree" aria-expanded="true" aria-controls="collapseTwo">
					<h4 class="panel-title red">
						<i class="more-less glyphicon glyphicon-plus"></i>
						Credit / Debit Cards
					</h4>
				</a>
			</div>
			<div id="collapseThree" class="panel-collapse collapse" role="tabpanel"
				aria-labelledby="headingThree">
				<div class="panel-body">
					<form class="classy-form" action="{func:constLink('/pay/sys/authorize.php') }"
						method="post" autocomplete="off">
						<div class="row">
							<input type="hidden" name="cardtype" value="" />
							<input type="hidden" name="pspid" value="{@pspid}" />
							<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
							<div class="form-group col-md-12">
								<div class="form-group col-md-12">
								<label for="cardholdername">
									<xsl:value-of select="/root/labels/cardholdername" />
								</label>
								<input type="text" name="cardholdername" class="form-control"
									id="card-name" placeholder="John Doe" />
								</div>
							</div>
							<div class="form-group col-md-12">
								<div class="col-md-10">
									<label for="cardnumber">
										<xsl:value-of select="/root/labels/cardnumber" />
									</label>
									<input type="tel" name="cardnumber" class="form-control cc-number"
										   autocomplete="cc-number" maxlength="23" required="required"
										   placeholder="1111 2222 3333 4444" />
								</div>
								<div class="col-md-2 vertical-align input-card-type-img-div" >
									<img name="card-logo" src="" class="input-card-type"
										 alt="Card" height="48"  />
								</div>
							</div>
							<div class="form-group col-md-12">
								<div class="col-md-3">
									<label for="expiry-month">
										<xsl:value-of select="/root/labels/expiry" />
									</label>
									<input type="text" name="expiry-month" class="form-control cc-month" maxlength="2" required="required" placeholder="MM" />
								</div>
								<div class="col-md-3">
									<label for="expiry-year">Enter Year</label>
									<input type="text" name="expiry-year" class="form-control cc-year" autocomplete="cc-year" maxlength="2" required="required" placeholder="YY" />
								</div>
								<div class="form-group col-md-6">
									<label for="cvv">
										<xsl:value-of select="/root/labels/cvv" />
									</label>
									<input type="password" name="cvv" class="form-control cc-cvv"
										autocomplete="off" maxlength="4" required="required"
										placeholder="CVV" />
								</div>
							</div>
							<div class="checkbox col-md-12">
								<label>
									<input id="save-this-card" name="store-card" type="checkbox"
										onchange="valueChanged()" />
									Save this card details for future use.
								</label>
							</div>
							<xsl:choose>
								<xsl:when test="/root/cards/@accountid > 0">
									<div id="saveNewCardName" class="form-group col-md-12"
										style="display: none">
										<label for="cardname">Name this card</label>
										<input type="text" name="cardname" class="form-control"
											id="exampleInputPassword1" placeholder="Name your card" />
									</div>
								</xsl:when>
								<xsl:otherwise>
									<div id="saveNewCardName" class="form-group col-md-12"
										style="display: none">
										<label for="cardname">Name this card</label>
										<input type="text" name="cardname" class="form-control"
											id="exampleInputPassword1" placeholder="Name your card" />
										<label for="new-password">
											<xsl:value-of select="/root/labels/password" />
										</label>
										<input type="password" class="form-control password"
											name="new-password" maxlength="20" placeholder="{/root/labels/new-password}"
											title="new-password" />
										<input type="password" class="form-control password"
											name="repeat-password" maxlength="20"
											placeholder="{/root/labels/repeat-password}" title="repeat-password" />
									</div>
								</xsl:otherwise>
							</xsl:choose>
							<div class="col-md-12 text-right">
								<br />
								<button type="submit" class="btn">Confirm Payment</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			var cards = [
			<xsl:for-each select="/root/cards/item">
				<xsl:if
					test="@id = 1 or @id = 2 or @id = 3 or @id = 5 or @id = 6 or @id = 7 or @id = 8 or @id = 9">
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
						/(\d{1,4})(\d{1,6})?(\d{1,5})?/
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
			var cardError =$('.cc-number').toggleInputError(!$.payment.validateCardNumber($('.cc-number').val()));
			var expiryError =$('.cc-month').toggleInputError(!$.payment.validateCardExpiry($('.cc-month').val(),$('.cc-year').val()));
			var expiryError =$('.cc-year').toggleInputError(!$.payment.validateCardExpiry($('.cc-month').val(),$('.cc-year').val()));
			var cvcError =$('.cc-cvv').toggleInputError(!$.payment.validateCardCVC($('.cc-cvv').val(),cardType));
			$('.cc-card-type div').attr('class', 'icon ' + cardType);
			if(cardType===null)
			{
			$(".input-card-type-img-div").hide();
			$(".input-card-type").attr("src","");
			}
			else{
			$(".input-card-type").attr("src","/css/swag/img/" + cardType.replace('-',"_") +".png");
			$(".input-card-type-img-div").show();
			}
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

			$('.cc-number, .cc-cvv, .cc-month, .cc-year').on('change paste keyup input',function()
			{
			validateInput();
			});
			});
		</script>

		
		<script type="text/javascript">
			function valueChanged()
			{
			if($('#save-this-card').is(":checked"))
			$("#saveNewCardName").show();
			else
			$("#saveNewCardName").hide();
			}
		</script>

		<!-- Script for +/- accordian for add card -->
		<script type="text/javascript">
			function toggleIcon(e) {
			$(e.target)
			.prev('.panel-heading')
			.find(".more-less")
			.toggleClass('glyphicon-plus glyphicon-minus');
			}
			$('.panel-group').on('hidden.bs.collapse', toggleIcon);
			$('.panel-group').on('shown.bs.collapse', toggleIcon);

		</script>


	</xsl:template>



	<xsl:template match="cards" mode="other-wallet">
		<div class="panel panel-default add-card">
			<div class="panel-heading" role="tab" id="headingTwo">
				<a role="button" data-toggle="collapse" data-parent="#accordion1"
					href="#collapseTwo" aria-expanded="true" aria-controls="collapseThree">
					<h4 class="panel-title red">
						<i class="more-less glyphicon glyphicon-plus"></i>
						Other Payment Options
					</h4>
				</a>
			</div>
			<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel"
				aria-labelledby="headingTwo">
				<div class="panel-body">
					<div class="row">
						<xsl:for-each select="/root/cards/item">
							<xsl:choose>					
								<xsl:when test="@id = '28'">
									<div class="col-md-12">
										<div class="wallet-type" id="walletvisa_{@id}" onClick="document.forms['walletform_{@id}'].submit();">

											<div class="row" data-toggle="modal" data-target=".login-wallet">
												<div class="payment-paypal-form payment-form">
													<form action="{func:constLink('/pay/sys/apm.php') }" method="POST" name="walletform_{@id}" id="walletform_{@id}"  autocomplete="off">
															<span class="glyphicon glyphicon-chevron-right right-icon pull-icon-right"
																	aria-hidden="true"></span>
															<div class="col-md-12" id="card-{@id}">
																<!-- <img src="/css/swag/img/paypal.png" class="wallet-img" 
																	alt="Paypal"/> -->
																	<img src="{/root/system/protocol}://{/root/system/host}/img/card_28.png" alt="PayPal" style="max-height: 80px"/>
															</div>
															<div class="checkbox col-md-12">
																<label>
																	<input id="save-this-paypal" name="store-card" type="checkbox" onchange="valueChangedPaypal()" />
																	<xsl:value-of select="/root/labels/savecard" />
																</label>
															</div>
															<xsl:choose>
																<xsl:when test="/root/cards/@accountid > 0">
																	<div id="saveNewPaypal" class="form-group col-md-12"
																		style="display: none">
																		<label for="cardname"><xsl:value-of select="/root/labels/name" /></label>
																		<input type="text" id="cardname" name="cardname" class="form-control"
																			 placeholder="{/root/labels/name}" />
																	</div>
																</xsl:when>
																<xsl:otherwise>
																	<div id="saveNewPaypal" class="form-group col-md-12"
																		style="display: none">
																		<label for="cardname"><xsl:value-of select="/root/labels/name" /></label>
																		<input type="text" name="cardname" class="form-control"
																			id="cardname" placeholder="{/root/labels/name}" />
																		<label for="new-password">
																			<xsl:value-of select="/root/labels/password" />
																		</label>
																		<input type="password" class="form-control password"
																			name="new-password" maxlength="20" placeholder="{/root/labels/new-password}"
																			title="new-password" />
																		<input type="password" class="form-control password"
																			name="repeat-password" maxlength="20"
																			placeholder="{/root/labels/repeat-password}" title="repeat-password" />
																	</div>
																</xsl:otherwise>
															</xsl:choose>
														<input type="hidden" name="transactionid" value="{/root/transaction/@id}" /> 
														<input type="hidden" name="cardtype" value="{@id}" /> 	
													</form>
													<script type="text/javascript">
														function valueChangedPaypal()
														{
														if($('#save-this-paypal').is(":checked"))
														$("#saveNewPaypal").show();
														else
														$("#saveNewPaypal").hide();
														}
													</script>
													
											</div>
										</div>
									</div>
								</div>
							</xsl:when>
								<xsl:when test="@id = '16'">
									<div class="col-md-12">
										<div class="wallet-type" id="walletvisa_{@id}" >
											<div class="row" data-toggle="modal" data-target=".login-wallet">
												<span class="glyphicon glyphicon-chevron-right right-icon pull-icon-right" aria-hidden="true"></span>
												<div class="col-md-12" id="card-{@id}">
													<!-- <img src="/css/swag/img/paypal.png" class="wallet-img" 
														alt="Paypal"/> -->
														<img src="{/root/system/protocol}://{/root/system/host}/img/card_28.png" alt="PayPal" style="max-height: 80px"/>
												</div>
											</div>
										</div>
									</div>
									<form name="walletform_{@id}" id="walletform_{@id}"
										action="{func:constLink('/pay/sys/authorize.php') }" method="post"  autocomplete="off">
										<input type="hidden" name="cardtype" value="{@id}" />
										<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
										<input type="hidden" name="token" id="token" value="" />
										<input type="hidden" name="verifier" id="verifier"
											value="" />
										<input type="hidden" name="checkouturl" id="checkouturl" value="" />
									</form>
									<script type="text/javascript">
										var id =<xsl:value-of select="@id" />;
	
										jQuery("head").append("<xsl:value-of select="head" />");
	
										jQuery("#card-"+id).html('<xsl:value-of select="body" />');
				
										$('#walletvisa_<xsl:value-of select="@id" />').on('click', function() {
											$('img.v-button').trigger('click');
											});
									</script>
								</xsl:when>
								<xsl:when test="@id = '30'">
									<div class="col-md-12">
										<div class="wallet-type" id="apm_{@id}" name="apm_{@id}" >
											<div class="row" data-toggle="modal" data-target=".login-wallet">
												<span class="glyphicon glyphicon-chevron-right right-icon pull-icon-right" aria-hidden="true"></span>
												<div class="col-md-12" id="card-{@id}">
													<img src="{/root/system/protocol}://{/root/system/host}/img/card_30.png" alt="MobilePay-Online" style="max-height: 80px"/>
												</div>
											</div>
										</div>
									</div>
									<div class="payment-form hide">
										<form name="walletform_{@id}" id="walletform_{@id}" class="form-inline classy-form" action="{func:constLink('/pay/sys/apm.php') }" method="post"  autocomplete="off">
											<input type="hidden" name="pspid" value="{@pspid}" />
											<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
											<input type="hidden" name="transactionid" value="{/root/transaction/@id}" />
											<input type="hidden" name="cardtype" value="{@id}" />
										</form>
									</div>
									<script type="text/javascript">
										$('#apm_<xsl:value-of select="@id" />').on('click', function(e) {
										document.forms['walletform_<xsl:value-of select="@id" />'].submit();
										});
									</script>
								</xsl:when>
								<xsl:when test="@id = '31'">
									<div class="col-md-12">
										<div class="wallet-type" id="sadad_{@id}" name="sadad_{@id}" >
											<div class="row" >
											<span class="glyphicon glyphicon-chevron-right right-icon pull-icon-right" aria-hidden="true"></span>
												<div class="col-md-12" id="card-{@id}">
														<img src="{/root/system/protocol}://{/root/system/host}/img/card_{@id}.png" alt="SaDaD" style="max-height: 60px"/>
												</div>
											</div>
										</div>
									</div>
									<div id="modalsadad{@id}" class="modal fade cvv-password"
											tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabelsadad{@id}">
											<div class="modal-dialog" role="document">
												<div class="modal-content text-center">
													<div class="modal-header">
														<button type="button" class="close" data-dismiss="modalsadad{@id}"
															aria-label="Close">
															<span aria-hidden="true"></span>
														</button>
														<h4 class="modal-title">Enter details to pay with sadad account</h4>
													</div>
													<div class="payment-form">
														<form name="walletform_{@id}" id="walletform_{@id}" class="form-inline classy-form" action="{func:constLink('/pay/sys/sadad.php') }" method="post" autocomplete="off">
															<input type="hidden" name="pspid" value="{@pspid}" />
															<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
															<div class="form-group row">
																<div class="col-lg-12 text-center">
																	<div class="input-group">
																			<input type="tel" name="sadad_payment_id" class="form-control" maxlength="23" required="required" placeholder="SADAD Payment Id" />
																	</div>
																</div>
																<div class="col-lg-12 text-center">
																	<button type="submit" class="btn"> <xsl:value-of select="/root/labels/button" /></button>
																	</div>
															</div>
														</form>
													</div>
												</div>
											</div>
										</div>
										<script type="text/javascript">
											$('#sadad_<xsl:value-of select="@id" />').on('click', function(e) {
												$('#modalsadad<xsl:value-of select="@id" />').modal('show');
											});
										</script>
								</xsl:when>
								<xsl:when test="@id = '32' or @id = '33'">
									<div class="col-md-12">
										<div class="wallet-type" id="apm_{@id}" name="apm_{@id}" >
											<div class="row" >
											<span class="glyphicon glyphicon-chevron-right right-icon pull-icon-right" aria-hidden="true"></span>
												<div class="col-md-12" id="card-{@id}">
														<img src="{/root/system/protocol}://{/root/system/host}/img/card_{@id}.png" alt="{name}" style="max-height: 60px"/>
												</div>
											</div>
										</div>
									</div>
									<div class="payment-form hide">
										<form name="walletform_{@id}" id="walletform_{@id}" class="form-inline classy-form" action="{func:constLink('/pay/sys/apm.php') }" method="post"  autocomplete="off">
											<input type="hidden" name="pspid" value="{@pspid}" />
											<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
											<input type="hidden" name="transactionid" value="{/root/transaction/@id}" /> 
											<input type="hidden" name="cardtype" value="{@id}" /> 												
										</form>
									</div>
									<script type="text/javascript">
										$('#apm_<xsl:value-of select="@id" />').on('click', function(e) {
											document.forms['walletform_<xsl:value-of select="@id" />'].submit();
										});
									</script>
								</xsl:when>	
								<xsl:when test="@id = '34'">
									<div class="col-md-12">
										<div class="wallet-type" id="apm_{@id}" name="apm_{@id}" >
											<div class="row" >
											<span class="glyphicon glyphicon-chevron-right right-icon pull-icon-right" aria-hidden="true"></span>
												<div class="col-md-12" id="card-{@id}">
														<img src="{/root/system/protocol}://{/root/system/host}/img/card_{@id}.png" alt="{name}" style="max-height: 60px"/>
												</div>
											</div>
										</div>
									</div>
									<div class="payment-form hide">
										<form name="walletform_{@id}" id="walletform_{@id}" class="form-inline classy-form" action="{func:constLink('/pay/sys/apm.php') }" method="post"  autocomplete="off">
											<input type="hidden" name="pspid" value="{@pspid}" />
											<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
											<input type="hidden" name="transactionid" value="{/root/transaction/@id}" /> 
											<input type="hidden" name="cardtype" value="{@id}" /> 												
										</form>
									</div>
									<script type="text/javascript">
										$('#apm_<xsl:value-of select="@id" />').on('click', function(e) {
											document.forms['walletform_<xsl:value-of select="@id" />'].submit();
										});
									</script>
								</xsl:when>									
						</xsl:choose>
					</xsl:for-each>
				</div>
			</div>
		</div>
	</div>
</xsl:template>


</xsl:stylesheet>
