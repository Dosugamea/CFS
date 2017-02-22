  <meta charset="utf-8">
  <meta name="GENERATOR" content="MSHTML 11.00.10011.0">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="viewport" content="width=880, target-densitydpi=device-dpi, user-scalable=no">

  <link rel="stylesheet" href="/resources/things/detail.css">
  <link rel="stylesheet" href="/resources/things/perfect-scrollbar.css">
  <link rel="stylesheet" href="/resources/things/list2.css">

  <script src="/resources/things/perfect-scrollbar.min.js"></script>
  <script src="/resources/things/button.js"></script>
<?php 
require "info.php"
?>
<?php 
if($_GET['id']==11||$_GET['id']==12) {
  header('Location: /webview.php/maintenance/bundleUpdate');
}
//此用户已被封禁
elseif($_GET['id']==13) {
  echo "<div id='outer'><div id='inner'><div id='header'><h2>您的账号遇到一些问题</h2>  </div><div id='body'><div id='container'><ul id='list'><li class='entry'><div class='entry-container'><h2 class='text'>详细信息</h2><div class='summary'>";
  echo '<h3>';
  try{
    $stmt=$mysql->prepare('SELECT username FROM authorize WHERE user_id=?');
    $stmt->execute([$_SESSION['server']['HTTP_USER_ID']]);
    $username=$stmt->fetchColumn();
    $banned=$mysql->query("select msg from banned_user where user='{$_SESSION['server']['HTTP_USER_ID']}' or user='{}'")->fetchColumn();
    if($banned) {
      echo $banned;
    }
  } catch(PDOException $e) {}
  echo '</h3>';
  echo "</div><div class='clearfix'></div></div></li><!--<li class='entry'><div class='entry-container'><h2 class='text'>反馈</h2><div class='summary' id='mail'><p>点击此处向使用邮件向我们进行反馈 我们会第一时间为您处理</p></div><div class='clearfix'></div></div></li>--></ul></div></div></div></div><script>  Ps.initialize(document.getElementById('body'), {suppressScrollX: true});   Button.initialize(document.getElementById('mail'), function() {    window.location.href='native://browser?url=mailto:<?=$pls_support_mail?>';  });</script>";
} 
//サポート
elseif($_GET['id']==5) {
  header('Location: /webview.php/announce/info');
}
//利用规约
elseif($_GET['id']==1) {
  header('Location: /webview.php/tos/read');
}
else{
  header('Location: /webview.php');
}