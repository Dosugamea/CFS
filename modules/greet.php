<?php

//greet/user 发邮件
function greet_user($post) {
	global $uid, $mysql;
	if($post['module'] != "greet" && $post['action'] != "user")
		throw403("WRONG-MODULE&ACTION");
	if(!is_numeric($post['to_user_id']))
		throw403("INVALID_ID");
	$mysql->query("INSERT INTO mail (from_id, to_id, message) VALUES(?, ?, ?)", [$uid, $post['to_user_id'], $post['message']]);
	return [];
}
?>