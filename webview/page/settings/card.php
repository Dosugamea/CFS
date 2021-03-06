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

<?php
$uid=$_SESSION['server']['HTTP_USER_ID'];
$params = [];
foreach ($mysql->query('SELECT * FROM user_params WHERE user_id='.$uid)->fetchAll() as $v) {
  $params[$v['param']] = (int)$v['value'];
}
$unit = getUnitDb();

//读取开卡权限
$query1=$mysql->query('SELECT stat FROM user_card_switch WHERE user_id='.$uid);
if($query1->rowCount()!=0 && $query1->fetchColumn()==1)
  $params['enable_card_switch']=1;

//切换开卡状态
if(isset($_GET['switch_card']) && $params['enable_card_switch']) {
  $mysql->prepare('UPDATE user_params SET value=? WHERE user_id=? and param="card_switch"')->execute([$_GET['switch_card'], $uid]);
  $mysql->prepare('UPDATE users SET authorize_token = "0" WHERE user_id=?')->execute([$uid]);
  $params['card_switch']=$_GET['switch_card'];
}

//开卡申请算心
$query2=$mysql->query('SELECT * FROM user_card_switch WHERE user_from='.$uid);
$loveca=$mysql->query('SELECT value FROM user_params WHERE param="item4" AND user_id='.$uid)->fetchColumn();
$loveca_use=floor(10*pow(sqrt(10),$query2->rowCount()));

//提交开卡申请
if(isset($_GET['target']) && !empty($_GET['target']) && $params['enable_card_switch']) {
  $query1=$mysql->query('SELECT stat FROM user_card_switch WHERE user_id='.(int)$_GET['target']);
  if($query1->rowCount()!=0){
    echo "用户".$_GET['target'].($query1->fetchColumn()==0?" 已经提交过开卡审核":" 已经开卡")."<br />";
  }else{
    
    if($loveca<$loveca_use){
      echo "Loveca不足，需要 ".$loveca_use." loveca，当前 ".$loveca." loveca";
    }else{
    	$target_c = $mysql -> query("SELECT * FROM user_params WHERE user_id = {$_GET['target']} AND param = 'enable_card_switch' LIMIT 1 ")->fetch();
    	if($target_c['value'] == 1){
    		echo "您申请 {$_GET['target']} 用户已经具备卡组权限";
    	}else{

	      	$mysql->prepare('INSERT INTO user_card_switch (user_id, user_from, stat) VALUES (?, ?, 0)')->execute([$_GET['target'],$uid]);
	      	echo "用户".$_GET['target']." 开卡审核已提交<br />";
	      	$loveca-=$loveca_use;
	      	$mysql->query('UPDATE user_params SET value='.$loveca.' WHERE user_id='.$uid.' and param="item4"');
	      	$loveca_use=floor($loveca_use*sqrt(10));
	    }
  	}
  }
}


require '../config/maintenance.php';

$max_album_id=$unit->query('SELECT max(unit_number) FROM unit_m WHERE unit_id<='.$max_unit_id)->fetchColumn();

if(isset($_GET['switch_card']) && $params['enable_card_switch']) {
  $mysql->prepare('UPDATE user_params SET value=? WHERE user_id=? and param="card_switch"')->execute([$_GET['switch_card'], $uid]);
  $mysql->prepare('UPDATE users SET authorize_token = "0" WHERE user_id=?')->execute([$uid]);
  $params['card_switch']=$_GET['switch_card'];
}elseif(isset($_GET['submit']) && $_GET['submit']=='提交') {
  if(is_numeric($_GET['avatar']) && $_GET['avatar']>0 && $_GET['avatar']<=$max_album_id) {
    $rankup=0;
    if(isset($_GET['rankup']))
      $rankup=1;
    $unit_id=$unit->query('SELECT unit_id FROM unit_m WHERE unit_number='.$_GET['avatar'])->fetchColumn();
    $mysql->query('REPLACE INTO user_params values (?, ?, ?)', [$uid, 'extend_avatar', $unit_id]);
    $mysql->query('REPLACE INTO user_params values (?, ?, ?)', [$uid, 'extend_avatar_is_rankup', $rankup]);
    echo '<h3>修改成功！重启游戏后生效。</h3>';
  }
  else echo '<p>输入错误！</p>';
}
?>
<div id="outer">
  <div id="inner">
    <div id="header">
      <h2>卡片设置</h2>
      <div id="back"></div>
    </div>

<div id="body">
<div id="container">
<ul id="list">
      <li class="entry">
        <div class="entry-container">
          <h2 class="text">卡片开关</h2>
          <div class="summary">
            您的UID：<?=$uid?>，已<?=($params['card_switch']?'启':'禁')?>用卡片功能。<br />
            <?php if($params['enable_card_switch']) : ?>
            <?php
            $query1=$mysql->query('SELECT user_from FROM user_card_switch WHERE user_id='.$uid);
            if($query1->rowCount()!=0)
              $result1=$query1->fetchColumn();
            else
              $result1="未知";

            $result2="";
            if($query2->rowCount()!=0)
              while($temp2=$query2->fetch())
                $result2.=$temp2['user_id'].($temp2['stat']==1?" ":"(待审) ");
            else
              $result2="无";

          ?>
          <p>
          您的开卡担保人ID：<?=$result1?><br />
          您担保的开卡者ID：<?=$result2?></p>
            <a href="/webview.php/settings/card?switch_card=<?=($params['card_switch']?'0':'1')?>"><?=($params['card_switch']?'禁':'启')?>用卡片功能</a>
            <br /><span style="color:red;font-weight:bold">
              <form method="get" action="/webview.php/settings/card/">
            下一次担保将扣除 <?=$loveca_use?> loveca, 当前的loveca为 <?=$loveca?><br />
            请输入申请被担保的用户ID：
            <input type="text" name="target" autocomplete="off" id="numkeyboard1" class="numkeyboard"  pattern="[0-9]*" readonly="true"/>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" value="提交" />
            </form>
            <?php else : ?>
            您无权启用卡片功能。
            <?php endif; ?>
            
          </div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry">
        <div class="entry-container">
          <h2 class="text">设置头像</h2>
          <div class="summary">
            <span style="font-weight:bold">设置头像</span><br />您可以设置无卡模式下的排行榜中自己显示的头像。<br /><br />
            <form method="get" action="/webview.php/settings/card">
            请输入卡片的相册ID：<input type="text" name="avatar" autocomplete="off" id="numkeyboard1" class="numkeyboard"  pattern="[0-9]*" readonly="true" />（最大ID：<?=$max_album_id?>）<br />
            <input type="checkbox" name="rankup" value="rankup" />觉醒
            <input type="submit" name="submit" value="提交" />
            </form>
            <key></key>
          </div>
          <div class="clearfix"></div>
        </div>
      </li>
      <li class="entry">
        <div class="entry-container">
          <h2 class="text">相关信息</h2>
          <div class="summary">
          注意：重启游戏后生效。不重启的话任何操作都可能导致客户端崩溃或者“服务器爆炸”！
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
