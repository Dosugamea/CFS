<meta charset='utf-8' />
<style>body{font-size:27px;}table{font-size:1em;}</style>
<?php
$uid=$_SESSION['server']['HTTP_USER_ID'];
$params = [];
foreach ($mysql->query('SELECT * FROM user_params WHERE user_id='.$uid)->fetchAll() as $v) {
	$params[$v['param']] = (int)$v['value'];
}

global $mysql;

<<<<<<< HEAD
if(isset($_GET['site']) && ($_GET['site'] == 1 || $_GET['site'] == 2)) {
	$mysql->query("UPDATE users SET download_site = ".$_GET['site']." WHERE user_id = ".$uid);
=======

if(isset($_POST['site']) && ((int)$_POST['site'] == 1 || (int)$_POST['site'] == 2)) {
	$mysql->query("UPDATE users SET download_site = ".$_POST['site']." WHERE user_id = ".$uid);
>>>>>>> origin/master
	header('Location: /webview.php/settings/index');
}else{
	echo '<h3>内部错误!请关闭本页重试</h3>';
	var_dump($_POST);
	var_dump($uid);
}