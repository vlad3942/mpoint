<!DOCTYPE html>
<html>
   <body>
      <h2>Data Migration Tool</h2>
      <p style="color: red;"><?=$msg?></p>
      <table width="80%" border="1px" cellpadding="10">
      	<tr>
      		<td><strong>File Upload </strong><!-- <a href="sample.csv" cursor="pointer">(download sample file) </a> --></td>
      		<td><strong>Initialize Payment</strong></td>
      		<td><strong>Login</strong></td>
      		<td><strong>Log</strong></td>
      	</tr>
      	<tr>
      		<td width="20%">
                  <form action="" method="POST" enctype="multipart/form-data">
                     <input type="file" name="fileupload" /> <br/><br/>
                     <input type="submit" name="submit" value="Push Into DB"/>
                  </form>
      		</td>
      		<td width="25%">
      			<form action="api/initialize.php" method="POST" target="_blank">
          			Enter Customer Ref. <br/>
          			<input type="text" name="cust_ref" value="" autocomplete="off" required />
          			<input type="submit" name="submit" value="Initialize Payment"/>
      			</form>
      		</td>
      		<td width="20%">
      			<form action="api/login.php" method="POST" target="_blank">
          			Enter Customer Ref. <br/>
          			<input type="text" name="cust_ref" value="" autocomplete="off" required />
          			<input type="submit" name="submit" value="Login"/>
      			</form>
      		</td>
      		<td width="15%">
      			<a href="log/log.csv" cursor="pointer"><?php if(file_exists("log/log.csv")) { ?> Click Here <?php } ?></a>
      		</td>
      	</tr>
      </table>
		
	  <br/><br/>
      
      <table width="40%" border="1px" cellpadding="10">
      	<tr>
      		<td><strong>PGP Encryption</strong></td>
      		<td><strong>PGP Decryption </strong></td>
      	</tr>
      	<tr>
      		<td width="20%">
                  <form method="POST" enctype="multipart/form-data">
                     <input type="file" name="fileupload" /> <br/><br/>
                     <input type="submit" name="submit" value="Encrypt"/>
                  </form>
      		</td>
      		<td width="20%">
                  <form method="POST" enctype="multipart/form-data">
                     <input type="file" name="fileupload" /> <br/><br/>
                     <input type="submit" name="submit" value="Decrypt"/>
                  </form>
      		</td>
      	</tr>
      </table>
      
   </body>
</html>