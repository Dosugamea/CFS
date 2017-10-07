<?php 
include_once("includes/check_admin.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>>_<</title>
	<style type="text/css">
		body{min-width:1080px;background-color:#eecf6c;}.window{width:960px;margin:0 auto;height: auto;}.tttt{height:500px;margin-top:25px;box-shadow:2px 2px 2px #ccc}.window-title{width:100%;height:45px;background-color:#fff}.window-text{width:100%;height:455px;background-color:#000;color:#fff;font-weight:100;font-size:18px;font-family:'mircosoft yahei';resize:none;overflow: auto;}
	</style>
	
</head>
<body>
	<div class="window">
		<h3>下方是已经拥有卡组权限的账户:</h3>
	</div>
	<div class="window tttt">
		<div class="window-title"></div>
			<div class="window-text" disabled="disabled" readonly="readonly" id="text">
				<table class="window-text" border="2">
				<?php
						require "../../config/database.php";
						$pdo = new PDO("mysql:host=".$mysql_server.";dbname=$mysql_db",$mysql_user,$mysql_pass); 
						$rs = $pdo -> query("select a.*,b.* from user_params as a left join users as b on a.user_id = b.user_id where param = 'enable_card_switch' and value =1"); 
						foreach($rs as $v) {
					?>
					<tr>
						<td>用户ID:</td>
						<td><?=$v['user_id']?></td>
						<td>用户名:</td>
						<td><?=$v['name']?></td>
					</tr>
					<?php } ?>
				</table>
			</div>
	</div>
	<div class="window">
		<h3>下方输入用户ID 进行开启和关闭卡组权限(PS:1代表开启 0代表关闭)</h3>
		<form action="card_en_bg.php" method="post">
			ID：<input type="text" name="user_id"> 
			<input type="submit" name="op" value="1">
			<input type="submit" name="op" value="0">
		</form>
	</div>
	<br>
	<hr><hr><br>
	<div class="window">
		<h3>下方是已被封禁的用户:</h3>
	</div>
	<div class="window tttt">
		<div class="window-title"></div>
			<div class="window-text" disabled="disabled" readonly="readonly" id="text">
				<table class="window-text" border="2">
					<tr>
						<td>用户ID</td>
						<td>用户名</td>
						<td>封禁信息</td>
					</tr>
				<?php
						require "../config/database.php";
						$pdo = new PDO("mysql:host=".$mysql_server.";dbname=$mysql_db",$mysql_user,$mysql_pass); 
						$rs = $pdo -> query("select a.*,b.* from banned_user as a left join users as b on a.user = b.user_id"); 
						foreach($rs as $v) {
					?>
					<tr>
						<td><?=$v['user']?></td>
						<td><?=$v['name']?></td>
						<td><?=$v['msg']?></td>
					</tr>
					<?php } ?>
				</table>
			</div>
	</div>
	<div class="window">
		<h3>封禁用户</h3>
		<form action="user_banned.php" method="post">
			ID：<input type="text" name="user_id"> <br>
			封禁信息: <input type="text" name="msg"><br>
			<input type="submit" value="ban" name="ban">
		</form><br>
		<h3>解封用户</h3>
		<form action="user_banned.php" method="post">
			ID：<input type="text" name="user_id"> <br>
			<input type="text" name="msg" value="**" style="display: none !important;"><br>
			<input type="submit" value="unban" name="ban">
		</form>
	</div>
	<br>
	<hr><hr><br>
</body>
</html>