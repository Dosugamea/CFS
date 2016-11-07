<?php
require 'config/maintenance.php';

if (isset($_SESSION['server']['HTTP_USER_ID'])) {
  $uid = $_SESSION['server']['HTTP_USER_ID'];
} else {
  $uid = 0;
}

if ($maintenance==true && array_search($uid, $bypass_maintenance)===false) {
  header('Location: /webview.php/maintenance/maintenance');
} else if ($uid == -1) {
  header('Location: /webview.php/login/welcome');
} else if (isset($_SESSION['server']['HTTP_BUNDLE_VERSION'])
  && preg_match('/^[0-9\.]+$/', $_SESSION['server']['HTTP_BUNDLE_VERSION'])
  && version_compare($_SESSION['server']['HTTP_BUNDLE_VERSION'], $bundle_ver, '<')) {
  header('Location: /webview.php/maintenance/bundleUpdate');
} else {
  if (isset($restrict_ver)) {
    $perm = $mysql->query('select value from user_params where user_id=? and param="allow_test_func"', [$uid])->fetchColumn();
    if (!$perm && isset($_SESSION['server']['HTTP_BUNDLE_VERSION']) && $_SESSION['server']['HTTP_BUNDLE_VERSION'] == $restrict_ver) {
      header('Location: /webview.php/maintenance/restrictedClientVersion');
      die();
    }
  }
  $enable_update = ($_SESSION['server']['HTTP_OS'] == 'Android' && $update_for_android) || ($_SESSION['server']['HTTP_OS'] == 'iOS' && $update_for_ios);
  if (!$enable_update && isset($_SESSION['server']['HTTP_CLIENT_VERSION'])
    && version_compare($_SESSION['server']['HTTP_CLIENT_VERSION'], $server_ver, '<')) {
    header('Location: /webview.php/maintenance/clientUpdate');
  } else {
    header('Location: /webview.php/maintenance/bomb');
  }
}
