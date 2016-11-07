<?php require('config/reg.php'); ?>
<!doctype html>
<html>
<head>
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
</head>

<body>
<h1>Welcome to Programmed Live!</h1>
<h2>使用协议：<br />
1、严禁在公开场合（贴吧、微博等）发布与PL有关的任何消息、截图、视频等
（特别的，在公开场合公开了群号的QQ群属于公开场合。）<br />
2、若私下传播，则必须确保【所有看到消息的人也遵守前面一条】。</h2>
<h1><a href="/webview.php/login/login">登录（继承数据）</a>&nbsp;&nbsp;&nbsp;&nbsp;<?=($allow_reg?'<a href="/webview.php/login/reg">注册新用户</a>':'已关闭注册')?></h1>
</body>
</html>