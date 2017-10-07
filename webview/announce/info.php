<meta charset='utf-8' />

<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="width=880, target-densitydpi=device-dpi, user-scalable=no">

<link rel="stylesheet" href="/resources/things/list.css">
<link rel="stylesheet" href="/resources/things/perfect-scrollbar.css">

<script src="/resources/things/perfect-scrollbar.min.js"></script>
<script src="/resources/things/button.js"></script>
<script src="/resources/things/list.js"></script>
<style type="text/css">
  table{width: 500px;}
  table td tr{width: 50%;}
  #mail{width: 140px;height: 50px;background-image: url(/resources/things/tab/mail.png);background-position: center;background-size: cover;margin: 5px;}
  #noah{width: 250px;height: 50px;background-image: url(/resources/things/tab/noah.png);background-position: center;background-size: 88%;margin: 5px;background-repeat: no-repeat;}
  #license-url , #license-eng-url{
    width: 140px;
    height: 50px;
    background-color: #FC6B9F;
    border: 2px solid #CCC;
    border-radius: 15px;
    text-align: center;
    margin: 10px;
    color: #FFFFFF;
    padding-top: 15px;
    box-shadow: 2px 2px 2px #ccc;
  }
</style>
<!--<style>body{font-size:2em;}table{font-size:1em;}</style>-->

<!--<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />-->
<?php  require "../config/database.php";
require "../config/maintenance.php";
require "../version.php";
require "../info.php"
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
          <div class="summary">Programmed Live! Server <span style="color: red;">*<?=$pls_version?>* </span><?=$pls_version_date?> <br />
            客户端版本：<?=(isset($_SESSION['server']["HTTP_BUNDLE_VERSION"]) ? $_SESSION['server']["HTTP_BUNDLE_VERSION"] : '客户端未提交')."(".$_SESSION['server']["HTTP_CLIENT_VERSION"].") 服务器版本：".$bundle_ver."(".$server_ver; ?>)</div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry" >
        <div class="entry-container" id="href">
          <h2 class="text">使用帮助</h2>
          <div class="summary">点击此处进入使用帮助</div>
          <div class="clearfix"></div>
        </div>
      </li>
       <li class="entry" >
        <div class="entry-container" id="href">
          <h2 class="text">软件许可</h2>
          <div class="summary" style="width:760px !important;">
            <?=$pls_license?><hr>
            <div id="license-url">开源许可</div>
            <div id="license-eng-url">引擎项目地址</div>
          </div>
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
            开发:<?=$pls_dev?><br>
            维护:<?=$pls_maintenance?><br><br>
            交流群: <?=$pls_qq_group?>
            <table>
              <tr><td><div id="mail"></div></td><td><div id="noah"></div></td></tr>
            </table>
           
          </div>
          <div class="clearfix"></div>
        </div>
      </li><br><br><br>
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
  Button.initialize(document.getElementById('load-next'), loadNext);
  Ps.initialize(document.getElementById('container'), {suppressScrollX: true});

  Button.initialize(document.getElementById('href'), function() {
    window.location.href='/webview.php/help/index';
  });
  Button.initialize(document.getElementById('mail'), function() {
    window.location.href='native://browser?url=mailto:<?=$pls_support_mail?>';
  });
  Button.initialize(document.getElementById('noah'), function() {
    window.location.href='native://browser?url=https://www.lovelivesupport.com';
  });
  Button.initialize(document.getElementById('license-url'), function() {
    window.location.href='native://browser?url=<?=$pls_license_url?>';
  });
  Button.initialize(document.getElementById('license-eng-url'), function() {
    window.location.href='native://browser?url=<?=$pls_license_eng_url?>';
  });
</script>