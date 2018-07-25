<? 
  header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>お知らせ一覧</title>

<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="width=880, target-densitydpi=device-dpi, user-scalable=no">

<link rel="stylesheet" href="/resources/things/list.css">
<link rel="stylesheet" href="/resources/things/perfect-scrollbar.css">

<script src="/resources/things/perfect-scrollbar.min.js"></script>
<script src="/resources/things/button.js"></script>
<script src="/resources/things/list.js"></script>
</head>

<body>
<?php
$announcement=$mysql->query('select * from webview where tab = 1 OR tab = 2 order by time desc')->fetchAll();
?>

  <ul id="tab">
      <li class="foo">
    <a href="/webview.php/announce/index">
      <img src="/resources/things/tab/tab1_off.png" alt="お知らせ">
    </a>
  </li>
    <li class="on">
    <a href="">
      <img src="/resources/things/tab/tab2_on.png" alt="お知らせ">
    </a>
  </li>
        <li class="off">
    <a href="/webview.php/announce/info">
      <img src="/resources/things/tab/tab3_off.png" alt="不具合">
    </a>
  </li>
    </ul>
<div id="main">
  <div id="container">

    <ul id="list">
      <SCRIPT type="text/javascript">
if(strUA.indexOf("iphone") >= 0 || strUA.indexOf("ipad") >= 0) {
  document.write('<div class="note" style="margin-top:100px;">');
} else {
  document.write('<div class="note">');
}
</SCRIPT>



<?php foreach($announcement as $v) {
  $time=explode(' ', $v['time'])[0];
?>
      <li class="entry" >
        <div class="entry-container" id="an_<?=$v['ID']?>">
          <h2 class="text"><?=$v['title']?></h2>
          <div class="summary"> <?=$v['content']?></div>
          <div class="start-date"><?=$time?></div>
          <div class="clearfix"></div>
        </div>
      </li>

<?php } ?>
</ul>
    <div id="load-next" data-loading-msg="（読み込み中…）" data-no-more-msg="（これ以上お知らせはありません）" style="display: none !important;">
      次の10件を表示
    </div>
  </div>
</div>

<script>
  const URL_BASE = '/webview.php';
  const DISP_FAULTY = 0;
  const USER_ID = 0;
  const AUTHORIZE_DATA = '';
  updateButtons();
  <?
    foreach ($announcement as $d){
      if($d['detail_id'] != 0){
        print("Button.initialize(document.getElementById('an_{$d['ID']}'), function() {
    window.location.href='/webview.php/announce/detail/?detail_id={$d['detail_id']}';
  });");
      }
    }
  ?>
  Button.initialize(document.getElementById('load-next'), loadNext);
  Ps.initialize(document.getElementById('container'), {suppressScrollX: true});
</script>
</body>
</html>