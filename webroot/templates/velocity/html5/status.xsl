<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions"
	extension-element-prefixes="func">
	<xsl:output method="html" indent="no" media-type="text/html"
		doctype-public="HTML" omit-xml-declaration="yes" standalone="yes" />

<xsl:template match="messages">
<script type="text/javascript">
$(document).ready(function() {

if(document.location.search.length) {
  $('#modalerror').modal('show'); 
}
    $("#ok").click(function(){
    var Qurl = window.location.href;
    var url = Qurl.substr(0, Qurl.indexOf('?'));
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
															data-dismiss="modal" aria-hidden="true" style="margin-top: -10px;"> × </button>
														<div class="bootbox-body " align="center">
															<h3 class="text-danger"> Oops something went wrong!!!</h3>
														</div>
													</div>
													<div class="modal-footer">
														<div class="row">
															<div class="col-md-12">
																<div class="col-md-12" align="center">
																	<h4>
																		<xsl:if test="count(item) &gt; 0">
																			<xsl:choose>
																				<xsl:when test="count(item) = 1">
																					<xsl:value-of select="item" />
																				</xsl:when>
																				<xsl:otherwise>
																					<ul>
																					<xsl:for-each select="item">
																						<li><xsl:value-of select="." /></li>
																					</xsl:for-each>
																					</ul>
																				</xsl:otherwise>
																			</xsl:choose>
																		</xsl:if>
																	</h4>
																	
																</div>

															</div>
														</div>
														<br />								
														<button id="ok" data-dismiss="modal" data-bb-handler="Ok" type="button" class="btn btn-danger btn-sm">Ok</button>
													</div>
												</div>
											</div>
										</div>
</xsl:template>

<xsl:template match="/root">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=ISO-8859-15" />
		<meta http-equiv="Cache-Control" content="max-age=86400" />
		<meta http-equiv="Content-Style-Type" content="text/css" />	
		<title><xsl:value-of select="/root/title" /></title>
		<link href="{client-config/css-url}" type="text/css" rel="stylesheet" media="handheld" />
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700" rel="stylesheet" type="text/css" />
		<link href="/css/swag/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
		<link href="/css/swag/css/style.css" type="text/css" rel="stylesheet" />
			<script src="https://code.jquery.com/jquery-3.1.0.min.js" integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s=" crossorigin="anonymous"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
		<!-- <link href="{/root/transaction/css-url}" type="text/css" rel="stylesheet" />-->
				
	</head>
	<body>
		<div id="logo">
			<img src="{client-config/logo-url}" alt="- {client-config/name} -" />
		</div>


		<!-- Display Status Messages -->
		<xsl:apply-templates select="messages" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files 
			as needed -->
		<script src="/css/swag/js/bootstrap.min.js"></script>
	</body>
	</html>
</xsl:template>

</xsl:stylesheet>