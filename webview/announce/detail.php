<?php
$announcement=$mysql->query('select * from webview where id='.($_GET['announce_id']))->fetch();
$time=explode(' ', $announcement['time'])[0];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,user-scalable=no" />
    <title></title>
    <link href="/resources/css/jquery-ui.min.css" type="text/css" rel="stylesheet" />
    <link href="/resources/css/public.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="/resources/js/jquery.min.js"></script>
    <script type="text/javascript" src="/resources/js/jquery-ui.min.js"></script>
</head>
<body>
<div class="news-detail-title">
    <div class="bg1"></div>
    <div class="bg2"><a class="back" href="/webview.php/announce/announce?disp_faulty=<?=$_GET['disp_faulty']?>"></a><span><?=$announcement['title']?></span></div>
</div>
<div class="news-detail-bg">
    <div class="news-detail-content">
        <p><?=$announcement['content']?></p>
        <p><?=$time?></p>
    </div>
</div>
</body>
</html>