<?php
function genBandMail($uid, $code){
	global $mysql;
	$mail = '<html>
	<head>
		<meta charset="utf-8" />
		<style type="text/css">
			.qmbox .hover-link, .qmbox .hover-link:hover, .qmbox .hover-link:visited {color:#000;}
			.qmbox .hover-link.small-text {color: #f00;}
			.qmbox .hover-link.small-text:hover, .qmbox .hover-link.small-text:visited {color:#f00;}
			.qmbox appleLinks a, .qmbox .appleLinksWhite a {color:#02a8e6 !important; text-decoration: none;}
			.qmbox p, .qmbox td, .qmbox span { -webkit-text-size-adjust:none; }
			.qmbox .long-link {
				-ms-word-break: break-all;
			    word-break: break-all;
			    word-break: break-word;
				-webkit-hyphens: auto;
			    -moz-hyphens: auto;
			    hyphens: auto;
			}
			.qmbox style, .qmbox script, .qmbox head, .qmbox link, .qmbox meta {display: none !important;}
		</style>
	</head>
	<body>
        <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #f3f3f3;">
        	<tr>
				<td align="left" colspan="3" height="14">
					<img src="http://app.llsupport.cn/wiki/img/spancer.gif" width="1" height="1" style="display: block; border:none;">
				</td>
			</tr>
			<tr>
				<td align="left" width="14">
					<img src="http://app.llsupport.cn/wiki/img/spancer.gif" width="14" style="display: block; border:none;">
				</td>
				<td align="left" width="572">
					<table width="572" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td height="75" width="572" style="background:#3d3d3d; color:#ffffff; font-family: Helvetica, Arial, sans-serif; font-size: 22px; font-weight: bold; padding:0 20px;">
								Programmed Live!
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<table width="572" cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td width="25" align="left">
											<img src="http://app.llsupport.cn/wiki/img/spancer.gif" width="25" style="display: block; border:none;">
										</td>
										<td width="522" align="left">
											<table width="522" cellpadding="0" cellspacing="0" border="0">
												<br />
												<tr>
													<td width="522" style="color: #000001; font-size: 20px; font-family: Helvetica, Arial, sans-serif;">
														Dear ';
	$mail .= $mysql->query('SELECT name FROM users WHERE user_id = ?', [$uid])->fetchColumn();
	$mail .= '<br><br>感谢使用 PL！<br>Thanks for using Programmed Live!
													</td>
												</tr>
												<tr>
													<td width="522" align="left" height="30"><img src="http://app.llsupport.cn/wiki/img/spancer.gif" height="30" style="display: block; border:none;"></td>
												</tr>
												<tr>
													<td width="522" style="color: #000001; font-size: 14px; font-family: Helvetica, Arial, sans-serif;">
														您请求绑定邮箱<br>
														You requestd to bind your mail<br><br>
														您账户的验证链接为:<br>
														Your verification link is: <br>
													</td>
												</tr>
												<tr>
													<td width="522" style="color: #000001; font-size: 24px; font-family: Helvetica, Arial, sans-serif;"><span class="appleLinksWhite">
														<br>
														<a href="https://plserver.xyz/webview/mails/bindMail.php?uid='.$uid.'&verify='.$code;
	$mail .= '" style="color: #02a8e6; text-decoration: none; font-weight: bold;"> <span >点击此处绑定邮箱<br>Click here to bind your mail</span></a>
													</td>
												</tr>
												<tr>
													<td width="522" align="left" height="30"><img src="http://app.llsupport.cn/wiki/img/spancer.gif" height="30" style="display: block; border:none;"></td>
												</tr>
												<tr>
													<td width="522" align="left" height="30"><img src="http://app.llsupport.cn/wiki/img/spancer.gif" height="30" style="display: block; border:none;"></td>
												</tr>
												<tr>
													<td width="522" align="left" height="30"><img src="http://app.llsupport.cn/wiki/img/spancer.gif" height="30" style="display: block; border:none;"></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
							<td align="left" width="14"><img src="http://app.llsupport.cn/wiki/img/spancer.gif" width="14" style="display: block; border:none;"></td>
						</tr>						
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>	';
	return $mail;
}

function genFindPassMail($uid, $code){
	global $mysql;
	$mail = '<html>
	<head>
		<meta charset="utf-8" />
		<style type="text/css">
			.qmbox .hover-link, .qmbox .hover-link:hover, .qmbox .hover-link:visited {color:#000;}
			.qmbox .hover-link.small-text {color: #f00;}
			.qmbox .hover-link.small-text:hover, .qmbox .hover-link.small-text:visited {color:#f00;}
			.qmbox appleLinks a, .qmbox .appleLinksWhite a {color:#02a8e6 !important; text-decoration: none;}
			.qmbox p, .qmbox td, .qmbox span { -webkit-text-size-adjust:none; }
			.qmbox .long-link {
				-ms-word-break: break-all;
			    word-break: break-all;
			    word-break: break-word;
				-webkit-hyphens: auto;
			    -moz-hyphens: auto;
			    hyphens: auto;
			}
			.qmbox style, .qmbox script, .qmbox head, .qmbox link, .qmbox meta {display: none !important;}
		</style>
	</head>
	<body>
        <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #f3f3f3;">
        	<tr>
				<td align="left" colspan="3" height="14">
					<img src="http://app.llsupport.cn/wiki/img/spancer.gif" width="1" height="1" style="display: block; border:none;">
				</td>
			</tr>
			<tr>
				<td align="left" width="14">
					<img src="http://app.llsupport.cn/wiki/img/spancer.gif" width="14" style="display: block; border:none;">
				</td>
				<td align="left" width="572">
					<table width="572" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td height="75" width="572" style="background:#3d3d3d; color:#ffffff; font-family: Helvetica, Arial, sans-serif; font-size: 22px; font-weight: bold; padding:0 20px;">
								Programmed Live!
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<table width="572" cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td width="25" align="left">
											<img src="http://app.llsupport.cn/wiki/img/spancer.gif" width="25" style="display: block; border:none;">
										</td>
										<td width="522" align="left">
											<table width="522" cellpadding="0" cellspacing="0" border="0">
												<br />
												<tr>
													<td width="522" style="color: #000001; font-size: 20px; font-family: Helvetica, Arial, sans-serif;">
														Dear ';
	$mail .= $mysql->query('SELECT name FROM users WHERE user_id = ?', [$uid])->fetchColumn();
	$mail .= '<br><br>感谢使用 PL！<br>Thanks for using Programmed Live!
													</td>
												</tr>
												<tr>
													<td width="522" align="left" height="30"><img src="http://app.llsupport.cn/wiki/img/spancer.gif" height="30" style="display: block; border:none;"></td>
												</tr>
												<tr>
													<td width="522" style="color: #000001; font-size: 14px; font-family: Helvetica, Arial, sans-serif;">
														您请求找回密码<br>
														You requestd to reset your password.<br><br>
														您账户的验证链接为:<br>
														Your verification link is: <br>
													</td>
												</tr>
												<tr>
													<td width="522" style="color: #000001; font-size: 24px; font-family: Helvetica, Arial, sans-serif;"><span class="appleLinksWhite">
														<br>
														<a href="https://plserver.xyz/webview/mails/findPass.php?uid='.$uid.'&verify='.$code;
	$mail .= '" style="color: #02a8e6; text-decoration: none; font-weight: bold;"> <span >点击此处重置密码<br>Click here to reset your password</span></a>
													</td>
												</tr>
												<tr>
													<td width="522" align="left" height="30"><img src="http://app.llsupport.cn/wiki/img/spancer.gif" height="30" style="display: block; border:none;"></td>
												</tr>
												<tr>
													<td width="522" align="left" height="30"><img src="http://app.llsupport.cn/wiki/img/spancer.gif" height="30" style="display: block; border:none;"></td>
												</tr>
												<tr>
													<td width="522" align="left" height="30"><img src="http://app.llsupport.cn/wiki/img/spancer.gif" height="30" style="display: block; border:none;"></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
							<td align="left" width="14"><img src="http://app.llsupport.cn/wiki/img/spancer.gif" width="14" style="display: block; border:none;"></td>
						</tr>						
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>	';
	return $mail;
}

function randomKey($pw_length){ 
	$randpwd = ''; 
	for ($i = 0; $i < $pw_length; $i++){ 
		$randpwd .= chr(mt_rand(33, 126));
	}
	return $randpwd; 
}
?>
<meta charset='utf-8' />
<style>body{font-size:2em;}table{font-size:1em;}</style>
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
  #keyboard_5xbogf8c{top: 430px !important;left: 30px !important;}
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
<style type="text/css">
a{color: #000000;}
</style>
<?php
//校验访问合法性
foreach (explode('&', $_SESSION['server']['HTTP_AUTHORIZE']) as $v) {
	$v = explode('=', $v);
	$authorize[$v[0]] = $v[1];
}

$uid=$_SESSION['server']['HTTP_USER_ID'];

$check = $mysql->query('SELECT user_id FROM users WHERE user_id = ? AND authorize_token = ?', [$uid, $authorize['token']])->fetchColumn();
if(!$check){
	header('HTTP/1.1 403 Forbidden');
	echo '<h1>非法访问</h1>';
	die();
}

$mail = $mysql->query('SELECT mail FROM users WHERE user_id='.$uid)->fetchColumn();
include_once("../includes/sendmail.php");
if(isset($_GET['submit']) && $_GET['submit']=='绑定') {
	$check = $mysql->query('SELECT mail FROM users WHERE mail = ?', [$_GET['mail']])->fetchColumn();
	if($check){
		echo '<h3>该邮箱已被绑定！  <a href="javascript:history.go(-1);">返回</a></h3>';
		die();
	}
	$code = base64_encode(randomKey(32));
	$mysql->query('UPDATE users SET mail_pending = ?, mail_secret_key = ? WHERE user_id = ?', [$_GET['mail'], $code, $uid]);
	sendMail($_GET['mail'], '绑定邮箱', genBandMail($uid, $code));
	echo '<h3>绑定申请已提交，请到邮箱内查收。</h3>';
}

if(isset($_GET['submit']) && $_GET['submit']=='提交') {
	$check = $mysql->query('SELECT mail FROM users WHERE mail = ?', [$_GET['mail']])->fetchColumn();
	if(!$check){
		echo '<h3>账号不存在！  <a href="javascript:history.go(-1);">返回</a></h3>';
		die();
	}
	$uid = $mysql->query('SELECT user_id FROM users WHERE mail = ?', [$_GET['mail']])->fetchColumn();
	$code = base64_encode(randomKey(32));
	$mysql->query('UPDATE users SET mail_secret_key = ? WHERE mail = ?', [$code, $_GET['mail']]);
	sendMail($_GET['mail'], '找回密码', genFindPassMail($uid, $code));
	echo '<h3>重置密码申请已提交，请到邮箱内查收。</h3>';
}
?>
<div id="outer">
  <div id="inner">
    <div id="header">
      <h2>邮箱设置</h2>
      <div id="back"></div>
    </div>

<div id="body">
<div id="container">
<ul id="list">
      <li class="entry"">
        <div class="entry-container">
          <h2 class="text">邮箱绑定/修改</h2>
          <div class="summary">
            <span>您当前绑定的邮箱:<?php $mail?print($mail):print("暂未绑定")?></span><br><br>
            <form method="" action="">
            请输入绑定邮箱<input type="text" name="mail" autocomplete="off" id="numkeyboard1" class="numkeyboard" readonly="true"/><br>
            <input type="submit" name="submit" value="绑定" />
            </form>
            <key></key>
          </div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry"">
        <div class="entry-container">
          <h2 class="text">忘记密码</h2>
          <div class="summary">
          在下方输入您账号绑定的邮箱，我们会将重置密码的链接发送到您的邮箱。<br>
            <form method="" action="">
            请输入账号绑定的邮箱<input type="text" name="mail" autocomplete="off" id="numkeyboard1" class="numkeyboard" readonly="true"/><br>
            <input type="submit" name="submit" value="提交" />
            </form>
            <key></key>

            <!--<a href="mailto:lijun00326@gmail.com"><p>进行账号申诉</p></a>-->
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
    window.location.href='/webview.php/settings/index';
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
