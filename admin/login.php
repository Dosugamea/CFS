<?php
session_start();
include_once("../config/database.php");
if(isset($_POST['pw']) && $_POST['pw'] == $admin_pw){
	$_SESSION['admin'] = true;
	header("HTTP/1.1 302 Found");
	header("Location: /admin/index.php");
	die();
}
?>
<html>
<header>
<title>登录-管理后台</title>
</header>
<body>
<h1>请输入密码</h1>
<form action="login.php" method="post"><input type="password" name="pw" ><input type="submit"></form>
</body>
</html>