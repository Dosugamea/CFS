<?php 
	if('anti-bang' != $_GET['pw']){
		print("<head><title>403 Forbidden</title></head><body bgcolor='white'><center><h1>403 Forbidden</h1></center><hr><center>F**K U 1.3.8</center></body>");
		die();
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>>_<</title>
	<style type="text/css">
		body{min-width:1080px;background-color:#eecf6c;}.window{width:960px;height:500px;margin:0 auto;margin-top:25px;box-shadow:2px 2px 2px #ccc}.window-title{width:100%;height:45px;background-color:#fff}.window-text{width:100%;height:455px;background-color:#000;color:#fff;font-weight:100;font-size:18px;font-family:'mircosoft yahei';resize:none;overflow: auto;}
	</style>
	
</head>
<body>
	<div class="window">
		<div class="window-title"></div>
			<div class="window-text" disabled="disabled" readonly="readonly" id="text">
				<table class="window-text">
				<?php
						require "../config/database.php";
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
	
</body>
</html>