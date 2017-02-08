<meta charset='utf-8' />

<link href="/resources/bstyle.css" rel="stylesheet">
<link href="/resources/news.css" rel="stylesheet">
<link href="/resources/css/style.css" rel="stylesheet">
<!--<style>body{font-size:2em;}table{font-size:1em;}</style>-->
<SCRIPT type="text/javascript">
var strUA = "";
strUA = navigator.userAgent.toLowerCase();

if(strUA.indexOf("iphone") >= 0) {
  document.write('<meta name="viewport" content="width=100%, minimum-scale=0.45, maximum-scale=0.45, user-scalable=no" />');
} else if (strUA.indexOf("ipad") >= 0) {
  document.write('<meta name="viewport" content="width=100%, minimum-scale=0.9, maximum-scale=0.9, user-scalable=no" />');
} else if (strUA.indexOf("android 2.3") >= 0) {
  document.write('<meta name="viewport" content="width=100%, minimum-scale=0.45, maximum-scale=0.45, initial-scale=0.45, user-scalable=yes" />');
} else {
  document.write('<meta name="viewport" content="width=100%, minimum-scale=0.38, maximum-scale=0.38, user-scalable=no" />');
}
</script>
<!--<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />-->
<?php  require "config/database.php";
require "config/maintenance.php";
require "version.php";
require "info.php"
?>

<DIV id="wrapper_news" style="width: 100% !important">
<div class="title_news fs34" style="width:100%">
  <span class="ml30">关于 About
  </span><a id="back" href="/webview.php/announce/index">
  <div class="topback">
    <img src="/resources/com_button_01.png" data-on="/resources/com_button_02se.png">
  </div>
  </a>
</div>
<div class="content_news_all" style="margin-top:0">
  <div id="box1">
        <div class="title_news_all fs30">
          <span class="ml40">版本信息：</span>
        </div>
        <div class="content_all">
          <div class="note">
            <p>Programmed Live! Server {<?=$pls_version_date?>}<br />客户端版本：<?=(isset($_SESSION['server']["HTTP_BUNDLE_VERSION"]) ? $_SESSION['server']["HTTP_BUNDLE_VERSION"] : '客户端未提交')."(".$_SESSION['server']["HTTP_CLIENT_VERSION"].") 服务器版本：".$bundle_ver."(".$server_ver; ?>)</p>
            <?php
            if ($mysql->query('SELECT length(`login_password`) FROM `users` WHERE `user_id`='.$_SESSION['server']['HTTP_USER_ID'])->fetchColumn() != 32) {
              echo '<b style="color:red">我们升级了服务器的密码存储机制，建议您前往“游戏设置”退出重新登录或修改密码，这将大幅降低服务器被攻击导致密码泄露的风险。</b>';
            } ?>
          </div>
        </div>
    	 <div class="title_news_all fs30">
          <span class="ml40">使用协议:</span>
        </div>
        <div class="content_all">
          <div class="note">
            <p>
            <?=$pls_agreement?>
            </p>
          </div>
        </div>
        <div class="title_news_all fs30">
          <span class="ml40">支持信息</span>
        </div>
        <div class="content_all">
          <div class="note">
            <p>
            维护:<?=$pls_maintenance?><br>
            开发:<?=$pls_dev?><br>
            运营:<?=$pls_operation?><br><br>
            <a href="native://browser?url=mailto:<?=$pls_support_mail?>" style="color:red;" >邮件反馈</a><br>
            </p>
          </div>
        </div>
	</div>
</div>
<DIV class="footer_news_all"><IMG width="100%" src="/resources/bg03.png"> 
</DIV>