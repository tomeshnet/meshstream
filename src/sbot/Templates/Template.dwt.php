<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- TemplateBeginEditable name="doctitle" -->
<title>Untitled Document</title>
<!-- TemplateEndEditable -->
<!-- TemplateBeginEditable name="head" -->
<!-- TemplateEndEditable -->
<link href="../main.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="TopMenu">
  <ul>
    <li><a href="index.php"><img src="../images/SSB-logo.png" width="45" height="45" /></a></li>
    <?php if (isset($_SESSION['login'])) { ?>
      <li><a href="view.php">View</a></li>
      <li><a href="changename.php">Change Name</a></li>
      <?php } else { ?>
      <li><a href="login.php">Login</a></li>
      <li><a href="create.php">Create</a></li>
      <?php } ?>
  </ul>
</div>
<!-- TemplateBeginEditable name="Body" -->Body<!-- TemplateEndEditable -->
</body>
</html>
