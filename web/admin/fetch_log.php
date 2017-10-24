<?php 
include_once("includes/check_admin.php");
?>
<html>
<head>日志查看</head>
<body>
<form action="fetch_log.php" method="post">
	module：<input type="text" name="module"> 
	action：<input type="text" name="action"> 
	uid：<input type="text" name="user_id"> 
	<input type="submit" value="获取">
</form>
<?php
include("../includes/db.php");
$mysql->query('start transaction');
if(!isset($_POST['module']))
	die();
if(isset($_POST['action']) && isset($_POST['user_id']))
	$logs = $mysql->query('SELECT * FROM log WHERE module = ? AND action = ? AND user_id = ? ORDER BY timestamp DESC limit 100',[$_POST['module'], $_POST['action'], $_POST['user_id']]);
elseif(!isset($_POST['action']) && isset($_POST['user_id']))
	$logs = $mysql->query('SELECT * FROM log WHERE module = ? AND user_id = ? ORDER BY timestamp DESC limit 100',[$_POST['module'], $_POST['user_id']]);
elseif(isset($_POST['action']) && !isset($_POST['user_id']))
	$logs = $mysql->query('SELECT * FROM log WHERE module = ? AND action = ? ORDER BY timestamp DESC limit 100',[$_POST['module'], $_POST['action']]);
else
	$logs = $mysql->query('SELECT * FROM log WHERE module = ? ORDER BY timestamp DESC limit 100',[$_POST['module']]);

while($log_detail = $logs->fetch()){
	print($log_detail['command_num']."<br>");
	print($log_detail['timestamp']."<br>");
	print($log_detail['module']."<br>");
	print($log_detail['action']."<br>");
	print(gzdecode($log_detail['request'])."<br>");
	print(gzdecode($log_detail['response'])."<br>");
}
print("Complete");