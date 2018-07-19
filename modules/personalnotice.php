<?php
//personalnotice.php

//personalnotice/get
function personalnotice_get() {
	global $uid, $mysql, $config;
	
	$personal_notice = $mysql->query("SELECT * FROM personalnotice WHERE user_id = ? AND agree = 0", [$uid])->fetch();
	if($personal_notice){
		$ret['has_notice'] = true;
		$ret['notice_id']  = $personal_notice['notice_id'];
		$ret['type']       = $personal_notice['type'];
		$ret['title']      = $personal_notice['title'];
		$ret['contents']   = $personal_notice['content'];
		return $ret;
	}
	foreach($config->m_personalnotice['global_notice'] as $i){
		$global_notice_agreement = $mysql->query("SELECT * FROM personalnotice_global WHERE user_id = ? AND notice_id = ?", [$uid, $i['notice_id']])->fetch();
		if($global_notice_agreement == false){
			$ret['has_notice'] = true;
			$ret['notice_id']  = $i['notice_id'];
			$ret['type']       = $i['type'];
			$ret['title']      = $i['title'];
			$ret['contents']   = $i['contents'];
			return $ret;
		}
	}
	return [
		"has_notice" => false
	];
}

//已读notice
function personalnotice_agree($post) {
	global $uid, $mysql;
	include("../config/modules_personalnotice.php");
	
	if(!isset($post['notice_id']) || !is_numeric($post['notice_id'])){
		throw403("INVALID PARAMETERS");
	}
	
	$global_flag = false;
	foreach($global_notice as $i){
		if($post['notice_id'] === $i['notice_id']){
			$mysql->query("INSERT INTO personalnotice_global (user_id, notice_id) VALUES (?, ?)", [$uid, $post['notice_id']]);
			$global_flag = true;
		}
	}
	if(!$global_flag){
		$mysql->query("UPDATE personalnotice SET agree = 1, agree_date = CURRENT_TIMESTAMP WHERE user_id = ? AND notice_id = ?", [$uid, $post['notice_id']]);
	}
	return [];
}
?>