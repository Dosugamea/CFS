<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>お知らせ一覧</title>

<link rel="stylesheet" href="/resources/css/list.css">
<!--<link rel="stylesheet" href="/resources/css/perfect-scrollbar.css">
<script src="/resources/js/perfect-scrollbar.min.js"></script>
<script src="/resources/js/button.js"></script>-->
<script src="/resources/js/news/list.js"></script>
<SCRIPT type="text/javascript">
var strUA = "";
strUA = navigator.userAgent.toLowerCase();

if(strUA.indexOf("iphone") >= 0) {
  document.write('<meta name="viewport" content="width=100%, minimum-scale=0.45, maximum-scale=0.45, user-scalable=no" />');
} else if (strUA.indexOf("ipad") >= 0) {
  document.write('<meta name="viewport" content="width=880, target-densitydpi=device-dpi, user-scalable=no"><meta name="apple-mobile-web-app-capable" content="yes">');
} else if (strUA.indexOf("android 2.3") >= 0) {
  document.write('<meta name="viewport" content="width=100%, minimum-scale=0.45, maximum-scale=0.45, initial-scale=0.45, user-scalable=yes" />');
} else {
  document.write('<meta name="viewport" content="width=100%, minimum-scale=0.38, maximum-scale=0.38, user-scalable=no" />');
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
</head>

<body>

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



<ul id="tab">
      <li class="on">
    <a href="/webview.php/announce/announce2?disp_faulty=0">
      <img src="/resources/newimg/tab1_on.png" >
    </a>
  </li>
        <li class="off">
    <a href="/webview.php/announce/announce2?disp_faulty=1">
      <img src="/resources/newimg/tab2_off.png">
    </a>
  </li>
        <li class="off">
    <a href="/webview.php/announce/announce2?disp_faulty=1">
      <img src="/resources/newimg/tab3_off.png">
    </a>
  </li>
    </ul>
<div id="main">
  <div id="container">
    <ul id="list">
      <li class="entry" data-announce-id="1617" data-disp-order="1529">
        <div class="entry-container">
          <h2 class="text">不正行為に関する対処につきまして</h2>
          <div class="summary">現在開催中のイベントにおきまして、本サービスの利用結果を不正に操作する行為が一部</div>
          <div class="start-date">2017/01/15</div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry" data-announce-id="1615" data-disp-order="1527">
        <div class="entry-container">
          <h2 class="text">小泉花陽誕生日限定勧誘実施のお知らせ</h2>
          <div class="summary">1月17日の小泉 花陽ちゃんのお誕生日を記念して、1/16(月)0時から1/18(水)24時までの</div>
          <div class="start-date">2017/01/14</div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry" data-announce-id="1616" data-disp-order="1526">
        <div class="entry-container">
          <h2 class="text">期間限定で過去の限定URが復刻！</h2>
          <div class="summary">いつも「ラブライブ！スクールアイドルフェスティバル」をお楽しみいただき、ありがと</div>
          <div class="start-date">2017/01/14</div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry" data-announce-id="1608" data-disp-order="1524">
        <div class="entry-container">
          <h2 class="text">1/15(日)メンテナンスのお知らせ</h2>
          <div class="summary">1/15(日)15時から16時までアップデートのためのメンテナンスを実施いたします。 アッ</div>
          <div class="start-date">2017/01/14</div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry" data-announce-id="1605" data-disp-order="1523">
        <div class="entry-container">
          <h2 class="banner"><img class="banner" src="//cf-static-prod.lovelive.ge.klabgames.net/resources/img/banner/secretbox_1701_14_cvdik.png" width="840" height="250" alt="【第3弾】μ&#039;s属性限定勧誘登場！"></h2>
          <div class="summary">1/12(木)0時から1/15(日)15時まで、3種類の属性のμ&#039;s部員が登場する「μ&#039;s属性限定勧</div>
          <div class="start-date">2017/01/14</div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry" data-announce-id="1614" data-disp-order="1521">
        <div class="entry-container">
          <h2 class="text">1/19(木)メンテナンスのお知らせ</h2>
          <div class="summary">以下の日程でアップデートのためのメンテナンスを行わせていただきます。 アップデー</div>
          <div class="start-date">2017/01/13</div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry" data-announce-id="1602" data-disp-order="1518">
        <div class="entry-container">
          <h2 class="banner"><img class="banner" src="//cf-static-prod.lovelive.ge.klabgames.net/resources/img/banner/secretbox_1701_10_mkdsd.png" width="840" height="250" alt="μ&#039;s新部員登場のお知らせ"></h2>
          <div class="summary">1/10(火)16時より、新しいμ&#039;s部員を追加いたしました。 今回もμ&#039;sメンバーが動物の</div>
          <div class="start-date">2017/01/10</div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry" data-announce-id="1598" data-disp-order="1514">
        <div class="entry-container">
          <h2 class="banner"><img class="banner" src="//cf-static-prod.lovelive.ge.klabgames.net/resources/img/login_news/news_1701_05_B.png" width="840" height="187" alt="第3回 転入生総選挙開催！"></h2>
          <div class="summary">「第3回 転入生総選挙」を開催いたします！ 応援したい転入生を3人選んで投票していた</div>
          <div class="start-date">2017/01/05</div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry" data-announce-id="1595" data-disp-order="1511">
        <div class="entry-container">
          <h2 class="banner"><img class="banner" src="//cf-static-prod.lovelive.ge.klabgames.net/resources/img/banner/event_1701_05_Ua1O4.png" width="840" height="250" alt="イベント開催のお知らせ"></h2>
          <div class="summary">1/5(木)16時から1/15(日)15時までの期間、イベント『第29回スコアマッチ』を開催いた</div>
          <div class="start-date">2017/01/04</div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry" data-announce-id="1590" data-disp-order="1509">
        <div class="entry-container">
          <h2 class="banner"><img class="banner" src="//cf-static-prod.lovelive.ge.klabgames.net/resources/img/login_news/news_1701_01_kso4b.png" width="840" height="250" alt=" 『正月限定！お年玉セット』登場！"></h2>
          <div class="summary">ショップに『正月限定！お年玉セット』が登場！ 1/1(日)0:00〜1/3(火)23:59までの期間</div>
          <div class="start-date">2017/01/01</div>
          <div class="clearfix"></div>
        </div>
      </li>
          </ul>
    <div id="load-next" data-loading-msg="（読み込み中…）" data-no-more-msg="（これ以上お知らせはありません）">
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
