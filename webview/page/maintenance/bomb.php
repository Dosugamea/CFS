<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />
<meta name="viewport" content="width=device-width,user-scalable=no" />
<title>服务器爆炸了！</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
#left {
    position: absolute;
    left: 0;
    top: 0;
    z-index: 1;
    width: 50%;
    height: 100%;
}
#bomb-img {
    width: 100%;
    height: 100%;
    background: #fff url("/resources/error.png") no-repeat center center;
    background-size: contain;
}
#right {
    position: absolute;
    right: 0;
    top: 0;
    z-index: 2;
    width: 50%;
    height: 100%;
}
#right p {
    width: 100%;
    height: 5%;
}
#right textarea {
    display: block;
    width: 100%;
    height: 95%;
    resize: none;
}
</style>
</head>

<body>
<h1>服务器爆炸了！</h1>
<div>
    <div id="left">
        <div id="bomb-img"></div>
    </div>
    <div id="right">
        <p>错误信息已被记录，我们会尽早修复。</p>
        <textarea><?=$result['text']?></textarea>
    </div>
</div>
</body>
</html>