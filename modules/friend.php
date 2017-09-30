<?php
//好友列表
function friend_list($post) {
	global $uid, $mysql;
	//if($post['module'] != "friend" && $post['action'] != "list")
		//throw403("WRONG-MODULE&ACTION");
	if($post['type'] == 0)
		$friends = $mysql->query("SELECT * FROM friend WHERE (applicant = ".$uid." AND status = 0) OR (applicated = ".$uid." AND status = 0)")->fetchAll(PDO::FETCH_ASSOC);
	if($post['type'] == 1)
		$friends = $mysql->query("SELECT * FROM friend WHERE applicant = ".$uid." AND status = 1")->fetchAll(PDO::FETCH_ASSOC);
	if($post['type'] == 2){
		$friends = $mysql->query("SELECT * FROM friend WHERE applicated = ".$uid." AND status = 1")->fetchAll(PDO::FETCH_ASSOC);
		$mysql->query("UPDATE friend SET `read` = 1 WHERE applicated = ".$uid." AND status = 1");
	}
	$ret['item_count'] = count($friends);
	$ret['friend_list'] = [];
	foreach($friends as $i){
		$detail = [];
		if((int)$i['applicant'] == $uid)
			$friend_id = $i['applicated'];
		else
			$friend_id = $i['applicant'];
		$friend_detail = $mysql->query("SELECT user_id, name, level, elapsed_time_from_login, introduction, award FROM users WHERE user_id = ".$friend_id)->fetch(PDO::FETCH_ASSOC);
		//优化上次登录时间
		$elapsed_time = " ".strtotime("now")-strtotime($friend_detail['elapsed_time_from_login']);
		if($elapsed_time >= 86400)
			$friend_detail['elapsed_time_from_login'] = " ".floor($elapsed_time / 86400)."天前";
		else if($elapsed_time >= 3600)
			$friend_detail['elapsed_time_from_login'] = " ".floor($elapsed_time / 3600)."小時前";
		else if($elapsed_time >= 60)
			$friend_detail['elapsed_time_from_login'] = " ".floor($elapsed_time / 60)."分前";
		else
			$friend_detail['elapsed_time_from_login'] = " ".$elapsed_time."秒前";
		
		$detail['user_data'] = $friend_detail;
		$detail['user_data']['comment'] = $detail['user_data']['introduction'];
		unset($detail['user_data']['introduction']);
		//优化显示加好友时间
		$elapsed_time = " ".strtotime("now")-strtotime($i['agree_date']);
		if($elapsed_time >= 86400)
			$detail['user_data']['elapsed_time_from_applied'] = " ".floor($elapsed_time / 86400)."天前";
		else if($elapsed_time >= 3600)
			$detail['user_data']['elapsed_time_from_applied'] = " ".floor($elapsed_time / 3600)."小時前";
		else if($elapsed_time >= 60)
			$detail['user_data']['elapsed_time_from_applied'] = " ".floor($elapsed_time / 60)."分前";
		else
			$detail['user_data']['elapsed_time_from_applied'] = " ".$elapsed_time."秒前";
		
		$center_info = GetUnitDetail($mysql->query('SELECT center_unit FROM user_deck WHERE user_id='.$friend_id)->fetchColumn());
		loadExtendAvatar([$friend_id]);
		setExtendAvatar($friend_id, $center_info);
		$detail['center_unit_info'] = $center_info;
		$detail['setting_award_id'] = (int)$friend_detail['award'];
		$ret['friend_list'][] = $detail;
	}
	return $ret;
}

//搜索好友
function friend_search($post) {
	global $mysql, $params;
	$ret2 = $mysql->query('SELECT user_id,name,level,award,background,9999 as unit_max,999 as friend_max,user_id as invite_code,introduction FROM users WHERE user_id='.$post['invite_code'])->fetch(PDO::FETCH_ASSOC);
	if (empty($ret2)) {
		$ret['error_code'] = 1102;
		retError(600);
		return $ret;
	}
	foreach($ret2 as $k2 => &$v2) {
		if ($k2 != 'invite_code' && $k2 != 'introduction' && $k2 != 'name' && is_numeric($v2)) $v2 = (int)$v2;
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
//回复别人的好友申请
function friend_response($post) {
	global $uid, $mysql;
	if($post['module'] != "friend" && $post['action'] != "response")
		throw403("WRONG-MODULE&ACTION");
	if(!is_numeric($post['user_id']) && !is_numeric($post['status']))
		throw403("INVALID_USER_ID"); //防注入
	if($post['status'] == 2)
		$mysql->query("UPDATE friend SET `status` = 0, agree_date = '".date("Y-m-d H:i:s")."' WHERE applicant = ".$post['user_id']." AND applicated = ".$uid);
	else if($post['status'] == 0)
		$mysql->query("DELETE FROM friend WHERE applicant = ".$post['user_id']." AND applicated = ".$uid);
	return [];
}
//取消好友申请
function friend_requestCancel($post) {
	global $uid, $mysql;
	if($post['module'] != "friend" && $post['action'] != "requestCancel")
		throw403("WRONG-MODULE&ACTION");
	if(!is_numeric($post['user_id']))
		throw403("INVALID_USER_ID"); //防注入
	$mysql->query("DELETE FROM friend WHERE applicant = ".$uid." AND applicated = ".$post['user_id']);
	return [];
}
//删好友
function friend_expel($post) {
	global $uid, $mysql;
	if($post['module'] != "friend" && $post['action'] != "expel")
		throw403("WRONG-MODULE&ACTION");
	if(!is_numeric($post['user_id']))
		throw403("INVALID_USER_ID"); //防注入
	$mysql->query("DELETE FROM friend WHERE (applicant = ".$uid." AND applicated = ".$post['user_id'].") OR (applicant = ".$post['user_id']." AND applicated = ".$uid.")");
	return [];
}
//加好友
function friend_request($post) {
	global $uid, $mysql;
	if($post['module'] != "friend" && $post['action'] != "request")
		throw403("WRONG-MODULE&ACTION");
	if(!is_numeric($post['user_id']))
		throw403("INVALID_USER_ID"); //防注入
	$mysql->query("INSERT INTO friend (applicant, applicated) VALUES(".$uid.", ".$post['user_id'].")");
	return [];
}
?>