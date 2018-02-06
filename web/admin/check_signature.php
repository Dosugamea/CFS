<?php 
include_once("includes/check_admin.php");
?>
<html>
<head><title>检查签名</title></head>
<body>
<form action="check_signature.php" method="post">
	官方签名：<input type="text" name="sign"> 
	<input type="submit" value="开始检查">
</form>
<?php
include("../../includes/db.php");
$mysql->query('start transaction');
if(isset($_POST['sign']))
	$datas = $mysql->query('SELECT * FROM auth_log');
else
	die();
?>
<table border="1">
	<tr>
		<td>ID</td>
		<td>时间</td>
		<td>授权信息</td>
		<td>设备信息</td>
		<td>IP</td>
		<td>数据包版本号</td>
		<td>客户端版本号</td>
	</tr>
	
<?php

while($data = $datas->fetch(PDO::FETCH_ASSOC)){
	$device = json_decode($data['device_data'], true);
	if(isset($device['signature']) && $device['signature'] != $_POST['sign']){
		print("<tr>");
		print("<td>".$data['user_id']."</td>");
		print("<td>".$data['time']."</td>");
		print("<td>".$data['device_data']."</td>");
		print("<td>".$data['hdr_device']."</td>");
		print("<td>".$data['ip']."</td>");
		print("<td>".$data['client_version']."</td>");
		print("<td>".$data['bundle_version']."</td>");
		print("</tr>");
	}
}