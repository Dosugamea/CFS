<?php
//tos.php 用户协议module

//tos/tosCheck 返回所有用户协议的接受情况
function tos_tosCheck() {
	global $mysql, $uid, $config;
	$ret['tos_id'] = $config->tos['tos_id'];
	$ret['tos_type'] = 3; //别问我我也不知道这啥
	$agreed_tos = $mysql->query('SELECT * FROM tos WHERE user_id = ?', [$uid])->fetch();
	if($agreed_tos == false || $agreed_tos['tos_id'] != $config->tos['tos_id']){
		$ret['is_agreed'] = false;
	}else{
		$ret['is_agreed'] = true;
	}
	$ret['server_timestamp'] = time();
	return $ret;
}

//tos/read 阅读用户协议
//tos/tosAgree 同意用户协议
function tos_tosAgree() {
	global $mysql, $uid, $config;
	$agreed_tos = $mysql->query('SELECT * FROM tos WHERE user_id='.$uid)->fetch();
	if($agreed_tos == false){
		$mysql->query('INSERT INTO tos VALUES (?,?)',[$uid, $config->tos['tos_id']]);
	}else{
		$mysql->query('UPDATE tos SET tos_id = ? WHERE user_id = ?',[$config->tos['tos_id'], $uid]);
	}
	return [];
}
?>