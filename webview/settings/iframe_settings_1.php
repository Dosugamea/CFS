<meta charset='utf-8' />
<style>body{font-size:27px;}table{font-size:1em;}</style>
<?php
$uid=$_SESSION['server']['HTTP_USER_ID'];
$params = [];
foreach ($mysql->query('SELECT * FROM user_params WHERE user_id='.$uid)->fetchAll() as $v) {
  $params[$v['param']] = (int)$v['value'];
}

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
      $mysql->prepare('INSERT INTO user_card_switch (user_id, user_from, stat) VALUES (?, ?, 0)')->execute([$_GET['target'],$uid]);
      echo "用户".$_GET['target']." 开卡审核已提交<br />";
      $loveca-=$loveca_use;
      $mysql->query('UPDATE user_params SET value='.$loveca.' WHERE user_id='.$uid.' and param="item4"');
      $loveca_use=floor($loveca_use*sqrt(10));
  }
  }
}
?>
<p>您的UID：<?=$uid?>，已<?=($params['card_switch']?'启':'禁')?>用卡片功能。<br />
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
<a href="/webview.php/settings/iframe_settings_1?switch_card=<?=($params['card_switch']?'0':'1')?>"><?=($params['card_switch']?'禁':'启')?>用卡片功能</a><br />
<span style="color:red;font-weight:bold">注意：重启游戏后生效。不重启的话任何操作都可能导致客户端崩溃或者“服务器爆炸”！</span><br />
<form method="get" action="/webview.php/settings/iframe_settings_1">
下一次担保将扣除 <?=$loveca_use?> loveca, 当前的loveca为 <?=$loveca?><br />
请输入申请被担保的用户ID：<input type="text" name="target" autocomplete="off" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="submit" value="提交" />
</form>
<?php else : ?>
您无权启用卡片功能。
<?php endif; ?></p>
