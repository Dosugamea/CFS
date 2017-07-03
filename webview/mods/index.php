<?php
$uid = $_SESSION['server']['HTTP_USER_ID'];

$params = [];
foreach ($mysql->query('SELECT * FROM user_params WHERE user_id='.$uid)->fetchAll() as $v) {
  $params[$v['param']] = (int)$v['value'];
}

$allowed_params = ['extend_mods_vanish', 'extend_mods_mirror', 'extend_mods_life', 'extend_mods_hantei_count'];

foreach($allowed_params as $v) {
  if (!isset($params[$v])) {
    $params[$v] = 0;
  }
}

if(isset($_GET['switch_random'])) {
  $params['random_switch'] = $_GET['switch_random'];
  $mysql->prepare('REPLACE INTO user_params values (?, ?, ?)')->execute([$uid, 'random_switch', $_GET['switch_random']]);
}

if(isset($_GET['switch_param']) && isset($_GET['param']) && array_search($_GET['switch_param'], $allowed_params) !== false) {
  $_GET['param'] = (int)$_GET['param'];
  $params[$_GET['switch_param']] = $_GET['param'];
  if ($_GET['param'] != 0) {
    $mysql->prepare('REPLACE INTO user_params VALUES (?, ?, ?)')->execute([$uid, $_GET['switch_param'], $_GET['param']]);
  } else {
    $mysql->prepare('DELETE FROM user_params WHERE user_id=? and param=?')->execute([$uid, $_GET['switch_param']]);
  }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta content="IE=11.0000" http-equiv="X-UA-Compatible">
<meta charset="utf-8">
<title></title>

<style type="text/css">
.note img { margin-left: -12px; }
a{color: #000000;}
        .white_content { 
            display: none; 
            position: absolute; 
            margin-left: 22%;
            width: 50%; 
            height: 50px; 
            padding: 20px; 
            border: 10px solid  #ff699c;
            border-radius: 15px; 
            background-color: #ffffff; 
            box-shadow: 2px 2px 2px #ccc;
            z-index:1002; 
            overflow: auto; 
        } 
</style>
<meta name="GENERATOR" content="MSHTML 11.00.10011.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="width=880, target-densitydpi=device-dpi, user-scalable=no">

<link rel="stylesheet" href="/resources/things/detail.css?">
<link rel="stylesheet" href="/resources/things/perfect-scrollbar.css">
<link rel="stylesheet" href="/resources/things/list2.css">

<script src="/resources/things/perfect-scrollbar.min.js"></script>
<script src="/resources/things/button.js"></script>

<!--KeyBoard-->
<link rel="stylesheet" type="text/css" href="/resources/key/ios7keyboard.css">
<script type="text/javascript" src="/resources/key/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="/resources/key/ios7keyboard.js"></script>
<style type="text/css">
  #keyboard_5xbogf8c{top: 780px !important;left: 30px !important;margin-bottom: 60px;}
</style>
<script>
  var num = 1589740651036;

  function printNumber(numDigits) {
    numDigits = numDigits || 10;
    
    var str = String(num);

    for (var i = 0; i < numDigits - str.length; i++) {
      document.write('<img src="http://cf-static-prod.lovelive.ge.klabgames.net/resources/img/thanksgiving/counter00.png" class="etc">');
    }
    for (var i = 0; i < str.length; i++) {
      var d = str.charAt(i);
      document.write('<img src="http://cf-static-prod.lovelive.ge.klabgames.net/resources/img/thanksgiving/counter0' + d + '.png" class="etc">');
    }
  }

  function printIcon(target) {
    if (num >= target) {
      document.write('<img src="http://cf-static-prod.lovelive.ge.klabgames.net/resources/img/thanksgiving/e_icon_02.png" class="etc">');
    } else {
      document.write('<img src="http://cf-static-prod.lovelive.ge.klabgames.net/resources/img/thanksgiving/e_icon_01.png" class="etc">');
    }
  }
</script>
</head>
<body>
<div id="outer">
  <div id="inner">
    <div id="header">
      <h2>Mods</h2>
      <div id="back"></div>
    </div>

<div id="body">
<div id="container">
<ul id="list">
     <!-- <li class="entry"">
        <div class="entry-container">
          <h2 class="text">兑换码</h2>
          <div class="summary">
            <input type="text" name="" id="numkeyboard1" class="numkeyboard" style="border:1px solid;height:27px; readonly="true">
            <a href = "JavaScript:void(0)" onclick = "document.getElementById('light').style.display='block';document.getElementById('fade').style.display='block'">
            <input type="" style="border:1px solid;height:27px;width:64px;" value="提交" readonly="readonly" /></a>
          <key></key>
            <div id="light" class="white_content">
              <a href = "javascript:void(0)" onclick = "document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'">兑换码有误 请点击重试</a>
            </div> 
           
            <div id="light" class="white_content">
                <a href = "javascript:void(0)" onclick = "document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'">兑换码有误 请点击重试</a>
                </div> 
            
          </div>
          <div class="clearfix"></div>
        </div>
      </li>-->
      <li class="entry"">
        <div class="entry-container">
          <h2 class="text">使用方法</h2>
          <div class="summary">以下的所有Mod均可以在游戏过程中随时切换。<br />使用 その他-ヘルプ 来快速到达本页面。</div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry"">
        <div class="entry-container">
          <h2 class="text">自定义模块</h2>
          <div class="summary">
          HI-SPEED:<font color="red"><b>请升级至3.2客户端，然后在“各种设定”中设置！</b></font>
          <br />
          随机：<a href="/webview.php/mods/index?switch_random=0">关闭</a>&nbsp;&nbsp;<a href="/webview.php/mods/index?switch_random=1">新随机</a>&nbsp;&nbsp;<a href="/webview.php/mods/index?switch_random=2">旧随机</a>&nbsp;&nbsp;<a href="/webview.php/mods/index?switch_random=3">无限制随机</a>&nbsp;&nbsp;（当前状态：<?php if($params['random_switch']==1) echo '新随机';elseif($params['random_switch']==2) echo '旧随机';elseif($params['random_switch']==3) echo '无限制随机'; else echo '关闭'; ?>）<br /><br />
          <?php $status = ['关闭', 'HIDDEN', 'SUDDEN'];?>
          VANISH：<?php foreach($status as $k => $v) {
            echo '<a href="index?switch_param=extend_mods_vanish&param='.$k.'">'.$v.'</a>&nbsp;&nbsp;&nbsp;&nbsp;';
          }?>（当前状态：<?=$status[$params['extend_mods_vanish']]?>）<br /><br />
          <?php $status = ['关闭', '开启'];?>
          MIRROR：<?php foreach($status as $k => $v) {
            echo '<a href="index?switch_param=extend_mods_mirror&param='.$k.'">'.$v.'</a>&nbsp;&nbsp;&nbsp;&nbsp;';
          }?>（当前状态：<?=$status[$params['extend_mods_mirror']]?>）<br /><br />
          <?php $status = ['关闭', 'NO FAIL', 'SUDDEN DEATH'];?>
          锁血：<br /><?php foreach($status as $k => $v) {
            echo '<a href="index?switch_param=extend_mods_life&param='.$k.'">'.$v.'</a>&nbsp;&nbsp;&nbsp;&nbsp;';
          }?>（当前状态：<?=$status[$params['extend_mods_life']]?>）<br /><br />          
          </div>
          <div class="clearfix"></div>
        </div>
      </li>
     
      <li class="entry"">
        <div class="entry-container">
          <h2 class="text">附加自定义功能</h2>
          <div class="summary">
            <b>注意：以下的功能会大幅降低游戏难度，因而打开后您的成绩【不会】被记录！</b><br />
          <form method="get" action="/webview.php/mods/index" autocomplete="off">
          在游戏开始（以及组曲换曲）时获得<input type="text" value="<?=$params['extend_mods_hantei_count']?>" name="param" style="border:1px solid;height:27px;"  id="numkeyboard1" class="numkeyboard"  pattern="[0-9]*" readonly="true"/>
          <input type="hidden" value="extend_mods_hantei_count" name="switch_param" />个超大判（设为0为关闭）<br />
          <input type="submit" style="border:1px solid;height:27px;width:64px;" value="提交" />
          </form>
          <key></key>
          </div>
          <div class="clearfix"></div>
        </div>
      </li>

</ul>

</div>
 </div>
  </div>
</div>
</div>
  </div>
</div>

<script>
  Button.initialize(document.getElementById('back'), function() {
    window.location.href='/webview.php/announce/index';
  });
  Ps.initialize(document.getElementById('body'), {suppressScrollX: true});
</script>
<script type="text/javascript">
                $(document).ready(function(){ 
                  $(".numkeyboard").ioskeyboard({
                    keyboardRadix:80,
                    keyboardRadixMin:30,
                    keyboardRadixChange:false,
                    keyfixed:false,
                    clickeve:false,
                    colorchange:false,
                    colorchangeStep:1,
                    colorchangeMin:154
                                    });
                                  })  
              </script>
</body>


