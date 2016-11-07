<meta charset='utf-8' />
<style>body{font-size:2em;}table{font-size:1em;}</style>
<SCRIPT type="text/javascript">
var strUA = "";
strUA = navigator.userAgent.toLowerCase();

if(strUA.indexOf("iphone") >= 0) {
  document.write('<meta name="viewport" content="width=960px, minimum-scale=0.45, maximum-scale=0.45, user-scalable=no" />');
} else if (strUA.indexOf("ipad") >= 0) {
  document.write('<meta name="viewport" content="width=1024px, minimum-scale=0.9, maximum-scale=0.9, user-scalable=no" />');
} else if (strUA.indexOf("android 2.3") >= 0) {
  document.write('<meta name="viewport" content="width=960px, minimum-scale=0.45, maximum-scale=0.45, initial-scale=0.45, user-scalable=yes" />');
} else {
  document.write('<meta name="viewport" content="width=960px, minimum-scale=0.38, maximum-scale=0.38, user-scalable=no" />');
}
</script>

<?php

if(isset($_GET["id"])) {
  $mysql->exec('INSERT INTO `extend_download_queue` (`ID`, `user_id`, `download_id`, `downloaded_version`) VALUES (NULL, "'.$_SESSION['server']["HTTP_USER_ID"].'", "'.$_GET["id"].'", "0"); ');
  echo '<h3>关闭本页后会提示您进行下载。 <a href="javascript:history.go(-1);">返回</a></h3>';
}