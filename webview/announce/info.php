<meta charset='utf-8' />

<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="width=880, target-densitydpi=device-dpi, user-scalable=no">

<link rel="stylesheet" href="/resources/things/list.css">
<link rel="stylesheet" href="/resources/things/perfect-scrollbar.css">

<script src="/resources/things/perfect-scrollbar.min.js"></script>
<script src="/resources/things/button.js"></script>
<script src="/resources/things/list.js"></script>
<!--<style>body{font-size:2em;}table{font-size:1em;}</style>-->

<!--<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />-->
<?php  require "config/database.php";
require "config/maintenance.php";
require "version.php";
require "info.php"
?>
<ul id="tab">
      <li class="off">
    <a href="/webview.php/announce/index">
      <img src="/resources/things/tab/tab1_off.png" alt="お知らせ">
    </a>
  </li>
    <li class="off">
    <a href="/webview.php/announce/announce">
       <img src="/resources/things/tab/tab2_off.png" alt="アップデート">
    </a>
  </li>
        <li class="on">
    <a href="">
      <img src="/resources/things/tab/tab3_on.png" alt="不具合">
    </a>
  </li>
</ul>
<div id="main">
  <div id="container">
    <ul id="list">
      <li class="entry" >
        <div class="entry-container">
          <h2 class="text">版本信息</h2>
          <div class="summary">Programmed Live! Server {<?=$pls_version_date?>}<br />客户端版本：<?=(isset($_SESSION['server']["HTTP_BUNDLE_VERSION"]) ? $_SESSION['server']["HTTP_BUNDLE_VERSION"] : '客户端未提交')."(".$_SESSION['server']["HTTP_CLIENT_VERSION"].") 服务器版本：".$bundle_ver."(".$server_ver; ?>)</div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry" >
        <div class="entry-container">
          <h2 class="text">使用协议</h2>
          <div class="summary" style="width:760px !important;"><?=$pls_agreement?></div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry" >
        <div class="entry-container">
          <h2 class="text">支持信息</h2>
          <div class="summary" >
            维护:<?=$pls_maintenance?><br>
            开发:<?=$pls_dev?><br>
            运营:<?=$pls_operation?><br><br>
            <a href="native://browser?url=mailto:<?=$pls_support_mail?>" style="color:red;" >邮件反馈</a>
          </div>
          <div class="clearfix"></div>
        </div>
      </li>
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