<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,user-scalable=no" />
  <title>お知らせ一覧</title>
  <link href="/resources/css/jquery-ui.min.css" type="text/css" rel="stylesheet" />
  <link href="/resources/css/public.css" type="text/css" rel="stylesheet" />
  <script type="text/javascript" src="/resources/js/jquery.min.js"></script>
  <script type="text/javascript" src="/resources/js/jquery-ui.min.js"></script>
</head>
<body>
<?php
require "config/database.php";
require "config/maintenance.php";
require "version.php";
?>
<div class="alert alert-info" role="alert">Programmed Live! Server <strong><?=$pls_version ?></strong></div>
<div class="alert alert-info" role="alert">客户端版本：<strong><?=(isset($_SESSION['server']["HTTP_BUNDLE_VERSION"]) ? $_SESSION['server']["HTTP_BUNDLE_VERSION"] : '客户端未提交')."(".$_SESSION['server']["HTTP_CLIENT_VERSION"].")</strong> 服务器版本：".'<strong>'.$bundle_ver."(".$server_ver.")".'</strong>'; ?></div>
<?php
if ($mysql->query('SELECT length(`login_password`) FROM `users` WHERE `user_id`='.$_SESSION['server']['HTTP_USER_ID'])->fetchColumn() != 32) {
  ?>
  <div class="alert alert-danger" role="alert">我们升级了服务器的密码存储机制，建议您前往“游戏设置”退出重新登录或修改密码，这将大幅降低服务器被攻击导致密码泄露的风险。</div>
<?php } ?>
<div class="row">
  <div class="fl btngroup">
    <a class="btn btn-primary btn-block" href="/webview.php/announce/announce"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;查看公告</a>
  </div>
  <div class="fl btngroup">
    <a class="btn btn-primary btn-block" href="/webview.php/settings/index"><span class="glyphicon glyphicon-cog"></span>&nbsp;游戏设置</a>
  </div>
  <div class="fl btngroup" style="margin-right:0">
    <a class="btn btn-primary btn-block" href="/webview.php/mods/index"><span class="glyphicon glyphicon-wrench"></span>&nbsp;Mods</a>
  </div>
  <div class="clearfix"></div>
</div>
<div class="contentbox">
  <ul class="lltab">
    <li class="on">
      <div class="bg1"></div>
      <div class="bg2">最新公告</div>
    </li>
    <li>
      <div class="bg1"></div>
      <div class="bg2"><a href="/webview.php/announce/announce">全部公告</a></div>
    </li>
  </ul>
  <?php
  if(!isset($_GET['disp_faulty']) || !is_numeric($_GET['disp_faulty'])) {
    $_GET['disp_faulty']=0;
  }
  $announcement=$mysql->query('select * from webview where tab!=0 order by time desc limit 3')->fetchAll();
  ?>
  <div class="news-list-bg">
    <ul class="news-list-ul">
      <?php foreach($announcement as $v) { $time=explode(' ', $v['time'])[0]; ?>
        <li>
          <a class="news-list-href" <?=($v['detail_id']?' href="/webview.php/announce/detail?0=&announce_id='.$v['detail_id'].'&disp_faulty='.$_GET['disp_faulty'].'"':'')?>>
            <div class="news-list-title">
              <div class="bg1"></div>
              <div class="bg2"><?=$v['title']?></div>
            </div>
            <div class="news-list-content">
              <p><?=$v['content']?></p>
              <p><?=$time?></p>
              <p class="detail"><?=($v['detail_id']?'※点击查看详情':'')?></p>
            </div>
          </a>
        </li>
      <?php } ?>
    </ul>
  </div>
</div>
</body>
</html>