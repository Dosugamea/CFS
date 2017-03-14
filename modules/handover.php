<?php
//账户界面发行继承码 返回空值
function handover_start() {
	$ret['id'] = "UR1234567";
	$ret['code'] = "0000000000000000";
	$ret['expire_date'] = "不支持發行繼承碼！";
	return $ret;
}

//更新防止崩溃 返回空
function handover_renew() {
	$ret['id'] = "UR1234567";
	$ret['code'] = "0000000000000000";
	$ret['expire_date'] = "不支持發行繼承碼！";
	return $ret;
}
?>