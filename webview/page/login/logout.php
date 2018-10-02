<meta charset='utf-8' />
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
    <style type="text/css">
        #noah{width: 250px;height: 50px;background-image: url(/resources/things/tab/noah.png);background-position: center;background-size: 88%;margin: 5px;background-repeat: no-repeat;}
    </style>

</head>
<body>
    <div id="outer">
    <div id="inner">
        <div id="header">
            <h2>维护</h2>

        </div>

<div id="body">

<?php if($result['status'] == "CONFIRM_PAGE"){?>
    <h3>确定要退出登录吗？</h3>
    <a href="/webview.php/login/logout?confirm=1">是</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/webview.php/settings/index">否</a>
<?php }else if($result['status'] == "SUCCESS_PAGE"){?>
    <h3>退出成功</h3>
<?php }?>
</div>
    </div>
</div>
</div>
    </div>
</div>

<script>
    Ps.initialize(document.getElementById('body'), {suppressScrollX: true});
     Button.initialize(document.getElementById('noah'), function() {
        window.location.href='native://browser?url=http://dash.moe';
    });
</script>
</body>
