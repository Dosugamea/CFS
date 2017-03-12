<?php require('config/maintenance.php'); ?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="GENERATOR" content="MSHTML 11.00.10011.0">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="viewport" content="width=880, target-densitydpi=device-dpi, user-scalable=no">

	<link rel="stylesheet" href="/resources/things/detail.css?">
	<link rel="stylesheet" href="/resources/things/perfect-scrollbar.css">
	<link rel="stylesheet" href="/resources/things/list2.css">

	<script src="/resources/things/perfect-scrollbar.min.js"></script>
	<script src="/resources/things/button.js"></script>
	<style type="text/css">
		.main-text-head{text-align: center;}
		.time{width:100%;text-align: right;}
	</style>
</head>
<body>
	<div id="outer">
  <div id="inner">
    <div id="header">
      <h2>版本升级</h2>

    </div>

<div id="body">

请升级您的客户端版本。<br />
<br />

服务器版本：<?=$bundle_ver ?><br />
<br />
客户端版本：<?=$_SESSION['server']['HTTP_BUNDLE_VERSION'] ?>（过低）<br />


</div>
  </div>
</div>
</div>
  </div>
</div>

<script>
  Ps.initialize(document.getElementById('body'), {suppressScrollX: true});
</script>
</body>
</html>
