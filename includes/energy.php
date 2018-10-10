<?php
//energy.php，负责LP管理、回复
function getMaxEnergy(){
	global $envi;
	return 100 + floor($envi->user['level'] / 2);
}

function getCurrentEnergy($level=0){
	global $uid, $mysql;
	$energy = $mysql->query("SELECT energy_full_time, over_max_energy, level FROM users WHERE user_id = ?", [$uid])->fetch(PDO::FETCH_ASSOC);
	if($level > 0)
		$energy['level'] = $level;
	$energy_max = getMaxEnergy();
	$ret = [];
	$ret['energy_max'] = $energy_max;
	if($energy['over_max_energy'] > $energy_max){
		$ret['energy_full_need_time']	= 0;
		$ret['energy_full_time']		= $energy['energy_full_time'];
		$ret['over_max_energy']			= (int)$energy['over_max_energy'];
		$ret['current_energy']			= $ret['over_max_energy'];
	}else{
		$ret['energy_full_need_time']	= (strtotime($energy['energy_full_time']) - strtotime("now")) < 0 ? 0 : (strtotime($energy['energy_full_time']) - strtotime("now"));
		$ret['energy_full_time']		= $energy['energy_full_time'];
		$ret['over_max_energy']			= 0;
		$ret['current_energy']			= $ret['energy_full_need_time'] == 0 ? $energy_max : floor((strtotime($energy['energy_full_time']) - time()) / 360);
	}
	return $ret;
}

function energyDecrease($amount){
	global $uid, $mysql;
	$energy = $mysql->query("SELECT energy_full_time, over_max_energy, level FROM users WHERE user_id = ?", [$uid])->fetch(PDO::FETCH_ASSOC);
	$energy_max = getMaxEnergy();
	if((strtotime($energy['energy_full_time']) - strtotime("now")) < 0)
		$energy_now = $energy_max;
	else
		$energy_now = $energy_max - floor((strtotime($energy['energy_full_time']) - strtotime("now"))/360);
	if($energy['over_max_energy'] > $energy_max){
		if(($energy['over_max_energy'] - $amount) > $energy_max){
			$mysql->query("UPDATE users SET over_max_energy = ? WHERE user_id = ?", [$energy['over_max_energy'] - $amount, $uid]);
			return true;
		}else{
			$decrease_cnt = $energy_max - ($energy['over_max_energy'] - $amount);
			$energy_full_time = date("Y-m-d H:i:s",strtotime($energy['energy_full_time']) + $decrease_cnt * 360);
			$mysql->query("UPDATE users SET over_max_energy = 0, energy_full_time = ? WHERE user_id = ?", [$energy_full_time, $uid]);
			return true;
		}
	}else if(($energy_now - $amount) >= 0){
		if($energy_now == $energy_max)
			$mysql->query("UPDATE users SET over_max_energy = 0, energy_full_time = ? WHERE user_id = ?", [date("Y-m-d H:i:s",(time() + $amount * 360)), $uid]);
		else
			$mysql->query("UPDATE users SET over_max_energy = 0, energy_full_time = ? WHERE user_id = ?", [date("Y-m-d H:i:s",(strtotime($energy['energy_full_time']) + $amount * 360)), $uid]);
		return true;
	}else{
		return false;
	}
}

function energyRecover($level = 0){
	global $uid, $mysql, $envi, $logger;
	$energy = $mysql->query("SELECT energy_full_time, over_max_energy, level FROM users WHERE user_id = ?", [$uid])->fetch();
	if($level > 0){
		$energy['level'] = $level;
	}
	$energy_max = getMaxEnergy();
	if($energy['over_max_energy'] != 0 || strtotime($energy['energy_full_time']) <= time())
		if($energy['over_max_energy'] >= $energy_max)
			if($level==0)
				return true;
			else
				$energy_now = (int)$energy['over_max_energy'];
	if(!isset($energy_now))
		$energy_now = $energy_max - floor((strtotime($energy['energy_full_time']) - strtotime("now")) / 360);
	if($energy_now == 0)
		$mysql->query("UPDATE users SET energy_full_time = ? WHERE user_id = ?", [date("Y-m-d H:i:s",time()), $uid]);
	else
		$mysql->query("UPDATE users SET over_max_energy = ?, energy_full_time = ? WHERE user_id = ?",[($energy_now + $energy_max), date("Y-m-d H:i:s",time()), $uid]);
	$logger->d("New energy: ".($energy_now + $energy_max));
	if($level == 0)
		$envi->params['item4'] -= 1;
	return true;
}