<html>
	<meta charset='utf-8'/>
	<head>
		<title>忘记密码-PLServer</title>
		<link href="/resources/css/web.css" rel="stylesheet">
		<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<style>body{font-size:2em;}table{font-size:1em;}</style>
		
<?php
include("../../includes/db.php");
include("../../includes/sendmail.php");

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
	$mail .= '<br><br>感谢使用 PL！<br>Thanks for using Custom Festival!
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

if(isset($_POST['mail'])){
	$check = $mysql->query('SELECT mail FROM users WHERE mail = ?', [$_POST['mail']])->fetchColumn();
	if(!$check){
		echo '<h3>账号不存在！  <a href="javascript:history.go(-1);">返回</a></h3>';
		die();
	}
	$uid = $mysql->query('SELECT user_id FROM users WHERE mail = ?', [$_POST['mail']])->fetchColumn();
	$code = base64_encode(randomKey(32));
	$mysql->query('UPDATE users SET mail_secret_key = ? WHERE mail = ?', [$code, $_POST['mail']]);
	sendMail($_POST['mail'], '找回密码', genFindPassMail($uid, $code));
	echo '<h3>重置密码申请已提交，请到邮箱内查收。</h3>';
}
?>
		<script type="text/javascript">
			function beforeSubmit(form){
				if(form.mail.value==''){
					alert('邮箱不能为空');
					form.username.focus();
					return false;
				}
			return true;
			}
		</script>
	</head>
	<body>
		<div class="header">
		    <a class="header-text">重置密码</a>
		</div>
		<div class="table">
			<form method="post" action="/webview/mails/forgetPass.php" autocomplete="off" onSubmit="return beforeSubmit(this);">
					请输账户绑定的邮箱:
				<div class="table-input">
					<input type="" name="mail" style="height:27px" value="" />
				</div>
				<div class="confirm">
					<input type="submit" name="submit" id="submit" value="提交" />
				</div>
			</form>
		</div>
	</body>
</html>