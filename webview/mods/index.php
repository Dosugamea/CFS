<?php
$uid = $_SESSION['server']['HTTP_USER_ID'];

$params = [];
foreach ($mysql->query('SELECT * FROM user_params WHERE user_id='.$uid)->fetchAll() as $v) {
  $params[$v['param']] = (int)$v['value'];
}

$allowed_params = ['extend_mods_vanish', 'extend_mods_mirror', 'extend_mods_life', 'extend_mods_hantei_count'];

foreach($allowed_params as $v) {
  if (!isset($params[$v])) {
    $params[$v] = 0;
  }
}

if(isset($_GET['switch_random'])) {
  $params['random_switch'] = $_GET['switch_random'];
  $mysql->prepare('REPLACE INTO user_params values (?, ?, ?)')->execute([$uid, 'random_switch', $_GET['switch_random']]);
}

if(isset($_GET['switch_param']) && isset($_GET['param']) && array_search($_GET['switch_param'], $allowed_params) !== false) {
  $_GET['param'] = (int)$_GET['param'];
  $params[$_GET['switch_param']] = $_GET['param'];
  if ($_GET['param'] != 0) {
    $mysql->prepare('REPLACE INTO user_params VALUES (?, ?, ?)')->execute([$uid, $_GET['switch_param'], $_GET['param']]);
  } else {
    $mysql->prepare('DELETE FROM user_params WHERE user_id=? and param=?')->execute([$uid, $_GET['switch_param']]);
  }
}
?><!DOCTYPE html PUBLIC "" "">
<html>
<head>
<meta content="IE=11.0000" http-equiv="X-UA-Compatible">
<meta charset="utf-8">
<title>お知らせ一覧</title>
<link href="/resources/bstyle.css" rel="stylesheet">
<link href="/resources/news.css" rel="stylesheet">
<link href="/resources/css/style.css" rel="stylesheet">
<script type="text/javascript">
var strUA = "";
strUA = navigator.userAgent.toLowerCase();
if (strUA.indexOf("iphone") >= 0) {
    document.write('<meta name="viewport" content="width=960px, minimum-scale=0.45, maximum-scale=0.45, user-scalable=no" />');
} else if (strUA.indexOf("ipad") >= 0) {
    document.write('<meta name="viewport" content="width=1024px, minimum-scale=0.9, maximum-scale=0.9, user-scalable=no" />');
} else if (strUA.indexOf("android 2.3") >= 0) {
    document.write('<meta name="viewport" content="width=960px, minimum-scale=0.45, maximum-scale=0.45, initial-scale=0.45, user-scalable=yes" />');
} else {
    document.write('<meta name="viewport" content="width=960px, minimum-scale=0.38, maximum-scale=0.38, user-scalable=no" />');
}
</script>
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
<style type="text/css">
.note img { margin-left: -12px; }
</style>
<meta name="GENERATOR" content="MSHTML 11.00.10011.0">
</head>
<body>
<DIV id="wrapper_news">
<div class="title_news fs34" style="width:100%">
  <span class="ml30">Mods
  </span><a id="back" href="/webview.php/announce/index">
  <div class="topback">
    <img src="/resources/com_button_01.png" data-on="/resources/com_button_02se.png">
  </div>
  </a>
</div>
<div class="content_news_all" style="margin-top:0">
  <script type="text/javascript">if(strUA.indexOf("iphone") >= 0 || strUA.indexOf("ipad") >= 0) { document.write('<div class="note" style="margin-top:100px;">'); } else { document.write('<div class="note">'); }</script>
  <div id="box1">
    <p>以下的所有Mod均可以在游戏过程中随时切换。<br />使用 その他-ヘルプ 来快速到达本页面。<br /><br />
    HI-SPEED:<font color="red"><b>请升级至3.2客户端，然后在“各种设定”中设置！</b></font>
    <br />
    随机：<a href="/webview.php/mods/index?switch_random=0">关闭</a>&nbsp;&nbsp;<a href="/webview.php/mods/index?switch_random=1">新随机</a>&nbsp;&nbsp;<a href="/webview.php/mods/index?switch_random=2">旧随机</a>&nbsp;&nbsp;（当前状态：<?php if($params['random_switch']==1) echo '新随机';elseif($params['random_switch']==2) echo '旧随机'; else echo '关闭'; ?>）<br /><br />
    <?php $status = ['关闭', 'HIDDEN', 'SUDDEN'];?>
    VANISH：<?php foreach($status as $k => $v) {
      echo '<a href="index?switch_param=extend_mods_vanish&param='.$k.'">'.$v.'</a>&nbsp;&nbsp;&nbsp;&nbsp;';
    }?>（当前状态：<?=$status[$params['extend_mods_vanish']]?>）<br /><br />
    <?php $status = ['关闭', '开启'];?>
    MIRROR：<?php foreach($status as $k => $v) {
      echo '<a href="index?switch_param=extend_mods_mirror&param='.$k.'">'.$v.'</a>&nbsp;&nbsp;&nbsp;&nbsp;';
    }?>（当前状态：<?=$status[$params['extend_mods_mirror']]?>）<br /><br />
    <?php $status = ['关闭', 'NO FAIL', 'SUDDEN DEATH'];?>
    锁血：<br /><?php foreach($status as $k => $v) {
      echo '<a href="index?switch_param=extend_mods_life&param='.$k.'">'.$v.'</a>&nbsp;&nbsp;&nbsp;&nbsp;';
    }?>（当前状态：<?=$status[$params['extend_mods_life']]?>）<br /><br />
    <b>注意：以下的功能会大幅降低游戏难度，因而打开后您的成绩【不会】被记录！</b><br />
    <form method="get" action="/webview.php/mods/index" autocomplete="off">
    在游戏开始（以及组曲换曲）时获得<input type="text" value="<?=$params['extend_mods_hantei_count']?>" name="param" style="border:1px solid;height:27px;" /><input type="hidden" value="extend_mods_hantei_count" name="switch_param" />个超大判（设为0为关闭）<br />
    <input type="submit" style="border:1px solid;height:27px;width:64px;" value="提交" />
    </form>
    </p>
  </div>
</div></div>
<div class="footer_news_all">
  <img width="100%" src="/resources/bg03.png">
</div>

</body>
</html>