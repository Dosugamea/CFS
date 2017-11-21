<?
  $k = $mysql -> query("SELECT * FROM webview WHERE tab = 0 AND ID =".$_GET['detail_id']." LIMIT 1")->fetch();
?>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="GENERATOR" content="MSHTML 11.00.10011.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="viewport" content="width=880, target-densitydpi=device-dpi, user-scalable=no">

    <link rel="stylesheet" href="/resources/things/detail.css">
    <link rel="stylesheet" href="/resources/things/perfect-scrollbar.css">
    <link rel="stylesheet" href="/resources/things/list2.css">

    <script src="/resources/things/perfect-scrollbar.min.js"></script>
    <script src="/resources/things/button.js"></script>
  </head>
  <body>
    <div id="outer">
      <div id="inner">
        <div id="header">
          <?=$k['title']?>
          <div id="back"></div>
        </div>
        <div id="body">
          <?=$k['content']?>
        </div>
      </div>
    </div>
    <script>
      Button.initialize(document.getElementById('back'), function() {
        window.history.go(-1);
      });
      Ps.initialize(document.getElementById('body'), {suppressScrollX: true});
    </script>
  </body>
</html>