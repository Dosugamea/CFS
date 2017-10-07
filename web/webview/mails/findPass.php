<html>
	<meta charset='utf-8'/>
	<head>
		<title>重置密码-PLServer</title>
		<link href="/resources/css/web.css" rel="stylesheet">
		<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<style>body{font-size:2em;}table{font-size:1em;}</style>
	</head>
	<body>
	<?php
if(!isset($_GET['uid']) || !isset($_GET['verify']) || !is_numeric($_GET['uid'])){
	header("HTTP 403 Forbidden");
	print("非法访问。");
	die();
}

include("../../../includes/db.php");
$key = $mysql->query("SELECT mail_secret_key FROM users WHERE user_id = ?", [$_GET['uid']])->fetchColumn();
if($key != $_GET['verify']){
	print("非法访问。请检查您的邮件是否过期。");
	die();
}

function genpassv2($_pass, $id) {
	$_pass .= $id;
	$pass = hash('sha512', $_pass);
	$pass .= hash('sha512', str_replace($_pass[0], 'RubyRubyRu', $_pass));
	$pass .= $pass;
	return substr($pass, hexdec(substr(md5($_pass), ord($_pass[0]) % 30, 2)), 32);
}

if(isset($_POST['password'])){
	$pass = genpassv2($_POST['password'], $_GET['uid']);
	$mysql->query("UPDATE users SET login_password = ?, mail_secret_key = Null, username = '', password = '' WHERE user_id = ?", [$pass, $_GET['uid']]);
	print("密码重置成功！");
	die();
}
?>
		<script>
			function beforeSubmit(form){
				if(form.password.value==''){
					alert('密码不能为空');
					form.username.focus();
					return false;
				}
				if(form.pass1.value!=form.password2.value) {
					alert('你两次输入的密码不一致，请重新输入！');
					form.password2.focus();
					return false;
				}
			return true;
			}
		</script>
		</script>
		<div class="header">
		    <a class="header-text">重置密码</a>
		</div>
		<div class="table">
			<form method="post" action="/webview/mails/findPass.php?uid=<?php print($_GET['uid'])?>&verify=<?php print($_GET['verify'])?>" autocomplete="off">
				请输入新密码:
				<div class="table-input">
					<input type="password" name="password" style="height:27px" value="" />
				</div>
				再次输入密码:
				<div class="table-input">
				<input type="password" name="password2" style="height:27px" value="" /><br />
				</div>
				<div class="confirm">
				<input type="submit" name="submit" id="submit" value="确认" />
				</div>
			</form>
		</div>
	</body>
</html>