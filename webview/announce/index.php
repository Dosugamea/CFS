<? 
	header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
?>

<!DOCTYPE html>
<HTML><HEAD>
<META content="IE=11.0000" http-equiv="X-UA-Compatible">
<META charset="utf-8"> 
<TITLE></TITLE> 

<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="width=880, target-densitydpi=device-dpi, user-scalable=no">

<link rel="stylesheet" href="/resources/things/list.css">
<link rel="stylesheet" href="/resources/things/perfect-scrollbar.css">
<script src="/resources/things/perfect-scrollbar.min.js"></script>
<script src="/resources/things/button.js"></script>
<script src="/resources/things/list.js"></script>

<style>
.Welcome-Icon{width: 89%;margin-right: 5%;margin-left: 5%;}
.Welcome-Icon tr td{width: 33%;}
.Welcome-Icon tr{height: 50px;}
.Welcome-Icon tr td a{color: #ffffff !important;}
.main-icon{
  width: 100%;
  height: 60px;
  background-position: center;
  background-size: 100%;
}
.icon1{background-image: url(/resources/things/tab/setting.png);}
.icon2{background-image: url(/resources/things/tab/help.png);}
.icon3{background-image: url(/resources/things/tab/mod.png);}
a{text-decoration:none;}
a:active{text-decoration:none;}
a:visited{text-decoration:none;}
a:focus{text-decoration:none;}
a:hover{text-decoration:none;}
</style>

</head>
<BODY>

<?php 
require "../config/database.php";
require "../config/maintenance.php";
require "../version.php";
?>
 <ul id="tab">
      <li class="on">
    <a href="#">
      <img src="/resources/things/tab/tab1_on.png" alt="お知らせ">
    </a>
  </li>
    <li class="off">
    <a href="/webview.php/announce/announce">
       <img src="/resources/things/tab/tab2_off.png" alt="アップデート">
    </a>
  </li>
        <li class="off">
    <a href="/webview.php/announce/info">
      <img src="/resources/things/tab/tab3_off.png" alt="不具合">
    </a>
  </li>
    </ul>
<?php
	$announcement=$mysql->query('select * from webview where tab != 0 order by time desc limit 3')->fetchAll();
?>
<div id="main">
  <div id="container">
  <ul id="list">
      

<table class="Welcome-Icon" cellspacing="20">
  <tr>
    <td>
      <a href="/webview.php/settings/index">
        <div class="main-icon icon1" >
          
        </div>
      </a>
    </td>
    <td>
      <a href="/webview.php/help/index">
        <div class="main-icon icon2">
          
        </div>
      </a>
    </td>
    <td>
      <a href="/webview.php/mods/index">
        <div class="main-icon icon3">
          
        </div>
      </a>
    </td>
  </tr>
</table>
<?php foreach($announcement as $v) {
  $time=explode(' ', $v['time'])[0];
?>
  <li class="entry" >
        <div class="entry-container" id="an_<?=$v['ID']?>">
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
  const USER_ID = 0;
  const AUTHORIZE_DATA = '';
  updateButtons();
    <?
    foreach ($announcement as $d){
      if($d['detail_id'] != 0){
        print("Button.initialize(document.getElementById('an_{$d['ID']}'), function() {
    window.location.href='/webview.php/announce/detail/?detail_id={$d['detail_id']}';
  });");
      }
    }
    ?>
  Button.initialize(document.getElementById('load-next'), loadNext);
  Ps.initialize(document.getElementById('container'), {suppressScrollX: true});
</script>
</BODY></HTML>