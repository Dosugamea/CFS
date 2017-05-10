<?php
require_once('includes/unit.php');
require_once('includes/extend_avatar.php');
//好友列表【暂时返回空】
function friend_list() {
	$ret['item_count'] = 0;
	$ret['friend_list'] = [];
	$ret['bushimo_reward_info'] = [];
	return $ret;
}

//搜索好友
function friend_search($post) {
	global $mysql, $params;
	$ret2 = $mysql->query('SELECT user_id,name,level,award,background,9999 as unit_max,999 as friend_max,user_id as invite_code,introduction FROM users WHERE user_id='.$post['invite_code'])->fetch(PDO::FETCH_ASSOC);
	if (empty($ret2)) {
		return [];
	}
	foreach($ret2 as $k2 => &$v2) {
		if ($k2 != 'invite_code' && is_numeric($v2)) $v2 = (int)$v2;
	}
	$time = $mysql->query('SELECT elapsed_time_from_login FROM users WHERE user_id='.$post['invite_code'])->fetchColumn();
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
	$center = GetUnitDetail($mysql->query('SELECT center_unit FROM user_deck WHERE user_id='.$post['invite_code'])->fetchColumn());
	$ret['center_unit_info'] = $center;
	loadExtendAvatar([$post['invite_code']]);
	setExtendAvatar($post['invite_code'], $ret['center_unit_info']);
	$ret['is_alliance'] = false;
	$ret['friend_status'] = 0;
	if (!$ret['user_info']['award']) $ret['user_info']['award'] = 1;
	if (!$ret['user_info']['background']) $ret['user_info']['background'] = 1;
	$ret['setting_award_id'] = $ret['user_info']['award'];
	$ret['setting_background_id'] = $ret['user_info']['background'];
	unset($ret['user_info']['award'], $ret['user_info']['background']);
	return $ret;
}
?>