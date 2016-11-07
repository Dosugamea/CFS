<?php
//album.php 相册

//album/albumAll 
function album_albumAll() {
  global $uid, $mysql, $max_unit_id, $params;
  $ret=[];
  if($params['card_switch']) {
    $album=$mysql->query("SELECT * from album WHERE user_id=$uid");
    while ($row=$album->fetch(PDO::FETCH_ASSOC)) {
      $row['unit_id']=(int)$row['unit_id'];
      $row['rank_max_flag']=($row['rank_max_flag']==1?true:false);
      $row['love_max_flag']=($row['love_max_flag']==1?true:false);
      $row['rank_level_max_flag']=($row['rank_level_max_flag']==1?true:false);
      $row['all_max_flag']=($row['love_max_flag'] && $row['rank_level_max_flag']);
      $row['highest_love_per_unit']=0;
      $row['total_love']=0;
      $row['favorite_point']=0;
      $ret[]=$row;
    }
  } else {
    $unit = getUnitDb();
    $real_max_unit_id = $unit->query('select max(unit_id) from unit_m where unit_id<='.$max_unit_id)->fetchColumn();
    for($i=1;$i<=$real_max_unit_id;$i++) {
      $row['unit_id']=$i;
      $row['rank_max_flag']=true;
      $row['love_max_flag']=false;
      $row['rank_level_max_flag']=false;
      $row['all_max_flag']=true;
      $row['highest_love_per_unit']=0;
      $row['total_love']=0;
      $row['favorite_point']=0;
      $ret[]=$row;
    }
  }
  return $ret;
}

?>