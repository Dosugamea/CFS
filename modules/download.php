<?php
//download.php 下载module

//download/additional 下载附加内容
function download_additional($post) {
	global $uid, $mysql, $additional_for_android, $additional_for_ios;
	include '../config/modules_download.php';
	$ret = $mysql->query(
		"SELECT update_id as download_additional_id, url, size FROM packages
		WHERE package_id = ? AND package_type = ? AND os = ?"
	, [$post['package_id'], $post['package_type'], $post['os']])->fetchAll(PDO::FETCH_ASSOC);
	pl_assert($ret, "找不到下载：ID：{$post['package_id']} 种类：{$post['package_type']} 系统：{$post['os']}");
	return $ret;
}

function download_batch($post) {
	global $uid, $mysql;
	$ret = $mysql->query(
		"SELECT package_id, url, size FROM packages
		WHERE package_type = ? AND os = ?"
	, [$post['package_type'], $post['os']])->fetchAll(PDO::FETCH_ASSOC);
	$ret = array_merge(array_filter($ret, function ($e) use ($post) {
		return array_search($e['package_id'], $post['excluded_package_ids']) === false;
	}));
	return $ret;
}

function download_event($post) {
	return download_batch($post);
}

function download_getUrl($post) {
	global $getUrl_address;
	return ['url_list' => array_map(function ($e) use ($getUrl_address) {
		return $getUrl_address . $e;
	}, $post['path_list'])];
}

//download/update 下载更新
function download_update($post) {
	global $uid, $mysql;
	if ($post['os'] != 'Android' && $post['os'] != 'iOS') {
		return [];
	}
	$ver = version_compare($post['install_version'], $post['external_version'], '<') ? $post['install_version'] : $post['external_version'];
	$ver = explode('.', $ver);
	$ver = $ver[0] * 1000 + $ver[1] * 10;
	$pkg = ['99_1'];
	foreach ($post['package_list'] as $v) {
		$pkg[] = $v['package_type'] . '_' . $v['package_id'];
	}
	$dl = $mysql->query("SELECT * FROM packages WHERE os='{$post['os']}' AND version > $ver AND concat(package_type, '_', package_id) in ('" . implode($pkg, "','") . "')");
	$ret = [];
	while ($col = $dl->fetch()) {
		$ret[] = [
			"download_update_id" => (int)$col['update_id'],
			"url" => $col['url'],
			"size" => (int)$col['size'],
			"version" => floor($col['version'] / 1000) . '.' . ($col['version'] % 1000),
			"package_type" => (int)$col['package_type'],
			"package_id" => (int)$col['package_id']
		];
	}
	$version = [];
	$order = [];
	foreach ($ret as $v) {
		$order[] = array_search($v['package_type'] . '_' . $v['package_id'], $pkg);
		$version[] = $v['version'];
	}
	array_multisort($version, $order, $ret);
	//下载私服专用包
	$res = $mysql->query(
	'SELECT extend_download.* FROM extend_download LEFT JOIN extend_download_queue
	ON extend_download.ID = extend_download_queue.download_id
	WHERE user_id='.$uid.' AND (downloaded_version < version OR downloaded_version=0)')->fetchAll();
	if (!empty($res)) {
		foreach ($res as $row) {
			$ret2['download_update_id'] = (int)$row[0] * -1;
			$ret2['url'] = $row[1];
			$ret2['size'] = (int)$row[2];
			$ret[] = $ret2;
			if ($row['version'] == 0) {
			$mysql->exec("DELETE FROM extend_download_queue WHERE user_id=$uid AND download_id={$row[0]}");
			} else {
			$mysql->exec("UPDATE extend_download_queue SET downloaded_version={$row['version']} WHERE user_id='.$uid AND download_id={$row[0]}");
			}
		}
	}
	return $ret;
}
?>
