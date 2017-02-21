<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>お知らせ一覧</title>

<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="width=880, target-densitydpi=device-dpi, user-scalable=no">

<link rel="stylesheet" href="/resources/things/list.css">
<link rel="stylesheet" href="/resources/things/perfect-scrollbar.css">

<script src="/resources/things/perfect-scrollbar.min.js"></script>
<script src="/resources/things/button.js"></script>
<script src="/resources/things/list.js"></script>
</head>

<body>
<?php
if(!isset($_GET['disp_faulty']) || !is_numeric($_GET['disp_faulty'])) {
  $_GET['disp_faulty']=0;
}
$announcement=$mysql->query('select * from webview where tab='.($_GET['disp_faulty']+1).' order by `order` desc, time desc')->fetchAll();
$has_new=$mysql->query('select distinct tab from webview where to_days(time)>to_days(CURRENT_TIMESTAMP)-5')->fetchAll();
?>

  <ul id="tab">
      <li class="foo">
    <a href="/webview.php/announce/index">
      <img src="/resources/things/tab/tab1_off.png" alt="お知らせ">
    </a>
  </li>
    <li class="on">
    <a href="">
      <img src="/resources/things/tab/tab2_on.png" alt="お知らせ">
    </a>
  </li>
        <li class="off">
    <a href="/webview.php/announce/info">
      <img src="/resources/things/tab/tab3_off.png" alt="不具合">
    </a>
  </li>
    </ul>
<div id="main">
  <div id="container">


  <SCRIPT type="text/javascript">
if(strUA.indexOf("iphone") >= 0 || strUA.indexOf("ipad") >= 0) {
  document.write('<div class="title_news_all_tab" style="position: fixed; top:0px; width:100%; z-index:20; background-color:white; height: 82px;">');
} else {
  document.write('<div class="title_news_all_tab">');
}
<?php foreach($has_new as $v) {
  if($v[0]==0) continue;
  switch($v[0]) {
    case 1:$left=280;break;
    case 2:$left=600;break;
    case 3:$left=920;
  }
?>
if(strUA.indexOf("iphone") >= 0 || strUA.indexOf("ipad") >= 0) {
  document.write('<img src="/resources/new.png" style="position: fixed; top:0px; left:<?=$left?>px; z-index:25; ">');
} else {
  document.write('<img src="/resources/new.png" style="position: absolute; top:0px; left:<?=$left?>px; z-index:25; ">');
}
<?php } ?>


</SCRIPT>
    <ul id="list">
      <SCRIPT type="text/javascript">
if(strUA.indexOf("iphone") >= 0 || strUA.indexOf("ipad") >= 0) {
  document.write('<div class="note" style="margin-top:100px;">');
} else {
  document.write('<div class="note">');
}
</SCRIPT>



<?php foreach($announcement as $v) {
  $time=explode(' ', $v['time'])[0];
?>
      <li class="entry" >
        <div class="entry-container">
          <h2 class="text"><?=$v['title']?></h2>
          <div class="summary"> <?=$v['content']?></div>
          <div class="start-date"><?=$time?></div>
          <div class="clearfix"></div>

        </div>
      </li>
<?php } ?>
</ul>
    <div id="load-next" data-loading-msg="（読み込み中…）" data-no-more-msg="（これ以上お知らせはありません）" style="display: none !important;">
      次の10件を表示
    </div>
  </div>
</div>

<script>
  const URL_BASE = '/webview.php';
  const DISP_FAULTY = 0;
  const USER_ID = 279412;
  const AUTHORIZE_DATA = 'consumerKey=lovelive_test&token=6NmJHLIcvs5SLhTMDLyeaz5G827U44PSYJH0BItNlINP9miZUINSFwVYy9RLRoeJyly9Po4UpDy1shXgE6YdCA0&version=1.1&timeStamp=1484453451&nonce=WV0';

  updateButtons();
  Button.initialize(document.getElementById('load-next'), loadNext);
  Ps.initialize(document.getElementById('container'), {suppressScrollX: true});
</script>
</body>
</html>

<!--<?=($v['detail_id']?'<BR><BR><SPAN style="color: red;">※点击查看详情</SPAN>':'')?>-->
<!--<?=($v['banner_on']?'<h2 class="banner"><img class="banner" src="<?=$banner_url?>"></h2>':'')?>-->