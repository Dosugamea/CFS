<!DOCTYPE html>
<HTML><HEAD>
<META content="IE=11.0000" http-equiv="X-UA-Compatible">
 
<META charset="utf-8"> 
<TITLE>お知らせ一覧</TITLE> 

<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="width=880, target-densitydpi=device-dpi, user-scalable=no">

<link rel="stylesheet" href="/resources/things/list.css">
<link rel="stylesheet" href="/resources/things/perfect-scrollbar.css">

<script src="/resources/things/perfect-scrollbar.min.js"></script>
<script src="/resources/things/button.js"></script>
<script src="/resources/things/list.js"></script>
<style>
.Welcome-Icon{width: 89%;margin-right: 5%;margin-left: 5%;text-align: center;}
.Welcome-Icon tr td{width: 33%; }
.Welcome-Icon tr td a{color: #ffffff !important;}
.main-icon{
  width: 100%;
  border: 4px solid #878787;
  border-radius: 15px;background-color:#FF6699;
}
a{text-decoration:none;}
a:active{text-decoration:none;}
a:visited{text-decoration:none;}
a:focus{text-decoration:none;}
a:hover{text-decoration:none;}
</style>
</head>
<BODY>
<?php require "config/database.php";
require "config/maintenance.php";
require "version.php";
?>

<!--<DIV id="wrapper_news">
<div class="title_news_all_tab">
<UL id="tabs">
  <LI class="fs30 open" id="newstab1" name="box1">最新公告 News</LI>
  <LI class="fs30" id="newstab2" name="box2"><A href="/webview.php/announce/announce">查看全部 ALL</A></LI>
  <LI class="fs30" id="newstab2" name="box2"><A href="/webview.php/announce/info">关于 About</A></LI>
</UL>
</DIV>-->
 <ul id="tab">
      <li class="on">
    <a href="/webview.php/announce/announce">
      <img src="/resources/things/tab1_on.png" alt="お知らせ">
    </a>
  </li>
    <li class="off">
    <a href="/webview.php/announce/index?disp_faulty=2">
       <img src="/resources/things/tab2_off.png" alt="アップデート">
    </a>
  </li>
        <li class="off">
    <a href="/webview.php/announce/info">
      <img src="/resources/things/tab3_off.png" alt="不具合">
    </a>
  </li>
    </ul>
<?php
if(!isset($_GET['disp_faulty']) || !is_numeric($_GET['disp_faulty'])) {
  $_GET['disp_faulty']=0;
}
$announcement=$mysql->query('select * from webview where tab!=0 order by time desc limit 3')->fetchAll();
?>
<div id="main">
  <div id="container">
  <ul id="list">
      

<!--<div class="title_news_all_tab bl1">
<UL id="tabs">
  <a href="/webview.php/announce/announce"><LI class="fs30 button"><p>News</p></LI></a>
  <a href="/webview.php/settings/index"><LI class="fs30 button"><p>Setting</p></LI></a>
  <a href="/webview.php/mods/index"><LI class="fs30 button"><p>Mods</p></LI></a>
</UL>
</div>-->
<table class="Welcome-Icon" cellspacing="20">
  <tr>
    <td>
      <a href="/webview.php/announce/announce">
        <div class="main-icon" style="font-size:4vw;">
          News
        </div>
      </a>
    </td>
    <td>
      <a href="/webview.php/settings/index">
        <div class="main-icon" style="font-size:4vw;">
          Setting
        </div>
      </a>
    </td>
    <td>
      <a href="/webview.php/mods/index">
        <div class="main-icon" style="font-size:4vw;">
          Mods
        </div>
      </a>
    </td>
  </tr>
</table>
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

<!--<A class="big-link"<?=($v['detail_id']?' href="/webview.php/announce/detail?0=&announce_id='.$v['detail_id'].'&disp_faulty='.$_GET['disp_faulty'].'"':'')?> data-animation="fade" data-reveal-id="readlist01">
  <DIV class="title_news_all fs30">
    <SPAN class="ml40"><?=$v['title']?></SPAN>
  </DIV>
  <DIV class="content_all">
    <DIV class="note">
      <P>
        <?=$v['content']?>
        <?=($v['detail_id']?'<BR><SPAN style="color: red;">※点击查看详情</SPAN>':'')?>
        <BR><BR>
      </P>
    <?=$time?> 
    </DIV>
  </DIV>
</A>


</DIV></div>
<DIV class="footer_news_all"><IMG width="100%" src="/resources/bg03.png"> 
</DIV>
<SCRIPT src="http://d3llrff26gioiw.cloudfront.net/resources/js/tab.js" type="text/javascript"></SCRIPT>
</div></div>-->
</BODY></HTML>