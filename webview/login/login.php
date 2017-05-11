<meta charset='utf-8' />


<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="width=880, target-densitydpi=device-dpi, user-scalable=no">

<link rel="stylesheet" href="/resources/things/detail.css?">
<link rel="stylesheet" href="/resources/things/perfect-scrollbar.css">
<link rel="stylesheet" href="/resources/things/list2.css">

<script src="/resources/things/perfect-scrollbar.min.js"></script>
<script src="/resources/things/button.js"></script>

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
      UPDATE users SET username = ?, password = ?, download_site = ?
      WHERE login_password=? AND user_id=?'
    )->execute([$username['username'], $username['password'], (int)$_POST['site'], $pass_v2, $_POST['id']]);
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
<body>
<div id="outer">
  <div id="inner">
    <div id="header">
      <h2>登录</h2>
      <div id="back"></div>
    </div>

<div id="body">
<div id="container">
<ul id="list">
      <li class="entry"">
        <div class="entry-container">
          <h2 class="text">如果您的设备是 iOS</h2>
           <a href="native://browser?url=http%3A%2F%2F<?=$_SERVER['SERVER_NAME']?>%2Fwebview%2Flogin%2Flogin_ios.php%3Ftoken%3D<?=$token?>%26username%3D<?=$username['username']?>">
           <div class="summary" style="color: #000000 !important;">
           iOS用户专用登录链接。若您点击下面的文本框后客户端崩溃，请点此进行登录！<br>
           iOS use this link to Login,If you client crash when click the text eara under the this box
          </div></a>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry"">
        <div class="entry-container">
          <h2 class="text">用户密码登录</h2>
          <div class="summary" >
          <br>
            <form method="post" action="/webview.php/login/login" autocomplete="off">
              用户ID/UserID：<input type="text" name="id" id="id" style="height:27px" /><br />
              密码/Password：<input type="password" id="pass1" name="password" style="height:27px" /><br />
                <br>
                <div class="first-kawai">
                	<div class="first-kawai-h">数据包下载节点选择</div>
                	<div class="first-kawai-t">
                  <input type="radio" name="site" value="1" checked>中国大陆地区<br>
                  <span style="color: #ff699c;">注:在中国大陆地区下载会加速,中国大陆以外地区下载可能会减速</span><br>
                  <input type="radio" name="site" value="2" >海外地区<br>
                  <span style="color: #ff699c;">注:适用于国际地区,中国大陆地区下载可能会失败</span><br>
                  </div>
                </div><br>
              <input type="submit" name="submit" id="submit" style="height:30px;width:120px" value="确认/Confirm" /><br>
            </form>
            <br>
          </div>
          <div class="clearfix"></div>
        </div>
      </li>
  </ul>
  </div>
</div>
</div>
</div>
<script>
  Button.initialize(document.getElementById('back'), function() {
    window.location.href='/webview.php/login/welcome';
  });
  Ps.initialize(document.getElementById('body'), {suppressScrollX: true});
</script>
</body>
