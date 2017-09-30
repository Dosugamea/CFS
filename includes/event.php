<?php
//获取活动pt和排名
function getUserEventStatus($uid, $event_id){
	global $mysql;
	$event_point = (int)$mysql->query("SELECT event_point FROM event_point WHERE user_id = ? AND event_id = ?", [$uid, $event_id])->fetchColumn();
	$rank = $mysql->query("SELECT rowNo FROM 
		(Select user_id, event_id, (@rowNum:=@rowNum+1) as rowNo
		From event_point,
		(Select (@rowNum :=0) ) b
		WHERE event_id = ?
		Order by event_point.event_point Desc) as unused
		WHERE user_id = ? AND event_id = ?",[$event_id, $uid, $event_id])->fetchColumn();
	return ["event_point" => $event_point, "rank" => (int)$rank];
}

?>