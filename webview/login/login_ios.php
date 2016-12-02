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
require '../../config/reg.php';

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
  require '../../includes/db.php';
  $token = isset($_POST['token']) ? $_POST['token'] : '';
  $username = $mysql->query('select username, password from tmp_authorize where token=?', [$token])->fetch();
  if (!$username || $username['username'] != $_POST['username']) {
    echo '<h1>非法登录请求（authkey&username验证失败）。请重新进入客户端内登录页，然后重新跳转到本页。</h1>';
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
      UPDATE users SET username = ?, password = ?
      WHERE login_password=? AND user_id=?'
    )->execute([$username['username'], $username['password'], $pass_v2, $_POST['id']]);
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

<h3>PLS用户登录</h3>
<form method="post" action="/webview/login/login_ios.php" autocomplete="off">
authkey：<input type="text" name="token" style="height:27px" value="<?=$token?>" /><br />
username：<input type="text" name="username" style="height:27px" value="<?=$username['username']?>" /><br />
用户ID：<input type="text" name="id" id="id" style="height:27px" /><br />
密码：<input type="password" id="pass1" name="password" style="height:27px" /><br />
<?php if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS']!='on') echo '<h3><span style="color:red">警告：将通过不安全的连接发送您的密码。</span></h3>'; ?>
<input type="submit" name="submit" id="submit" style="height:30px;width:50px" value="登录" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</form>