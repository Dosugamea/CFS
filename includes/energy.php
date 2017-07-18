<?php
//energy.php，负责LP管理、回复
function getCurrentEnergy($level=0){
	global $uid, $mysql;
	$energy = $mysql->query("SELECT energy_full_time, over_max_energy, level FROM users WHERE user_id = ".$uid)->fetch(PDO::FETCH_ASSOC);
	if($level>0)
		$energy['level']=$level;
	$energy_max = 100 + floor($energy['level']/2);
	$ret = [];
	$ret['energy_max'] = $energy_max;
	if($energy['over_max_energy'] > $energy_max){
		$ret['energy_full_need_time'] = 0;
		$ret['energy_full_time'] = $energy['energy_full_time'];
		$ret['over_max_energy'] = (int)$energy['over_max_energy'];
	}else{
		$ret['energy_full_need_time'] = (strtotime($energy['energy_full_time']) - strtotime("now")) < 0 ? 0 : (strtotime($energy['energy_full_time']) - strtotime("now"));
		$ret['energy_full_time'] = $energy['energy_full_time'];
		$ret['over_max_energy'] = 0;
	}
	return $ret;
}

function energyDecrease($amount){
	global $uid, $mysql;
	$energy = $mysql->query("SELECT energy_full_time, over_max_energy, level FROM users WHERE user_id = ".$uid)->fetch(PDO::FETCH_ASSOC);
	$energy_max = 100 + floor($energy['level']/2);
	if((strtotime($energy['energy_full_time']) - strtotime("now")) < 0)
		$energy_now = $energy_max;
	else
		$energy_now = $energy_max - floor((strtotime($energy['energy_full_time']) - strtotime("now"))/360);
	if($energy['over_max_energy'] > $energy_max){
		if(($energy['over_max_energy'] - $amount) > $energy_max){
			$mysql->query("UPDATE users SET over_max_energy = ".($energy['over_max_energy'] - $amount)." WHERE user_id = ".$uid);
			return true;
		}else{
			$decrease_cnt = $energy_max - ($energy['over_max_energy'] - $amount);
			$energy_full_time = date("Y-m-d H:i:s",strtotime($energy['energy_full_time']) + $decrease_cnt * 360);
			$mysql->query("UPDATE users SET over_max_energy = 0, energy_full_time = '".$energy_full_time."' WHERE user_id = ".$uid);
			return true;
		}
	}else if(($energy_now - $amount) >= 0){
		if($energy_now == $energy_max)
			$mysql->query("UPDATE users SET over_max_energy = 0, energy_full_time = '". date("Y-m-d H:i:s",(time() + $amount * 360))."' WHERE user_id = ".$uid);
		else
			$mysql->query("UPDATE users SET over_max_energy = 0, energy_full_time = '". date("Y-m-d H:i:s",(strtotime($energy['energy_full_time']) + $amount * 360))."' WHERE user_id = ".$uid);
		return true;
	}else
		return false;
}

function energyRecover($level=0){
	global $uid, $mysql, $params;
	$energy = $mysql->query("SELECT energy_full_time, over_max_energy, level FROM users WHERE user_id = ".$uid)->fetch(PDO::FETCH_ASSOC);
	if($level>0)
		$energy['level']=$level;
	$energy_max = 100 + floor($energy['level']/2);
	if($energy['over_max_energy'] != 0 || strtotime($energy['energy_full_time']) <= time())
		if($energy['over_max_energy'] >= $energy_max)
			if($level==0)
				return true;
			else
				$energy_now=(int)$energy['over_max_energy'];
	if(!isset($energy_now))
		$energy_now = $energy_max - floor((strtotime($energy['energy_full_time']) - strtotime("now"))/360);
	if($energy_now == 0)
		$mysql->query("UPDATE users SET energy_full_time = '".date("Y-m-d H:i:s",time())."' WHERE user_id = ".$uid);
	else
		$mysql->query("UPDATE users SET over_max_energy = ".($energy_now + $energy_max).", energy_full_time = '".date("Y-m-d H:i:s",time())."' WHERE user_id = ".$uid);
	if($level==0)
		$params['item4'] -= 1;
	return true;
}