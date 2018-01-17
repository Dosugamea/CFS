<meta charset='utf-8' />
<style>body{font-size:2em;}table{font-size:1em;}</style>
<script src="/resources/js/reg.js"></script>

<?php
@$token = isset($_GET['token']) ? $_GET['token'] : $_POST['token'];
@$username = isset($_GET['username']) ? $_GET['username'] : $_POST['username'];
require_once dirname(__FILE__).'/../../../includes/db.php';
$tmp_authorize = $mysql->query('select username, password from tmp_authorize where token=? and username=?', [$token, $username])->fetch();
require(dirname(__FILE__).'/../../../webview/login/reg_common.php');
?>
<script type="text/javascript">
    console.log('%c 为了您和您的账户安全','background-image:-webkit-gradient( linear, left top, right top, color-stop(0, #f22), color-stop(0.15, #f2f), color-stop(0.3, #22f), color-stop(0.45, #2ff), color-stop(0.6, #2f2),color-stop(0.75, #2f2), color-stop(0.9, #ff2), color-stop(1, #f22) );color:transparent;-webkit-background-clip: text;font-size:3em;');
    console.log('%c 请不要在这里执行任何命令','background-image:-webkit-gradient( linear, left top, right top, color-stop(0, #f22), color-stop(0.15, #f2f), color-stop(0.3, #22f), color-stop(0.45, #2ff), color-stop(0.6, #2f2),color-stop(0.75, #2f2), color-stop(0.9, #ff2), color-stop(1, #f22) );color:transparent;-webkit-background-clip: text;font-size:3em;');
    console.log('%c 否则你的JJ可能不保','background-image:-webkit-gradient( linear, left top, right top, color-stop(0, #f22), color-stop(0.15, #f2f), color-stop(0.3, #22f), color-stop(0.45, #2ff), color-stop(0.6, #2f2),color-stop(0.75, #2f2), color-stop(0.9, #ff2), color-stop(1, #f22) );color:transparent;-webkit-background-clip: text;font-size:3em;');
    console.log("%c", "padding:30px 300px;line-height:120px;background:url('https://app.lovelivesupport.com/pl/jitui.gif');");
</script>
<style type="text/css">
  .protect{z-index: 555;width: 100%;height: 130px;background-color:transparent;position: absolute;left: 0px;}
  .table-input{z-index: 1;}
</style>
<link href="/resources/css/web.css" rel="stylesheet">


<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<body class="body">
<div class="header">
    <a class="header-text">注册（Signup）</a>
</div>

<div class="table">
  
    <form method="post" action="/webview/login/reg_ios.php" autocomplete="off">
    <div class="protect"></div>
    <div class="table-input">
      <input type="text" name="token" value="<?=$token?>"/>
    </div>
    <div class="table-input">
     <input type="text" name="username" value="<?=$tmp_authorize['username']?>" />
    </div>
    <span class="tittle">用户ID：</span>
    <div class="table-input">
     <input type="text" name="id" id="id"  onkeyup="verify()" onchange="verify()"/><span id="info" style="color:red"></span>
    </div>
    <span class="tittle">昵称：</span>
    <div class="table-input">
      <input type="text" name="name" id="name"  onkeyup="verify()" onchange="verify()"/><br />
    </div>
    <span class="tittle">输入密码：</span>
    <div class="table-input">
      <input type="password" id="pass1" name="password"  onKeyUp="verify2();" onchange="verify2();" /><span id="info2" style="color:red"></span><br />
    </div>
    <span class="tittle">再次输入密码：</span>
    <div class="table-input">
      <input type="password" id="pass2"  onKeyUp="verify2();" onchange="verify2();" />
    </div>
	<span class="tittle">邀请人（如没有请留空）：</span>
    <div class="table-input">
      <input type="text" name="invite"/>
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
      <input type="submit" name="submit" id="submit" value="注册" />
    </div>
     
    </form>
</div>
