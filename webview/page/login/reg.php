<?php
	$authorize = substr($_SESSION['server']['HTTP_AUTHORIZE'], strpos($_SESSION['server']['HTTP_AUTHORIZE'], 'token=') + 6);
	$token = substr($authorize, 0, strpos($authorize, '&'));
	$tmp_authorize = $mysql->query('select username, password from tmp_authorize where token=?', [$token])->fetch();

	$id = $mysql->query('SELECT user_id FROM users')->fetchAll(PDO::FETCH_COLUMN);
	$id[] = 0;
	print("<script>var exist_id=new Array(".implode(', ', $id).");</script>");

 	$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
 	if(strpos($agent, 'iphone') || strpos($agent, 'ipad') )
 		$device_type = 'ios';
 	else
 		$device_type = 'other';
 	
?>
<script type="text/javascript">
	function doReg(){
		var valid = false;
		var username = $("#usr").val();
		var nickname = $("$nick").val();
		var passwd = $("#pass1").val();
		var passwd2 = $("#pass2").val();
		var inviter = $("#inv").val();
		if(!isNaN(username) && parseInt(username)>0 && parseInt(username)<=999999999){
			valid=true;
			$("#usrVal").innerHTML = '用户ID不能为空';
			$("#usrDiv").deleteClass("mdui-textfield-invalid");
		}else{
			valid=false;
			$("#usrVal").innerHTML = '请输入一个正整数';
			$("#usrDiv").addClass("mdui-textfield-invalid");
		}
		if (valid) {
			$("#usrDiv").deleteClass("mdui-textfield-invalid");
			for(var i in exit_id){
				if(parseInt(username) == exit_id[i]){
					valid = false;
					$("#usrVal").innerHTML = '用户名已存在';
					$("#usrDiv").addClass("mdui-textfield-invalid");
				}else{
					valid = true;
					$("#usrVal").innerHTML = '用户ID不能为空';
					$("#usrDiv").deleteClass("mdui-textfield-invalid");
				}
			}
		}
		if (valid) {
			if (passwd != passwd2 && passwd != '') {
				valid = false;
				$("#pass2Val").innerHTML = '密码不一致';
				$("#pass2Div").addClass("mdui-textfield-invalid");
			}else{
				valid = true;
				$("#pass2Val").innerHTML = '请再次输入密码';
				$("#pass2Div").deleteClass("mdui-textfield-invalid");
			}
		}
		if (valid) {
			reg();
		}
	}

	function hexToBase64(str) {
	    return btoa(String.fromCharCode.apply(null,
	      str.replace(/\r|\n/g, "").replace(/([\da-fA-F]{2}) ?/g, "0x$1 ").replace(/ +$/, "").split(" "))
	    );
	}
	function reg(){
		var username = $("#usr").val();
		var nickname = $("$nick").val();
		var passwd = $("#pass1").val();
		var inviter = $("#inv").val();
		var rsa = new JSEncrypt();
		var pubKey = `<?=$result['pub_key']?>`;
		rsa.setKey(pubKey);
		passwd = rsa.encrypt(passwd);
		$.ajax({
			method: "POST",
			url: "//<?=$_SERVER['SERVER_NAME']?>/webview.php/api",
			dataType: "json",
			data: JSON.stringify({
				"module": "login",
				"action": "doLogin",
				"timeStamp": Date.parse(new Date()) / 1000,
				"payload": {
					"userId": username,
					"nickname": nickname,
					"inviter": inviter,
					"password": passwd
				}
			}),
			contentType: "application/json",
			success:function(data){
				if(data.status != 0){
					mdui.snackbar({
 					 	message: "注册失败！<br>错误信息：" + data.errmsg
					});
				}else{
					mdui.snackbar({
 					 	message: "注册成功！"
					});
				}
			}
		});
	}
</script>
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
		<div class="mdui-textfield mdui-textfield-floating-label" id="usrDiv">
	  		<label class="mdui-textfield-label">用户ID</label>
	  		<input class="mdui-textfield-input" type="text" id="usr" maxlength="9" required/>
	 		<div class="mdui-textfield-error" id="usrVal">用户ID不能为空</div>
	 		<div class="mdui-textfield-helper">用户ID最大9位且只能为数字</div>
		</div>
		<div class="mdui-textfield mdui-textfield-floating-label">
	  		<label class="mdui-textfield-label">昵称</label>
	  		<input class="mdui-textfield-input" type="text" id="nick" maxlength="9" required/>
	 		<div class="mdui-textfield-error">昵称不能为空</div>
	 		<div class="mdui-textfield-helper">昵称最大9位任意字符</div>
		</div>
		<div class="mdui-textfield mdui-textfield-floating-label">
	  		<label class="mdui-textfield-label">密码</label>
	  		<input class="mdui-textfield-input" type="text" id="pass1" maxlength="64" required/>
	 		<div class="mdui-textfield-error">密码不能为空</div>
	 		<div class="mdui-textfield-helper">密码最大64位任意字符</div>
		</div>
		<div class="mdui-textfield mdui-textfield-floating-label" id="pass2Div">
	  		<label class="mdui-textfield-label">密码确认</label>
	  		<input class="mdui-textfield-input" type="text" id="pass2" maxlength="64" required/>
	  		<div class="mdui-textfield-error" id="pass2Val" >请再次输入密码</div>
		</div>
		<div class="mdui-textfield mdui-textfield-floating-label">
	  		<label class="mdui-textfield-label">邀请人用户名</label>
	  		<input class="mdui-textfield-input" type="text" id="inv" maxlength="9"/>
	  		<div class="mdui-textfield-helper">输入邀请人的用户ID（如有）</div>
		</div>
	  	<div class="br"></div>
	  	<input class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme-accent" type="submit" value="注册" onclick="doReg()"/>
	</div>
</div>

<div class="mdui-container framecard"  <?if($device_type == 'other') print('style="display:none;"'); ?>>
	<div class="br"></div>
	<div class="mdui-card" onclick="location.href='native://browser?url=http%3A%2F%2F<?=$_SERVER['SERVER_NAME']?>%2Fwebview%2Flogin%2Freg_ios.php%3Ftoken%3D<?=$token?>%26username%3D<?=$tmp_authorize['username']?>'">
	  	<div class="mdui-card-media">
	    	<img src="/assets/img/apple_out.jpg"/>
	    	<div class="mdui-card-media-covered">
	      		<div class="mdui-card-primary">
	        		<div class="mdui-card-primary-title">我们识别到你的设备为iOS</div>
	        		<div class="mdui-card-primary-subtitle">由于本软件机能受限，暂不支持iOS设备软件内登陆，点击此处调用系统浏览器进行操作</div>
	      		</div>
	    	</div>
	  	</div>
	</div>
</div>