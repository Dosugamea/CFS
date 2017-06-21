<?php
//SIS.php �����ܱ�ʯ

function addSIS($skill_id, $amount = 1){
	global $mysql, $uid;
	$amount_now = $mysql->query("SELECT * FROM removable_skill WHERE user_id = ".$uid." AND skill_id = ".$skill_id)->fetch();
	if($amount_now == false){
		$mysql->query("INSERT INTO removable_skill (user_id, skill_id, amount, equipped) VALUES(".$uid.",".$skill_id.",".$amount.",0)");
	}else{
		$mysql->query("UPDATE removable_skill SET amount = amount + ".$amount." WHERE user_id = ".$uid." AND skill_id = ".$skill_id);
	}
}

function getRandomSIS($box){
	//$rate_list=[[45,15,2,1],[45,15,2,1],[36,12,2,1],[30,10,2,1],[18,6,2,1]];
	$rate_list=[[20,10,2,1],[12,6,2,1],[8,4,2,1],[6,3,2,1],[4,2,2,1]];
	$p=$rate_list[$box-1];
	$sum=array_sum($p);
	$rand=rand(1,$sum);

	$sum_now=0;
	$cost=1;
	for($i=0;$i<4;$i++){
		$sum_now+=$p[$i];
		if($rand<=$sum_now){
			$cost=$i+1;
			break;
		}
	}

	switch($cost){
		case 1:return rand(1,3);
		case 2:return rand(4,15);
		case 3:return rand(16,27);
		case 4:return rand(28,39);
	}
}