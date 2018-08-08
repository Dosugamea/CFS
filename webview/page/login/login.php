<header class="mdui-appbar mdui-appbar-fixed">
	<div class="mdui-toolbar mdui-color-theme">
		<span class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white" >
			<i class="mdui-icon material-icons" onclick="location.href='/webview.php/login/welcome'">arrow_back</i>
		</span>
		<a class="mdui-typo-title" style="text-transform:capitalize;">登录</a>
		<div class="mdui-toolbar-spacer"></div>
	</div>
</header>
<div class="mdui-container" <?if($result['device_type'] == 'ios') print('style="display:none;"'); ?>>
	<div class="doc-container">
		<form autocomplete="off">
			<div class="mdui-textfield mdui-textfield-floating-label">
	  			<label class="mdui-textfield-label">用户名</label>
	  			<input class="mdui-textfield-input" type="text" id="usr" maxlength="9" required/>
	 			 <div class="mdui-textfield-error">用户名不能为空</div>
			</div>
			<div class="mdui-textfield mdui-textfield-floating-label">
	  			<label class="mdui-textfield-label">密码</label>
	  			<input class="mdui-textfield-input" type="text" id="passwd" maxlength="64" required/>
	 			 <div class="mdui-textfield-error">密码不能为空</div>
			</div>
			<div class="br"></div>
	  		<input class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme-accent" value="登入" />
		</form>
	</div>
</div>
<div class="mdui-container framecard" <?if($result['device_type'] == 'other') print('style="display:none;"'); ?>>
	<div class="br"></div>
	<div class="mdui-card" onclick="location.href='native://browser?url=http%3A%2F%2F<?=$_SERVER['SERVER_NAME']?>%2Fwebview%2Flogin%2Flogin_ios.php%3Ftoken%3D<?=$token?>%26username%3D<?=$result['username']?>'" >
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

<script type="text/javascript">
	//加密没想好用哪个zz
	function login(){
		var timestamp = Date.parse(new Date());
		var username = $("#usr").val();
		var passwd = $("passwd").val();
		$$.ajax({
			type:"post",
			url:"",
			dataType:"json",
			data:{
				"module":"login";
				"action":"doLogin";
				"timeStamp":timestamp,
				"payload":{
					"userId":username,
					"password":passwd
				}
			},
			success:function(json){
				if(json.status != 0){
					mdui.snackbar({
 					 	message: "登入失败！<br>错误信息："+json.errmsg;
					});
				}else{
					mdui.snackbar({
 					 	message: "登入成功！";
					});
				}
			}
		});
	}
</script>