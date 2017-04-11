<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <title>Payment Status</title>

  <!-- Bootstrap -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">


  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
    </head>
    <body>

      <section>
        <div class="container main">
          <div class="row">
            <div class="col-md-3">
              <a href="" class="logo"><img src="img/logo.jpg" alt="CellPoint Mobile" /></a>
            </div>
            <div class="col-md-9 text-right">
              <h2 class="sub-header">Payment Status <small>(step 3/3)</small></h2>
            </div>
          </div> 
          <hr>
          <div class="row panel-main finish-message">
            <div class="col-md-6 col-md-offset-3 text-center">
              <p class="success-icon"><span class="glyphicon glyphicon-ok"></span></p>
              <h3>Your payment has been processed successfuly!</h3>
              <p>Payment reference id: <?php echo $_REQUEST['order'];?></p>
              <a href="#" class="btn">Finish <span class="glyphicon glyphicon-ok"></a>
            </div>
          </div>
        </div>
      </section>
      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
      <!-- Include all compiled plugins (below), or include individual files as needed -->
      <script src="js/bootstrap.min.js"></script>
    </body>
    </html>