<?php 
date_default_timezone_set("Asia/Tokyo");

//后面感觉会经常用这个BASE PATH
define("BASE_PATH", __DIR__."/../");
//定义控制器
define("CONTROLLER", "main");
//LOG记录模块
require(BASE_PATH."includes/logger.php");
$logger = new log;

/* include所有includes目录下的文件 */
require(BASE_PATH."includes/errorHandler.php");
require(BASE_PATH."includes/errorUtil.php");
require(BASE_PATH."includes/configManager.php");

/* 配置管理器 */
$config = new configManager;

require(BASE_PATH."includes/envi.php");
require(BASE_PATH."includes/AES.php");
require(BASE_PATH."includes/RedLock.php");
require(BASE_PATH."includes/db.php");
require(BASE_PATH."includes/energy.php");
require(BASE_PATH."includes/event.php");
require(BASE_PATH."includes/exchange.php");
require(BASE_PATH."includes/extend_avatar.php");
require(BASE_PATH."includes/item.php");
require(BASE_PATH."includes/ipUtils.php");
require(BASE_PATH."includes/live.php");
require(BASE_PATH."includes/passwordUtil.php");
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

/* 验证访问合法性 */

if(!isset($_SERVER['HTTP_AUTHORIZE'])){
	throw403('ILLEGAL_ACCESS');
}
if(!isset($_SERVER['PATH_INFO'])) {
	throw403('NO_PATH_INFO');
}

/* 写入访问日志 */
$envi = new envi;

if(!file_exists("../PLSAccess.log")){
	fopen("../PLSAccess.log", "w");
}
$LOGFILE = fopen("../PLSAccess.log","a");
fwrite($LOGFILE, date("Y-m-d H:i:s"));
fwrite($LOGFILE, " ".$_SERVER['PATH_INFO']);
fwrite($LOGFILE, " ".$envi->ip);
if(isset($_SERVER['HTTP_USER_ID'])){
	fwrite($LOGFILE, " ".$_SERVER['HTTP_USER_ID']."\r\n");
}else{
	fwrite($LOGFILE, " Unknown user\r\n");
}
fclose($LOGFILE);

/* 初始化环境 */
$envi->checkAll();
$uid = &$envi->uid;

/* 检查是否维护 */
if (((strtotime($config->maintenance['maintenance_start']) < time() && 
	strtotime($config->maintenance['maintenance_start']) > time()) || 
	$config->maintenance['maintenance']) && 
	gettype($envi->uid) == "integer" &&
	!in_array($envi->uid, $config->maintenance['bypass_maintenance'])) {
	header('Maintenance: 1');
	die();
}
if(in_array($_SERVER['PATH_INFO'], $config->maintenance['maintenance_endpoint'])){
	header('Maintenance: 1');
	die();
}

//有用户的时候读取道具信息
if($envi->uid){
	$envi->initItem();
	$envi->initUser();
}

/* 维护及更新 */
$bundle_ver = $config->basic['bundle_ver'];
$server_ver = $config->basic['server_ver'];
//客户端版本
if (!isset($_SERVER['HTTP_BUNDLE_VERSION'])) {
	throw403('NO_BUNDLE_VERSION');
}
if (!isset($_SERVER['HTTP_CLIENT_VERSION'])) {
	throw403('NO_CLIENT_VERSION');
}

if (version_compare($_SERVER['HTTP_BUNDLE_VERSION'], $bundle_ver, '<')) {
	header('Maintenance: 1');
	die();
}

//数据包版本
if (version_compare($_SERVER['HTTP_CLIENT_VERSION'], $server_ver, '<')) {
	if(floor((float)$_SERVER['HTTP_CLIENT_VERSION']) < floor((float)$server_ver)){
		$version_array = explode(".", $_SERVER['HTTP_CLIENT_VERSION']);
		$version_len = count($version_array);
		$version_array[$version_len - 1] = (string)((int)$version_array[$version_len - 1] + 1);
		header("Server-Version: ".implode(".",$version_array));
	}
	else{
		header("Server-Version: ".$server_ver);
	}
}

//扩展下载 TODO
/*if (isset($uid)) {
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
}*/


function runAction($module, $action, $post=[], ...$args) {
	global $envi;
	if (isset($envi->params) && $envi->params['allow_test_func'] && file_exists('../modules.dev/'.$module.'.php')) {
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
	return call_user_func($module.'_'.$action, $post, $args);
}

//处理POST数据
if (isset($_POST['request_data'])) {
	$post = json_decode($_POST['request_data'], true);
} else {
	print("INVALID_POST_DATA");
	exit();
}

//处理调用URI
$action = explode('/', $_SERVER['PATH_INFO']);
if (!isset($action[1])) {
	print("INVALID_URI");
	exit();
}
if (!isset($action[2])) {
	$action[2] = '';
}

//防止重放攻击
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
$ret['release_info'] = $config->basic['release_info'];

/* 写回对users和params的修改 */
if($envi->uid){
	$envi->saveAll();
}

/* 处理用户请求 */
if(!isset($ret['status_code'])){
	$ret['status_code'] = 200;
}

/*写入日志*/
if($log){
	if(error_get_last() == NULL){
		$mysql->query("INSERT INTO log VALUES(?, ?, ?, ?, ?, ?)", [
		$post['commandNum'], 
		date("Y-m-d H:i:s", time()), 
		$post['module'], 
		$post['action'], 
		json_encode($post), 
		json_encode($ret)]);
	}else{
		$logger->i("Error occured, response will not cache.");
	}
}

$ret = json_encode($ret);
function retError($statusCode) {
	global $ret, $logger;
	$ret['status_code'] = 600;
	return ['error_code' => $statusCode];
}

$mysql->query('commit');
//header('Server-Version: '.$server_ver);
$authorize = [
	"consumerKey"		=> "lovelive_test",
	"timeStamp"			=> time(),
	"version"			=> "1.1",
	"token"				=> $envi->authorize['token'],
	"nonce"				=> $envi->authorize['nonce'],
	"requestTimeStamp"	=> $envi->authorize['timeStamp']
];
header("authorize: ".http_build_query($authorize));
$XMS = RSAsign($ret.$_SERVER['HTTP_X_MESSAGE_CODE']);
header("X-Message-Sign: ".$XMS);
header('Content-Type: application/json');
header("X-Powered-By: Project Custom Festival");
print($ret);
