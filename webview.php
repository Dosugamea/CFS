<?php
error_reporting(E_ALL); 

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
      $error .= '  at '.$v['function'].' ('.$v['file'].':'.$v['line'].")\n";
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
    require 'webview/maintenance/bomb.php';
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

require('includes/db.php');
$mysql->query('start transaction');

session_start();
//第一次访问把所有内容全存进session
if (isset($_SERVER['HTTP_AUTHORIZE'])) {
  $_SESSION['server'] = $_SERVER;
}

if (!isset($_SESSION['server'])) {
  header('HTTP/1.1 403 Forbidden');
  echo '<h1>出现了一些问题，请尝试关闭页面重新打开</h1>';
  die();
}

if(!isset($_SERVER['PATH_INFO']) || $_SERVER['PATH_INFO']=='') {
  $module='announce';
  $action='index';
} else {
  $module=explode('/', $_SERVER['PATH_INFO']);
  $action=$module[2];
  $module=$module[1];
}

if(!file_exists('webview/'.$module.'/'.$action.'.php')) {
  header('HTTP/1.1 404 Not Found');
  echo '<h1>404 Not Found</h1>';
  die();
}
require_once 'webview/'.$module.'/'.$action.'.php';
