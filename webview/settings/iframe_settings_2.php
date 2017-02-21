<meta charset='utf-8' />
<style>body{font-size:27px;}table{font-size:1em;}</style>
<?php
$uid=$_SESSION['server']['HTTP_USER_ID'];
$params = [];
foreach ($mysql->query('SELECT * FROM user_params WHERE user_id='.$uid)->fetchAll() as $v) {
  $params[$v['param']] = (int)$v['value'];
}
$unit = getUnitDb();

require 'config/maintenance.php';

$max_album_id=$unit->query('SELECT max(unit_number) FROM unit_m WHERE unit_id<='.$max_unit_id)->fetchColumn();

if(isset($_GET['submit']) && $_GET['submit']=='提交') {
  if(is_numeric($_GET['avatar']) && $_GET['avatar']>0 && $_GET['avatar']<=$max_album_id) {
    $rankup=0;
    if(isset($_GET['rankup']))
      $rankup=1;
    $unit_id=$unit->query('SELECT unit_id FROM unit_m WHERE unit_number='.$_GET['avatar'])->fetchColumn();
    $mysql->query('REPLACE INTO user_params values (?, ?, ?)', [$uid, 'extend_avatar', $unit_id]);
    $mysql->query('REPLACE INTO user_params values (?, ?, ?)', [$uid, 'extend_avatar_is_rankup', $rankup]);
    echo '<h3>修改成功！重启游戏后生效。</h3>';
  }
  else echo '<h3>输入错误！</h3>';
}
?>
<form method="get" action="/webview.php/settings/iframe_settings_2">
请输入卡片的相册ID：<input type="text" name="avatar" autocomplete="off" />（最大ID：<?=$max_album_id?>）<br /><input type="checkbox" name="rankup" value="rankup" />觉醒&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="submit" value="提交" /></form>