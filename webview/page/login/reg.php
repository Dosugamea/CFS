
<script src="/resources/js/reg.js"></script>

<?php
	$authorize = substr($_SESSION['server']['HTTP_AUTHORIZE'], strpos($_SESSION['server']['HTTP_AUTHORIZE'], 'token=') + 6);
	$token = substr($authorize, 0, strpos($authorize, '&'));
	$tmp_authorize = $mysql->query('select username, password from tmp_authorize where token=?', [$token])->fetch();

	$id = $mysql->query('SELECT user_id FROM users')->fetchAll(PDO::FETCH_COLUMN);
	$id[] = 0;
	print("<script>var exist_id=new Array(".implode(', ', $id).");</script>");

 	/*$user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
 	if(strpos($agent, 'iphone') || strpos($agent, 'ipad') )
 		$device_type = 'ios';
 	else
 		$device_type = 'other';
 	*/
 		$device_type = '111'；
?>
<header class="mdui-appbar mdui-appbar-fixed">
	<div class="mdui-toolbar mdui-color-theme">
		<span class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white" >
			<i class="mdui-icon material-icons" onclick="location.href='/webview.php/login/welcome'">arrow_back</i>
		</span>
		<a class="mdui-typo-title" style="text-transform:capitalize;"><?=$action?></a>
		<div class="mdui-toolbar-spacer"></div>
	</div>
</header>
<div class="mdui-container" <?if($device_type == 'ios') print('style="display:none;"'); ?>>
	<div class="doc-container">
		<form method="post" action="/webview.php/login/reg" autocomplete="off">
			<div class="mdui-textfield mdui-textfield-floating-label">
	  			<label class="mdui-textfield-label">用户名</label>
	  			<input class="mdui-textfield-input" type="text" name="id" maxlength="9" required/>
	 			 <div class="mdui-textfield-error">用户名不能为空</div>
			</div>
			<div class="mdui-textfield mdui-textfield-floating-label">
	  			<label class="mdui-textfield-label">昵称</label>
	  			<input class="mdui-textfield-input" type="text" name="name" maxlength="9" required/>
	 			 <div class="mdui-textfield-error">昵称不能为空</div>
			</div>
			<div class="mdui-textfield mdui-textfield-floating-label">
	  			<label class="mdui-textfield-label">密码</label>
	  			<input class="mdui-textfield-input" type="text" name="password" maxlength="64" required/>
	 			 <div class="mdui-textfield-error">密码不能为空</div>
			</div>
			<div class="mdui-textfield mdui-textfield-floating-label">
	  			<label class="mdui-textfield-label">密码确认</label>
	  			<input class="mdui-textfield-input" type="text" maxlength="64" required/>
			</div>
			<div class="mdui-textfield mdui-textfield-floating-label">
	  			<label class="mdui-textfield-label">邀请人用户名</label>
	  			<input class="mdui-textfield-input" type="text" maxlength="9" required/>

			</div>
	  		<div class="br"></div>
	  		<input class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme-accent" type="submit" value="注册" />
		</form>
	</div>
</div>

<div class="mdui-card" <?if($device_type == 'other') print('style="display:none;"'); ?>>
  	<div class="mdui-card-actions" onclick="location.href='native://browser?url=http%3A%2F%2F<?=$_SERVER['SERVER_NAME']?>%2Fwebview%2Flogin%2Freg_ios.php%3Ftoken%3D<?=$token?>%26username%3D<?=$tmp_authorize['username']?>'">
    	<span>我们识别到你的设备为iOS，点击此处从外部浏览器登陆</span>
  	</div>
</div>