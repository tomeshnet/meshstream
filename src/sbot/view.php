<?php
include ("client.php");
if (isset($_POST['post'])) {
	$p=$_POST['post'];
	$sbot->Post($p);
    header("location: view.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!-- InstanceBegin template="/Templates/Template.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Untitled Document</title>
<link href="video-js.min.css" rel="stylesheet">
<script src="video.min.js"></script> 
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
<script>
HTMLTextAreaElement.prototype.insertAtCaret = function (text) {
  text = text || '';
  if (document.selection) {
    // IE
    this.focus();
    var sel = document.selection.createRange();
    sel.text = text;
  } else if (this.selectionStart || this.selectionStart === 0) {
    // Others
    var startPos = this.selectionStart;
    var endPos = this.selectionEnd;
    this.value = this.value.substring(0, startPos) +
      text +
      this.value.substring(endPos, this.value.length);
    this.selectionStart = startPos + text.length;
    this.selectionEnd = startPos + text.length;
  } else {
    this.value += text;
  }
};
function InsertIPFSVideo() {
	var hash=prompt("Please enter IPFS Hash");
	var p=document.getElementById("post");
	p.insertAtCaret("[!videoIPFS:" + hash + "]");
}
	
function InsertIPNSVideo() {
	var hash=prompt("Please enter IPFS Hash");
	var p=document.getElementById("post");
	p.insertAtCaret("[!videoIPNS:" + hash + "]");
}
</script>
<style>
.post {
	width:600px;
	margin:0 auto;
	padding:20px;
}
.post .author { font-size:10px; }
.post .timestamp { font-size:10px; }
.meta { display:inline-block; width:200px; vertical-align:top; background:#CCC;}
.data { display:inline-block; width:395px;}
</style>
<div style="width:395px; margin:0 auto">
  <form method="post">
     <input type="button" value="Insert IPFS Video" onclick="InsertIPFSVideo()" />
     <input type="button" value="Insert IPNS Stream" onclick="InsertIPNSVideo()" />
    <textarea name="post" cols="40" rows="3" id="post"></textarea>
    <br />
    <input  type="submit" value="Post">
  </form>
</div>
<div style="float:left; width:200px; display:block; border:1px solid red; overflow: hidden;">
<h2>Peers</h2><br>
<?php 
$p=$sbot->getPeers()['local'];
foreach ($p as $peer)
        echo substr($peer['name'],0,20) . "<br>";

?>
</div>
<?php
$v=shell_exec("cat /var/www/backend/keys/" . $_SESSION['login'] . " |grep -v \\#");
//$v=file_get_contents("/var/www/backend/keys/" . $_SESSION['login']);
$r=json_decode($v);
//echo $r->id;
$v= shell_exec("/usr/local/bin/sbot feed --reverse 2>&1");
$v="[" . str_replace("}\n\n{","}\n,\n{",$v) . "]";
$v=json_decode($v,true);

foreach ($v as $post) {
	echo "<!--";
	print_r($post);
	echo "-->";
	?>
<div class="post">
  <div class="meta"> <span class="author"><b>Author:</b><span>
    <?=$sbot->getName($post['value']['author'])?>
    </span></span><br />
    <span class="timestamp"><span>
    <?=$sbot->toDate($post['value']['timestamp'])?>
    </span></span><br />
  </div>
  <div class="data">
    <?php
	switch  ($post['value']['content']['type']){
		case "contact":
			echo "Following " . $post['value']['content']['contact'];
			break;
		case "about":
			echo "Self Identified as " . $post['value']['content']['name'];
			break;
		case "post":
			echo  $sbot->render($post['value']['content']['text']);
			break;
		default:
		print_r($post);
	}
	?>
  </div>
</div>
<?php } ?>
<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd -->
</html>
