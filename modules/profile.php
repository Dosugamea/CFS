<?php
//profile.php 显示用户信息
//profile/liveCnt 返回用户clear各难度谱面的数目
function profile_liveCnt($post) {
	global $mysql;
	$live = getLiveDb();
	$clear_id = $mysql->query('SELECT DISTINCT notes_setting_asset FROM live_ranking WHERE user_id='.$post['user_id'])->fetchAll(PDO::FETCH_COLUMN);
	if (!$clear_id) {
		$clear_id[0] = -1;
	}
	$result = [
		['difficulty'=>1,'clear_cnt'=>0],
		['difficulty'=>2,'clear_cnt'=>0],
		['difficulty'=>3,'clear_cnt'=>0],
		['difficulty'=>4,'clear_cnt'=>0],
		['difficulty'=>5,'clear_cnt'=>0],
		['difficulty'=>6,'clear_cnt'=>0]
	];
	$allcount = $live->query('
		SELECT count(notes_setting_asset) as clear_cnt,difficulty FROM
		(SELECT notes_setting_asset, min(difficulty) difficulty FROM live_setting_m group by notes_setting_asset) a
		WHERE notes_setting_asset in ("'.implode('","',$clear_id).'") GROUP BY difficulty
	');
	while($cnt = $allcount->fetch()) {
		$result[$cnt['difficulty']-1]['clear_cnt'] = (int)$cnt['clear_cnt'];
	}
	$result[4]['clear_cnt'] = $result[5]['clear_cnt'];
	return $result;
}

//profile/cardRanking 返回最大的绊
function profile_cardRanking($post) {
	global $mysql;
	$res = $mysql->query('
		SELECT @id:=@id+1 as rank, unit_id, sum(love) as total_love
		FROM unit_list,(SELECT @id:=0)rank WHERE user_id='.$post['user_id'].'
		GROUP BY unit_id ORDER BY total_love DESC LIMIT 0,10
	');
	$ret = [];
	while ($t = $res->fetch(PDO::FETCH_ASSOC)) {
		foreach($t as &$v2)
			if (is_numeric($v2)) $v2 = (int)$v2;
		$ret[] = $t;
	}
	return $ret;
}

//profile/profileInfo 返回详细信息
function profile_profileInfo($post) {
	global $mysql, $params, $uid;
	$ret2 = $mysql->query('SELECT user_id,name,level,award,background,9999 as unit_max,999 as friend_max,user_id as invite_code,introduction FROM users WHERE user_id='.$post['user_id'])->fetch(PDO::FETCH_ASSOC);
	if (empty($ret2)) {
		return [];
	}
	foreach($ret2 as $k2 => &$v2) {
		if ($k2 != 'invite_code' && $k2 != 'introduction' && $k2 != 'name' && is_numeric($v2)) $v2 = (int)$v2;
	}
	$time = $mysql->query('SELECT elapsed_time_from_login FROM users WHERE user_id='.$post['user_id'])->fetchColumn();
	$ret['user_info'] = $ret2;
	$elapsed_time = " ".strtotime("now")-strtotime($time);
	if($elapsed_time >= 86400)
		$time = " ".floor($elapsed_time / 86400)."天前";
	else if($elapsed_time >= 3600)
		$time = " ".floor($elapsed_time / 3600)."小時前";
	else if($elapsed_time >= 60)
		$time = " ".floor($elapsed_time / 60)."分前";
	else
		$time = " ".$elapsed_time."秒前";
	$ret['user_info']['elapsed_time_from_login'] = $time;
	$center = GetUnitDetail($mysql->query('SELECT center_unit FROM user_deck WHERE user_id = ?', [$post['user_id']])->fetchColumn());
	$ret['center_unit_info'] = $center;
	$ret['navi_unit_info'] = $center;
	loadExtendAvatar([$post['user_id']]);
	setExtendAvatarForce($post['user_id'], $ret['navi_unit_info']);
	setExtendAvatar($post['user_id'], $ret['center_unit_info']);
	$ret['is_alliance'] = false;
	//处理好友状态
	$friend = $mysql->query("SELECT * FROM friend WHERE applicant IN(".$uid.", ".$post['user_id'].") AND applicated IN(".$uid.", ".$post['user_id'].")")->fetch(PDO::FETCH_ASSOC);
	if($friend == false)
		$ret['friend_status'] = 0;
	else if($friend['status'] == "0")
		$ret['friend_status'] = 1;
	else if((int)$friend['applicant'] == $uid)
		$ret['friend_status'] = 3;
	else if((int)$friend['applicated'] == $uid)
		$ret['friend_status'] = 2;
	if (!$ret['user_info']['award']) $ret['user_info']['award'] = 1;
	if (!$ret['user_info']['background']) $ret['user_info']['background'] = 1;
	$ret['setting_award_id'] = $ret['user_info']['award'];
	$ret['setting_background_id'] = $ret['user_info']['background'];
	unset($ret['user_info']['award'], $ret['user_info']['background']);
	return $ret;
}

//profile/profileRegister 更改简介
function profile_profileRegister($post) {
	global $user;
	$user['introduction'] = $post['introduction'];
	return [];
}

?>