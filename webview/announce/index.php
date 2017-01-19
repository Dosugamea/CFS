<!DOCTYPE html>
<HTML><HEAD><META content="IE=11.0000" 
http-equiv="X-UA-Compatible">
 
<META charset="utf-8"> 
<TITLE>お知らせ一覧</TITLE> 
<LINK href="/resources/bstyle.css" rel="stylesheet"> 
<LINK href="/resources/news.css" rel="stylesheet"> 
<link href="/resources/css/style.css" rel="stylesheet">
<SCRIPT type="text/javascript">
var strUA = "";
strUA = navigator.userAgent.toLowerCase();

if(strUA.indexOf("iphone") >= 0) {
  document.write('<meta name="viewport" content="width=100%, minimum-scale=0.45, maximum-scale=0.45, user-scalable=no" />');
} else if (strUA.indexOf("ipad") >= 0) {
  document.write('<meta name="viewport" content="width=100%, minimum-scale=0.9, maximum-scale=0.9, user-scalable=no" />');
} else if (strUA.indexOf("android 2.3") >= 0) {
  document.write('<meta name="viewport" content="width=100% minimum-scale=0.45, maximum-scale=0.45, initial-scale=0.45, user-scalable=yes" />');
} else {
  document.write('<meta name="viewport" content="width=100%, minimum-scale=0.38, maximum-scale=0.38, user-scalable=no" />');
}
</SCRIPT>
<style>
li.button{
  vertical-align:top;
  height:120px !important;
  background:url(/resources/m_button_01.png) !important;
  background-position:center !important;
  background-repeat: no-repeat !important;
  background-size: auto !important;
}
li.button p{
  padding-top:5px;
}
ul#tabs a{
  color:white;
}
ul#tabs a:visited{
  color:white;
}

</style>
</head>
<BODY>
<?php require "config/database.php";
require "config/maintenance.php";
require "version.php";
?><p>Programmed Live! Server {<?=$pls_version_date?>}<br />客户端版本：<?=(isset($_SESSION['server']["HTTP_BUNDLE_VERSION"]) ? $_SESSION['server']["HTTP_BUNDLE_VERSION"] : '客户端未提交')."(".$_SESSION['server']["HTTP_CLIENT_VERSION"].") 服务器版本：".$bundle_ver."(".$server_ver; ?>)</p>
<?php
if ($mysql->query('SELECT length(`login_password`) FROM `users` WHERE `user_id`='.$_SESSION['server']['HTTP_USER_ID'])->fetchColumn() != 32) {
  echo '<b style="color:red">我们升级了服务器的密码存储机制，建议您前往“游戏设置”退出重新登录或修改密码，这将大幅降低服务器被攻击导致密码泄露的风险。</b>';
} ?>
<p>&nbsp;</p>
<DIV id="wrapper_news">
<div class="title_news_all_tab">
<UL id="tabs">
  <a href="/webview.php/announce/announce"><LI class="fs30 button"><p>News</p></LI></a>
  <a href="/webview.php/settings/index"><LI class="fs30 button"><p>Setting</p></LI></a>
  <a href="/webview.php/mods/index"><LI class="fs30 button"><p>Mods</p></LI></a>
</UL>
</div>
<div style="clear:both;height:50px;"></div>
<div class="title_news_all_tab">
<UL id="tabs">
  <LI class="fs30 open" id="newstab1" name="box1">最新公告 News</LI>
  <LI class="fs30" id="newstab2" name="box2"><A href="/webview.php/announce/announce">查看全部 ALL</A></LI>
  <LI class="fs30" id="newstab2" name="box2"><A href="/webview.php/announce/info">关于 About</A></LI>
</UL>
</DIV>
<?php
if(!isset($_GET['disp_faulty']) || !is_numeric($_GET['disp_faulty'])) {
  $_GET['disp_faulty']=0;
}
$announcement=$mysql->query('select * from webview where tab!=0 order by time desc limit 3')->fetchAll();
?>
<DIV class="content_news_all">
<DIV id="box1">
<?php foreach($announcement as $v) {
  $time=explode(' ', $v['time'])[0];
?>
<A class="big-link"<?=($v['detail_id']?' href="/webview.php/announce/detail?0=&announce_id='.$v['detail_id'].'&disp_faulty='.$_GET['disp_faulty'].'"':'')?> data-animation="fade" data-reveal-id="readlist01">
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
<?php } ?>

</DIV></div>
<DIV class="footer_news_all"><IMG width="100%" src="/resources/bg03.png"> 
</DIV>
<!--<SCRIPT src="http://d3llrff26gioiw.cloudfront.net/resources/js/tab.js" type="text/javascript"></SCRIPT>-->
</div></div>
</BODY></HTML>