<?php 
include_once("includes/check_admin.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>>_<</title>
	<style type="text/css">
		body{min-width:1600px;background-color:#eecf6c;}.window{width:1500px;height:800px;margin:0 auto;margin-top:25px;box-shadow:5px 5px 5px #ccc}.window-title{width:100%;height:45px;background-color:#fff}.window-text{width:100%;height:755px;background-color:#000;color:#fff;font-weight:100;font-size:18px;font-family:'mircosoft yahei';resize:none;overflow: auto;}table{border-collapse: 2px;}/*table tr td{width: 10%;}*/.name{text-overflow: ellipsis;overflow: auto;}
	</style>
	
</head>
<body>
	<div class="window">
		<div class="window-title"></div>
			<div class="window-text" disabled="disabled" readonly="readonly" id="text">
				<table style="width: 100%" border="2">
					<tr>
						<td>用户id</td>
						<td>卡组权限</td>
						<td>用户名</td>
						<td>等级</td>
						<td>绑定邮箱</td>
						<td>Lovaca</td>
						<td>金币</td>
						<td>R 贴纸</td>
						<td>SR 贴纸</td>
						<td>SSR 贴纸</td>
						<td>UR 贴纸</td>
					</tr>
					<?php
						require "../../config/database.php";
						$pdo = new PDO("mysql:host=".$mysql_server.";dbname=$mysql_db",$mysql_user,$mysql_pass); 
						$users = $pdo -> query("select * from users")->fetchAll(PDO::FETCH_ASSOC); 
						foreach($users as $i){
							$card_en = $pdo -> query("SELECT value FROM user_params WHERE param = 'enable_card_switch' AND user_id = ".$i['user_id'])->fetchColumn();
							$card_en2 = $pdo -> query("SELECT stat FROM user_card_switch WHERE user_id = ".$i['user_id'])->fetchColumn();
							$lv = $pdo -> query("SELECT level FROM users WHERE user_id = ".$i['user_id'])->fetchColumn();
							$mail = $pdo -> query("SELECT mail FROM users WHERE user_id = ".$i['user_id'])->fetchColumn();
							$item4 = $pdo -> query("SELECT value FROM user_params WHERE param = 'item4' AND user_id = ".$i['user_id'])->fetchColumn();
							$item3 = $pdo -> query("SELECT value FROM user_params WHERE param = 'item3' AND user_id = ".$i['user_id'])->fetchColumn();
							$seal1 = $pdo -> query("SELECT value FROM user_params WHERE param = 'seal1' AND user_id = ".$i['user_id'])->fetchColumn();
							$seal2 = $pdo -> query("SELECT value FROM user_params WHERE param = 'seal2' AND user_id = ".$i['user_id'])->fetchColumn();
							$seal3 = $pdo -> query("SELECT value FROM user_params WHERE param = 'seal3' AND user_id = ".$i['user_id'])->fetchColumn();
							$seal4 = $pdo -> query("SELECT value FROM user_params WHERE param = 'seal4' AND user_id = ".$i['user_id'])->fetchColumn();
							
					?>
					<tr>
						<td><?php print($i['user_id'])?></td>
						<td>
							<?php 
								if(($card_en + $card_en2) > 0){
									print('是');
								}else{
									print('否');
								}
							?>
						</td>
						<td class="name"><?php print($i['name'])?></td>
						<td><?php print($lv)?></td>
						<td class="name"><?php print($mail)?></td>
						<td><?php print($item4)?></td>
						<td><?php print($item3)?></td>
						<td><?php print($seal1)?></td>
						<td><?php print($seal2)?></td>
						<td><?php print($seal3)?></td>
						<td><?php print($seal4)?></td>
					</tr>
					<?php } ?>
				</table>
			</div>
			
			
	</div>
	
</body>
</html>