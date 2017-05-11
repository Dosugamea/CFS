<meta charset='utf-8' />
<style>body{font-size:27px;}table{font-size:1em;}</style>
<?php
$uid=$_SESSION['server']['HTTP_USER_ID'];
$params = [];
foreach ($mysql->query('SELECT * FROM user_params WHERE user_id='.$uid)->fetchAll() as $v) {
	$params[$v['param']] = (int)$v['value'];
}

global $mysql;

if(isset($_POST['site']) && ($_POST['site'] == 1 || $_POST['site'] == 2)) {
	$mysql->query("UPDATE users SET download_site = ".$_POST['site']." WHERE user_id = ".$uid);
	echo '<h3>修改成功！</h3>';
}else echo '<h3>内部错误！</h3>';