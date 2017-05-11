<?php
//SIS.php 处理技能宝石

function addSIS($skill_id, $amount = 1){
	global $mysql, $uid;
	$amount_now = $mysql->query("SELECT * FROM removable_skill WHERE user_id = ".$uid." AND skill_id = ".$skill_id)->fetch();
	if($amount_now == false){
		$mysql->query("INSERT INTO removable_skill (user_id, skill_id, amount, equipped) VALUES(".$uid.",".$skill_id.",".$amount.",0)");
	}else{
		$mysql->query("UPDATE removable_skill SET amount = amount + ".$amount." WHERE user_id = ".$uid." AND skill_id = ".$skill_id);
	}
}