<title>>_<</title>
<?php 
	include_once("includes/check_admin.php");
	//链接数据库
	require "../../config/database.php";
	$pdo = new PDO("mysql:host=".$mysql_server.";dbname=$mysql_db",$mysql_user,$mysql_pass); 

	//管理强制开卡
	if (isset($_POST['op']) && ($_POST['op'] != NULL)) {
	$pdo -> query("UPDATE user_params SET value=".$_POST['op']." WHERE param = 'enable_card_switch' AND user_id =".$_POST['user_id']);
	print("更新成功 <a href='javascript:history.go(-1);'>返回上一页</a>");
	}

	//同意开卡申请
	if(isset($_GET['accept'])){
		//删除旧开卡信息
		$pdo -> query("DELETE FROM user_params WHERE param = 'enable_card_switch' AND user_id =".$_GET['accept']);
		$pdo -> query("UPDATE user_card_switch SET stat = 1 WHERE user_id =".$_GET['accept']);
		print("同意成功 <a href='javascript:history.go(-1);'>返回上一页</a>");
	}

	//拒绝开卡申请
	if(isset($_GET['reject'])){
		$pdo -> query("DELETE FROM user_card_switch WHERE user_id =".$_GET['reject']);
		print("拒绝成功 <a href='javascript:history.go(-1);'>返回上一页</a>");
	}
	
	//关闭数据库链接
	$pdo = NULL;
?>