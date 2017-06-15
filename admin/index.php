<?php 
	require "../config/database.php";
	if(!isset($_GET['pw']) || ($_GET['pw'] !=$admin_pw)){
		print('<h1>请输入密码</h1><form action="index.php"><input type="text" name="pw" ><input type="submit"></form>');
		die();
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>>_<</title>
	<style type="text/css">
		body{min-width:1600px;background-color:#eecf6c;}a{text-decoration: none;}.window{width:1500px;height:800px;margin:0 auto;margin-top:25px;box-shadow:5px 5px 5px #ccc}.window-title{width:100%;height:45px;background-color:#fff}.window-text{width:100%;height:755px;background-color:#000;color:#fff;font-weight:100;font-size:18px;font-family:'mircosoft yahei';resize:none;overflow: auto;}table{}/*table tr td{width: 10%;}*/.name{text-overflow: ellipsis;overflow: auto;}.index-icon{width: 100%;background-color: #FB0094;border:2px solid #FFFFFF;border-radius: 15px;box-shadow: 2px 2px 2px #ccc;color: #ffffff;}.index-table{width: 500px;margin:0 auto;margin-top: 50px;text-align: center;}.index-table tr td{width: 50%;}
	</style>
</head>
<body>
	<table class="index-table" cellspacing="25">
		<tr>
			<td>
				<a href="user.php?pw=<? print($_GET['pw'])?>">
					<div class="index-icon">用户详情</div>
				</a>
			</td>
			<td>
				<a href="card_en.php?pw=<? print($_GET['pw'])?>">
					<div class="index-icon">卡组权限用户</div>
				</a>
			</td>
		</tr>
	</table>
</body>
</html>