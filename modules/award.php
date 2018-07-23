<?php
function award_awardInfo() {
	global $envi, $mysql, $uid, $config;
	if ($envi->user['award'] == 0) {
		$envi->user['award'] = 1;
	}
	$ret['award_info'] = [];
	foreach($config->m_awards['default_awards'] as $i){
		$ret['award_info'][]=[
		'award_id'=>$i,
		'is_set'=>$i==$envi->user['award'],
		"insert_date"=>"2013-04-15 00:00:00"
		];
	}
	$extra_awards = $mysql->query("SELECT * FROM award WHERE user_id = ?", [$uid])->fetchAll();
	foreach($extra_awards as $i){
		$ret['award_info'][]=[
		'award_id'=>(int)$i['award_id'],
		'is_set'=>(int)$i['award_id']==$envi->user['award'],
		"insert_date"=>$i['insert_date']
		];
	}
	return $ret;
}

function award_set($post) {
	global $envi, $mysql, $uid, $config;
	if(!is_numeric($post['award_id']))
		throw403("INVALID_DATA");
	if(!in_array($post['award_id'], $config->m_awards['default_awards'])){
		$extra_awards = $mysql->query("SELECT * FROM award WHERE user_id = ?", [$uid])->fetchAll();
		foreach($extra_awards as $i)
			if((int)$i['award_id'] == $post['award_id'])
				$result = true;
		if(!$result)
			throw403("AWARD_NOT_EXIST");
	}
	$envi->user['award'] = $post['award_id'];
	return [];
}
