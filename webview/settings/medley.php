<meta charset='utf-8' />
<style>body{font-size:2em;}table{font-size:1em;}</style>
<SCRIPT type="text/javascript">
var strUA = "";
strUA = navigator.userAgent.toLowerCase();

if(strUA.indexOf("iphone") >= 0) {
  document.write('<meta name="viewport" content="width=960px, minimum-scale=0.45, maximum-scale=0.45, user-scalable=no" />');
} else if (strUA.indexOf("ipad") >= 0) {
  document.write('<meta name="viewport" content="width=1024px, minimum-scale=0.9, maximum-scale=0.9, user-scalable=no" />');
} else if (strUA.indexOf("android 2.3") >= 0) {
  document.write('<meta name="viewport" content="width=960px, minimum-scale=0.45, maximum-scale=0.45, initial-scale=0.45, user-scalable=yes" />');
} else {
  document.write('<meta name="viewport" content="width=960px, minimum-scale=0.38, maximum-scale=0.38, user-scalable=no" />');
}
</script>
<?php
$uid=$_SESSION['server']['HTTP_USER_ID'];
$live = getLiveDb();

$difficulty=[null, 'Easy', 'Normal', 'Hard', 'Expert', null, 'Master'];

if(isset($_GET['count']) && is_numeric($_GET['count']) && isset($_GET['difficulty']) && is_numeric($_GET['difficulty']) && isset($_GET['medley']) && is_numeric($_GET['medley'])) {
  $mysql->prepare('DELETE FROM extend_medley_bind WHERE user_id=? and count=? and difficulty=? and medley=?')->execute([$uid, $_GET['count'], $_GET['difficulty'], $_GET['medley']]);
  $mysql->prepare('INSERT INTO extend_medley_bind VALUES(null,?,?,?,?)')->execute([$uid, $_GET['difficulty'], $_GET['count'], $_GET['medley']]);
}

if(isset($_GET['erase']) && is_numeric($_GET['erase'])) {
  $mysql->exec('DELETE FROM extend_medley_bind WHERE ID='.$_GET['erase']);
}

if(isset($_GET['add'])) {
  $mysql->prepare('INSERT INTO extend_medley (medley_id,user_id,medley_type) VALUES(null,?,?)')->execute([$uid, $_GET['add']]);
  $id=$mysql->lastInsertId();
  $mysql->exec("INSERT INTO extend_medley_song_30 VALUES (null, $id, 'Live_0350.json', 0, 1),(null, $id, 'Live_0068.json', 0, 2),(null, $id, 'Live_0075.json', 0, 3)");
}

if(isset($_GET['add_song'])) {
  $mysql->prepare('INSERT INTO extend_medley_song_30 VALUES (null,?,?,?,?)')->execute([$_GET['add_song'], $_GET['id'], $_GET['random'], $_GET['count']]);
  $mysql->prepare('UPDATE extend_medley SET song_count=? WHERE medley_id=? and medley_type=0')->execute([$_GET['count'], $_GET['add_song']]);
}
if(isset($_GET['edit_song'])) {
  $mysql->prepare('UPDATE extend_medley_song_30 SET notes_setting_asset=?, random_switch=? WHERE ID=?')->execute([$_GET['id'], $_GET['random'], $_GET['edit_song']]);
}

if(isset($_GET['delete_song'])) {
  $mysql->prepare('DELETE FROM extend_medley_song_30 WHERE ID=?')->execute([$_GET['delete_song']]);
  $mysql->prepare('UPDATE extend_medley SET song_count=? WHERE medley_id=? and (medley_type=0 or song_count=? ) and song_count>2')->execute([$_GET['count'], $_GET['medley_id'], $_GET['count']+1]);
}

if(isset($_GET['change_count']) && is_numeric($_GET['change_count']) && isset($_GET['medley_id']) && is_numeric($_GET['medley_id'])) {
  if($_GET['change_count']>1 && $_GET['change_count']<=$_GET['max_count']) {
    $mysql->prepare('UPDATE extend_medley SET song_count=? WHERE medley_id=?')->execute([$_GET['change_count'], $_GET['medley_id']]);
  }
}

$medley=[];
$all_songs=$mysql->query('
  SELECT * FROM extend_medley_song_30
  LEFT JOIN extend_medley
  ON extend_medley.medley_id=extend_medley_song_30.medley_id
  WHERE extend_medley.user_id='.$uid.' order by extend_medley.medley_id, `order`')->fetchAll();
foreach($all_songs as $row) {
  $medley[$row['medley_id']]['type']=$row['medley_type'];
  $medley[$row['medley_id']]['song_count']=$row['song_count'];
  $medley[$row['medley_id']]['live'][]=$row['ID'];
  $medley[$row['medley_id']]['is_random'][]=$row['random_switch'];
  $info=$live->query('
    SELECT name, difficulty, attribute_icon_id FROM festival.event_festival_live_m
    LEFT JOIN live_setting_m ON live_setting_m.live_setting_id=festival.event_festival_live_m.live_setting_id
    LEFT JOIN live_track_m ON live_track_m.live_track_id=live_setting_m.live_track_id
    WHERE notes_setting_asset="'.$row['notes_setting_asset'].'"')->fetch();
  if (!$info) {
    $info = $live->query('
      SELECT name, difficulty, attribute_icon_id, 1 as extend FROM live_setting_m
      LEFT JOIN live_track_m ON live_track_m.live_track_id=live_setting_m.live_track_id
      WHERE notes_setting_asset="'.$row['notes_setting_asset'].'"')->fetch();
  }
  $medley[$row['medley_id']]['live_info'][]=$info;
}

if(isset($_GET['delete'])) {
  if(!isset($_GET['confirm'])) {
    echo '<h3>确实要删除下面的组曲吗？</h3>';
    foreach($medley[$_GET['delete']]['live_info'] as $k=>$v)
      echo '<p>'.$v['name'].'&nbsp;&nbsp;&nbsp;&nbsp;'.$difficulty[$v['difficulty']].'</p>';
    echo '<a href="medley?confirm=1&delete='.$_GET['delete'].'">是</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="medley">否</a>';
    die();
  } else {
    $mysql->prepare('DELETE FROM extend_medley WHERE medley_id=?')->execute([$_GET['delete']]);
    $mysql->prepare('DELETE FROM extend_medley_song_30 WHERE medley_id=?')->execute([$_GET['delete']]);
    $mysql->prepare('DELETE FROM extend_medley_bind WHERE medley=?')->execute([$_GET['delete']]);
    unset($medley[$_GET['delete']]);
  }
}

if(isset($_GET['setid']) && is_numeric($_GET['setid'])) {
  echo '<h3>为组曲设置入口&nbsp;&nbsp;&nbsp;&nbsp;<a href="medley">取消</a></h3><p>为你的组曲选择一个位置吧（同一入口设置多组组曲的话会随机抽取）<br />
*若您将实际长度为2的组曲绑定到“3曲”入口上，进入游戏和结算时会显示有3首曲目，但游戏时实际仍为2首</p><table border="1"><tr><td></td><th>EASY</th><th>NORMAL</th><th>HARD</th><th>EXPERT</th></tr>';
  for($i=1;$i<=3;$i++) {
    echo "<tr><th>$i 首</th>";
    for($j=1;$j<=4;$j++) {
      echo '<td><a href="medley?count='.$i.'&difficulty='.$j.'&medley='.$_GET['setid'].'">'.$difficulty[$j].' '.$i.'曲</a></td>';
    }
    echo '</tr>';
  }
  echo '</table><p>您可以在下面确认刚选择的组曲信息：</p>';
  foreach($medley[$_GET['setid']]['live_info'] as $k=>$v)
    echo '<p>'.$v['name'].'&nbsp;&nbsp;&nbsp;&nbsp;'.$difficulty[$v['difficulty']].'</p>';
  die();
}

$current_medley=[
  '1'=>['1'=>[], '2'=>[], '3'=>[], '4'=>[]],
  '2'=>['1'=>[], '2'=>[], '3'=>[], '4'=>[]],
  '3'=>['1'=>[], '2'=>[], '3'=>[], '4'=>[]]
];
$res=$mysql->query('SELECT * FROM extend_medley_bind WHERE user_id='.$uid)->fetchAll();
foreach($res as $v) {
  $current_medley[$v['count']][$v['difficulty']][]=[$v['medley'], $v['ID']];
}

  
?>
<p><a href="/webview.php/settings/index">返回</a></p>
<h2>组曲设置</h2>
<p>您当前每个入口所关联的组曲如下（点击组曲编号取消关联）：<br />（注：此处的难度和曲目数只用于区分不同的入口，进入后的实际曲数以对应组曲中的设置为准）。</p>
<table border="1">
<tr><td></td><th>EASY</th><th>NORMAL</th><th>HARD</th><th>EXPERT</th></tr>
<?php foreach($current_medley as $k=>$v) {
  echo "<tr><th>$k 首</th>";
  foreach($v as $k2=>$v2) {
    echo '<td>';
    if(empty($v2)) echo '使用默认';
    else {
      foreach($v2 as $k3=>$v3) {
        if($k3) echo '<br />';
        echo '<a href="medley?erase='.$v3[1].'">'.$v3[0].'</a>';
      }
    }
    echo '</td>';
  }
} ?>
</table>

<hr>
<h2>组曲列表</h2>

<p>您可在下方自定义自己的组曲。<br />组曲有两种：<br />定长组曲：设置2~9首曲目，游戏时使用这些曲目。<br />随机组曲：设置至少2首曲目及使用曲数（2~9），游戏时随机抽取。</p>

<p>若客户端进入MF崩溃，请尝试移除<i>斜体</i>的曲目。</p>

<table border="1">
<tr><th>ID</th><th>类别</th><th>组曲操作</th><th>曲目</th><th>难度</th><th>曲目操作</th></tr><tr>
<?php
foreach($medley as $k=>$v) {
  echo '<td rowspan="'.count($v['live']).'">'.$k.'</td>';
  echo '<td rowspan="'.count($v['live']).'">'.($v['type']?'随机':'定长').'</td><td>';
  echo '<a href="medley?delete='.$k.'">删除</a>';
  if($v['type'] || count($v['live'])<9)
    echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="medleyAdd?id='.$k.'&count='.(count($v['live'])+1).'">增加曲目</a>';
  echo '</td>';
  $flag=false;
  foreach($v['live'] as $k2=>$v2) {
    if(isset($flag) && $flag==true) {
      echo '<td rowspan="'.(count($v['live'])-1).'"><a href="medley?setid='.$k.'">设置难度入口</a>';
      if(!$v['type']) {
        echo '<br /><s>调整顺序</s><br />（↑工事中）';
      }
      if($v['type']) {
        echo '<br /><form method="get" action="medley">曲数（2~9）：<input name="change_count" type="text" value="'.$v['song_count'].'" autocomplete="off" /><input name="medley_id" type="hidden" value="'.$k.'" /><input name="max_count" type="hidden" value="'.min(9,count($v['live'])).'" /><input name="submit" type="submit" value="更改" /></form>';
      }
      echo '</td>';
      unset($flag);
    }
    switch($v['live_info'][$k2]['attribute_icon_id']) {
      case 1: echo '<td style="color:red">';break;
      case 2: echo '<td style="color:green">';break;
      case 3: echo '<td style="color:blue">';break;
      default: echo '<td>';
    }
    if (isset($v['live_info'][$k2]['extend'])) {
      echo '<i>'.$v['live_info'][$k2]['name'].'</i></td>';
    } else {
      echo $v['live_info'][$k2]['name'].'</td>';
    }
    echo '<td>'.$difficulty[$v['live_info'][$k2]['difficulty']];
    switch($v['is_random'][$k2]) {
    case 2:echo '旧随机';break;
    case 1:echo '新随机';break;
    }
    echo '</td><td><a href="medleyAdd?modify='.$v2.'">改</a>'.((count($v['live'])>2)?'&nbsp;&nbsp;&nbsp;&nbsp;<a href="medley?delete_song='.$v2.'&medley_id='.$k.'&count='.(count($v['live'])-1).'">删</a>':'').'</td></tr><tr>';
    if(isset($flag)) $flag=true;
  }
} ?>
<td colspan="6"><a href="medley?add=0">增加定长组曲</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="medley?add=1">增加随机组曲</a></td></tr>
</table>
