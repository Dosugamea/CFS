<title>>_<</title>
<?php 
	include_once("includes/check_admin.php");
	if (!isset($_POST['op']) || ($_POST['op'] == NULL)) {
		print("请求有误 <a href='javascript:history.go(-1);'>返回上一页</a>");
		die();
	};

	//链接数据库 并处理请求
	require "../config/database.php";
	$pdo = new PDO("mysql:host=".$mysql_server.";dbname=$mysql_db",$mysql_user,$mysql_pass); 
	$pdo -> query("UPDATE user_params SET value=".$_POST['op']." WHERE param = 'enable_card_switch' AND user_id =".$_POST['user_id']."");
	print("更新成功 <a href='javascript:history.go(-1);'>返回上一页</a>");
	
	//关闭数据库链接
	$pdo = NULL;
?>