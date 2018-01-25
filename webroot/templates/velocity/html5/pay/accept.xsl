<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions"
	extension-element-prefixes="func">
	<xsl:output method="html" indent="no" media-type="text/html"
		doctype-public="HTML" omit-xml-declaration="yes" standalone="yes" />
	<xsl:include href="../mobile.xsl" />

	<xsl:template match="/root">

		<xsl:choose>
			<xsl:when test="transactionstatus = 0">
				<xsl:apply-templates select="." mode="fail">
					<xsl:with-param name="transaction_id" select="transactionid" />
					<xsl:with-param name="cssurldata" select="cssurl" />
					<xsl:with-param name="accepturldata" select="accepturl" />
					<xsl:with-param name="cancelurldata" select="cancelurl" />
				</xsl:apply-templates>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="." mode="success">
					<xsl:with-param name="transaction_id" select="transactionid" />
					<xsl:with-param name="cssurldata" select="cssurl" />
					<xsl:with-param name="accepturldata" select="accepturl" />
					<xsl:with-param name="cancelurldata" select="cancelurl" />
				</xsl:apply-templates>
			</xsl:otherwise>
		</xsl:choose>






		<!-- <div class="success-page-wrapper"> <div class="content"> <h2> <xsl:value-of 
			select="labels/mpoint" /> </h2> <img src="{system/protocol}://{system/host}/img/mpoint_logo.png" 
			width="{mpoint-logo/width}" height="{mpoint-logo/height}" alt="- mPoint -" 
			style="width:128px;max-width:128px;"/> <div class="status"> <div class="checkmark"></div> 
			<div class="info"><xsl:value-of select="labels/status" /></div> </div> <div 
			class="receipt"> <div class="label"><xsl:value-of select="labels/txnid" />:</div> 
			<div class="number"><xsl:value-of select="transaction/@id" /></div> Order 
			Number Provided <xsl:if test="string-length(transaction/orderid) &gt; 0"> 
			<div class="label"><xsl:value-of select="labels/orderid" />:</div> <div class="number"><xsl:value-of 
			select="transaction/orderid" /></div> </xsl:if> <div class="label"><xsl:value-of 
			select="labels/price" />:</div> <div class="number"><xsl:value-of select="transaction/price" 
			/></div> </div> <div class="info-wrapper"> SMS Receipt Enabled <xsl:if test="client-config/sms-receipt 
			= 'true'"> <div class="info"> <xsl:value-of select="labels/sms-receipt" /> 
			</div> </xsl:if> Display Status Messages <xsl:apply-templates select="messages" 
			/> E-Mail Receipt Enabled <xsl:if test="client-config/email-receipt = 'true'"> 
			<div> <a href="{func:constLink('email.php')}"><xsl:value-of select="labels/email-receipt" 
			/></a> </div> </xsl:if> <xsl:choose> Current transaction is an Account Top-Up 
			and a previous transaction is in progress <xsl:when test="original-transaction-id 
			&gt; 0"> <div> <form action="{func:constLink('/cpm/payment.php?msg=1')}" 
			method="post"> <p> <input type="hidden" name="resume" value="true" /> </p> 
			<p> <input type="submit" value="{labels/resume}" class="button" /> </p> </form> 
			</div> </xsl:when> Client has specified a return URL for successful payments 
			<xsl:when test="string-length(transaction/accept-url) &gt; 0"> <div> <form 
			id="continue" action="{func:constLink(transaction/accept-url)}" method="post"> 
			<div> Standard mPoint Variables <input type="hidden" name="mpoint-id" value="{transaction/@id}" 
			/> <input type="hidden" name="orderid" value="{transaction/orderid}" /> <input 
			type="hidden" name="status" value="2000" /> <input type="hidden" name="amount" 
			value="{transaction/amount}" /> <input type="hidden" name="currency" value="{transaction/amount/@currency}" 
			/> <input type="hidden" name="mobile" value="{transaction/mobile}" /> <input 
			type="hidden" name="operator" value="{transaction/operator}" /> <input type="hidden" 
			name="mac" value="{transaction/mac}" /> Custom Client Variables <xsl:for-each 
			select="client-vars/item"> <input type="hidden" name="{name}" value="{value}" 
			/> </xsl:for-each> </div> <div id="submit"> <input type="submit" value="{labels/continue}" 
			class="button" /> </div> </form> </div> Automatically send customer back 
			to merchant's site <script type="text/javascript"> //setTimeout(function() 
			{ document.getElementById('continue').submit(); }, 5000); </script> </xsl:when> 
			</xsl:choose> </div> </div> </div> -->
	</xsl:template>

	<xsl:template match="root" mode="success">
		<xsl:param name="cssurldata" />
		<xsl:param name="accepturldata" />
		<xsl:param name="transaction_id" />
		<link href="/css/bootstrap/bootstrap.min.css" rel="stylesheet" />
		<link href="/css/bootstrap/styles.css" rel="stylesheet" />
		<link href="{$cssurldata}" rel="stylesheet" />


		<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700"
			rel="stylesheet" />
		<section>
			<div class="container main">
				<div class="row">
					<div class="col-md-3">
						<a href="" class="logo">
							<img src="/../templates/velocity/html5/pay/img/logo.jpg" alt="CellPoint Mobile" />
						</a>
					</div>
					<div class="col-md-9 text-right">
						<h2 class="sub-header">
							Payment Status
							<small>(step 3/3)</small>
						</h2>
					</div>
				</div>
				<hr />
				<div class="row panel-main finish-message">
					<div class="col-md-6 col-md-offset-3 text-center">
						<p class="success-icon">
							<span class="glyphicon glyphicon-ok"></span>
						</p>
						<h3>Your payment has been processed successfuly!</h3>
						<p>
							Payment reference id:
							<xsl:value-of select="$transaction_id" />
						</p>
						<a href="{$accepturldata}" class="btn">
							Finish
							<span class="glyphicon glyphicon-ok"></span>
						</a>
					</div>
				</div>
			</div>
		</section>
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="js/jquery.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files 
			as needed -->
		<script src="js/bootstrap.min.js"></script>
	</xsl:template>

	<xsl:template match="root" mode="fail">
		<xsl:param name="cssurldata" />
		<xsl:param name="cancelurldata" />
		<xsl:param name="transaction_id" />

		<link href="/css/bootstrap/bootstrap.min.css" rel="stylesheet" />
		<link href="/css/bootstrap/styles.css" rel="stylesheet" />
		<link href="{$cssurldata}" rel="stylesheet" />


		<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700"
			rel="stylesheet" />
		<section>
			<div class="container main">
				<div class="row">
					<div class="col-md-3">
						<a href="" class="logo">
							<img src="/../templates/velocity/html5/pay/img/logo.jpg" alt="CellPoint Mobile" />
						</a>
					</div>
					<div class="col-md-9 text-right">
						<h2 class="sub-header">
							Payment Status
							<small>(step 3/3)</small>
						</h2>
					</div>
				</div>
				<hr />
				<div class="row panel-main finish-message">
					<div class="col-md-6 col-md-offset-3 text-center">
						<p class="failure-icon">
							<span class="glyphicon glyphicon-remove"></span>
						</p>
						<h3>Oops... Something went wrong!</h3>
						<p>
							failed Payment ReferenceId
							<xsl:value-of select="$transaction_id" />
						</p>
						<p>Please try with another payment method or different card.</p>
						<a href="{$cancelurldata}" class="btn">
							<span class="glyphicon glyphicon-arrow-left"></span>
							Try again
						</a>
					</div>
				</div>
			</div>
		</section>
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="js/jquery.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files 
			as needed -->
		<script src="js/bootstrap.min.js"></script>

	</xsl:template>
</xsl:stylesheet>
