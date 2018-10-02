<?php
define("CONTROLLER", "webview");
define("BASE_PATH", __DIR__."/../");
date_default_timezone_set("Asia/Tokyo");
header("X-Powered-By: Project Custom Festival");
header("Y-Powered-By: LLS/0.3");
header('Server: LLS/0.3');
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Copyright: PCF@2018');

require(__DIR__.'/../includes/includeCommon.php');
require(__DIR__.'/../includes/passwordUtil.php');
require(__DIR__.'/../includes/logger.php');
$logger = new log;

//HTTPS强制跳转
if($config->reg['enable_ssl'] && $_SERVER['SERVER_PORT'] == "80") {
	header('Location: https://'.$config->reg['ssl_domain'].$_SERVER['REQUEST_URI']);
	exit();
}

$mysql->query('START TRANSACTION');
session_start();
//第一次访问把所有内容全存进session
if (isset($_SERVER['HTTP_AUTHORIZE'])) {
	$_SESSION['server'] = $_SERVER;
	$authorize_ = $_SERVER['HTTP_AUTHORIZE'];
	$uid = isset($_SERVER['HTTP_USER_ID']) ? $_SERVER['HTTP_USER_ID'] : false;
}else if(isset($_GET['external'])){
	//从外置浏览器打开
	$authorize_ = "token=".$_GET['token'];
	$_SESSION['server']['HTTP_AUTHORIZE'] = $authorize_;
}else if(isset($_SESSION['server'])){
	$authorize_ = $_SESSION['server']['HTTP_AUTHORIZE'];
	$uid = isset($_SESSION['server']['HTTP_USER_ID']) ? $_SESSION['server']['HTTP_USER_ID'] : false;
}else{
	header('HTTP/1.1 403 Forbidden');
	print("<h1>出现了一些问题，请尝试关闭页面重新打开</h1>");
	exit();
}


//处理authorize

$authorize = [];

$authorize_ = explode("&", $authorize_);
foreach($authorize_ as $i){
	$j = explode("=", $i);
	$authorize[$j[0]] = $j[1];
}

//default page
$path = explode('/', $_SERVER['PATH_INFO']);
if(count($path) == 3){
	$module = $path[1];
	$action = $path[2];
}else if(count($path) == 2){
	$module = $path[1];
	$action = NULL;
}else{
	header('HTTP/1.1 403 Forbidden');
	print("<h1>URI无效</h1>");
}

function runWebview($module, $action){
	global $result;
	require_once(BASE_PATH."webview/modules/".$module.".php");
	$funcName = $module.'_'.$action;
	if(function_exists($funcName)){
		$result = call_user_func($funcName);
	}
}
function goDie(){
	global $result;
	header("Content-Type: application/json");
	print(json_encode($result));
	exit();
}

//处理XHR请求，不渲染页面
if($module == "api"){
	require_once(BASE_PATH."webview/modules/api.php");
	define("CONTROLLER_MODULE", "api");
	$post = json_decode(file_get_contents("php://input"), true);
	if($post == NULL){
		$result = [
			"status" => -1,
			"errmsg" => "提交数据解析失败"
		];
		goDie();
	}else{
		if(!isset($post['module']) || !isset($post['action'])){
			$result = [
				"status" => -2,
				"errmsg" => "无module或action"
			];
			goDie();
		}
		$apiFuncName = $post['module'].'_'.$post['action'];
		if(!function_exists($apiFuncName)){
			$result = [
				"status" => -3,
				"errmsg" => "找不到对应的函数"
			];
			goDie();
		}

		$result = call_user_func($apiFuncName, $post['payload']);
		goDie();
	}
}

//file exist
$pagePath = sprintf(__DIR__."/../webview/page/%s/%s.php", $module, $action);
if(!file_exists($pagePath)) {
	header('HTTP/1.1 404 Not Found');
	print("<h1>404 Not Found</h1>");
	exit();
}

$result = [];
runWebview($module, $action);
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
    <title>PCF_WEBVIEW</title>
    <!-- CSS -->
	<link href="/assets/css/mdui.min.css" rel="stylesheet" />
	<link href="/assets/css/doc.css?v=<?=time()?>" rel="stylesheet" />
	<link href="/assets/css/main.css?v=<?=time()?>" rel="stylesheet"/>
	<!-- Script -->
	<script type="text/javascript" src="/assets/js/jquery.min.js"></script>
	<script type="text/javascript" src="/assets/js/jsencrypt.js"></script>
	<script type="text/javascript" src="/assets/js/smooth-scroll.js"></script>
	<script type="text/javascript" src="/assets/js/holder.js"></script>
	<script type="text/javascript" src="/assets/js/highlight.js"></script>
	<script type="text/javascript" src="/assets/js/mdui.js" ></script>
	<script>var $$ = mdui.JQ;</script>
	<script type="text/javascript" src="/assets/js/main.js?v=<?=time()?>"></script>
</head>
<body class="mdui-loaded mdui-locked mdui-theme-primary-pink mdui-theme-accent-pink" style="overflow-y: auto !important;">
	<?php require_once("../webview/page/".$module."/".$action.".php"); ?>
</body>
</html>

