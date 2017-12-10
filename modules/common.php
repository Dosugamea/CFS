<?php
//common/recoveryEnergy 恢复LP防止崩溃
function common_recoveryEnergy() {
	global $params;
	$before_sns_coin = $params['item4'];
	if(!energyRecover()){
		$ret = retError(1102);
		return $ret;
	}
	$ret = getCurrentEnergy();
	$ret['before_sns_coin'] = $before_sns_coin;
	$ret['after_sns_coin'] = $params['item4'];
	return $ret;
}
?>