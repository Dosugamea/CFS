<?php
date_default_timezone_set("Asia/Tokyo");
header("X-Powered-By: Project Custom Festival");
header("Y-Powered-By: LLS/0.3");
header('Server: LLS/0.3');
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Copyright: PCF@2018');

/* 错误处理 */
function error($errno=null, $errstr=null, $errfile=null, $errline=null) {
	$error['type']=$errno;
	$error['message']=$errstr;
	$error['file']=$errfile;
	$error['line']=$errline;
	if($error!=NULL) {
		global $mysql, $uid, $livedb, $unitdb, $achievementdb, $authorize;
		if(isset($_SESSION['server']['HTTP_USER_ID'])) $uid=$_SESSION['server']['HTTP_USER_ID'];
		else $uid=0;
		switch($error['type']) {
		case 1: $errno='Fatal Error';break;
		case 2: $errno='Warning';break;
		case 4: $errno='Parse Error';break;
		case 8: case 1024: $errno='Notice';break;
		default: $errno=$error['type'];
		}
		$error=$errno.':'.$error['message'].' in '.$error['file'].' at line '.$error['line']."\n";
		foreach (debug_backtrace() as $k => $v) {
			if($k == 0) continue;
			$error .= '	at '.$v['function'].' ('.$v['file'].':'.$v['line'].")\n";
		}
		if(isset($mysql) && $mysql) {
			$error.="MySQL:\n".$mysql->errorInfo()."\n";
		}
		if(isset($livedb) && $livedb) {
			$error.="live.db:\n".$livedb->errorInfo()."\n";
		}
		if(isset($unitdb) && $unitdb) {
			$error.="unit.db:\n".$unitdb->errorInfo()."\n";
		}
		if(isset($achievementdb) && $achievementdb) {
			$error.="achievement.db:\n".$achievementdb->errorInfo()."\n";
		}
		$error.='错误模块：'.$_SERVER['PATH_INFO'];
		if($mysql) {
			$mysql->query('rollback');
			$mysql->prepare('INSERT INTO error_report VALUES (null,?,?,?, 0)')->execute([0, $uid, $error]); 
		}
		require '../webview/maintenance/bomb.php';
		die();
	}
}

function commit () {
	global $mysql;
	if($mysql) {
	$mysql->query('commit');
	}
}

set_error_handler("error");
register_shutdown_function('commit');

require('../includes/configManager.php');
$config = new configManager;
require('../includes/db.php');

$mysql->query('start transaction');

session_start();
//第一次访问把所有内容全存进session
if (isset($_SERVER['HTTP_AUTHORIZE'])) {
	$_SESSION['server'] = $_SERVER;
}

/*if (!isset($_SESSION['server'])) {
	header('HTTP/1.1 403 Forbidden');
	echo '<h1>出现了一些问题，请尝试关闭页面重新打开</h1>';
	die();
}
*/

//default page
if(!isset($_SERVER['PATH_INFO']) || $_SERVER['PATH_INFO']=='') {
	$module='announce';
	$action='index';
} else {
	$path=explode('/', $_SERVER['PATH_INFO']);
	$module=$path[1];
	$action=$path[2];
}
//file exist
if(!file_exists('../webview/page/'.$module.'/'.$action.'.php')) {
	header('HTTP/1.1 404 Not Found');
	echo '<h1>404 Not Found</h1>';
	die();
}
//fix https url
if($config->reg['enable_ssl'] && $_SERVER['HTTPS']!='on') {
  header('Location: https://'.$config->reg['ssl_domain'].$_SERVER['REQUEST_URI']);
  exit();
}

function genpassv2($_pass, $id) {
  $_pass .= $id;
  $pass = hash('sha512', $_pass);
  $pass .= hash('sha512', str_replace($_pass[0], 'RubyRubyRu', $_pass));
  $pass .= $pass;
  return substr($pass, hexdec(substr(md5($_pass), ord($_pass[0]) % 30, 2)), 32);
}

error_reporting(E_ALL & E_NOTICE);
//module break
if($module != 'maintenance'){
	require_once '../webview/module/'.$module.'.php';
}


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="mobile-web-app-capable" content="yes">
	<meta content="black-translucent" name="apple-mobile-web-app-status-bar-style">
	<meta http-equiv="Cache-Control" content="no-siteapp"/>
	<meta name="apple-mobile-web-app-title" content="LLSupport">
	<meta content="telephone=no" name="format-detection"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>PCF_WEBVIEW_<?=$module.'/'.$action?></title>
    <!-- CSS -->
	<link href="/assets/css/mdui.min.css?v=<?=time()?>" rel="stylesheet" />
	<link href="/assets/css/doc.css?v=<?=time()?>" rel="stylesheet" />
	<link href="/assets/css/main.css?v=<?=time()?>" rel="stylesheet"/>
</head>
<body class="mdui-loaded mdui-locked mdui-theme-primary-pink mdui-theme-accent-pink" style="overflow-y: auto !important;">
	<?php require_once '../webview/page/'.$module.'/'.$action.'.php'; ?>
	<!-- Script -->
	<script src="/assets/js/smooth-scroll.js?v=<?=time()?>"></script>
	<script src="/assets/js/holder.js?v=<?=time()?>"></script>
	<script src="/assets/js/highlight.js?v=<?=time()?>"></script>
	<script type="text/javascript" src="/assets/js/mdui.js?v=<?=time()?>" ></script>
	<script>var $$ = mdui.JQ;</script>
	<script src="/assets/js/main.js?v=<?=time()?>"></script>
	<script type="text/javascript">
	</script>
</body>
</html>

