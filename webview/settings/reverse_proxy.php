<meta charset='utf-8' />
<style>body{font-size:27px;}table{font-size:1em;}</style>
<?php
$uid=$_SESSION['server']['HTTP_USER_ID'];
$params = [];
foreach ($mysql->query('SELECT * FROM user_params WHERE user_id='.$uid)->fetchAll() as $v) {
	$params[$v['param']] = (int)$v['value'];
}

global $mysql;

if(isset($_GET['site']) && ($_GET['site'] == 1 || $_GET['site'] == 2)) {
	$mysql->query("UPDATE users SET download_site = ".$_GET['site']." WHERE user_id = ".$uid);
	header('Location: /webview.php/settings/index');
}else echo '<h3>内部错误!请关闭本页重试</h3>';