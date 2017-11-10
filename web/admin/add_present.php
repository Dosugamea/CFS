<?php 
include_once("includes/check_admin.php");
?>
<html>
<head><title>批量送礼物</title></head>
<body>
<form action="add_present.php" method="post" id="main">
	物品：<select name="item" form="main">
			<option value="loveca">爱心</option>
			<option value="ticket">招募券</option>
		</select><br>
	数量：<input type="text" name="amount"> <br>
	信息：<input type="text" name="description"> <br>
	额外用户筛选参数（SQL，以WHERE开头）：<input type="text" name="extra"> <br>
	<input type="submit" value="提交">
</form>
<?php
if(!isset($_POST['item']) || !isset($_POST['amount']) || !isset($_POST['description']) || !isset($_POST['extra'])){
	die();
}
switch($_POST['item']){
	case "loveca":
		$incentive_item_id = 4;
		$item_id = Null;
		$is_card = 0;
		break;
	case "ticket":
		$incentive_item_id = 1;
		$item_id = Null;
		$is_card = 0;
		break;
	default:
		print("找不到你要加的礼物喵");
		die();
}
include("../../includes/db.php");
$mysql->query('start transaction');
$users = $mysql->query("SELECT user_id FROM users ".$_POST['extra']);
$count = 0;
while($user = $users->fetch(PDO::FETCH_ASSOC)){
	$mysql->query("INSERT INTO incentive_list (user_id, incentive_item_id, item_id, amount, is_card, incentive_message) VALUES(?,?,?,?,?,?)", [$user['user_id'], $incentive_item_id, $item_id, $_POST['amount'], $is_card, $_POST['description']]);
	$count ++;
}
$mysql->query("COMMIT");
print("Successfully inserted ".$count." records");
?>