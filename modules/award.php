<?php
function award_awardInfo() {
	include_once("config/modules_award.php");
	global $user, $mysql, $uid;
	if ($user['award'] == 0) {
		$user['award'] = 1;
	}
	$ret['award_info'] = [];
	foreach($default_awards as $i){
		$ret['award_info'][]=[
		'award_id'=>$i,
		'is_set'=>$i==$user['award'],
		"insert_date"=>"2013-04-15 00:00:00"
		];
	}
	$extra_awards = $mysql->query("SELECT * FROM award WHERE user_id = ?", [$uid])->fetchAll(PDO::FETCH_ASSOC);
	foreach($extra_awards as $i){
		$ret['award_info'][]=[
		'award_id'=>(int)$i['award_id'],
		'is_set'=>(int)$i['award_id']==$user['award'],
		"insert_date"=>$i['insert_date']
		];
	}
	return $ret;
}

function award_set($post) {
	include_once("config/modules_award.php");
	global $user, $mysql, $uid;
	if(!is_numeric($post['award_id']))
		throw403("INVALID_DATA");
	if(!in_array($post['award_id'], $default_awards)){
		$extra_awards = $mysql->query("SELECT * FROM award WHERE user_id = ?", [$uid])->fetchAll(PDO::FETCH_ASSOC);
		foreach($extra_awards as $i)
			if((int)$i['award_id'] == $post['award_id'])
				$result = true;
		if(!$result)
			throw403("AWARD_NOT_EXIST");
	}
	$user['award'] = $post['award_id'];
	return [];
}
