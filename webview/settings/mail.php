<meta charset='utf-8' />
<style>body{font-size:2em;}table{font-size:1em;}</style>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="width=880, target-densitydpi=device-dpi, user-scalable=no">

<link rel="stylesheet" href="/resources/things/detail.css?">
<link rel="stylesheet" href="/resources/things/perfect-scrollbar.css">
<link rel="stylesheet" href="/resources/things/list2.css">

<script src="/resources/things/perfect-scrollbar.min.js"></script>
<script src="/resources/things/button.js"></script>

<!--KeyBoard-->
<link rel="stylesheet" type="text/css" href="/resources/key/ios7keyboard.css">
<script type="text/javascript" src="/resources/key/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="/resources/key/ios7keyboard.js"></script>
<style type="text/css">
  #keyboard_5xbogf8c{top: 430px !important;left: 30px !important;}
</style>
<script>
  var num = 1589740651036;

  function printNumber(numDigits) {
    numDigits = numDigits || 10;
    
    var str = String(num);

    for (var i = 0; i < numDigits - str.length; i++) {
      document.write('<img src="http://cf-static-prod.lovelive.ge.klabgames.net/resources/img/thanksgiving/counter00.png" class="etc">');
    }
    for (var i = 0; i < str.length; i++) {
      var d = str.charAt(i);
      document.write('<img src="http://cf-static-prod.lovelive.ge.klabgames.net/resources/img/thanksgiving/counter0' + d + '.png" class="etc">');
    }
  }

  function printIcon(target) {
    if (num >= target) {
      document.write('<img src="http://cf-static-prod.lovelive.ge.klabgames.net/resources/img/thanksgiving/e_icon_02.png" class="etc">');
    } else {
      document.write('<img src="http://cf-static-prod.lovelive.ge.klabgames.net/resources/img/thanksgiving/e_icon_01.png" class="etc">');
    }
  }
</script>
<style type="text/css">
a{color: #000000;}
</style>
<div id="outer">
  <div id="inner">
    <div id="header">
      <h2>卡片设置</h2>
      <div id="back"></div>
    </div>

<div id="body">
<div id="container">
<ul id="list">
      <li class="entry"">
        <div class="entry-container">
          <h2 class="text">邮箱绑定/修改</h2>
          <div class="summary">
            <span>您当前绑定的邮箱:{{NULL}}</span><br><br>
            <form method="" action="">
            请输入绑定邮箱<input type="text" name="" autocomplete="off" id="numkeyboard1" class="numkeyboard"  pattern="[0-9]*" /><br>
            请输入当前账号的密码<input type="text" name="" autocomplete="off" id="numkeyboard1" class="numkeyboard"  pattern="[0-9]*" /><br>
            <input type="submit" name="submit" value="提交" />
            </form>

            <key></key>
          </div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry"">
        <div class="entry-container">
          <h2 class="text">忘记密码</h2>
          <div class="summary">
          在下方输入您账号绑定的邮箱 我们会自动发送密码到您绑定的邮箱 为了您的账号安全 建议忘记密码后更改密码<br>
            <form method="" action="">
            请输入账号绑定的邮箱<input type="text" name="" autocomplete="off" id="numkeyboard1" class="numkeyboard"  pattern="[0-9]*" /><br>
            <input type="submit" name="submit" value="提交" />
            </form>
            <key></key>

            <a href="mailto:"><p>进行账号申诉</p></a>
          </div>
          <div class="clearfix"></div>
        </div>
      </li>
</ul>

</div>
 </div>
  </div>
</div>


      
    </div>
  </div>
</div>

<script>
  Button.initialize(document.getElementById('back'), function() {
    window.location.href='/webview.php/settings/index';
  });
  Ps.initialize(document.getElementById('body'), {suppressScrollX: true});
</script>
<script type="text/javascript">
                $(document).ready(function(){ 
                  $(".numkeyboard").ioskeyboard({
                    keyboardRadix:80,
                    keyboardRadixMin:30,
                    keyboardRadixChange:false,
                    keyfixed:false,
                    clickeve:false,
                    colorchange:false,
                    colorchangeStep:1,
                    colorchangeMin:154
                  });
                })  
</script>
