<?php
$announcement=$mysql->query('select * from webview where id='.($_GET['announce_id']))->fetch();
$time=explode(' ', $announcement['time'])[0];
?>

<!DOCTYPE HTML>
<!DOCTYPE html PUBLIC "" ""><HTML lang="ja"><HEAD><META content="IE=11.0000" 
http-equiv="X-UA-Compatible">
 
<META charset="utf-8"> 
<META name="apple-mobile-web-app-capable" content="yes"> <TITLE>news 
detail</TITLE> <LINK href="/resources/bstyle.css" rel="stylesheet"> 
<LINK href="/resources/news.css" rel="stylesheet"> 
<STYLE>
    html, body {
    background-color: transparent;
  }
p{     
    background-image: url(/resources/bug_trans.png); 
}
</STYLE>
 
<SCRIPT type="text/javascript">
 window.onload = function() {
 setTimeout(function(){window.scrollTo(0, 1);}, 100);
 }

var strUA = "";
strUA = navigator.userAgent.toLowerCase();

if(strUA.indexOf("iphone") >= 0) {
  document.write('<meta name="viewport" content="width=880px, minimum-scale=0.45, maximum-scale=0.45" />');
} else if (strUA.indexOf("ipad") >= 0) {
  document.write('<meta name="viewport" content="width=1024px, minimum-scale=0.9, maximum-scale=0.9" />');
} else {
  document.write('<meta name="viewport" content="width=880px, minimum-scale=0.38, maximum-scale=0.38" />');
}
</SCRIPT>
 
<META name="GENERATOR" content="MSHTML 11.00.10011.0"></HEAD> 
<BODY>
<DIV id="wrapper">
<DIV class="title_news fs34"><SPAN class="ml30"><?=$announcement['title']?></SPAN>     <A id="back" 
href="/webview.php/announce/announce?disp_faulty=<?=$_GET['disp_faulty']?>">
<DIV class="topback"><IMG src="/resources/com_button_01.png" data-on="/resources/com_button_02se.png"> 
        </DIV></A> </DIV>
<DIV class="content_news">
<DIV class="note">
<P><?=$announcement['content']?><BR><BR></P><?=$time?>              <A 
id="back" href="/webview.php/announce/announce?disp_faulty=<?=$_GET['disp_faulty']?>">
<DIV class="bottomback"><IMG src="/resources/com_button_01.png" 
data-on="/resources/com_button_02se.png"> 
            </DIV></A>   </DIV></DIV>
<DIV class="footer_news fs34"><IMG width="100%" src="/resources/bg03.png"> 
</DIV></DIV>
<SCRIPT>
document.getElementById('back').addEventListener('touchstart', function(ev){
  var img = this.querySelector('img');
  img.src = img.getAttribute('data-on');
}, false);
</SCRIPT>
 </BODY></HTML>