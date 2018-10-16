<?php
function announce_info(){
    global $config;
    $result = [];

    $phraseCommand = function ($cmd) {
        exec($cmd, $output);
        if(count($output) == 0){
            $result = "Unknown";
        }else{
            $result = $output[0];
        }
        return $result;
    };

    if(is_file(BASE_PATH.".git/HEAD")){
        $head = file_get_contents(BASE_PATH.".git/HEAD");
        $tmp = explode("/", $head);
        $result['branch'] = end($tmp);
    }else{
        $result['branch'] = "Unknown";
    }

    $result['commit']   = $phraseCommand("cd .. && git rev-parse --short HEAD");
    $result['date']     = $phraseCommand("cd .. && git log -1 --pretty=format:\"%ai\"");
    $result['bundle']   = isset($_SESSION['server']["HTTP_BUNDLE_VERSION"]) ? $_SESSION['server']["HTTP_BUNDLE_VERSION"] : 'Unknown';
    $result['server']   = $config->basic['server_ver'];
    $result['support']  = $config->basic['support_mail'];

    return $result;
}

function announce_index(){
    global $mysql;
    $announcement = $mysql->query("SELECT * FROM webview WHERE tab != 0 ORDER BY time DESC LIMIT 3")->fetchAll();
    $buttonInit = "";
    foreach($announcement as &$v) {
        $v['time'] = explode(' ', $v['time'])[0];
        if($v['detail_id'] != 0){
            $buttonInit .= "Button.initialize(document.getElementById('an_";
            $buttonInit .= $v['ID'];
            $buttonInit .= "'), function(){window.location.href='/webview.php/announce/detail/?detail_id=";
            $buttonInit .= $v['detail_id'];
            $buttonInit .= "';});\n";
        }
    }
    return [
        "announce" => $announcement,
        "button_initlize" => $buttonInit
    ];
}

function announce_detail(){
    global $mysql;
    $res = $mysql -> query("SELECT * FROM webview WHERE tab = 0 AND ID = ? LIMIT 1", [$_GET['detail_id']])->fetch();
    if(!$res){
        return [
            "title"     => "",
            "content"   => "找不到公告"
        ];
    }
    return [
        "title"     => $res['title'],
        "content"   => $res['content']
    ];
}