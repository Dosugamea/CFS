<?php

//notice/noticeMarquee 应该是主界面滚动显示的通知（比如继承码到期）
function notice_noticeMarquee() {
	include("../config/modules_notice.php");
	$notice = [];
	foreach($noticeMarquee as $i){
		if(strtotime($i['start_date']) < time() && strtotime($i['end_date']) > time()){
			$notice[] = $i;
		}
	}
	$ret = [];
	$ret['item_count'] = count($notice);
	$ret['marquee_list'] = $notice;
	return $ret;
}
//notice/noticeFriendVariety 新着信息 返回空
function notice_noticeFriendVariety() {
	  return json_decode('{
            "item_count": 0,
            "notice_list": []
        }');
}
//notice/noticeFriendGreeting 收到的邮件
function notice_noticeFriendGreeting() {
	global $uid, $mysql;
	$mails = $mysql->query("SELECT * FROM mail WHERE to_id = ".$uid." ORDER BY notice_id DESC")->fetchAll(PDO::FETCH_ASSOC);
	$ret = [];
	$ret['item_count'] = count($mails);
	$ret['notice_list'] = [];
	foreach($mails as $i){
		$detail = [];
		$detail['notice_id'] = $i['notice_id'];
		$detail['new_flag'] = true;
		$detail['reference_table'] = 6;
		$detail['message'] = $i['message'];
		$detail['list_message'] = $i['message'];
		$detail['readed'] = (bool)$i['read'];
		$elapsed_time = " ".strtotime("now")-strtotime($i['insert_date']);
		if($elapsed_time >= 86400)
			$i['insert_date'] = " ".floor($elapsed_time / 86400)."天前";
		else if($elapsed_time >= 3600)
			$i['insert_date'] = " ".floor($elapsed_time / 3600)."小時前";
		else if($elapsed_time >= 60)
			$i['insert_date'] = " ".floor($elapsed_time / 60)."分前";
		else
			$i['insert_date'] = " ".$elapsed_time."秒前";
		$detail['insert_date'] = $i['insert_date'];
		$detail['affector'] = [];
		$from_user = $mysql->query("SELECT user_id, name, level, award FROM users WHERE user_id = ".$i['from_id'])->fetch(PDO::FETCH_ASSOC);
		$detail['affector']['user_data'] = $from_user;
		$center_id = $mysql->query("SELECT center_unit FROM user_deck WHERE user_id = ".$i['from_id'])->fetchColumn();
		$center_info = GetUnitDetail((int)$center_id);
		$detail['affector']['center_unit_info'] = $center_info;
		$detail['affector']['setting_award_id'] = (int)$from_user['award'];
		$detail['reply_flag'] = (bool)$i['replied'];
		$ret['notice_list'][] = $detail;
	}
	$mysql->query("UPDATE mail SET `read` = 1 WHERE `read` = 0 AND to_id = ".$uid);
	return $ret;
}
//notice/noticeUserGreetingHistory 发送的邮件
function notice_noticeUserGreetingHistory() {
	global $uid, $mysql;
	$mails = $mysql->query("SELECT * FROM mail WHERE from_id = ".$uid." ORDER BY notice_id DESC")->fetchAll(PDO::FETCH_ASSOC);
	$ret = [];
	$ret['item_count'] = count($mails);
	$ret['has_next'] = false;
	$ret['notice_list'] = [];
	foreach($mails as $i){
		$detail = [];
		$detail['notice_id'] = $i['notice_id'];
		$detail['new_flag'] = true;
		$detail['reference_table'] = 6;
		$detail['message'] = $i['message'];
		$detail['list_message'] = $i['message'];
		$elapsed_time = " ".strtotime("now")-strtotime($i['insert_date']);
		if($elapsed_time >= 86400)
			$i['insert_date'] = " ".floor($elapsed_time / 86400)."天前";
		else if($elapsed_time >= 3600)
			$i['insert_date'] = " ".floor($elapsed_time / 3600)."小時前";
		else if($elapsed_time >= 60)
			$i['insert_date'] = " ".floor($elapsed_time / 60)."分前";
		else
			$i['insert_date'] = " ".$elapsed_time."秒前";
		$detail['insert_date'] = $i['insert_date'];
		$detail['receiver'] = [];
		$from_user = $mysql->query("SELECT user_id, name, level, award FROM users WHERE user_id = ".$i['to_id'])->fetch(PDO::FETCH_ASSOC);
		$detail['receiver']['user_data'] = $from_user;
		$center_id = $mysql->query("SELECT center_unit FROM user_deck WHERE user_id = ".$i['to_id'])->fetchColumn();
		$center_info = GetUnitDetail((int)$center_id);
		$detail['receiver']['center_unit_info'] = $center_info;
		$detail['receiver']['setting_award_id'] = (int)$from_user['award'];
		$detail['reply_flag'] = (bool)$i['replied'];
		$ret['notice_list'][] = $detail;
	}
	return $ret;
}
?>