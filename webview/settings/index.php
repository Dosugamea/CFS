<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title></title>

<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="width=880, target-densitydpi=device-dpi, user-scalable=no">

<link rel="stylesheet" href="/resources/things/detail.css?">
<link rel="stylesheet" href="/resources/things/perfect-scrollbar.css">
<link rel="stylesheet" href="/resources/things/list2.css">

<script src="/resources/things/perfect-scrollbar.min.js"></script>
<script src="/resources/things/button.js"></script>
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
<style type="text/css">
a{color: #000000;}
</style>
</head>
          <?php
        $uid=$_SESSION['server']['HTTP_USER_ID'];
        global $mysql;
        $download_site = $mysql->query("SELECT download_site FROM users WHERE user_id = ?", [$uid])->fetchColumn();
        ?>

<body>
<div id="outer">
  <div id="inner">
    <div id="header">
      <h2>游戏设置</h2>
      <div id="back"></div>
    </div>

<div id="body">
<div id="container">
<ul id="list">
      <li class="entry">
        <div class="entry-container">
          <h2 class="text">数据包下载节点设置</h2>
          <div class="summary kawai-div">
            <a href="/webview.php/settings/reverse_proxy?site=1">
                <span class="kawai-ne <?php if($download_site == 1) print("kawai-ne2");?>">中国大陆地区</span>  
                <div class="kawai-icon <?php if($download_site == 1) print("kawai-icon2");?>"></div>
                <p style="color: #ccc;">注:在中国大陆地区下载会加速,中国大陆以外地区下载可能会减速</p>
            </a><hr>
            <a href="/webview.php/settings/reverse_proxy?site=2">
              <span class="kawai-ne <?php if($download_site == 2) print("kawai-ne2");?>">国际地区</span>
              <div class="kawai-icon <?php if($download_site == 2) print("kawai-icon2");?>"></div>
              <p style="color: #ccc;">注:适用于国际地区,中国大陆地区下载可能会失败</p>
            </a>
          </div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry">
        <div class="entry-container">
          <h2 class="text">自定义组曲</h2>
          <a href="/webview.php/settings/medley"><div class="summary">点击跳转至自定义组曲设置页<br>
          </div></a>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry">
        <div class="entry-container">
          <h2 class="text">卡片开关</h2>
          <div class="summary">
          <script type="text/javascript">
          var strUA = "";
          strUA = navigator.userAgent.toLowerCase();
          if (strUA.indexOf("iphone") >= 0) {
              document.write('新设置页暂时不兼容iOS，请使用旧版设置页：<a href="/webview.php/settings/card">卡片开关/头像设置</a>');
          } else if (strUA.indexOf("ipad") >= 0) {
              document.write('新设置页暂时不兼容iOS，请使用旧版设置页：<a href="/webview.php/settings/card">卡片开关/头像设置</a>');
          } else {
              document.write('<iframe src="/webview.php/settings/iframe_settings_1" style="width: 100%;height: 430px;border: none;" scrolling="no"></iframe>');
          }
          </script>
          
          </div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry">
        <div class="entry-container">
          <h2 class="text">头像设置</h2>
          <div class="summary">
          <script type="text/javascript">
          var strUA = "";
          strUA = navigator.userAgent.toLowerCase();
          if (strUA.indexOf("iphone") >= 0) {
              document.write('新设置页暂时不兼容iOS，请使用旧版设置页：<a href="/webview.php/settings/card">卡片开关/头像设置</a>');
          } else if (strUA.indexOf("ipad") >= 0) {
              document.write('新设置页暂时不兼容iOS，请使用旧版设置页：<a href="/webview.php/settings/card">卡片开关/头像设置</a>');
          } else {
              document.write('<iframe src="/webview.php/settings/iframe_settings_2" style="width: 100%;height: 250px;border: none;" scrolling="no"></iframe>');
          }
          </script>
          </div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry">
        <div class="entry-container">
          <h2 class="text">用户</h2>
          <div class="summary">
          	<a href="/webview.php/settings/mail">绑定/修改 邮箱</a><br><br>
            <a href="/webview.php/login/changePassword">修改密码 ChangePassword</a><br><br>
            <a href="/webview.php/login/logout">退出登录 Logout</a><br>
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
</body>
</html>
