<?php
if(!isset($_GET['uid']) || !isset($_GET['verify']) || !is_numeric($_GET['uid'])){
	header("HTTP 403 Forbidden");
	print("非法访问。");
	die();
}

include("../../includes/db.php");
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
<html>
	<meta charset='utf-8'/>
	<head><title>重置密码-PLServer</title></head>
	<body>
		<script>
			var valid2;
			function verify2() {
			verify();
			valid2=true;
			var info='';
			var t1=document.getElementById('pass1');
			var t2=document.getElementById('pass2');
			if(t1.value=='') {
				valid2=false;
				t1.style.backgroundColor='#FF0000';
				info='请输入密码'
			} else {
				t1.style.backgroundColor='#00FF00';
			}
			if(t1.value!=t2.value && t1.value!='') {
				valid2=false;
				t2.style.backgroundColor='#FF0000';
				info='两次输入的密码不一致'
			} else if(t1.value=='') {
				t2.style.backgroundColor='#FF0000';
			} else {
				t2.style.backgroundColor='#00FF00';
			}
			document.getElementById('info2').innerText=info;
			verify3();
			}
		</script>
		<form method="post" action="/webview/mails/findPass.php?uid=<?php print($_GET['uid'])?>&verify=<?php print($_GET['verify'])?>" autocomplete="off">
			请输入新密码:
			<input type="password" id="pass1" name="password" style="height:27px" onKeyUp="verify2();" onchange="verify2();" />
			<span id="info2" style="color:red"></span><br />
			再次输入密码:
			<input type="password" id="pass2" style="height:27px" onKeyUp="verify2();" onchange="verify2();" /><br />
			<input type="submit" name="submit" id="submit" value="确认" />
		</form>
	</body>
</html>