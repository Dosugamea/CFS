<?php 
include_once("../includes/check_admin.php");
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
		<h3>自制谱管理</h3>
	</div>
	<div class="window tttt">
		<div class="window-title"></div>
			<div class="window-text" disabled="disabled" readonly="readonly" id="text">
				<table class="window-text" border="2">
				<?php
						require "../../../config/database.php";
						$pdo = new PDO("mysql:host=".$mysql_server.";dbname=$mysql_db",$mysql_user,$mysql_pass); 
						$rs = $pdo -> query("select * from programmed_live order by id"); 
						foreach($rs as $v) {
					?>
					<tr>
						<td>Custom ID:</td>
						<td><?=$v['notes_setting_asset']?></td>
						<td>
						<?php
							print("Type:");
							if($v['category']){
								print("AC街机歌曲");
							}elseif ($v['category'] == NULL) {
								print("普通歌曲");
							}
						?>
						</td>
						<td><a style="color: #ffffff !important;">编辑</a></td>
					</tr>
					<?php } ?>
				</table>
			</div>
	</div>

	<hr><hr>
	<div class="window">
		<h3>自制谱算分</h3>
	</div>
	<div class="window">
		<form action="calcScore.php" method="post">
			<textarea type="text" name="maps" placeholder="请再此处输入SIF格式谱面" style="width: 430px;height: 200px;"></textarea>
			<input type="submit" value="提交">
		</form>
	</div>

</body>
</html>