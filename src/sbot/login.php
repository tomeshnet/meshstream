<?php

include ("global.php");
if (isset($_SESSION['login'])) header("location: view.php");			
if (isset($_POST['action'])) {
	$l=$_POST['login'];
	$p=$_POST['password'];
	$lp=file_get_contents ("/var/www/backend/userlist");
	$lpa=explode("\n",$lp);
	foreach ($lpa as $k=>$v) {
		$kv=explode("\t",$v);
		
        if ($kv[0]==$l) {			
			if ($kv[1] == GetPasswordHash($l,$p)) {
				$_SESSION['login']=$l;
				header("location: view.php");			
			}
		}
	}
	die("incorrect l/p");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/Template.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Untitled Document</title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
<link href="main.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="TopMenu">
  <ul>
    <li><a href="index.php"><img src="images/SSB-logo.png" width="45" height="45" /></a></li>
    <?php if (isset($_SESSION['login'])) { ?>
      <li><a href="view.php">View</a></li>
      <li><a href="changename.php">Change Name</a></li>
      <?php } else { ?>
      <li><a href="login.php">Login</a></li>
      <li><a href="create.php">Create</a></li>
      <?php } ?>
  </ul>
</div>
<!-- InstanceBeginEditable name="Body" --><br /><br />
<form method="post" class="login">
<h1>Login Account</h1>
<input class="textbox" placeholder="Login" name="login"><br>
<input class="textbox"  placeholder="Password" name="password" type="password"><br>
<input type="submit" name="action" value="Login"> <input type="button" onclick="document.location='create.php'"  value="Create Account">
</form>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
