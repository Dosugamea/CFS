<?php 
date_default_timezone_set("Asia/Tokyo");

//后面感觉会经常用这个BASE PATH
define("BASE_PATH", __DIR__."/../");
//LOG记录模块
require(BASE_PATH."includes/logger.php");
$logger = new log;

/* include所有includes目录下的文件 */
require(BASE_PATH."includes/errorHandler.php");
require(BASE_PATH."includes/errorUtil.php");
require(BASE_PATH."includes/configManager.php");
require(BASE_PATH."includes/envi.php");
require(BASE_PATH."includes/AES.php");
require(BASE_PATH."includes/db.php");
require(BASE_PATH."includes/energy.php");
require(BASE_PATH."includes/event.php");
require(BASE_PATH."includes/exchange.php");
require(BASE_PATH."includes/extend_avatar.php");
require(BASE_PATH."includes/item.php");
require(BASE_PATH."includes/live.php");
require(BASE_PATH."includes/present.php");
require(BASE_PATH."includes/RSA.php");
require(BASE_PATH."includes/sendmail.php");
require(BASE_PATH."includes/SIS.php");
require(BASE_PATH."includes/unit.php");
require(BASE_PATH."includes/util.php");

/* 连接数据库 */
$mysql->query('start transaction');
$rolled_back = false;
function rollback() {
	global $rolled_back, $mysql;
	$rolled_back = true;
	$mysql->query('rollback');
}

/* 配置管理器 */
$config = new configManager;

/* 写入访问日志 */
if(!file_exists("../PLSAccess.log")){
	fopen("../PLSAccess.log", "w");
}
$LOGFILE = fopen("../PLSAccess.log","a");
fwrite($LOGFILE,date("Y-m-d H:i:s"));
fwrite($LOGFILE," ".$_SERVER['PATH_INFO']);
fwrite($LOGFILE," ".$_SERVER["REMOTE_ADDR"]);
if(isset($_SERVER['HTTP_USER_ID'])){
	fwrite($LOGFILE," ".$_SERVER['HTTP_USER_ID']."\r\n");
}else{
	fwrite($LOGFILE," Unknown user\r\n");
}
fclose($LOGFILE);

/* 验证访问合法性 */
if(!isset($_SERVER['PATH_INFO'])) {
	throw403('NO_PATH_INFO');
}
if(!isset($_SERVER['HTTP_AUTHORIZE'])){
	throw403('ILLEGAL_ACCESS');
}

/* 初始化环境 */
$envi = new envi;
$envi->checkAll();

/* 检查是否维护 */
if (((strtotime($configManager->maintenance['maintenance_start']) < time() && 
	strtotime($configManager->maintenance['maintenance_start']) > time()) || 
	$maintenance) && 
	gettype($envi->uid) == "integer" &&
	!in_array($envi->uid, $configManager->maintenance['bypass_maintenance'])) {
	header('Maintenance: 1');
	die();
}

//这个好像是注册用的？暂时不明
if (isset($_SERVER['HTTP_USER_ID']) && $_SERVER['HTTP_USER_ID'] == -1) {
	header('Maintenance: 1');
	die();
}

//有用户的时候读取道具信息
if($envi->uid){
	$envi->initItem();
	$envi->initUser();
}

/* 维护及更新 */
require '../config/maintenance.php';
//客户端版本
if (!isset($_SERVER['HTTP_BUNDLE_VERSION'])) {
	throw403('NO_BUNDLE_VERSION');
}
if (!isset($_SERVER['HTTP_CLIENT_VERSION'])) {
	throw403('NO_CLIENT_VERSION');
}

if (isset($_SERVER['HTTP_BUNDLE_VERSION']) && preg_match('/^[0-9\.]+$/', $_SERVER['HTTP_BUNDLE_VERSION']) && version_compare($_SERVER['HTTP_BUNDLE_VERSION'], $bundle_ver, '<')) {
	header('Maintenance: 1');
	die();
}

//数据包版本
if (version_compare($_SERVER['HTTP_CLIENT_VERSION'], $server_ver, '<')) {
	if (($_SERVER['HTTP_OS'] == 'Android' && $update_for_android == true) || ($_SERVER['HTTP_OS'] == 'iOS' && $update_for_ios == true)) {
		if(floor((float)$_SERVER['HTTP_CLIENT_VERSION']) < floor((float)$server_ver)){
			$version_array = explode(".", $_SERVER['HTTP_CLIENT_VERSION']);
			$version_len = count($version_array);
			$version_array[$version_len - 1] = (string)((int)$version_array[$version_len - 1] + 1);
			header("Server-Version: ".implode(".",$version_array));
		}
		else
			header("Server-Version: ".$server_ver);
	} else {
		header('Maintenance: 1');
		die();
	}
}

//扩展下载
if (isset($uid)) {
	$res = $mysql->query('
		SELECT extend_download.* FROM extend_download_queue
		LEFT JOIN extend_download
		ON extend_download.ID=extend_download_queue.download_id
		WHERE downloaded_version < version OR downloaded_version=0
		AND extend_download_queue.user_id='.$uid
	)->fetch();
	if (!empty($res)) {
		$version_array = explode(".", $_SERVER['HTTP_CLIENT_VERSION']);
		$version_len = count($version_array);
		$version_array[$version_len - 1] = (string)((int)$version_array[$version_len - 1] + 1);
		header("Server-Version: ".implode(".",$version_array));
	}
}


function runAction($module, $action, $post=[]) {
	global $params;
	if (isset($params) && $params['allow_test_func'] && file_exists('../modules.dev/'.$module.'.php')) {
		require_once '../modules.dev/'.$module.'.php';
	} else {
		if (!file_exists('../modules/'.$module.'.php')) {
			return [];
		}
		require_once '../modules/'.$module.'.php';
	}
	if (!function_exists($module.'_'.$action)) {
		return [];
	}
	if (empty($post)) {
		return call_user_func($module.'_'.$action);
	}
	return call_user_func($module.'_'.$action, $post);
}

if (isset($_POST['request_data'])) {
	$post = json_decode($_POST['request_data'],true);
} else {
	$post = [];
}
$action = explode('/', $_SERVER['PATH_INFO']);
if (!isset($action[2])) {
	$action[2]='';
}
if(isset($post['commandNum']) && isset($post['module'])){
	$cached_history = $mysql->query("SELECT * FROM log WHERE command_num = ?",[$post['commandNum']])->fetch(PDO::FETCH_ASSOC);
	if($cached_history){
		$ret = $cached_history['response'];
		$XMS = RSAsign($ret.$_SERVER['HTTP_X_MESSAGE_CODE']);
		header("X-Message-Sign: ".$XMS);
		header('Content-Type: application/json');
		print($ret);
		exit();
	}else{
		$log = true;
	}
}else{
	$log = false;
}
$ret['response_data'] = runAction($action[1], $action[2], $post);
$ret['release_info'] = isset($release_info) ? $release_info : '[]';

/* 写回对users和params的修改 */
$env->saveAll();

/* 处理用户请求 */
if(!isset($ret['status_code'])){
	$ret['status_code'] = 200;
}

/*写入日志*/
if($log){
	$mysql->query("INSERT INTO log VALUES(?, ?, ?, ?, ?, ?)", [$post['commandNum'], date("Y-m-d H:i:s", time()), $post['module'], $post['action'], json_encode($post), json_encode($ret)]);
}

$ret = json_encode($ret);
function retError($statusCode) {
	global $ret;
	$ret['status_code'] = 600;
	return ['error_code' => $statusCode];
}

$mysql->query('commit');
//header('Server-Version: '.$server_ver);

$XMS = RSAsign($ret.$_SERVER['HTTP_X_MESSAGE_CODE']);
header("X-Message-Sign: ".$XMS);
header('Content-Type: application/json');
echo $ret;
