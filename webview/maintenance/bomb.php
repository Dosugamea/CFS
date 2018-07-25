<?php
global $mysql;
if(!isset($_SESSION['server']['HTTP_USER_ID'])) {
  $_SESSION['server']['HTTP_USER_ID']=0;
}

if (isset($_SESSION['server']['HTTP_AUTHORIZE'])) {
  $authorize = substr($_SESSION['server']['HTTP_AUTHORIZE'], strpos($_SESSION['server']['HTTP_AUTHORIZE'], 'token=') + 6);
  $token = substr($authorize, 0, strpos($authorize, '&'));
} else {
  $token=0;
}

$error = $mysql->query('SELECT text,dele,ID FROM error_report WHERE user_id = ? order by ID desc limit 1', [$_SESSION['server']['HTTP_USER_ID']])->fetch();
if ($error) {
  if($error['dele']==1) {
    $mysql->exec("DELETE FROM error_report WHERE ID={$error['ID']}");
  }
  $error = $error['text'];
} else {
  $error = '未知的错误（我们已经记录）';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />
<meta name="viewport" content="width=device-width,user-scalable=no" />
<title>服务器爆炸了！</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
#left {
    position: absolute;
    left: 0;
    top: 0;
    z-index: 1;
    width: 50%;
    height: 100%;
}
#bomb-img {
    width: 100%;
    height: 100%;
    background: #fff url("/resources/error.png") no-repeat center center;
    background-size: contain;
}
#right {
    position: absolute;
    right: 0;
    top: 0;
    z-index: 2;
    width: 50%;
    height: 100%;
}
#right p {
    width: 100%;
    height: 5%;
}
#right textarea {
    display: block;
    width: 100%;
    height: 95%;
    resize: none;
}
</style>
</head>

<body>
<h1>服务器爆炸了！</h1>
<div>
    <div id="left">
        <div id="bomb-img"></div>
    </div>
    <div id="right">
        <p>错误信息已被记录，我们会尽早修复。</p>
        <textarea><?=$error?></textarea>
    </div>
</div>
</body>
</html>