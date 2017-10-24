<?php
//login.php 登录module

function login_startUp($post) {
	$post['user_id'] = 0;
	return $post;
}

function login_startWithoutInvite() {
	return [];
}

//login/authkey 获取一个认证token
function login_authkey($post) {
	global $mysql;
	sscanf($_SERVER['HTTP_AUTHORIZE'], 'consumerKey=lovelive_test&timeStamp=%d&version=1.1&nonce=%d', $timestamp, $nonce);
	if($nonce != 1) {
		throw403('HTTP_AUTHORIZE_INVALID_AUTHKEY');
	}
	@$AES_token_client = RSAdecrypt($post['dummy_token']);
	if($AES_token_client == Null){
		throw403('INVALID_DUMMY_TOKEN');
	}
	//解密auth_data
	@$auth_data = AESdecrypt(substr(base64_decode($post['auth_data']), 16), substr($AES_token_client, 0, 16), substr(base64_decode($post['auth_data']), 0, 16));
	if($auth_data == Null){
		throw403('INVALID_AUTH_DATA_A');
	}
	$auth_data = json_decode(preg_replace('/[^[:print:]]/', '', $auth_data), true);//正则匹配不可显示的padding并删除
	if(!isset($auth_data['1']) || !isset($auth_data['2']) || !isset($auth_data['3'])){
		throw403('INVALID_AUTH_DATA_B');
	}
	if(!isset($_SERVER['HTTP_OS_VERSION']) || !isset($_SERVER['HTTP_CLIENT_VERSION']) || !isset($_SERVER['HTTP_BUNDLE_VERSION'])){
		throw403('INVALID_DEVICE');
	}
	$enc = login_v2(["login_key" => $auth_data['1'], "login_passwd" => $auth_data['2']]);
	if($enc[0] !== false){
		$uid = $enc[0];
	}else{
		$uid = 0;
	}
	$mysql->query("INSERT INTO auth_log (user_id, login_key, login_passwd, device_data, hdr_device, ip, client_version, bundle_version) VALUES(?,?,?,?,?,?,?,?)", [$uid, $enc[1], $enc[2], base64_decode($auth_data['3']), $_SERVER['HTTP_OS_VERSION'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_CLIENT_VERSION'], $_SERVER['HTTP_BUNDLE_VERSION']]);
	/*var_dump($auth_data);
	var_dump(base64_decode(json_decode($auth_data, true)["3"]));
	die();*/
	
	//生成随机AES key
	$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890#%*';
	mt_srand((double)microtime()*1000000*getmypid());
	$AES_token_server='';   
	while(strlen($AES_token_server) < 32){
		$AES_token_server.=substr($chars,(mt_rand()%strlen($chars)),1);  
	}
	for($i=0;$i < strlen($AES_token_server);$i++){
		$sessionKey[$i] = ($AES_token_server[$i] ^ $AES_token_client[$i % strlen($AES_token_client)]);
	}
	$sessionKey = implode("",$sessionKey);
	$ret['authorize_token'] = sha1(rand(10000000, 99999999));
	$ret['dummy_token'] = base64_encode($AES_token_server);
	$ret['review_version'] = "";
	$ret['server_timestamp'] = time();
	$mysql->query('insert into tmp_authorize(token, sessionKey) values (?,?)', [$ret['authorize_token'],base64_encode($sessionKey)]);
	//header('version_up: 0');
	header('authorize: consumerKey=lovelive_test&timeStamp='.time().'&version=1.1&token='.$ret['authorize_token'].'&nonce=1&user_id=&requestTimeStamp='.time());
	return $ret;
}

//login/login 执行登录，返回一个UID
function login_login($post) {
	global $mysql;
	$authorize = explode('&', $_SERVER['HTTP_AUTHORIZE']);
	sscanf($authorize[1], 'timeStamp=%d', $timestamp);
	sscanf($authorize[3], 'token=%s', $token);
	sscanf($authorize[4], 'nonce=%d', $nonce);
	if ($nonce != 2) {
		throw403('HTTP_AUTHORIZE_INVALID_LOGIN');
	}
	if (!$mysql->query('select * from tmp_authorize where token=?', [$token])->fetch()) {
		throw403('AUTHORIZE_TOKEN_NOT_FOUND_LOGIN');
	}
	$sessionKey = $mysql->query('select sessionKey from tmp_authorize where token=?', [$token])->fetchColumn();
	$sessionKey = base64_decode($sessionKey);
	$mysql->query('delete from tmp_authorize where token=?', [$token]);
	$raw_login_key = base64_decode($post['login_key']);
	$iv = substr($raw_login_key,0,16);
	$login_key = AESdecrypt(substr($raw_login_key,16), substr($sessionKey,0,16), $iv);
	$post['login_key'] = preg_replace('/[^[:print:]]/', '', $login_key);//正则匹配去除padding
	
	$raw_login_passwd = base64_decode($post['login_passwd']);
	$iv = substr($raw_login_passwd,0,16);
	$login_passwd = AESdecrypt(substr($raw_login_passwd,16), substr($sessionKey,0,16), $iv);
	$post['login_passwd'] = preg_replace('/[^[:print:]]/', '', $login_passwd);//正则匹配去除padding
	
	$login_result = login_v2($post);
	$id = $login_result[0];
	$login_result_v1 = false;
	if ($login_result[0] === false) {
		$login_result_v1 = login_v1($post);
		if ($login_result_v1[0] !== false) {
			$mysql->exec("UPDATE users SET password='{$login_result[2]}', username='{$login_result[1]}' WHERE username='{$login_result_v1[1]}'"); 
			$id = $login_result_v1[0];
		}
	}
	if($id === false) {
		//if($enable_web_reg) {
			$id = -1;
		//}else{
		//	$id = $mysql->query('select ifnull(max(user_id), 0) from users')->fetchColumn();
		//	$id++;
		//	$mysql->exec("INSERT INTO `users` (`user_id`, `username`, `password`) VALUES ($id, '$uname', '$pass')");
		//	$mysql->exec("INSERT INTO `user_info` (`user_id`, `name`, `invite_code`) VALUES($id, 'New User', '$id')");
		//	$mysql->exec("INSERT INTO `user_perm` VALUES($id, 1, 1, 1, 0, 0)");
		//}
	}
	$ret['authorize_token'] = sha1(rand(10000000, 99999999));
	$ret['user_id'] = $id;
	if ($id !== -1) {
		$encoded_sessionKey = base64_encode($sessionKey);
		$mysql->exec("UPDATE users SET nonce=2, authorize_token='{$ret['authorize_token']}', elapsed_time_from_login=CURRENT_TIMESTAMP, sessionKey = '{$encoded_sessionKey}' WHERE username='{$login_result[1]}'");
	} else {
		$mysql->query('delete from tmp_authorize where username=?', [$login_result[1]]);
		$mysql->query('insert into tmp_authorize (token, username, password, sessionKey) values (?, ?, ?, ?)', [$ret['authorize_token'], $login_result[1], $login_result[2], base64_encode($sessionKey)]);
	}
	$ret['review_version'] = '';
	$ret['server_timestamp'] = time();
	return $ret;
}

function login_v2($post) {
	global $mysql;
	$pass = hash('sha512', $post['login_passwd']);
	$uname = hash('sha512', str_replace('-', 'MakiMakiMa', $post['login_key'])).$pass;
	$pass .= hash('sha512', str_replace('-', 'NicoNicoNi', $post['login_key']));
	$uname .= $uname; //长度不够
	$pass .= $pass;
	$uname = substr($uname, hexdec(substr($post['login_key'], 0, 2)), 32);
	$pass = substr($pass, hexdec(substr($post['login_passwd'], 0, 2)), 32);
	return [$mysql->query("SELECT user_id FROM users WHERE username='$uname' and password='$pass'")->fetchColumn(), $uname, $pass];
}

function login_v1($post) {
	global $mysql;
	$uname = sha1($post['login_key']);
	$pass = sha1($post['login_passwd']);
	return [$mysql->query("SELECT user_id FROM users WHERE username='$uname' and password='$pass'")->fetchColumn(), $uname, $pass];
}

//login/unitList 返回首次登录的队伍信息
function login_unitList() {
	require '../config/reg.php';
	$i = 1;
	foreach($default_deck as $v) {
		$tmp['unit_initial_set_id'] = $i;
		$i++;
		$tmp['unit_list'] = $v;
		$tmp['center_unit_id'] = $v[4];
		$ret[] = $tmp;
	}
	return ['unit_initial_set' => $ret];
}

//login/unitSelect 选择首次登录的队伍
function login_unitSelect($post) {
	global $uid, $mysql, $max_unit_id;
	require '../config/reg.php';
	$unit = getUnitDb();
	$selected = login_unitList();
	$selected = $selected['unit_initial_set'][$post['unit_initial_set_id']-1];
	$position = 1;
	foreach($selected['unit_list'] as $v) {
		$mysql->exec("INSERT INTO `unit_list` (`user_id`, `unit_id`) VALUES ('$uid', '$v');");
		$tmp['position'] = $position;
		$tmp['unit_owning_user_id'] = (int)$mysql->lastInsertId();
		if($position == 5) {
			$center = $tmp['unit_owning_user_id'];
		}
		$unit_deck_detail[] = $tmp;
		$position++;
	}
	if($all_card_by_default) {
		$card_list = $unit->query('select unit_id from unit_m where unit_id<='.$max_unit_id)->fetchAll();
		$query = 'INSERT INTO `unit_list` (`user_id`, `unit_id`) VALUES ';
		foreach($card_list as $v) {
			$query .= '('.$uid.', '.$v[0].'), ';
		}
		$query = substr($query, 0,strlen($query)-1);
		$mysql->exec($query);
	}
	$mysql->exec("INSERT IGNORE INTO album (user_id,unit_id) SELECT DISTINCT $uid, unit_id FROM unit_list WHERE user_id = {$uid}");
	//修正特典卡的rank
	$default_rankup = $unit->query('select unit_id from unit_m where unit_m.normal_icon_asset like "%rankup%"')->fetchAll(PDO::FETCH_COLUMN);
	$mysql->exec('UPDATE unit_list SET rank=2 WHERE user_id='.$uid.' AND unit_id in('.implode(', ', $default_rankup).')');
	$mysql->exec('UPDATE album SET rank_max_flag=1 WHERE user_id='.$uid.' AND unit_id in('.implode(', ', $default_rankup).')');
	$tmp2['unit_deck_detail'] = $unit_deck_detail;
	$tmp2['unit_deck_id'] = 1;
	$tmp2['main_flag'] = true;
	$tmp2['deck_name'] = '';
	$unit_deck_list[] = $tmp2;
	$json = json_encode($unit_deck_list);
	$mysql->exec("INSERT IGNORE INTO user_deck (user_id,json,center_unit) VALUES ($uid, '$json', $center)");
	//这两个初始成员一旦没有进不去
	$mysql->exec("INSERT INTO `unit_list` (`user_id`, `unit_id`) VALUES ('$uid', '9');");
	$mysql->exec("INSERT INTO `unit_list` (`user_id`, `unit_id`) VALUES ('$uid', '13');");
	
	runAction('tutorial', 'skip');
	throw403('restart client');
	return [];
}

//login/topInfo 返回首页显示的一些信息
function login_topInfo() {
	global $uid, $mysql, $params;
	$present_count = $mysql->query('SELECT count(*) FROM incentive_list WHERE user_id='.$uid.' and opened_date=0')->fetchColumn();
	$last_muse_free_gacha = date("Y-m-d", strtotime($mysql->query("SELECT free_gacha_muse FROM secretbox WHERE user_id = ?", [$uid])->fetchColumn()));
	$last_aqours_free_gacha = date("Y-m-d", strtotime($mysql->query("SELECT free_gacha_aqours FROM secretbox WHERE user_id = ?", [$uid])->fetchColumn()));
	$mail_cnt = count($mysql->query("SELECT * FROM mail WHERE `read` = 0 AND `to_id` = ".$uid)->fetchAll(PDO::FETCH_ASSOC));
	$friend_cnt = count($mysql->query("SELECT * FROM friend WHERE `read` = 0 AND `applicated` = ".$uid)->fetchAll(PDO::FETCH_ASSOC));
	$ret = [];
	$ret['free_muse_gacha_flag'] = $last_muse_free_gacha == date("Y-m-d", time());
	$ret['free_muse_gacha_flag'] = $last_aqours_free_gacha == date("Y-m-d", time());
	$ret['next_free_muse_gacha_timestamp'] = $last_muse_free_gacha == date("Y-m-d", time()) ? (strtotime($last_muse_free_gacha) + 86400) : strtotime(date("Y-m-d", time()));
	$ret['next_free_aqours_gacha_timestamp'] = $last_aqours_free_gacha == date("Y-m-d", time()) ? (strtotime($last_muse_free_gacha) + 86400) : strtotime(date("Y-m-d", time()));
	$ret['next_free_gacha_timestamp'] = strtotime(date('Y-m-d',strtotime('+1 day')));
	$ret['friend_action_cnt'] = $mail_cnt;
	$ret['friend_greet_cnt'] = $mail_cnt;
	$ret['friend_variety_cnt'] = 0;
	$ret['notice_friend_datetime'] = "2013-04-15 11:47:00";
	$ret['notice_mail_datetime'] = "2000-01-01 12:00:00";
	$ret['present_cnt'] = (int)$present_count;
	$ret['server_datetime'] = date('Y-m-d H:i:s');
	$ret['server_timestamp'] = time();
	$ret['friends_approval_wait_cnt'] = $friend_cnt;
	
	return $ret;
}

//login/topInfoOnce 返回新成就数目
function login_topInfoOnce() {
	global $mysql, $uid;
	$daily_reward = $mysql->query("SELECT daily_reward FROM users WHERE user_id = ".$uid)->fetchColumn();
	return ["new_achievement_cnt"=>0, 'unaccomplished_achievement_cnt'=>0, 'handover_expire_status'=>0,'live_daily_reward_exist' => date("Y-m-d",strtotime($daily_reward)) != date("Y-m-d",time())];
}

?>