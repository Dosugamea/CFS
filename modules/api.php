<?php
//api.php 批量执行指令

function api_($post) {
  foreach($post as $v) {
    $ret2['status']=200;
    $ret2['commandNum']=false;
    $ret2['timestamp']=time();
    $module=$v['module'];
    $action=$v['action'];
    unset($v['module']);
    unset($v['action']);
    unset($v['timeStamp']);
    $ret2['result']=runAction($module, $action, $v);
    $ret[]=$ret2;
  }
  return $ret;
}

?>