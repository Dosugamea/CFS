<meta charset='utf-8' />
<style>body{font-size:2em;}table{font-size:1em;}</style>
<title>Custom Festival Server</title>


<?php
require '../../../config/reg.php';

$token = isset($_GET['token']) ? $_GET['token'] : '';
$username['username'] = isset($_GET['username']) ? $_GET['username'] : '';

function genpassv2($_pass, $id) {
  $_pass .= $id;
  $pass = hash('sha512', $_pass);
  $pass .= hash('sha512', str_replace($_pass[0], 'RubyRubyRu', $_pass));
  $pass .= $pass;
  return substr($pass, hexdec(substr(md5($_pass), ord($_pass[0]) % 30, 2)), 32);
}

if(isset($_POST['submit'])) {
  require '../../../includes/db.php';
  $token = isset($_POST['token']) ? $_POST['token'] : '';
  $username = $mysql->query('select username, password from tmp_authorize where token=?', [$token])->fetch();
  if (!$username || $username['username'] != $_POST['username'] || !is_numeric($_POST['site'])) {
    echo '<h1>非法登录请求。请重新进入客户端内登录页，然后重新跳转到本页。</h1>';
    die();
  }
  $pass_v2 = genpassv2($_POST['password'], $_POST['id']);
  $success = $mysql->query('SELECT user_id FROM users WHERE login_password=? AND user_id=?', [$pass_v2, $_POST['id']])->fetch();
  if($success === false) {
    $pass_v1 = sha1($_POST['password']);
    $success = $mysql->query('SELECT user_id FROM users WHERE login_password=? AND user_id=?', [$pass_v1, $_POST['id']])->fetch();
    if($success === false) {
      echo '<h3><font color="red">错误：您输入的ID或密码有误</font></h3>';
    } else {
      $mysql->query('update users set login_password=? where login_password=? AND user_id=?', [$pass_v2, $pass_v1, $_POST['id']]);
    }
  }
  if ($success !== false) {
    $result = $mysql->prepare('
      UPDATE users SET username = ?, password = ?, download_site = ?
      WHERE login_password=? AND user_id=?'
    )->execute([$username['username'], $username['password'], $_POST['site'], $pass_v2, $_POST['id']]);
    if ($result) {
      $mysql->query('delete from tmp_authorize where token=?', [$token]);
      echo '<h3>登录成功！请重启游戏。</h3>';
      die();
    } else {
      echo '<h3><font color="red">出现未知错误，请通知开发者！</font></h3>';
    }
  }
}
?>
<style type="text/css">
	.protect{z-index: 555;width: 100%;height: 130px;background-color:transparent;position: absolute;left: 0px;}
	.table-input{z-index: 1;}
</style>
<script type="text/javascript">
		console.log('%c 为了您和您的账户安全','background-image:-webkit-gradient( linear, left top, right top, color-stop(0, #f22), color-stop(0.15, #f2f), color-stop(0.3, #22f), color-stop(0.45, #2ff), color-stop(0.6, #2f2),color-stop(0.75, #2f2), color-stop(0.9, #ff2), color-stop(1, #f22) );color:transparent;-webkit-background-clip: text;font-size:3em;');
		console.log('%c 请不要在这里执行任何命令','background-image:-webkit-gradient( linear, left top, right top, color-stop(0, #f22), color-stop(0.15, #f2f), color-stop(0.3, #22f), color-stop(0.45, #2ff), color-stop(0.6, #2f2),color-stop(0.75, #2f2), color-stop(0.9, #ff2), color-stop(1, #f22) );color:transparent;-webkit-background-clip: text;font-size:3em;');
		console.log('%c 否则你的JJ可能不保','background-image:-webkit-gradient( linear, left top, right top, color-stop(0, #f22), color-stop(0.15, #f2f), color-stop(0.3, #22f), color-stop(0.45, #2ff), color-stop(0.6, #2f2),color-stop(0.75, #2f2), color-stop(0.9, #ff2), color-stop(1, #f22) );color:transparent;-webkit-background-clip: text;font-size:3em;');
	</script>
<link href="/resources/css/web.css" rel="stylesheet">
<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<body class="body">
<div class="header">
    <a class="header-text">登入（login）</a>
</div>

<div class="table">
  
    <form method="post" action="/webview/login/login_ios.php" autocomplete="off">
    <div class="protect"></div>
    <div class="table-input">
      <input type="text" name="token" value="<?=$token?>"/>
    </div>
    <div class="table-input">
     <input type="text" name="username"  value="<?=$username['username']?>"/>
    </div>
    <span class="tittle">用户ID：</span>
    <div class="table-input">
      <input type="text" name="id" id="id" />
    </div>
    <span class="tittle">输入密码：</span>
    <div class="table-input">
      <input type="password" id="pass1" name="password" />
    </div>
                  <div class="first-kawai">
                  <div class="first-kawai-h">数据包下载节点选择</div>
                  <div class="first-kawai-t">
                  <input type="radio" name="site" value="1" checked>中国大陆地区<br>
                  <span style="color: #ff699c;">注:在中国大陆地区下载会加速,中国大陆以外地区下载可能会减速</span><br>
                  <input type="radio" name="site" value="2" >海外地区<br>
                  <span style="color: #ff699c;">注:适用于国际地区,中国大陆地区下载可能会失败</span><br>
                  </div>
                  </div><br>



    <div class="confirm">
      <input type="submit" name="submit" id="submit" value="登录" />
    </div>
     
    </form>
    <br><br>
    <a href="http%3A%2F%2F<?=$_SERVER['SERVER_NAME']?>%2Fwebview%2Fmails%2FforgetPass.php">忘记密码</a>

</div>



</body>