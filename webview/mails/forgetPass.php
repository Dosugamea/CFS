<html>
	<meta charset='utf-8'/>
	<head>
		<title>忘记密码-PLServer</title>
		<link href="/resources/css/web.css" rel="stylesheet">
		<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<style>body{font-size:2em;}table{font-size:1em;}</style>
		<script type="text/javascript">
			function beforeSubmit(form){
				if(form.mail.value==''){
					alert('邮箱不能为空');
					form.username.focus();
					return false;
				}
				if(form.username.value==''){
					alert('用户名不能为空');
					form.password.focus();
					return false;
				}
			return true;
			}
		</script>
	</head>
	<body>
		<div class="header">
		    <a class="header-text">重置密码</a>
		</div>
		<div class="table">
			<form action="" autocomplete="off" onSubmit="return beforeSubmit(this);">
					请输账户邮箱:
				<div class="table-input">
					<input type="" name="mail" style="height:27px" value="" />
				</div>
					输入用户ID:
				<div class="table-input">
					<input type="" name="username" style="height:27px" value="" /><br />
				</div>
				<div class="confirm">
					<input type="submit" name="submit" id="submit" value="确认" />
				</div>
			</form>
		</div>
	</body>
</html>