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
          <div class="summary">Custom Festival! Server 
            <br />Branch: <?=$result['branch']?> Commit: <?=$result['commit']?> 
            <br />Date: <?=$result['date']?>
            <br />客户端版本：<?=$result['bundle']?>
            服务器数据包版本：<?=$result['server']?>
          </div>
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
              1.本软件基于 KLab Inc 的开源引擎 Playground OSS 进行开发 并遵循 Apache 2.0 开源协议 <br>
              2.本软件不提供 原KLab Inc 开发的并且经过 编译 加密 过的代码, 其余附属更改代码由 HoshizoraCodeAcademy 开发 并且持有著作权<br>
              3.本软件的 内部资源来源 KLab Inc 并且未改作 未解密 KLab Inc 享有完整版权 <br>
              4.本软件不具备任何 盈利行为 和 商业性质, 属于个人开发学习练习用途, 并且 HoshizoraCoadAcademy 具有最终解释权<br>
              <hr>
            <tr>
              <td>
                <div id="license-url">开源许可</div>
              </td>
              <td>
                <div id="license-eng-url">引擎项目地址</div>
                </td>
            </tr>
          </div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry" >
        <div class="entry-container" id="tos">
          <h2 class="text">使用协议</h2>
          <div class="summary" style="width:760px !important;">点击查看</div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry" >
        <div class="entry-container">
          <h2 class="text">支持信息</h2>
          <div class="summary" >
            开发: 双草酸酯 NOAH<br>
            维护: 双草酸酯 NOAH<br><br>
            交流群: 641147818
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
    window.location.href='native://browser?url=mailto:<?=$result['support']?>';
  });
  Button.initialize(document.getElementById('noah'), function() {
    window.location.href='native://browser?url=https://www.lovelivesupport.com';
  });
  Button.initialize(document.getElementById('tos'), function() {
    window.location.href='/webview.php/tos/read';
  });
  Button.initialize(document.getElementById('license-url'), function() {
    window.location.href='native://browser?url=http://www.apache.org/licenses/LICENSE-2.0';
  });
  Button.initialize(document.getElementById('license-eng-url'), function() {
    window.location.href='native://browser?url=https://github.com/KLab/PlaygroundOSS';
  });
</script>