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
$authorize=substr($_SESSION['server']['HTTP_AUTHORIZE'], strpos($_SESSION['server']['HTTP_AUTHORIZE'], 'token=') + 6);
$token=substr($authorize, 0, strpos($authorize, '&'));

if(!isset($_GET['confirm'])) {
  echo "<h3>确定要退出登录吗？</h3>";
  echo '<a href="/webview.php/login/logout?confirm=1">是</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/webview.php/settings/index">否</a>';
  die();
} else {
  if (!isset($_SESSION['server']['HTTP_USER_ID'])) {
    echo "<p>出现了一点问题，请关闭页面并重新打开，然后重试</p>";
    die();
  }
  $mysql->exec('update users set username="",password="" where authorize_token="'.$token.'" and user_id='.$_SESSION['server']['HTTP_USER_ID']);
  echo "<h3>退出成功</h3>";
  die();
}