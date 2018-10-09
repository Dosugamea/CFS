<?php
//common/recoveryEnergy 恢复LP防止崩溃
function common_recoveryEnergy() {
	global $envi;
	$before_sns_coin = $envi->params['item4'];
	if(!energyRecover()){
		$ret = retError(1102);
		return $ret;
	}
	$ret = getCurrentEnergy();
	$ret['before_sns_coin'] 	= $before_sns_coin;
	$ret['after_sns_coin']		= $envi->params['item4'];
	$ret['item_list']			= runAction("user", "showAllItem")['items'];
	$ret['server_timestamp']	= time();
	return $ret;
}
?>