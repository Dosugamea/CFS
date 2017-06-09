<?php
if(!isset($_GET['uid']) || !isset($_GET['verify']) || !is_numeric($_GET['uid'])){
	header("HTTP 403 Forbidden");
	print("非法访问。");
	die();
}

include("../../includes/db.php");
$key = $mysql->query("SELECT mail_secret_key FROM users WHERE user_id = ?", [$_GET['uid']])->fetchColumn();
if($key == $_GET['verify']){
	$mail_pending = $mysql->query("SELECT mail_pending FROM users WHERE user_id = ?", [$_GET['uid']])->fetchColumn();
	$mail_check = $mysql->query("SELECT mail FROM users WHERE mail = ?", [$mail_pending])->fetchColumn();
	if($mail_check){
		print("此邮箱已被绑定！请重新绑定！");
		die();
	}
	$mysql->query("UPDATE users SET mail_secret_key = Null, mail = mail_pending, mail_pending = Null WHERE user_id = ?", [$_GET['uid']]);
	print("绑定成功！");
}else{
	header("HTTP 403 Forbidden");
	print("非法访问。请检查您的邮件是否过期。");
	die();
}