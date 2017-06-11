<?php require('config/reg.php'); ?>

<!doctype html>
<html>
<head>
<meta charset='utf-8' />


<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="width=880, target-densitydpi=device-dpi, user-scalable=no">

<link rel="stylesheet" href="/resources/things/detail.css?">
<link rel="stylesheet" href="/resources/things/perfect-scrollbar.css">
<link rel="stylesheet" href="/resources/things/list2.css">

<script src="/resources/things/perfect-scrollbar.min.js"></script>
<script src="/resources/things/button.js"></script>

<style type="text/css">
	a:link,a:visited{text-decoration:none;color: #ffffff;}
	.Welcome-Header{font-size: 4vw;}
	.summary{
		width: 770px !important;
	}
	.main-icon{
		width: 100%;
		border: 2px solid #ccc;
		box-shadow: 2px 2px 4px #ccc;
		border-radius: 10px;
		background-color:#FD689A;
		padding-top:5px;
		padding-bottom: 5px;
	}
	.Welcome-Icon{width: 89%;margin-right: 5%;margin-left: 5%;text-align: center;}
</style>

</head>

<body>
<div id="outer">
  <div id="inner">
    <div id="header">
      <h2>Welcome to Programmed Live!</h2>
    </div>

<div id="body">
<div id="container">
<ul id="list">

	<li class="entry"">
        <div class="entry-container">
          <h2 class="text">开始使用之前 o(≧v≦)o</h2>
          <div class="summary" >
			<p>使用协议：<br />
			1、除了官方钦定的宣传方式外，严禁在公开场合（贴吧、微博等）发布与PL有关的任何消息、截图、视频等
			（特别的，在公开场合公开了群号的QQ群属于公开场合）<br />
			2、若私下传播，则必须确保【所有看到消息的人也遵守前面一条】</p><br>
			<p>Usage agreement:<br>
			1, is strictly prohibited in public (like Twitter) publishing any messages associated with the PL, screenshots and videos<br>
			2,Private communications, you must make sure that "all who saw the message also to comply with the previous"</p>
          </div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry"">
        <div class="entry-container">
          <h2 class="text">开始使用吧 (*^o^*)</h2>
          <div class="summary" >
			<table class="Welcome-Icon" cellspacing="20">
				<tr>
					<td>
						<a href="/webview.php/login/login">
							<div class="main-icon" style="font-size:3vw;">
								登录
							</div>
						</a>
					</td>
					<td>
						<?=($allow_reg?'<a href="/webview.php/login/reg"><div class="main-icon" style="font-size:3vw;">注册 </div></a>':'已关闭注册')?>
					</td>
				</tr>
			</table>
          </div>
          <div class="clearfix"></div>
        </div>
      </li><br>

	




  </ul>
  </div>
</div>
</div>
</div>
<script>
  Ps.initialize(document.getElementById('body'), {suppressScrollX: true});
</script>

</body>
</html>