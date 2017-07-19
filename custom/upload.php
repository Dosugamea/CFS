<html>
<head>
<meta charset="utf-8">
<title>自制谱在线提交</title>
</head>
<body>
<h1>自制谱在线提交</h1>
<form action="submit_custom.php" method="post" enctype="multipart/form-data">
	项目名：custom_<input type="text" name="proj_name">【只能包含英文小写字母和下划线！】<br>
	<label for="file">音频文件：</label>
	<input type="file" name="audio" id="file"><br>
	<label for="file">封面图片：</label>
	<input type="file" name="cover" id="file"><br>
	谱面json：
	<input type="text" name="notes"><br>
	谱面名：<input type="text" name="name"><br>
	属性：<input type="text" name="attribute"><br>
	谱面等级：<input type="text" name="level"><br>
	打歌背景ID：<input type="text" name="bg"><br>
	标题第二行作词、作曲：<input type="text" name="subtitle"><br>
	<input type="submit" name="submit" value="提交">
</form>

</body>
</html>