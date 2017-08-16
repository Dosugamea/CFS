<?php
//ranking.php 排名相关module
require_once('includes/live.php');
require_once('includes/unit.php');
require_once('includes/extend_avatar.php');
//ranking/live 曲目排名
function ranking_live($post) {
  global $mysql, $params;
  $notes_setting = getLiveSettings($post['live_difficulty_id'], 'notes_setting_asset');
  $ret['total_cnt'] = 10;
  $ret['page'] = 0;
  $ret['rank'] = null;
  $ret['items'] = [];
  $rank=$mysql->query('
  SELECT @id:=@id+1 as rank, a.* FROM  (
    SELECT live_ranking.user_id,hi_score,name,level,center_unit,award FROM live_ranking
    LEFT JOIN users ON users.user_id=live_ranking.user_id
    LEFT JOIN user_deck ON user_deck.user_id=live_ranking.user_id
    WHERE notes_setting_asset="'.$notes_setting['notes_setting_asset'].'" 
    AND card_switch='.$params['card_switch'].' 
    AND random_switch='.($params['random_switch']+$params['extend_mods_key']*10).'
    ORDER BY hi_score DESC LIMIT 0,10
  ) a,(SELECT @id:=0) id');
  $notes=$mysql->query("SELECT notes_list FROM notes_setting WHERE notes_setting_asset='".$notes_setting['notes_setting_asset']."'")->fetch(PDO::FETCH_ASSOC)['notes_list'];
	$score_max=calcScore(60500,json_decode($notes,true));
  while ($item = $rank->fetch()) {
    $ret2['rank'] = (int)$item['rank'];
    $ret2['score'] = (int)$item['hi_score'];
    if($params['card_switch']==0 && $ret2['score']>$score_max){
      $mysql->query("UPDATE live_ranking SET cheat = 1
      WHERE user_id=".$item['user_id']."
      AND notes_setting_asset='".$notes_setting['notes_setting_asset']."'
      AND card_switch=".$params['card_switch']." 
      AND random_switch=".($params['random_switch']+$params['extend_mods_key']*10));

      $LOGFILE = fopen("IllegalScore.log","a");
      fwrite($LOGFILE,date("Y-m-d H:i:s"));
      fwrite($LOGFILE," ".$item['user_id']);
      fwrite($LOGFILE," ".$item['name']);
      fwrite($LOGFILE," ".$ret2['score']);
      fwrite($LOGFILE,"/".$score_max);
      fclose($LOGFILE);

      $item['name']="CHEATED (".$score_max." MAX)";
    }
    $ret2['user_data']['user_id'] = (int)$item['user_id'];
    $user_list[] = $item['user_id'];
    $ret2['user_data']['name'] = $item['name'];
    $ret2['user_data']['level'] = (int)$item['level'];
    $center_units[] = $item['center_unit'];
    $ret2['setting_award_id'] = $item['award'];
    $ret['items'][] = $ret2;
  }
  if (empty($ret['items'])) {
    return $ret;
  }
  loadExtendAvatar($user_list);
  $unit_detail = GetUnitDetail($center_units);
  foreach($ret['items'] as $k => &$v) {
    $v['center_unit_info'] = $unit_detail[$k];
    setExtendAvatar($v['user_data']['user_id'], $v['center_unit_info']);
  }
  return $ret;
}

//ranking/player 玩家排名
function ranking_player($post) {
  global $mysql, $params;
  $ret['rank'] = null;
  $ret['items'] = [];
  $count = 20;
  $ret['total_cnt'] = $mysql->query('SELECT count(DISTINCT user_id) FROM live_ranking WHERE card_switch=0')->fetchColumn();
  if (isset($post['id'])) {
    $begin = $mysql->query('
      SELECT rank-1 FROM (
        SELECT @id:=@id+1 as rank,a.* from (
          SELECT sum(hi_score) as score, user_id FROM live_ranking
          WHERE card_switch='.$params['card_switch'].'
          GROUP BY user_id ORDER BY score DESC
        )a, (SELECT @id:=0)rank
      ) b WHERE user_id='.$post['id']
    )->fetchColumn();
    $ret['page'] = 0;
    if ($begin === false) {
      return $ret;
    }
  } else {
    $begin = $post['page']*$count;
    $ret['page'] = $post['page'];
  }
  $rank = $mysql->query('
    SELECT @id:=@id+1 as rank,a.* from (
      SELECT b.*,name,level,center_unit,award FROM (
        SELECT sum(hi_score) as score, user_id FROM live_ranking
        WHERE card_switch='.$params['card_switch'].'
        GROUP BY user_id ORDER BY score DESC
      ) b
      LEFT JOIN users ON users.user_id=b.user_id
      LEFT JOIN user_deck ON user_deck.user_id=b.user_id
    )a, (SELECT @id:='.($ret['page']*$count).')rank LIMIT '.($ret['page']*$count).','.$count);
  while($item = $rank->fetch()) {
    $ret2['rank'] = (int)$item['rank'];
    $ret2['score'] = (int)$item['score'];
    $ret2['user_data']['user_id'] = (int)$item['user_id'];
    $ret2['user_data']['name'] = $item['name'];
    $ret2['user_data']['level'] = (int)$item['level'];
    $user_list[] = $item['user_id'];
    $center_units[] = $item['center_unit'];
    $ret2['setting_award_id'] = (($item['award'] == 0) ? 1 : $item['award']);
    $ret['items'][] = $ret2;
  }
  if (empty($ret['items'])) {
    return $ret;
  }
  loadExtendAvatar($user_list);
  $unit_detail = GetUnitDetail($center_units);
  foreach($ret['items'] as $k => &$v) {
    $v['center_unit_info'] = $unit_detail[$k];
    setExtendAvatar($v['user_data']['user_id'], $v['center_unit_info']);
  }
  return $ret;
}

function ranking_eventPlayer($post) {
	global $mysql;
	include_once("includes/unit.php");
	$ret = [];
	$ret['total_cnt'] = (int)$mysql->query("SELECT COUNT(*) FROM event_point WHERE event_id = ?", [$post['event_id']])->fetchColumn();
	$ret['page'] = $post['page'];
	$result = $mysql->query("SELECT * FROM event_point WHERE user_id NOT IN (SELECT user_id FROM (SELECT user_id FROM event_point WHERE event_id = ? ORDER BY event_point DESC LIMIT ".($post['page'] * 20 + $post['buff']).") AS A) AND event_id = ? ORDER BY event_point DESC LIMIT ".($post['page'] * 20 + 20),[$post['event_id'], $post['event_id']])->fetchAll(PDO::FETCH_ASSOC);
	$ret['items'] = [];
	foreach($result as $k=>$i){
		$item = [];
		$item['rank'] = $ret['page']*20 + $k + 1;
		$item['score'] = (int)$i['event_point'];
		$item['user_data'] = $mysql->query("SELECT user_id, name, level, award FROM users WHERE user_id = ?", [$i['user_id']])->fetch(PDO::FETCH_ASSOC);
		$item['user_data']['user_id'] = (int)$item['user_data']['user_id'];
		$item['user_data']['level'] = (int)$item['user_data']['level'];
		$center = (int)$mysql->query("SELECT center_unit FROM user_deck WHERE user_id = ?", [$i['user_id']])->fetchColumn();
		$item['center_unit_info'] = GetUnitDetail($center, true);
		$item['setting_award_id'] = (int)$item['user_data']['award'];
		unset($item['user_data']['award']);
		$ret['items'][] = $item;
	}
	
	return $ret;
}

function ranking_eventLive($post) {
	global $mysql;
	include_once("includes/unit.php");
	$ret = [];
	$ret['total_cnt'] = (int)$mysql->query("SELECT COUNT(*) FROM event_point WHERE event_id = ?", [$post['event_id']])->fetchColumn();
	$ret['page'] = $post['page'];
	$result = $mysql->query("SELECT * FROM event_point WHERE user_id NOT IN (SELECT user_id FROM (SELECT user_id FROM event_point WHERE event_id = ? ORDER BY score_point DESC LIMIT ".($post['page'] * 20 + $post['buff']).") AS A) AND event_id = ? ORDER BY score_point DESC LIMIT ".($post['page'] * 20 + 20),[$post['event_id'], $post['event_id']])->fetchAll(PDO::FETCH_ASSOC);
	$ret['items'] = [];
	foreach($result as $k=>$i){
		$item = [];
		$item['rank'] = $ret['page']*20 + $k + 1;
		$item['score'] = (int)$i['score_point'];
		$item['user_data'] = $mysql->query("SELECT user_id, name, level, award FROM users WHERE user_id = ?", [$i['user_id']])->fetch(PDO::FETCH_ASSOC);
		$item['user_data']['user_id'] = (int)$item['user_data']['user_id'];
		$item['user_data']['level'] = (int)$item['user_data']['level'];
		$center = (int)$mysql->query("SELECT center_unit FROM user_deck WHERE user_id = ?", [$i['user_id']])->fetchColumn();
		$item['center_unit_info'] = GetUnitDetail($center, true);
		$item['setting_award_id'] = (int)$item['user_data']['award'];
		unset($item['user_data']['award']);
		$ret['items'][] = $item;
	}
	
	return $ret;
}

function ranking_eventFriendLive($post) {
	$ret = [];
	$ret['total_cnt'] = 0;
	$ret['page'] = 0;
	$ret['items'] = [];
	return $ret;
}

function ranking_eventFriendPlayer($post) {
	$ret = [];
	$ret['total_cnt'] = 0;
	$ret['page'] = 0;
	$ret['items'] = [];
	return $ret;
}
?>
