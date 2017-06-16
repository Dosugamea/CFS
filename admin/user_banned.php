<title>>_<</title>
<?php 
	//验证请求是否合法为空 为空返回错误
	if(!isset($_POST['user_id']) || ($_POST['user_id'] == NULL)){
		print("请求有误 <a href='javascript:history.go(-1);'>返回上一页</a>");
		die();
	}elseif (!isset($_POST['msg']) || ($_POST['msg'] == NULL)) {
		print("请求有误 <a href='javascript:history.go(-1);'>返回上一页</a>");
		die();
	}elseif (!isset($_POST['ban']) || ($_POST['ban'] == NULL)) {
		print("请求有误 <a href='javascript:history.go(-1);'>返回上一页</a>");
		die();
	};
	//链接数据库 并处理请求
	require "../config/database.php";
	$pdo = new PDO("mysql:host=".$mysql_server.";dbname=$mysql_db",$mysql_user,$mysql_pass); 
	//分别处理封禁与解封
	switch ($_POST['ban']) {
		case 'unban':
			$pdo -> query("DELETE FROM banned_user WHERE user = ".$_POST['user_id']."");
			print("解封成功 <a href='javascript:history.go(-1);'>返回上一页</a>");
			break;
		default:
			$pdo -> query("INSERT INTO banned_user VALUES ('".$_POST['user_id']."','".$_POST['msg']."');");
			$pdo -> query("commit");
			print("封禁成功 <a href='javascript:history.go(-1);'>返回上一页</a>");
			break;
	};
	//关闭数据库链接
	$pdo = NULL;
?>