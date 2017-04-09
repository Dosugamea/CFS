<?php
//api.php 批量执行指令

function api_($post) {
	foreach($post as $v) {
		$module=$v['module'];
		$action=$v['action'];
		$ret2['result']=runAction($module, $action, $v);
		$ret2['status']=200;
		$ret2['commandNum']=false;
		$ret2['timestamp']=time();
		unset($v['module']);
		unset($v['action']);
		unset($v['timeStamp']);
		$ret[]=$ret2;
	}
	return $ret;
}

?>