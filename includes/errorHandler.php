<?php 
error_reporting(E_ALL); 

function error($errno=null, $errstr=null, $errfile=null, $errline=null) {
	if ($errline != NULL) { //error_handler
		$error['type'] = $errno;
		$error['message'] = $errstr;
		$error['file'] = $errfile;
		$error['line'] = $errline;
		$error['trace'] = debug_backtrace();
		array_shift($error['trace']);
	} else if (is_subclass_of($errno, 'Throwable')) { //exception_handler
		if (is_subclass_of($errno, 'Exception')) {
			$error['type'] = 'Uncaught exception '.$errno->getCode();
		} else {
			$error['type'] = 'Error '.$errno->getCode();
		}
		$error['message'] = $errno->getMessage();
		$error['file'] = $errno->getFile();
		$error['line'] = $errno->getLine();
		$error['trace'] = $errno->getTrace();
	} else {
		$error = error_get_last();
	}
	if ($error != NULL && strpos($error['message'], 'HTTP_RAW_POST_DATA') === false && strpos($error['message'], 'PHP Startup') === false) { //php爆炸
		global $mysql, $livedb, $unitdb, $authorize, $error_handled;
		//防止error_handler之后执行shutdown_function导致无法trace
		if ($error_handled) {
			return;
		} else {
			$error_handled = true;
		}
		if (isset($_SERVER['HTTP_USER_ID'])) {
			$uid = $_SERVER['HTTP_USER_ID'];
		} else {
			$uid = 0;
		}
		if (!isset($_POST['request_data'])) {
			$_POST['request_data'] = '';
		}
		switch ($error['type']) {
		case 1: $errno = 'Fatal Error'; break;
		case 2: $errno = 'Warning'; break;
		case 4: $errno = 'Parse Error'; break;
		case 8: case 1024: $errno = 'Notice'; break;
		default: $errno = $error['type'];
		}
		$error = $errno.':'.$error['message'].' in '.$error['file'].' at line '.$error['line']."\n";
		foreach (debug_backtrace() as $k => $v) {
			if($k == 0) {
				continue;
			}
			if (isset ($v['file'])) {
				$error .= '	at '.$v['function'].' ('.$v['file'].':'.$v['line'].")\n";
			} else {
				$error .= '	at '.$v['function']." (closure)\n";
			}
		}
		if (isset($mysql) && $mysql) {
			$error .= "MySQL:\n".$mysql->errorInfo()."\n";
		}
		if (isset($livedb) && $livedb) {
			$error .= "live.db:\n".$livedb->errorInfo()."\n";
		}
		if (isset($unitdb) && $unitdb) {
			$error .= "unit.db:\n".$unitdb->errorInfo()."\n";
		}
		$error .= '错误模块：'.$_SERVER['PATH_INFO']."\n提交信息：".$_POST['request_data'];
		if (!isset($authorize)) {
			$authorize['token'] = 0;
		}
		if (!isset($authorize['token'])) {
			$authorize['token'] = 0;
		}
		if ($mysql) {
			$mysql->query('rollback');
			$mysql->query('INSERT INTO error_report VALUES (null,?,?,?, 0)', [$authorize['token'], $uid, $error]);
		}
		header("Maintenance: 1");
		die();
	}
}

register_shutdown_function("error");
set_error_handler("error");
set_exception_handler("error");