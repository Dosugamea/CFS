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
$live=getLiveDb();

$difficulty=[null, 'Easy', 'Normal', 'Hard', 'Expert', null, 'Master'];

$liveid=$mysql->query('SELECT notes_setting_asset FROM notes_setting')->fetchAll(PDO::FETCH_COLUMN);


$names=[];
$info=$live->query('
  SELECT name, difficulty, notes_setting_asset, attribute_icon_id FROM festival.event_festival_live_m
  LEFT JOIN live_setting_m ON live_setting_m.live_setting_id=festival.event_festival_live_m.live_setting_id
  LEFT JOIN live_track_m ON live_track_m.live_track_id=live_setting_m.live_track_id
  WHERE notes_setting_asset in ("'.implode('","', $liveid).'") and live_setting_m.live_setting_id < 10000
')->fetchAll();

$avail_info = array_map(function ($e) {
  return $e['notes_setting_asset'];
}, $info);

$other_info = $live->query('
  SELECT name, difficulty, notes_setting_asset, attribute_icon_id, 1 as extend FROM live_setting_m
  LEFT JOIN live_track_m ON live_track_m.live_track_id=live_setting_m.live_track_id
  WHERE notes_setting_asset in ("'.implode('","', array_diff($liveid, $avail_info)).'") and difficulty!=5
')->fetchAll();

$info = array_merge($info, $other_info);
usort($info, function ($x, $y) {
  return strcmp($x['notes_setting_asset'], $y['notes_setting_asset']);
});

foreach ($info as $row) {
  if(!isset($row['attribute_icon_id'])) {
    $row['attribute_icon_id'] = 0;
  }
  $live_list[]=$row;
  if(strpos($row['name'], ']')!==false)
    $name=substr($row['name'],strpos($row['name'], ']')+1);
  else
    $name=$row['name'];
  if(array_search($name, $names)===false) $names[]=$name;
  $sort[]=array_search($name, $names);
  $sort2[]=$row['difficulty'];
}
array_multisort($sort, $sort2, $live_list);

if(isset($_GET['id'])) echo '<h2>为组曲'.$_GET['id'].'增加曲目 <a href="medley">返回</a></h2>';
elseif(isset($_GET['modify'])) echo '<h2>修改曲目 <a href="medley">返回</a></h2>';
else {echo '<h2>出错了！<a href="medley">返回</a></h2>';die();}
?>
<h3><font color="red">由于3.0客户端代码限制，较旧（16年4月前）的客户端使用本表中斜体的曲目会崩溃，选择时请注意自己的客户端版本。</font></h3>
<table border='1'>
<tr><th width="50%">曲目</th><th>难度</th><th colspan="3">选择</th></tr>
<?php
foreach($live_list as $k=>$v) {
  echo '<tr>';
  switch($v['attribute_icon_id']) {
    case 1: echo '<td style="color:red">';break;
    case 2: echo '<td style="color:green">';break;
    case 3: echo '<td style="color:blue">';break;
    default: echo '<td>';
  }
  if (isset($v['extend'])) {
    echo '<i>'.$v['name'].'</i>';
  } else {
    echo $v['name'];
  }
  echo '</td><td>'.$difficulty[$v['difficulty']].'</td>';
  if(isset($_GET['id']))
    echo '<td><a href="medley?add_song='.$_GET['id'].'&id='.$v['notes_setting_asset'].'&random=0&count='.$_GET['count'].'">通常</a></td><td><a href="medley?add_song='.$_GET['id'].'&id='.$v['notes_setting_asset'].'&random=1&count='.$_GET['count'].'">新随机</a></td><td><a href="medley?add_song='.$_GET['id'].'&id='.$v['notes_setting_asset'].'&random=2&count='.$_GET['count'].'">旧随机</a></td></tr>';
  else
    echo '<td><a href="medley?edit_song='.$_GET['modify'].'&id='.$v['notes_setting_asset'].'&random=0">通常</a></td><td><a href="medley?edit_song='.$_GET['modify'].'&id='.$v['notes_setting_asset'].'&random=1">新随机</a></td><td><a href="medley?edit_song='.$_GET['modify'].'&id='.$v['notes_setting_asset'].'&random=2">旧随机</a></td></tr>';
} ?>

</table>
