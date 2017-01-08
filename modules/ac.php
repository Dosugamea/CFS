<?php
function ac_acProfile() {
	$ret['error_code'] = 11102;
	retError(600);
	return $ret;
}
/*
顺带加上一个能正常返回AC情报的请求
"response_data":{
	"name": "コーエー",
	"level": 50,
	"character_id": 7,
	"award_id": 3009,
	"display_flag": 1
}
*/