<?php
include_once("includes/check_admin.php");
?>
<!DOCTYPE html>
<html>
<head>
	<title>>_<</title>
	<style type="text/css">
		body{background-color:#eecf6c;}a{text-decoration: none;}.window{width:1500px;height:800px;margin:0 auto;margin-top:25px;box-shadow:5px 5px 5px #ccc}.window-title{width:100%;height:45px;background-color:#fff}.window-text{width:100%;height:755px;background-color:#000;color:#fff;font-weight:100;font-size:18px;font-family:'mircosoft yahei';resize:none;overflow: auto;}table{}/*table tr td{width: 10%;}*/.name{text-overflow: ellipsis;overflow: auto;}.index-icon{width: 100%;background-color: #FB0094;border:2px solid #FFFFFF;border-radius: 15px;box-shadow: 2px 2px 2px #ccc;color: #ffffff;}.index-table{width: 500px;margin:0 auto;margin-top: 50px;text-align: center;}.index-table tr td{width: 50%;}
	</style>
</head>
<body>
	<table class="index-table" cellspacing="25">
		<tr>
			<td>
				<div class="index-icon"><a href="user.php">用户详情</a></div>
			</td>
			<td>
				<div class="index-icon"><a href="card_en.php">用户管理</a></div>
			</td>
		</tr>
		<tr>
			<td>
				<div class="index-icon"><a href="custom/index.php">自制谱功能</a></div>
			</td>
			<td>
				<div class="index-icon"><a href="updateCode.php">部署master代码</a></div>
			</td>
		</tr>
		<tr>
			<td>
				<div class="index-icon"><a href="fetch_log.php">获取日志</a></div>
			</td>
		</tr>
	</table>
</body>
</html>