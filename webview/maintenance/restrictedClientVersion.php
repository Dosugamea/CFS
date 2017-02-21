<?php require('config/maintenance.php'); ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>メンテナンス</title>
<link rel="stylesheet" href="/resources/bstyle.css">
<script type="text/javascript">
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
</head>

<body>
<div id="wrapper">
<div class="title">
<img src="/resources/bg01_maint.png" width="95%">
</div>

<div class="content">
  <div class="note">
<p>
您在使用Programmed Live内部开发客户端，但您的ID未经过授权。<br />
<br />
若您确定您有权使用此版本客户端，请联系PLS开发者申请授权，并带上以下信息：<br />
您的ID：<?=$_SESSION['server']['HTTP_USER_ID'] ?><br />
<br />
若您不处于开发群内，而是通过其他人（朋友等）得到此客户端，请立刻停止使用。谢谢合作。
<br />
<br />
</p>
  </div>
</div>
<div class="footer">
<img src="/resources/bg03.png" width="95%">
</div>
</div>
</body>
</html>
