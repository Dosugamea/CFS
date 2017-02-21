<meta charset='utf-8' />

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
<link href="/resources/bstyle.css" rel="stylesheet">
<link href="/resources/news.css" rel="stylesheet">
<link href="/resources/css/style.css" rel="stylesheet">
<?php
require 'config/reg.php';

if($enable_ssl && $_SERVER['HTTPS']!='on') {
  header('Location: https://'.$ssl_domain.$_SERVER['REQUEST_URI']);
  die();
}

$authorize = substr($_SESSION['server']['HTTP_AUTHORIZE'], strpos($_SESSION['server']['HTTP_AUTHORIZE'], 'token=') + 6);
$token = substr($authorize, 0, strpos($authorize, '&'));
$username = $mysql->query('select username, password from tmp_authorize where token=?', [$token])->fetch();
if (!$username) {
  echo '<h1>出现了错误，请关闭此页面重新进入</h1>';
  die();
}

function genpassv2($_pass, $id) {
  $_pass .= $id;
  $pass = hash('sha512', $_pass);
  $pass .= hash('sha512', str_replace($_pass[0], 'RubyRubyRu', $_pass));
  $pass .= $pass;
  return substr($pass, hexdec(substr(md5($_pass), ord($_pass[0]) % 30, 2)), 32);
}

if(isset($_POST['submit'])) {
  $pass_v2 = genpassv2($_POST['password'], $_POST['id']);
  $success = $mysql->query('SELECT user_id FROM users WHERE login_password=? AND user_id=?', [$pass_v2, $_POST['id']])->fetch();
  if($success === false) {
    $pass_v1 = sha1($_POST['password']);
    $success = $mysql->query('SELECT user_id FROM users WHERE login_password=? AND user_id=?', [$pass_v1, $_POST['id']])->fetch();
    if($success === false) {
      echo '<h3><font color="red">错误：您输入的ID或密码有误 <br> Error: You Input The Wrong UserID or Password</font></h3>';
    } else {
      $mysql->query('update users set login_password=? where login_password=? AND user_id=?', [$pass_v2, $pass_v1, $_POST['id']]);
    }
  }
  if ($success !== false) {
    $result = $mysql->prepare('
      UPDATE users SET username = ?, password = ?
      WHERE login_password=? AND user_id=?'
    )->execute([$username['username'], $username['password'], $pass_v2, $_POST['id']]);
    if ($result) {
      $mysql->query('delete from tmp_authorize where token=?', [$token]);
      echo '<h3>登录成功！关闭本窗口即可进入游戏。<br> Login Success! Plz Close This Window</h3>';
      die();
    } else {
      echo '<h3><font color="red">出现未知错误，请通知开发者！<br> Unknow Error! Plz mine the Admin</font></h3>';
    }
  }
}
?>

<SCRIPT type="text/javascript">
var strUA = "";
strUA = navigator.userAgent.toLowerCase();

if(strUA.indexOf("iphone") >= 0) {
  document.write('<h3>我们检测到您的设备是iOS设备 暂不支持iOS设备游戏内登陆 将在<span id="num"></span>秒后自动调用外部浏览器进行登陆</h3><br><a href="/webview.php/login/welcome">返回欢迎页</a>');
} else if (strUA.indexOf("ipad") >= 0) {
  document.write('<h3>我们检测到您的设备是iOS设备 暂不支持iOS设备游戏内登陆 将在<span id="num"></span>秒后自动调用外部浏览器进行登陆</h3><br><a href="/webview.php/login/welcome">返回欢迎页</a>');
} else {
  document.write('<DIV id="wrapper_news" style="width: 100% !important"><div class="title_news fs34" style="width:100%"><span class="ml30">登录 Login</span><a id="back" href="/webview.php/login/welcome"><div class="topback"><img src="/resources/com_button_01.png" data-on="/resources/com_button_02se.png"></div></a></div><div class="content_news_all" style="margin-top:0"><div id="box1"><div class="title_news_all fs30"><span class="ml40">iOS登陆专用链接(iOS special login link)</span></div><div class="content_all"><div class="note"><a href="native://browser?url=http%3A%2F%2F<?=$_SERVER['SERVER_NAME']?>%2Fwebview%2Flogin%2Flogin_ios.php%3Ftoken%3D<?=$token?>%26username%3D<?=$username['username']?>">iOS用户专用登录链接。若您点击下面的文本框后客户端崩溃，请点此进行登录！<br>iOS plz use this to Login,If you click the text eara under the this box</a></div></div><div class="title_news_all fs30"><span class="ml40">普通登陆(Normal Login)</span></div><div class="content_all"><div class="note"><form method="post" action="/webview.php/login/login" autocomplete="off">          用户ID UserID：<input type="text" name="id" id="id" style="height:27px" /><br />          密码 Password：<input type="password" id="pass1" name="password" style="height:27px" /><br /><input type="submit" name="submit" id="submit" style="height:30px;width:120px" value="确认/Confirm" /><br></form><?php if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS']!='on') echo '<span style="color:red;font-size:2vw;">*警告：将通过不安全的连接发送您的密码*</span>'; ?></div></div><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></div></div></DIV><DIV class="footer_news_all"><IMG width="100%" src="/resources/bg03.png"></DIV>');
}

var num=3;
	function redirect(){
		num--;
		document.getElementById("num").innerHTML=num;
		if(num<0){
			document.getElementById("num").innerHTML=0;
			location.href="native://browser?url=https%3A%2F%2F<?=$_SERVER['SERVER_NAME']?>%2Fwebview%2Flogin%2Flogin_ios.php%3Ftoken%3D<?=$token?>%26username%3D<?=$username['username']?>";
			}
		}
	setInterval("redirect()", 1000);
</script>







<!--<DIV id="wrapper_news" style="width: 100% !important">
<div class="title_news fs34" style="width:100%">
  <span class="ml30">登录 Login
  </span><a id="back" href="/webview.php/login/welcome">
  <div class="topback">
    <img src="/resources/com_button_01.png" data-on="/resources/com_button_02se.png">
  </div>
  </a>
</div>
<div class="content_news_all" style="margin-top:0">
  <div id="box1">
    <div class="title_news_all fs30">
      <span class="ml40">iOS登陆专用链接(iOS special login link)</span>
    </div>
    <div class="content_all">
      <div class="note">
        <a href="native://browser?url=http%3A%2F%2F<?=$_SERVER['SERVER_NAME']?>%2Fwebview%2Flogin%2Flogin_ios.php%3Ftoken%3D<?=$token?>%26username%3D<?=$username['username']?>">iOS用户专用登录链接。若您点击下面的文本框后客户端崩溃，请点此进行登录！<br>iOS plz use this to Login,If you click the text eara under the this box</a>
      </div>
    </div>
    <div class="title_news_all fs30">
      <span class="ml40">普通登陆(Normal Login)</span>
    </div>
    <div class="content_all">
      <div class="note">
       <form method="post" action="/webview.php/login/login" autocomplete="off">
          用户ID UserID：<input type="text" name="id" id="id" style="height:27px" /><br />
          密码 Password：<input type="password" id="pass1" name="password" style="height:27px" /><br />
        
        <input type="submit" name="submit" id="submit" style="height:30px;width:120px" value="确认/Confirm" /><br></form>
        <?php if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS']!='on') echo '<span style="color:red;font-size:2vw;">*警告：将通过不安全的连接发送您的密码*</span>'; ?>
      </div>
    </div>
  </div>
</div>
</DIV>
<DIV class="footer_news_all"><IMG width="100%" src="/resources/bg03.png"> 
</DIV>-->