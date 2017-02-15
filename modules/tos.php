<?php
//tos.php 用户协议module

//tos/tosCheck 返回所有用户协议的接受情况
function tos_tosCheck() {
	global $mysql, $uid;
	include "config/tos.php";
	$ret['tos_id'] = $tos_id;
	$agreed_tos = $mysql->query('SELECT * FROM tos WHERE user_id='.$uid)->fetch();
	if($agreed_tos == false || $agreed_tos != $tos_id){
		$ret['is_agree'] = false;
	}else{
		$ret['is_agree'] = true;
	}
	return $ret;
}

//tos/read 阅读用户协议
//tos/tosAgree 同意用户协议
function tos_tosAgree() {
	global $mysql, $uid;
	include "config/tos.php";
	$agreed_tos = $mysql->query('SELECT * FROM tos WHERE user_id='.$uid)->fetch();
	if($agreed_tos == false){
		$mysql->query('INSERT INTO tos VALUES (?,?)',[$uid,$tos_id]);
	}else{
		$mysql->query('UPDATE tos SET tos_id = ? WHERE user_id = ?',[$tos_id, $uid]);
	}
	return [];
}
?>