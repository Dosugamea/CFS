<meta charset='utf-8' />
<link href="/resources/bstyle.css" rel="stylesheet">
<link href="/resources/news.css" rel="stylesheet">
<link href="/resources/css/style.css" rel="stylesheet">
<!--<style>body{font-size:2em;}table{font-size:1em;}</style>-->
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
</script>
<!--<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />-->
<?php
require 'config/reg.php';

if($enable_ssl && $_SERVER['HTTPS']!='on') {
  header('Location: https://'.$ssl_domain.$_SERVER['REQUEST_URI']);
  die();
}

$authorize=substr($_SESSION['server']['HTTP_AUTHORIZE'], strpos($_SESSION['server']['HTTP_AUTHORIZE'], 'token=') + 6);
$token=substr($authorize, 0, strpos($authorize, '&'));

function genpassv2($_pass, $id) {
  $_pass .= $id;
  $pass = hash('sha512', $_pass);
  $pass .= hash('sha512', str_replace($_pass[0], 'RubyRubyRu', $_pass));
  $pass .= $pass;
  return substr($pass, hexdec(substr(md5($_pass), ord($_pass[0]) % 30, 2)), 32);
}

if(isset($_POST['submit'])) {
  if ($mysql->prepare('UPDATE users SET login_password=? WHERE authorize_token=? AND user_id=?')->execute([genpassv2($_POST['password'], $_SESSION['server']['HTTP_USER_ID']), $token, $_SESSION['server']['HTTP_USER_ID']])) {
    echo '<h3>密码设置成功！</h3>';
  } else {
    echo '<h3><font color="red">密码设置失败，请通知开发者！</font></h3>';
  }
}

?>
<script>
function verify2() {
  valid2=true;
  var info='';
  var t1=document.getElementById('pass1');
  var t2=document.getElementById('pass2');
  if(t1.value=='') {
    valid2=false;
    t1.style.backgroundColor='#FF0000';
    info='请输入密码'
  } else {
    t1.style.backgroundColor='#00FF00';
  }
  if(t1.value!=t2.value && t1.value!='') {
    valid2=false;
    t2.style.backgroundColor='#FF0000';
    info='两次输入的密码不一致'
  } else if(t1.value=='') {
    t2.style.backgroundColor='#FF0000';
  } else {
    t2.style.backgroundColor='#00FF00';
  }
  document.getElementById('info2').innerText=info;
  if(valid2) {
    document.getElementById('submit').disabled=false;
  } else {
    document.getElementById('submit').disabled=true;
  }
}
</script>

<DIV id="wrapper_news" style="width: 100% !important">
<div class="title_news fs34" style="width:100%">
  <span class="ml30">修改密码 Change Password
  </span><a id="back" href="/webview.php/settings/index">
  <div class="topback">
    <img src="/resources/com_button_01.png" data-on="/resources/com_button_02se.png">
  </div>
  </a>
</div>
<div class="content_news_all" style="margin-top:0">
  <div id="box1">
    <div class="title_news_all fs30">
      <span class="ml40"></span>
    </div>
    <div class="content_all">
      <div class="note">
        <form method="post" action="changePassword" autocomplete="off">
        <p>
          新密码 NewPassword：<input type="password" id="pass1" name="password" style="height:27px" onKeyUp="verify2();" onchange="verify2();" /><span id="info2" style="color:red"></span><br />
          再次输入密码 Confirm：<input type="password" id="pass2" style="height:27px" onKeyUp="verify2();" onchange="verify2();" /><br /><br /></p>
          <input type="submit" name="submit" id="submit" style="height:30px;width:120px" value="确认/Confirm" disabled="disabled" />
          <?php if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS']!='on') echo '<span style="color:red;font-size:2vw;">*警告：将通过不安全的连接发送您的密码*</span>'; ?>
      </div>
    </div>
  </div>
</div>








<!--<p><a href="/webview.php/settings/index">返回</a></p>
<h3>修改密码</h3>

<form method="post" action="changePassword" autocomplete="off">
<p>新密码：<input type="password" id="pass1" name="password" style="height:27px" onKeyUp="verify2();" onchange="verify2();" /><span id="info2" style="color:red"></span><br />
再次输入密码：<input type="password" id="pass2" style="height:27px" onKeyUp="verify2();" onchange="verify2();" /><br /><br /></p>
<?php if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS']!='on') echo '<h3><span style="color:red">警告：将通过不安全的连接发送您的密码。</span></h3>' ?>
<input type="submit" name="submit" id="submit" style="height:30px;width:50px" value="修改" disabled="disabled" />
<table><tr><td height="200px"></td></tr></table>-->

<DIV class="footer_news_all"><IMG width="100%" src="/resources/bg03.png"> 
</DIV>