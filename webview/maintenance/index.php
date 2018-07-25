<?php
//判断Maintenance=1时应该跳转到哪

if (isset($_SESSION['server']['HTTP_USER_ID'])) {
	$uid = $_SESSION['server']['HTTP_USER_ID'];
} else {
	$uid = 0;
}

if (((strtotime($config->maintenance['maintenance_start']) < time() && strtotime($config->maintenance['maintenance_end']) > time()) || 
$config->maintenance['maintenance']) && !isset($config->maintenance['bypass_maintenance'][$uid])) {
	header('Location: /webview.php/maintenance/maintenance');
} else if ($uid == -1) {
	header('Location: /webview.php/login/welcome');
} else if (isset($_SESSION['server']['HTTP_BUNDLE_VERSION'])
	&& preg_match('/^[0-9\.]+$/', $_SESSION['server']['HTTP_BUNDLE_VERSION'])
	&& version_compare($_SESSION['server']['HTTP_BUNDLE_VERSION'], $bundle_ver, '<')) {
	header('Location: /webview.php/maintenance/bundleUpdate');
} else {
	if (isset($restrict_ver)) {
		$perm = $mysql->query('SELECT value FROM user_params WHERE user_id = ? and param = "allow_test_func"', [$uid])->fetchColumn();
		if (!$perm && isset($_SESSION['server']['HTTP_BUNDLE_VERSION']) && $_SESSION['server']['HTTP_BUNDLE_VERSION'] == $restrict_ver) {
			header('Location: /webview.php/maintenance/restrictedClientVersion');
			exit();
		}
	}
	header('Location: /webview.php/maintenance/bomb');
}
