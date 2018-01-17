<meta charset='utf-8' />


<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="width=880, target-densitydpi=device-dpi, user-scalable=no">

<link rel="stylesheet" href="/resources/things/detail.css?">
<link rel="stylesheet" href="/resources/things/perfect-scrollbar.css">
<link rel="stylesheet" href="/resources/things/list2.css">

<script src="/resources/things/perfect-scrollbar.min.js"></script>
<script src="/resources/things/button.js"></script>
<script src="/resources/js/reg.js"></script>

<?php
$authorize = substr($_SESSION['server']['HTTP_AUTHORIZE'], strpos($_SESSION['server']['HTTP_AUTHORIZE'], 'token=') + 6);
$token = substr($authorize, 0, strpos($authorize, '&'));
$tmp_authorize = $mysql->query('select username, password from tmp_authorize where token=?', [$token])->fetch();
require(dirname(__FILE__).'/reg_common.php');

?>
<body>
<div id="outer">
	<div id="inner">
		<div id="header">
			<h2>注册</h2>
			<div id="back"></div>
		</div>

<div id="body">
<div id="container">
<ul id="list">
			<li class="entry"">
				<div class="entry-container">
					<h2 class="text">如果您的设备是 iOS</h2>
					 <a href="native://browser?url=http%3A%2F%2F<?=$_SERVER['SERVER_NAME']?>%2Fwebview%2Flogin%2Freg_ios.php%3Ftoken%3D<?=$token?>%26username%3D<?=$tmp_authorize['username']?>">
					 <div class="summary" style="color: #000000 !important;">
					 iOS用户专用注册链接。若您点击下面的文本框后客户端崩溃，请点此进行登录！<br>
					 iOS use this link to Login,If you client crash when click the text eara under the this box
					</div></a>
					<div class="clearfix"></div>
				</div>
			</li>
			<li class="entry"">
				<div class="entry-container">
					<h2 class="text">注册</h2>
					<div class="summary" >
					<br>
						 <form method="post" action="/webview.php/login/reg" autocomplete="off">
							请输入一个你想使用的ID：
							<input type="text" name="id" id="id" style="height:27px" onkeyup="verify()" onchange="verify()"/>
							<span id="info" style="color:red"></span><br />
							昵称：
							<input type="text" name="name" id="name" style="height:27px" onkeyup="verify()" onchange="verify()"/><br />
							密码：
							<input type="password" id="pass1" name="password" style="height:27px" onKeyUp="verify2();" onchange="verify2();" />
							<span id="info2" style="color:red"></span><br />
							再次输入密码：
							<input type="password" id="pass2" style="height:27px" onKeyUp="verify2();" onchange="verify2();" /><br />
							邀请人（如没有请留空）：
							<input type="text" name="invite" style="height:27px"/><br />

							<br>
								<div class="first-kawai">
									<div class="first-kawai-h">数据包下载节点选择</div>
									<div class="first-kawai-t">
									<input type="radio" name="site" value="1" checked>中国大陆地区<br>
									<span style="color: #ff699c;">注:在中国大陆地区下载会加速,中国大陆以外地区下载可能会减速</span><br>
									<input type="radio" name="site" value="2" >海外地区<br>
									<span style="color: #ff699c;">注:适用于国际地区,中国大陆地区下载可能会失败</span><br>
									</div>
								</div><br>

								<br>	
							<input type="submit" name="submit" id="submit" style="height:30px;width:120px" value="确认/Confirm" disabled="disabled" />
							</form>
						<br>
					</div>
					<div class="clearfix"></div>
				</div>
			</li>
	</ul>
	</div>
</div>
</div>
</div>
<script>
	Button.initialize(document.getElementById('back'), function() {
		window.location.href='/webview.php/login/welcome';
	});
	Ps.initialize(document.getElementById('body'), {suppressScrollX: true});
</script>
</body>
