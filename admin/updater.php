<?php 
	require "../config/database.php";
	include_once("includes/check_admin.php");
	print("开始更新...<br>");

	print("清除临时文件...<br>");
		exec("rm download/unit.db_");
		exec("rm download/live.db_");
		exec("rm download/marathon.db_");
		exec("rm download/conf.json");
	print("清除完成<br>");

	print("获取密钥...<br>");
		exec("python /download/getkey.py");
	print("获取完成<br>");
		
	print("下载数据库...<br>");
		exec("wget https://dl-plserver.lovelivesupport.com/external/db/live/unit.db_ -P download");
		exec("wget https://dl-plserver.lovelivesupport.com/external/db/live/live.db_ -P download");
		exec("wget https://dl-plserver.lovelivesupport.com/external/db/live/marathon.db_ -P download");
		print("下载完成<br>");

	print("解密数据库...<br>");
		exec("node llcrypt.js -d download");
		print("解密完成<br>");

	print("解密AES...<br>");
		exec("python download/decrypt.py unit.db_");
		exec("python download/decrypt.py unit.db_");
		exec("python download/decrypt.py marathon.db_");
		print("解密完成<br>");

	print("移动文件...<br>");
		exec("mv download/unit.db_ ../db/");
		exec("mv download/live.db_ ../db/");
		exec("mv download/marathon.db_ ../db/");
		print("移动完成");

	print("更新完成!<br>");



?>