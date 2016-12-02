<?php
if(!isset($_GET['disp_faulty']) || !is_numeric($_GET['disp_faulty'])) {
  $_GET['disp_faulty']=0;
}
$announcement=$mysql->query('select * from webview where tab='.($_GET['disp_faulty']+1).' order by `order` desc, time desc')->fetchAll();
$has_new=$mysql->query('select distinct tab from webview where to_days(time)>to_days(CURRENT_TIMESTAMP)-5')->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,user-scalable=no" />
  <title>お知らせ一覧</title>
  <link href="/resources/css/jquery-ui.min.css" type="text/css" rel="stylesheet" />
  <link href="/resources/css/public.css" type="text/css" rel="stylesheet" />
  <script type="text/javascript" src="/resources/js/jquery.min.js"></script>
  <script type="text/javascript" src="/resources/js/jquery-ui.min.js"></script>
</head>
<body>
<div class="contentbox">
  <ul class="lltab">
    <li<?php if (!isset($_GET['disp_faulty']) || $_GET['disp_faulty'] == 0) { ?> class="on"<?php } ?>>
      <div class="bg1"></div>
      <div class="bg2"><a href="/webview.php/announce/announce?disp_faulty=0">通知</a></div>
    </li>
    <li<?php if (isset($_GET['disp_faulty']) && $_GET['disp_faulty'] == 1) { ?> class="on"<?php } ?>>
      <div class="bg1"></div>
      <div class="bg2"><a href="/webview.php/announce/announce?disp_faulty=1">游戏更新</a></div>
    </li>
    <li>
      <div class="bg1"></div>
      <div class="bg2"><a href="/webview.php/announce/index">返回主页</a></div>
    </li>
  </ul>
  <div class="news-list-bg">
    <ul class="news-list-ul">
      <?php foreach($announcement as $v) { $time=explode(' ', $v['time'])[0]; ?>
        <li>
          <a class="news-list-href" <?=($v['detail_id']?' href="/webview.php/announce/detail?0=&announce_id='.$v['detail_id'].'&disp_faulty='.$_GET['disp_faulty'].'"':'')?>>
            <div class="news-list-title">
              <div class="bg1"></div>
              <div class="bg2"><?=$v['title']?></div>
            </div>
            <div class="news-list-content">
              <p><?=$v['content']?></p>
              <p class="detail"><?=($v['detail_id']?'※点击查看详情':'')?></p>
              <p><?=$time?></p>
            </div>
          </a>
        </li>
      <?php } ?>
    </ul>
  </div>
</div>
</body>
</html>