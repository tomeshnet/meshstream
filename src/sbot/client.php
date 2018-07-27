<?php
session_start();
class sbotClient {
        var $nameRepo;
        function __construct() {
        }
        function getLogin() {
                return $_SESSION['login'];

        }
        function Post($msg) {
          if (isset($_POST['post'])) {
                $l=$this->getLogin();
                shell_exec ("nodejs /var/www/backend/post.js $l \"$msg\" 2>&1");
                return 1;
          }
    	}
        function toDate($ts) {
                $ts=$ts/1000;
                return  date('Y-m-d H:i:s',$ts);
        }
        function getName($r) {
                $l=$this->getLogin();
                if (isset($this->nameRepo[$r])) {
                        return $this->nameRepo[$r];
                } else {
                	$v=shell_exec ("nodejs /var/www/backend/getname.js $l \"$r\" 2>&1");
                        if (trim($v)=="") {
                                return $r;
                        } else {
                                return $this->nameRepo[$r]=$v;
                        }
                }
        }
	function render($msg) {
			$msg=str_replace("\n","<br/>",$msg);
			$msg=preg_replace("#\[!videoIPFS:(.*?)\]#si",$this->renderPlayerIPFS("\\1"),$msg);
	 		$msg=preg_replace("#\[!videoIPNS:(.*?)\]#si",$this->renderPlayerIPNS("\\1"),$msg);
			return $msg;
	}
	var $counter=0;
        function renderPlayerIPFS($url) {
                $this->counter++;
                $res ='<video id="live' . $this->counter  .'" class="video-js vjs-default-skin vjs-big-play-centered" controls >';
                $res.='<p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that supports HTML5 video</p>';
                $res.='<source src="/ipfs/\\1">';
		$res.='</video>';
		$res.="<script>var player".$this->counter." = videojs('#live".$this->counter."', { height: 360 });</script>";
                return $res;
        }
        function renderPlayerIPNS($url) {
                $this->counter++;
                $res ='<video id="live' . $this->counter  .'" class="video-js vjs-default-skin vjs-big-play-centered" controls >';
                $res.='<p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that supports HTML5 video</p>';
                $res.='<source src="/ipns/\\1"  type="application/x-mpegURL">';
                $res.='</video>';
		$res.="<script>var player".$this->counter." = videojs('#live".$this->counter."', { height: 360 });</script>";
                return $res;
        }
	function changeName($newName) {
		$l=$this->getLogin();
		$n=$newName;
		shell_exec ("nodejs /var/www/backend/changename.js $l \"$n\" 2>&1");
	}
  	function getPeers() {
		$source=shell_exec ("sbot gossip.peers");
                $source=json_decode($source,true);

                foreach ($source as $peer) {
                    if (!isset($peer['failure']) || $peer['failure']=='0') {
                        $r['name']=$this->getName($peer['key']);
                        if ($peer['source']=='local') {
                           $local[]=$r;
                        } else {
                            $remote[]=$r;
                       }
                    }
                }
                $peers['local']=$local;
                $peers['remote']=$remote;

                return $peers;
               }
}
$sbot=new sbotClient();
?>
