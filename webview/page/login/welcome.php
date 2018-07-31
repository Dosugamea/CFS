
	<div class="mdui-card">
	  	<div class="mdui-card-media">
	    	<img src="/assets/img/welcome.png"/>
	    	<div class="mdui-card-media-covered">
	      		<div class="mdui-card-primary">
	        		<div class="mdui-card-primary-title">开始使用之前 o(≧v≦)o</div>
	        		<div class="mdui-card-primary-subtitle">
	        			本修改客户端由游戏玩家自行制作，并未获得官方许可。为了您和他人能够继续使用本程序，也为了制作者的安全考虑，请您不要在公开场合发布明显的本程序截图视频等，也不要通过其他途径进行大范围传播，谢谢合作!
	        		</div>
	      		</div>
		      	<div class="mdui-card-actions">
		      		<script type="text/javascript"> var reg = '/webview.php/login/reg';</script>
		        	<button class="mdui-btn mdui-ripple" onclick="location.href='/webview.php/login/login'">登录</button>
		        	<button class="mdui-btn mdui-ripple" <?=($config->reg['allow_reg']?'onclick="location.href= reg "':'style="display:none;"')?>>注册</button>
		      	</div>
	    	</div>
	  	</div>
	</div>
