<?php
//festival.php FESTIVAL活动相关功能
//festival/festivalInfo 返回festival相关信息
function festival_festivalInfo() {
	include("config/event.php");
	return json_decode('{"base_info": {
		"event_id": '.$festival['event_id'].',
		"asset_bgm_id": 4,
		"event_point": 0,
		"total_event_point": 0,
		"whole_event_point": 0,
		"max_skill_activation_rate": 30
	}}', true);
}

function festival_deckList($post) {
  require 'config/modules_festival.php';
  global $uid, $mysql;
  $ret = runAction('live','deckList',['ScoreMatch'=>true]);
  $live_list = [];
  $medley_id = $mysql->query('
    SELECT medley FROM extend_medley_bind
    WHERE user_id='.$uid.' AND difficulty='.$post['difficulty'].' AND count='.$post['live_count'].' 
    ORDER BY rand() limit 1
  ')->fetchColumn();
  if ($medley_id === false) {
    shuffle($festival_live_lifficulty_ids);
    $live = $festival_live_lifficulty_ids[0];
    foreach ($live[$post['difficulty']] as $v)
      $live_list[] = ['notes_setting_asset'=>$v, 'random_switch'=>0];
    $type = 1;
    $count = $post['live_count'];
  } else {
    $live_list = $mysql->query('
      SELECT notes_setting_asset, random_switch FROM `extend_medley_song_30`
      WHERE medley_id = '.$medley_id.'
      ORDER BY `order` ASC'
    )->fetchAll(PDO::FETCH_ASSOC);
    $type = $mysql->query('SELECT medley_type, song_count FROM extend_medley WHERE medley_id=?', [$medley_id])->fetch();
    $count = $type['song_count'];
    $type = $type['medley_type'];
  }
  if ($type) {
    shuffle($live_list);
  }
  $live_list = array_slice($live_list, 0, $count);
  
  $live = getLiveDb();
  $exist_live = $mysql->query('select notes_setting_asset from notes_setting')->fetchAll(PDO::FETCH_COLUMN);
  $allowed_live = $live->query('
    SELECT notes_setting_asset FROM festival.event_festival_live_m
    LEFT JOIN live_setting_m ON live_setting_m.live_setting_id=festival.event_festival_live_m.live_setting_id
    WHERE notes_setting_asset in ("'.implode('","',$exist_live).'")
  ')->fetchAll(PDO::FETCH_COLUMN);
  $i = 0;
  foreach($live_list as &$v) {
    pl_assert(array_search($v['notes_setting_asset'], $exist_live), 'FESTIVAL 找不到谱面：'.$v['notes_setting_asset']);
    if (!array_search($v['notes_setting_asset'], $allowed_live)) {
      $row=$live->query('SELECT stage_level, live_difficulty_id from normal_live_m left join live_setting_m on normal_live_m.live_setting_id=live_setting_m.live_setting_id where notes_setting_asset="'.$v['notes_setting_asset'].'"')->fetch();
      if (!$row) {
        $row=$live->query('SELECT stage_level, live_difficulty_id from special_live_m left join live_setting_m on special_live_m.live_setting_id=live_setting_m.live_setting_id where notes_setting_asset="'.$v['notes_setting_asset'].'"')->fetch();
      }
    } else {
      $row=$live->query('SELECT ifnull(festival.event_festival_live_m.stage_level, live_setting_m.stage_level) stage_level, live_difficulty_id from festival.event_festival_live_m left join live_setting_m on live_setting_m.live_setting_id = festival.event_festival_live_m.live_setting_id where notes_setting_asset="'.$v['notes_setting_asset'].'"')->fetch();
    }
    $v['live_difficulty_id'] = (int)$row['live_difficulty_id'];
    if ($i < $post['live_count']) {
      $ret['festival']['event_festival_live_list'][] = [
        'live_difficulty_id' => (int)$row['live_difficulty_id'],
        'use_quad_point' => $v['random_switch'] == 2,
        'is_random' => $v['random_switch'] > 0,
        'dangerous' => $row['stage_level'] >= 11,
      ];
      $i++;
    }
  }
  //填充数据防止崩溃
  if (count($ret['festival']['event_festival_live_list']) < $post['live_count']) {
    $ret['festival']['event_festival_live_list'][] = $ret['festival']['event_festival_live_list'][count($ret['festival']['event_festival_live_list'])-1];
  }
  $ret['festival_previous_item_ids']=[];
  $live_list_json = json_encode($live_list);
  $mysql->exec('DELETE FROM tmp_festival_playing WHERE user_id='.$uid);
  $mysql->query('INSERT INTO tmp_festival_playing VALUES(?,?)', [$uid,$live_list_json]);
  $ret['energy_full_time'] = "2014-01-01 10:00:00";
  $ret['over_max_energy'] = 0;
  return $ret;
}

function festival_liveStart($post) {
  return runAction('live','play',array_merge($post,['festival'=>true]));
}

function festival_liveReward($post) {
  $ret=runAction('live','reward',array_merge($post,['festival'=>true]));
  $ret['event_info'] = json_decode('{
    "event_id": 91,
    "event_point_info": {
            "before_event_point": 0,
            "before_total_event_point": 0,
            "after_event_point": 0,
            "after_total_event_point": 0,
            "added_event_point": 0,
            "score_bonus": 1,
            "combo_bonus": 1,
            "item_bonus": 1,
            "guest_bonus": 1
        },
        "event_reward_info": [],
        "next_event_reward_info": [],
        "extra_reward_info": [],
    "event_notice": []
  }');
  return $ret;
}

//festival/continue 失败后继续游戏（不扣心）
function festival_continue() {
  $ret['before_sns_coin'] = runAction('user','userInfo');
  $ret['before_sns_coin'] = $ret['before_sns_coin']['user']['sns_coin'];
  $ret['after_sns_coin'] = $ret['before_sns_coin'];
  return $ret;
}

function festival_gameover() {
  $ret['event_info'] = json_decode('{
    "event_id": 91,
    "event_point_info": {
            "before_event_point": 0,
            "before_total_event_point": 0,
            "after_event_point": 0,
            "after_total_event_point": 0,
            "added_event_point": 0
        },
        "event_reward_info": [],
        "next_event_reward_info": []
  }');
  $ret['after_user_info'] = runAction('user','userInfo')['user'];
  return $ret;
}

function festival_top() {
  return json_decode('{"event_status":{"total_event_point":0,"event_rank":0},"has_history":false}',true);
}
?>