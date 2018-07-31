<meta charset='utf-8' />


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
  #num{
    color: red;
  }
</style>

<?php

if($config->reg['enable_ssl'] && $_SERVER['HTTPS'] != 'on') {
  header('Location: https://'.$config->reg['ssl_domain'].$_SERVER['REQUEST_URI']);
  die();
}

$authorize=substr($_SESSION['server']['HTTP_AUTHORIZE'], strpos($_SESSION['server']['HTTP_AUTHORIZE'], 'token=') + 6);
$token=substr($authorize, 0, strpos($authorize, '&'));

function genpassv2($_pass, $id) {
  $_pass .= $id;
  $pass = hash('sha512', $_pass);
  $pass .= hash('sha512', str_replace($_pass[0], 'RubyRubyRu', $_pass));
  $pass .= $pass;
  return substr($pass, hexdec(substr(md5($_pass), ord($_pass[0]) % 30, 2)), 32);
}

if(isset($_POST['submit'])) {
  if ($mysql->prepare('UPDATE users SET login_password=? WHERE authorize_token=? AND user_id=?')->execute([genpassv2($_POST['password'], $_SESSION['server']['HTTP_USER_ID']), $token, $_SESSION['server']['HTTP_USER_ID']])) {
    echo '<h3>密码设置成功！</h3>';
  } else {
    echo '<h3><font color="red">密码设置失败，请通知开发者！</font></h3>';
  }
}

?>
<script>
function verify2() {
  valid2=true;
  var info='';
  var t1=document.getElementById('pass1');
  var t2=document.getElementById('pass2');
  if(t1.value=='') {
    valid2=false;
    t1.style.backgroundColor='#FF0000';
    info='请输入密码'
  } else {
    t1.style.backgroundColor='#00FF00';
  }
  if(t1.value!=t2.value && t1.value!='') {
    valid2=false;
    t2.style.backgroundColor='#FF0000';
    info='两次输入的密码不一致'
  } else if(t1.value=='') {
    t2.style.backgroundColor='#FF0000';
  } else {
    t2.style.backgroundColor='#00FF00';
  }
  document.getElementById('info2').innerText=info;
  if(valid2) {
    document.getElementById('submit').disabled=false;
  } else {
    document.getElementById('submit').disabled=true;
  }
}
</script>
<body>
<div id="outer">
  <div id="inner">
    <div id="header">
      <h2>修改密码</h2>
      <div id="back"></div>
    </div>

<div id="body">
<div id="container">
<ul id="list">
	 <li class="entry"">
        <div class="entry-container">
          <h2 class="text">修改密码</h2>
          <div class="summary" >

	        <form method="post" action="changePassword" autocomplete="off">
	        <p>
	          新密码：<input type="password" id="pass1 numkeyboard1" name="password" style="height:27px" onKeyUp="verify2();" onchange="verify2();" class="numkeyboard"  pattern="[0-9]*" readonly="readonly"/>
	          <span id="info2" style="color:red"></span><br />
	          再次输入密码：<input type="password" id="pass2 numkeyboard1" style="height:27px" onKeyUp="verify2();" onchange="verify2();"  class="numkeyboard"  pattern="[0-9]*" readonly="readonly"/><br /><br />
	         </p>
	          <input type="submit" name="submit" id="submit" style="height:30px;width:120px" value="确认/Confirm"/>
	          </form>
	          <key></key>
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
</body>