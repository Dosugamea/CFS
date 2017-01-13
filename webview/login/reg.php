<meta charset='utf-8' />

<link href="/resources/bstyle.css" rel="stylesheet">
<link href="/resources/news.css" rel="stylesheet">
<link href="/resources/css/style.css" rel="stylesheet">
<!--<style>body{font-size:2em;}table{font-size:1em;}</style>-->
<SCRIPT type="text/javascript">
var strUA = "";
strUA = navigator.userAgent.toLowerCase();

if(strUA.indexOf("iphone") >= 0) {
  document.write('<meta name="viewport" content="width=100%, minimum-scale=0.45, maximum-scale=0.45, user-scalable=no" />');
} else if (strUA.indexOf("ipad") >= 0) {
  document.write('<meta name="viewport" content="width=100%, minimum-scale=0.9, maximum-scale=0.9, user-scalable=no" />');
} else if (strUA.indexOf("android 2.3") >= 0) {
  document.write('<meta name="viewport" content="width=100%, minimum-scale=0.45, maximum-scale=0.45, initial-scale=0.45, user-scalable=yes" />');
} else {
  document.write('<meta name="viewport" content="width=100%, minimum-scale=0.38, maximum-scale=0.38, user-scalable=no" />');
}
</script>
<!--<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />-->

<?php
require 'config/reg.php';
if(!$allow_reg) {
  echo '<h1>注册已关闭！</h1>';
  die();
}

if($enable_ssl && $_SERVER['HTTPS'] != 'on') {
  header('Location: https://'.$ssl_domain.$_SERVER['REQUEST_URI']);
  die();
}

$unit = getUnitDb();

$authorize = substr($_SESSION['server']['HTTP_AUTHORIZE'], strpos($_SESSION['server']['HTTP_AUTHORIZE'], 'token=') + 6);
$token = substr($authorize, 0, strpos($authorize, '&'));
$username = $mysql->query('select username, password from tmp_authorize where token=?', [$token])->fetch();
if (!$username) {
  echo '<h1>出现了错误，请关闭此页面重新进入</h1>';
  die();
}

require 'config/maintenance.php';

$id = $mysql->query('SELECT user_id FROM users')->fetchAll(PDO::FETCH_COLUMN);
$id[] = 0;

function genpassv2($_pass, $id) {
  $_pass .= $id;
  $pass = hash('sha512', $_pass);
  $pass .= hash('sha512', str_replace($_pass[0], 'RubyRubyRu', $_pass));
  $pass .= $pass;
  return substr($pass, hexdec(substr(md5($_pass), ord($_pass[0]) % 30, 2)), 32);
}

if(isset($_POST['submit'])) {
  if (!is_numeric($_POST['id'])) {
    echo '<h3><font color="red">错误：ID必须是数字 Error: the ID must be a number</font></h3>';
  } elseif($_POST['id']>999999999) {
    echo '<h3><font color="red">错误：你输入的数太大了！Number is too large</font></h3>';
  } else {
    $check_uid = $mysql->prepare('SELECT user_id FROM users WHERE user_id=?');
    $check_uid->execute([$_POST['id']]);
    if ($check_uid->rowCount()) {
      echo '<h3><font color="red">错误：此ID已被注册 </font></h3>';
    } else {
      $password = genpassv2($_POST['password'], $_POST['id']);
      $mysql->prepare('
        INSERT INTO `users` (`user_id`, `username`, `password`,`login_password`, `name`, `introduction`)
        VALUES (?, ?, ?, ?, ?, "")
      ')->execute([$_POST['id'], $username['username'], $username['password'], $password, $_POST['name']]);
      $param = $mysql->prepare('INSERT INTO user_params VALUES('.$_POST['id'].', ?, ?)');
      $param->execute(['enable_card_switch', $disable_card_by_default ? 0 : 1]);
      $param->execute(['card_switch', $disable_card_by_default ? 0 : 1]);
      $param->execute(['random_switch', 0]);
      $param->execute(['allow_test_func', 0]);
      $param->execute(['item1', 0]);
      $param->execute(['item2', 0]);
      $param->execute(['item3', 2525200]);
      $param->execute(['item4', 0]);
      $param->execute(['item5', 0]);
      
      if($all_card_by_default) {
        $card_list=$unit->query('select unit_id from unit_m where unit_id<='.$max_unit_id)->fetchAll();
        $query='INSERT INTO `unit_list` (`user_id`, `unit_id`) VALUES ';
        foreach($card_list as $v)
          $query.='('.$_POST['id'].', '.$v[0].'),';
        $query=substr($query, 0,strlen($query)-1);
        $mysql->exec($query);
      }
      
      $position=1;
      foreach($default_deck_web as $k=>$v) {
        $mysql->exec("INSERT INTO `unit_list` (`user_id`, `unit_id`) VALUES ('{$_POST['id']}', '$v');");
        $tmp['position']=$position;
        $tmp['unit_owning_user_id']=(int)$mysql->lastInsertId();
        if($position==5)
          $center=$tmp['unit_owning_user_id'];
        $unit_deck_detail[]=$tmp;
        $position++;
      }
      
      $mysql->exec("INSERT INTO album (user_id,unit_id) SELECT DISTINCT {$_POST['id']}, unit_id FROM unit_list WHERE user_id = {$_POST['id']}");
      //修正特典卡的rank
      $default_rankup = $unit->query('select unit_id from unit_m where unit_m.normal_icon_asset like "%rankup%"')->fetchAll(PDO::FETCH_COLUMN);
      $mysql->exec('UPDATE unit_list SET rank=2 WHERE user_id='.$_POST['id'].' AND unit_id in('.implode(', ', $default_rankup).')');
      $mysql->exec('UPDATE album SET rank_max_flag=1 WHERE user_id='.$_POST['id'].' AND unit_id in('.implode(', ', $default_rankup).')');
      
      $tmp2['unit_deck_detail']=$unit_deck_detail;
      $tmp2['unit_deck_id']=1;
      $tmp2['main_flag']=true;
      $tmp2['deck_name']='';
      $unit_deck_list[]=$tmp2;
      $json=json_encode($unit_deck_list);
      $mysql->exec("INSERT INTO user_deck (user_id,json,center_unit) VALUES ({$_POST['id']}, '$json', $center)");
      
      $mysql->query('delete from tmp_authorize where token=?', [$token]);
      echo '<h3>注册成功！关闭本窗口即可进入游戏 <br />Registration Success! Plz Close This Window <br />若关闭窗口后仍然无法进入游戏，或者进入游戏时游戏崩溃，请通知开发者！</h3>';
      die();
    }
  }
}
?>
<script>
var valid,valid2;
function verify() {
  valid=false;
  var info='';
  var id=document.getElementById('id');
  if(!isNaN(id.value) && parseInt(id.value)>0 && parseInt(id.value)<=999999999)
    valid=true;
  else if(parseInt(id.value)>999999999)
    info='你输入的数太大了！ Number is too large';
  else
    info='请输入一个正整数 Please enter a positive integer';
  if(valid) {
    var exist_id=new Array(<?=implode(', ', $id)?>);
    for(var i in exist_id) {
      if(parseInt(id.value)==exist_id[i]) {
        valid=false;
        info='错误：指定的ID('+exist_id[i]+')已被使用 Error: The ID('+exist_id[i]+')be Used';
      }
    }
  }
  if(valid) {
    id.style.backgroundColor='#00FF00';
  } else {
    id.style.backgroundColor='#FF0000';
  }
  document.getElementById('info').innerText=info;
  if(document.getElementById('name').value=='') {
    valid=false;
    document.getElementById('name').style.backgroundColor='#FF0000';
  } else document.getElementById('name').style.backgroundColor='#00FF00';
  verify3();
}
function verify2() {
  verify();
  valid2=true;
  var info='';
  var t1=document.getElementById('pass1');
  var t2=document.getElementById('pass2');
  if(t1.value=='') {
    valid2=false;
    t1.style.backgroundColor='#FF0000';
    info='请输入密码 Plz enter the password'
  } else {
    t1.style.backgroundColor='#00FF00';
  }
  if(t1.value!=t2.value && t1.value!='') {
    valid2=false;
    t2.style.backgroundColor='#FF0000';
    info='两次输入的密码不一致 Two entered passwords do not match'
  } else if(t1.value=='') {
    t2.style.backgroundColor='#FF0000';
  } else {
    t2.style.backgroundColor='#00FF00';
  }
  document.getElementById('info2').innerText=info;
  verify3();
}
function verify3() {
  if(valid && valid2) {
    document.getElementById('submit').disabled=false;
  } else {
    document.getElementById('submit').disabled=true;
  }
}
</script>

<DIV id="wrapper_news" style="width: 100% !important">
<div class="title_news fs34" style="width:100%">
  <span class="ml30">注册 Sign Up
  </span><a id="back" href="/webview.php/login/welcome">
  <div class="topback">
    <img src="/resources/com_button_01.png" data-on="/resources/com_button_02se.png">
  </div>
  </a>
</div>
<div class="content_news_all" style="margin-top:0">
  <div id="box1">
    <div class="title_news_all fs30">
      <span class="ml40">iOS用户专用注册链接(iOS special registration link)</span>
    </div>
    <div class="content_all">
      <div class="note">
      <a href="native://browser?url=http%3A%2F%2F<?=$_SERVER['SERVER_NAME']?>%2Fwebview%2Flogin%2Freg_ios.php%3Ftoken%3D<?=$token?>%26username%3D<?=$username['username']?>">iOS用户专用注册链接。若您点击下面的文本框后客户端崩溃，请点此进行注册！<br>
      iOS plz use this to Sign Up,If you click the text eara under the this box
      </a>
      </div>
    </div>
    <div class="title_news_all fs30">
      <span class="ml40">普通注册(Normal SignUp)</span>
    </div>
    <div class="content_all">
      <div class="note">
        请输入一个你想使用的ID Input the UserID：<input type="text" name="id" id="id" style="height:27px" onkeyup="verify()" onchange="verify()"/><span id="info" style="color:red"></span><br />
        昵称 Nickname：<input type="text" name="name" id="name" style="height:27px" onkeyup="verify()" onchange="verify()"/><br />
        密码 Password：<input type="password" id="pass1" name="password" style="height:27px" onKeyUp="verify2();" onchange="verify2();" /><span id="info2" style="color:red"></span><br />
        再次输入密码 Confirm：<input type="password" id="pass2" style="height:27px" onKeyUp="verify2();" onchange="verify2();" /><br /><br />
        <input type="submit" name="submit" id="submit" style="height:30px;width:120px" value="确认/Confirm" disabled="disabled" />
        <?php if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS']!='on') echo '<h3><span style="color:red;font-size:2vw;">警告：将通过不安全的连接发送您的密码。请避免使用任何使用过的密码。</span></h3>' ?>
      </div>
    </div>
  </div>
</div>
<DIV class="footer_news_all"><IMG width="100%" src="/resources/bg03.png"> 
</DIV>



<!--<h3>用户注册</h3>
<h3><a href="native://browser?url=http%3A%2F%2F<?=$_SERVER['SERVER_NAME']?>%2Fwebview%2Flogin%2Freg_ios.php%3Ftoken%3D<?=$token?>%26username%3D<?=$username['username']?>">iOS用户专用注册链接。若您点击下面的文本框后客户端崩溃，请点此进行注册！</a></h3>

<form method="post" action="/webview.php/login/reg" autocomplete="off">
请输入一个你想使用的ID：<input type="text" name="id" id="id" style="height:27px" onkeyup="verify()" onchange="verify()"/><span id="info" style="color:red"></span><br />
昵称：<input type="text" name="name" id="name" style="height:27px" onkeyup="verify()" onchange="verify()"/><br />
密码：<input type="password" id="pass1" name="password" style="height:27px" onKeyUp="verify2();" onchange="verify2();" /><span id="info2" style="color:red"></span><br />
再次输入密码：<input type="password" id="pass2" style="height:27px" onKeyUp="verify2();" onchange="verify2();" /><br /><br />

<?php if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS']!='on') echo '<h3><span style="color:red">警告：将通过不安全的连接发送您的密码。请避免使用任何使用过的密码。</span></h3>' ?>
<input type="submit" name="submit" id="submit" style="height:30px;width:50px" value="注册" disabled="disabled" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="/webview.php/login/welcome">返回</a>
</form>
<table><tr><td height="200px"></td></tr></table>-->