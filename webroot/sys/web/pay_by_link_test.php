<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>Cart Screen</title>

<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">


<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700"
		rel="stylesheet" />
		</head>
		<body>
    <form action="http://<?= $_SERVER['HTTP_HOST']; ?>/buy/web.php" method="post">
			<table>
				<tr>


					<td><?php echo "<input name=\"country\" id=\"country\" value=\"100\" type=\"text\" />"; ?>
	    </td>
				</tr>
				<tr>
					<td><?php echo "<input name=\"clientid\" id=\"clientid\" value=\"10007\" type=\"text\" /></td>"; ?>

				
				</tr>
				<tr>
					<td><?php echo "<input name=\"account\" id=\"account\" value=\"100007\" type=\"text\" /></td>"; ?></td>
				</tr>
				<tr>
					<td><?php echo "<input name=\"amount\" id=\"amount\" value=\"1\" type=\"text\" /></td>"; ?></td>
				</tr>
				<tr>
					<td><?php echo "<input name=\"txnid\" id=\"txnid\" value=\"1813231\" type=\"text\" /></td>"; ?></td>
				</tr>
				<tr>
<input type="submit"/>
				</tr>
			</table>
		</form>
    </body>
    </html>
						