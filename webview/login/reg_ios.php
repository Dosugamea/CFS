<meta charset='utf-8' />
<style>body{font-size:2em;}table{font-size:1em;}</style>

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

include_once("includes/unit.php");
if(isset($_POST['submit'])) {
	if (!is_numeric($_POST['id'])) {
		echo '<h3><font color="red">错误：ID必须是数字 Error: the ID must be a number</font></h3>';
	} elseif($_POST['id']>999999999) {
		echo '<h3><font color="red">错误：你输入的数太大了！Number is too large</font></h3>';
	} else if(!is_numeric($_POST['site'])){
		echo '<h3><font color="red">错误：提交数据异常</font></h3>';
	} else	{
		$check_uid = $mysql->prepare('SELECT user_id FROM users WHERE user_id=?');
		$check_uid->execute([$_POST['id']]);
		if ($check_uid->rowCount()) {
			echo '<h3><font color="red">错误：此ID已被注册 </font></h3>';
		} else {
			$password = genpassv2($_POST['password'], $_POST['id']);	
			$mysql->prepare('
				INSERT INTO `users` (`user_id`, `username`, `password`,`login_password`, `name`, `introduction`, `download_site`)
				VALUES (?, ?, ?, ?, ?, "", ?)
			')->execute([$_POST['id'], $username['username'], $username['password'], $password, $_POST['name'], $_POST['site']]);
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
		
			//送三个初期宝石
			$mysql->query("INSERT INTO removable_skill (user_id, skill_id, amount, equipped) VALUES(".$_POST['id'].",1,1,0)");
			$mysql->query("INSERT INTO removable_skill (user_id, skill_id, amount, equipped) VALUES(".$_POST['id'].",2,1,0)");
			$mysql->query("INSERT INTO removable_skill (user_id, skill_id, amount, equipped) VALUES(".$_POST['id'].",3,1,0)");
			
			$uid = $_POST['id'];
			if($all_card_by_default) {
				$card_list=$unit->query('select unit_id from unit_m where unit_id<='.$max_unit_id)->fetchAll();
				//$query='INSERT INTO `unit_list` (`user_id`, `unit_id`) VALUES ';
				foreach($card_list as $v){
					addUnit($v[0]);
				}
			//$query.='('.$_POST['id'].', '.$v[0].'),';
				//$query=substr($query, 0,strlen($query)-1);
				//$mysql->exec($query);
			}
			
			$position=1;
			foreach($default_deck_web as $k=>$v) {
				$tmp['position'] = $position;
				$tmp['unit_owning_user_id'] = addUnit($v)[0]['unit_owning_user_id'];
				if($position == 5)
					$center = $tmp['unit_owning_user_id'];
				$unit_deck_detail[] = $tmp;
				$position++;
			}
			
			//$mysql->exec("INSERT INTO album (user_id,unit_id) SELECT DISTINCT {$_POST['id']}, unit_id FROM unit_list WHERE user_id = {$_POST['id']}");
			//修正特典卡的rank
			//$default_rankup = $unit->query('select unit_id from unit_m where unit_m.normal_icon_asset like "%rankup%"')->fetchAll(PDO::FETCH_COLUMN);
			//$mysql->exec('UPDATE unit_list SET rank=2 WHERE user_id='.$_POST['id'].' AND unit_id in('.implode(', ', $default_rankup).')');
			//$mysql->exec('UPDATE album SET rank_max_flag=1 WHERE user_id='.$_POST['id'].' AND unit_id in('.implode(', ', $default_rankup).')');
			
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
    info='你输入的数太大了！';
  else
    info='请输入一个正整数';
  if(valid) {
    var exist_id=new Array(<?=implode(', ', $id)?>);
    for(var i in exist_id) {
      if(parseInt(id.value)==exist_id[i]) {
        valid=false;
        info='错误：指定的ID('+exist_id[i]+')已被使用';
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
<script type="text/javascript">
    console.log('%c 为了您和您的账户安全','background-image:-webkit-gradient( linear, left top, right top, color-stop(0, #f22), color-stop(0.15, #f2f), color-stop(0.3, #22f), color-stop(0.45, #2ff), color-stop(0.6, #2f2),color-stop(0.75, #2f2), color-stop(0.9, #ff2), color-stop(1, #f22) );color:transparent;-webkit-background-clip: text;font-size:3em;');
    console.log('%c 请不要在这里执行任何命令','background-image:-webkit-gradient( linear, left top, right top, color-stop(0, #f22), color-stop(0.15, #f2f), color-stop(0.3, #22f), color-stop(0.45, #2ff), color-stop(0.6, #2f2),color-stop(0.75, #2f2), color-stop(0.9, #ff2), color-stop(1, #f22) );color:transparent;-webkit-background-clip: text;font-size:3em;');
    console.log('%c 否则你的JJ可能不保','background-image:-webkit-gradient( linear, left top, right top, color-stop(0, #f22), color-stop(0.15, #f2f), color-stop(0.3, #22f), color-stop(0.45, #2ff), color-stop(0.6, #2f2),color-stop(0.75, #2f2), color-stop(0.9, #ff2), color-stop(1, #f22) );color:transparent;-webkit-background-clip: text;font-size:3em;');
    console.log("%c", "padding:30px 300px;line-height:120px;background:url('http://app.lovelivesupport.com/pl/jitui.gif');");
  </script>
<style type="text/css">
  .protect{z-index: 555;width: 100%;height: 130px;background-color:transparent;position: absolute;left: 0px;}
  .table-input{z-index: 1;}
</style>
<link href="/resources/css/web.css" rel="stylesheet">


<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<body class="body">
<div class="header">
    <a class="header-text">注册（Signup）</a>
</div>

<div class="table">
  
    <form method="post" action="/webview/login/reg_ios.php" autocomplete="off">
    <div class="protect"></div>
    <div class="table-input">
      <input type="text" name="token" value="<?=$token?>"/>
    </div>
    <div class="table-input">
     <input type="text" name="username" value="<?=$username['username']?>" />
    </div>
    <span class="tittle">用户ID：</span>
    <div class="table-input">
     <input type="text" name="id" id="id"  onkeyup="verify()" onchange="verify()"/><span id="info" style="color:red"></span>
    </div>
    <span class="tittle">昵称：</span>
    <div class="table-input">
      <input type="text" name="name" id="name"  onkeyup="verify()" onchange="verify()"/><br />
    </div>
    <span class="tittle">输入密码：</span>
    <div class="table-input">
      <input type="password" id="pass1" name="password"  onKeyUp="verify2();" onchange="verify2();" /><span id="info2" style="color:red"></span><br />
    </div>
    <span class="tittle">再次输入密码：</span>
    <div class="table-input">
      <input type="password" id="pass2"  onKeyUp="verify2();" onchange="verify2();" />
    </div>

 <div class="first-kawai">
                  <div class="first-kawai-h">数据包下载节点选择</div>
                  <div class="first-kawai-t">
                  <input type="radio" name="site" value="1" checked>中国大陆地区<br>
                  <span style="color: #ff699c;">注:在中国大陆地区下载会加速,中国大陆以外地区下载可能会减速</span><br>
                  <input type="radio" name="site" value="2" >海外地区<br>
                  <span style="color: #ff699c;">注:适用于国际地区,中国大陆地区下载可能会失败</span><br>
                  </div>
                  </div><br>
    <div class="confirm">
      <input type="submit" name="submit" id="submit" value="登录" />
    </div>
     
    </form>
</div>