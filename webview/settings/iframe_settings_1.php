<meta charset='utf-8' />
<style>body{font-size:27px;}table{font-size:1em;}</style>
<?php
$uid=$_SESSION['server']['HTTP_USER_ID'];
$params = [];
foreach ($mysql->query('SELECT * FROM user_params WHERE user_id='.$uid)->fetchAll() as $v) {
  $params[$v['param']] = (int)$v['value'];
}
if(isset($_GET['switch_card']) && $params['enable_card_switch']) {
  $mysql->prepare('UPDATE user_params SET value=? WHERE user_id=? and param="card_switch"')->execute([$_GET['switch_card'], $uid]);
  $mysql->prepare('UPDATE users SET authorize_token = "0" WHERE user_id=?')->execute([$uid]);
  $params['card_switch']=$_GET['switch_card'];
}
?>
<p>您的UID：<?=$uid?>，已<?=($params['card_switch']?'启':'禁')?>用卡片功能。<br />
<?php if($params['enable_card_switch']) : ?>
<a href="/webview.php/settings/iframe_settings_1?switch_card=<?=($params['card_switch']?'0':'1')?>"><?=($params['card_switch']?'禁':'启')?>用卡片功能</a>
<br /><span style="color:red;font-weight:bold">注意：重启游戏后生效。不重启的话任何操作都可能导致客户端崩溃或者“服务器爆炸”！</span>
<?php else : ?>
您无权启用卡片功能。
<?php endif; ?></p>
