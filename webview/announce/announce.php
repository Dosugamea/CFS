<!DOCTYPE html PUBLIC "" ""><HTML><HEAD><META content="IE=11.0000" 
http-equiv="X-UA-Compatible">
 
<META charset="utf-8"> <TITLE>お知らせ一覧</TITLE> 
<TITLE>news 
detail</TITLE> 
<LINK href="/resources/bstyle.css" rel="stylesheet"> 
<LINK href="/resources/news.css" rel="stylesheet"> 
<link href="/resources/css/style.css" rel="stylesheet">
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

function getUrlParamFromKey(key) {
  var temp_params = window.location.search.substring(1).split('&');
  var vars =  new Array();
  for(var i = 0; i <temp_params.length; i++) {
    params = temp_params[i].split('=');
    vars[params[0]] = params[1];
  }
  return vars[key];
}

window.onload = function() {
  var disp_faulty = getUrlParamFromKey("disp_faulty");
  if (disp_faulty == 1) {
    tabItem = document.getElementById("newstab2");
  } else {
    tabItem = document.getElementById("newstab1");
  }
  tabItem.className += " open";
}
</SCRIPT>
 <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
<STYLE type="text/css">
.note img{
  margin-left: -12px;
}
</STYLE>
 
<META name="GENERATOR" content="MSHTML 11.00.10011.0"></HEAD> 
<BODY>
<?php
if(!isset($_GET['disp_faulty']) || !is_numeric($_GET['disp_faulty'])) {
  $_GET['disp_faulty']=0;
}
$announcement=$mysql->query('select * from webview where tab='.($_GET['disp_faulty']+1).' order by `order` desc, time desc')->fetchAll();
$has_new=$mysql->query('select distinct tab from webview where to_days(time)>to_days(CURRENT_TIMESTAMP)-5')->fetchAll();
?>

<DIV id="wrapper_news">
<SCRIPT type="text/javascript">
if(strUA.indexOf("iphone") >= 0 || strUA.indexOf("ipad") >= 0) {
  document.write('<div class="title_news_all_tab" style="position: fixed; top:0px; width:960px; z-index:20; background-color:white; height: 82px;">');
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
     
<UL id="tabs">
  <LI class="fs30" id="newstab1" name="box1"><A style="color: rgb(255, 255, 255);" 
  href="/webview.php/announce/announce?disp_faulty=0">通知 News</A></LI>
  <LI class="fs30" id="newstab2" name="box2"><A style="color: rgb(255, 255, 255);" 
  href="/webview.php/announce/announce?disp_faulty=1">游戏更新 Update</A></LI>
  <LI class="fs30" id="newstab3" name="box3"><A style="color: rgb(255, 255, 255);" 
  href="/webview.php/announce/index">返回主页 Back</A></LI></UL></DIV>
<DIV class="content_news_all">
<SCRIPT type="text/javascript">
if(strUA.indexOf("iphone") >= 0 || strUA.indexOf("ipad") >= 0) {
  document.write('<div class="note" style="margin-top:100px;">');
} else {
  document.write('<div class="note">');
}
</SCRIPT>
 
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

</DIV></div></div>
<DIV class="footer_news_all"><IMG width="100%" src="/resources/bg03.png"> 
</DIV>
<!--<SCRIPT src="http://d3llrff26gioiw.cloudfront.net/resources/js/tab.js" type="text/javascript"></SCRIPT>-->
</div></div>
</BODY></HTML>