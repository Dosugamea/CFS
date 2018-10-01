<script type="text/javascript">
	function hexToBase64(str) {
	    return btoa(String.fromCharCode.apply(null,
	      str.replace(/\r|\n/g, "").replace(/([\da-fA-F]{2}) ?/g, "0x$1 ").replace(/ +$/, "").split(" "))
	    );
	}
	function login(){
		var username = $("#usr").val();
		var passwd = $("#passwd").val();
		var rsa = new JSEncrypt();
		var pubKey = `<?=$result['pub_key']?>`;
		rsa.setKey(pubKey);
		passwd = rsa.encrypt(passwd);
		$.ajax({
			method: "POST",
			url: "/webview.php/api",
			dataType: "json",
			data: JSON.stringify({
				"module": "login",
				"action": "doLogin",
				"timeStamp": Date.parse(new Date()) / 1000,
				"payload": {
					"userId": username,
					"password": passwd
				}
			}),
			contentType: "application/json",
			success:function(data){
				if(data.status != 0){
					mdui.snackbar({
 					 	message: "登入失败！<br>错误信息：" + data.errmsg
					});
				}else{
					var mainContainer = document.getElementById("mainContainer");
					mainContainer.style.display = "none"
					mdui.snackbar({
 					 	message: "登入成功！关闭该页面即可进入游戏。"
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
		<a class="mdui-typo-title" style="text-transform:capitalize;">登录</a>
		<div class="mdui-toolbar-spacer"></div>
	</div>
</header>
<div class="mdui-container"<?php if($result['device_type'] == 'ios') print('style="display:none;"'); ?>>
	<div class="doc-container" id="mainContainer">
		<div class="mdui-textfield mdui-textfield-floating-label">
	  		<label class="mdui-textfield-label">用户名</label>
	  		<input class="mdui-textfield-input" type="text" id="usr" maxlength="9" required/>
	 		 <div class="mdui-textfield-error">用户名不能为空</div>
		</div>
		<div class="mdui-textfield mdui-textfield-floating-label">
	  		<label class="mdui-textfield-label">密码</label>
	  		<input class="mdui-textfield-input" type="password" id="passwd" maxlength="64" required/>
	 		 <div class="mdui-textfield-error">密码不能为空</div>
		</div>
		<div class="br"></div>
	  	<input class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme-accent" type="submit" value="登入"  onclick='login()'/>
	</div>
</div>
<div class="mdui-container framecard" <?php if($result['device_type'] == 'other') print('style="display:none;"'); ?>>
	<div class="br"></div>
	<div class="mdui-card" onclick="location.href='native://browser?url=http%3A%2F%2F<?=$_SERVER['SERVER_NAME']?>%2Fwebview%2Flogin%2Flogin_ios.php%3Fusername%3D<?=$result['username']?>'" >
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