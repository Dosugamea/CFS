<script type="text/javascript">
	function jumpToExternal(){
		var host = window.location.host;
		location.href="native://browser?url=http%3A%2F%2F" + host + "%2fwebview.php%2flogin%2freg%3fexternal%3dtrue%26token%3d<?=$result['token']?>";
	}
	function getQueryVariable(variable){
       var query = window.location.search.substring(1);
       var vars = query.split("&");
       for (var i=0;i<vars.length;i++) {
               var pair = vars[i].split("=");
               if(pair[0] == variable){return pair[1];}
       }
       return(false);
	}
	function doReg(){
		var valid = false;
		var username = $("#usr").val();
		var nickname = $("#nick").val();
		var passwd = $("#pass1").val();
		var passwd2 = $("#pass2").val();
		var inviter = $("#inv").val();
		if(!isNaN(username) && parseInt(username) > 0 && parseInt(username) <= 999999999){
			valid = true;
			$("#usrVal").innerHTML = '用户ID不能为空';
			$("#usrDiv").removeClass("mdui-textfield-invalid");
		}else{
			valid = false;
			$("#usrVal").innerHTML = '请输入一个正整数';
			$("#usrDiv").addClass("mdui-textfield-invalid");
		}
		if (valid) {
			$("#usrVal").innerHTML = '用户ID不能为空';
			$("#usrDiv").removeClass("mdui-textfield-invalid");
			if (passwd != passwd2 && passwd2 != '') {
				valid = false;
				$("#pass2Val").innerHTML = '密码不一致';
				$("#pass2Div").addClass("mdui-textfield-invalid");
			}else if(passwd == ''){
				valid = false;
				$("#pass2Val").innerHTML = '请再次输入密码';
				$("#pass2Div").removeClass("mdui-textfield-invalid");
			}
		}
		if (valid) {
			reg();
		}
	}

	function reg(){
		var username = $("#usr").val();
		var nickname = $("#nick").val();
		var passwd = $("#pass1").val();
		var inviter = $("#inv").val();
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
				"action": "reg",
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
					var mainContainer = document.getElementById("mainContainer");
					mainContainer.style.display = "none"
					mdui.snackbar({
 					 	message: "注册成功！关闭该页面即可进入游戏。"
					});
				}
			}
		});
	}
	$(function(){
		if(getQueryVariable("external")){
			document.getElementById("iosCover").style.display = "none";
			document.getElementById("arrorBack").style.display = "none";
			document.getElementById("mainContainer").style.display = "inline";
		}
	});
</script>
<header class="mdui-appbar mdui-appbar-fixed">
	<div class="mdui-toolbar mdui-color-theme">
		<span class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white" id="arrorBack">
			<i class="mdui-icon material-icons" onclick="location.href='/webview.php/login/welcome'">arrow_back</i>
		</span>
		<a class="mdui-typo-title" style="text-transform:capitalize;">注册</a>
		<div class="mdui-toolbar-spacer"></div>
	</div>
</header>
<div class="mdui-container" id="mainContainer" <?php if($result['device_type'] == 'ios') print('style="display:none;"'); ?>>
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
	  		<input class="mdui-textfield-input" type="password" id="pass1" maxlength="64" required/>
	 		<div class="mdui-textfield-error">密码不能为空</div>
	 		<div class="mdui-textfield-helper">密码最大64位任意字符</div>
		</div>
		<div class="mdui-textfield mdui-textfield-floating-label" id="pass2Div">
	  		<label class="mdui-textfield-label">密码确认</label>
	  		<input class="mdui-textfield-input" type="password" id="pass2" maxlength="64" required/>
	  		<div class="mdui-textfield-error" id="pass2Val" >请再次输入密码</div>
		</div>
		<div class="mdui-textfield mdui-textfield-floating-label">
	  		<label class="mdui-textfield-label">邀请人用户名</label>
	  		<input class="mdui-textfield-input" type="text" id="inv" maxlength="9"/>
	  		<div class="mdui-textfield-helper">输入邀请人的用户ID（选填）</div>
		</div>
	  	<div class="br"></div>
	  	<input class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme-accent" type="submit" value="注册" onclick="doReg()"/>
	</div>
</div>

<div class="mdui-container framecard" id="iosCover" <?php if($result['device_type'] == 'other') print('style="display:none;"'); ?>>
	<div class="br"></div>
	<div class="mdui-card" onclick="jumpToExternal();">
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