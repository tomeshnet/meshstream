<?php
session_start();
        $l=$_SESSION['login'];

function toDate($ts) {
        $ts=$ts/1000;
        return  date('Y-m-d H:i:s',$ts);
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
<!-- InstanceBeginEditable name="Body" -->
<style>
.post {
        width:400px;
        margin:0 auto;
        padding:20px;
}
.post .author { font-size:10px; }
.post .timestamp { font-size:10px; }

</style>
<center><a href="post.php">Post</a></center>
<?php
$v=shell_exec ("nodejs /var/www/backend/view.js $l 2>&1");
$v="[" . $v . " {}]";
$v=json_decode($v,true);
array_pop($v);
foreach ($v as $post) {
        echo "<!--";
        print_r($post);
        echo "-->";
        ?>
        <div class="post">
        <span class="author"><b>Author:</b><span><?=$post['value']['author']?></span></span><br />
        <span class="timestamp"><span><?=toDate($post['value']['timestamp'])?></span></span><br />
        <hr />
        <?php
        switch  ($post['value']['content']['type']){
                case "contact":
                        echo "Following " . $post['value']['content']['contact'];
                        break;
                case "about":
                        echo "Self Identified as " . $post['value']['content']['name'];
                        break;
                case "post":
                        echo  $post['value']['content']['text'];
                        break;
                default:
                print_r($post);
        }
        ?>

        </div>
<?php } ?>

<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd --></html>
