<?php require('config/reg.php'); ?>
<!doctype html>
<html>
<head>
<meta charset='utf-8' />
<!--<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />-->
<SCRIPT type="text/javascript">
var strUA = "";
strUA = navigator.userAgent.toLowerCase();

if(strUA.indexOf("iphone") >= 0) {
  document.write('<meta name="viewport" content="width=100%, minimum-scale=0.45, maximum-scale=0.45, user-scalable=no" />');
} else if (strUA.indexOf("ipad") >= 0) {
  document.write('<meta name="viewport" content="width=100%, minimum-scale=0.9, maximum-scale=0.9, user-scalable=no" />');
} else if (strUA.indexOf("android 2.3") >= 0) {
  document.write('<meta name="viewport" content="width=100%, minimum-scale=0.45, maximum-scale=0.45, initial-scale=0.45, user-scalable=yes" />');
} else {
  document.write('<meta name="viewport" content="width=100%, minimum-scale=0.38, maximum-scale=0.38, user-scalable=no" />');
}
</script><!-- Fix WebView Bug-->
<link href="/resources/css/style.css" rel="stylesheet">
<style type="text/css">
.Welcome-BG{
	background-image: url(/resources/top.jpg);
	background-size: cover;
	background-repeat: no-repeat; 
	background-color: transparent;
	width: 100%;
	height: 100%;
	z-index: -2;
	left: 0px;
	top: 0px;
	position: fixed;
	border-radius: 60px;
}
	a:link,a:visited{text-decoration:none;color: #ffffff;}
</style>

</head>

<body style="width: 98% !important;">
<div class="Welcome-BG"></div>
<div class="Welcome-N1"></div>
<div class="Welcome-N2">
<h1 class="Welcome-Header shadow">Welcome to Programmed Live!</h1>
<p>使用协议：<br />
1、严禁在公开场合（贴吧、微博等）发布与PL有关的任何消息、截图、视频等
（特别的，在公开场合公开了群号的QQ群属于公开场合）<br />
2、若私下传播，则必须确保【所有看到消息的人也遵守前面一条】</p>
<p>Usage agreement:<br>
1, is strictly prohibited in public (like Twitter) publishing any messages associated with the PL, screenshots and videos<br>
2,Private communications, you must make sure that "all who saw the message also to comply with the previous"</p>
<table class="Welcome-Icon" cellspacing="20">
	<tr>
		<td><a href="/webview.php/login/login">登录 (Login)</a></td>
		<td><?=($allow_reg?'<a href="/webview.php/login/reg">注册 (Sign up)</a>':'已关闭注册/Stop Sign up')?></td>
	</tr>
</table>
</div>
</body>
</html>