<?php

function login_login(){
    global $mysql, $authorize, $config;
    $token = $authorize['token'];
    $username = $mysql->query('SELECT username, password FROM tmp_authorize WHERE token = ?', [$token])->fetch();
    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
 	if(strpos($ua, "iphone") || strpos($ua, "ipad")){
        $device_type = "ios";
    }else{
        $device_type = "other";
    }
    
    if (!$username) {
        echo("<h1>出现了错误，请关闭此页面重新进入</h1>");
        exit();
    }
    return [
        "device_type"   => $device_type,
        "username"      => $username['username'],
        "pub_key"       => $config->basic['pub_key'],
        "token"         => $token
    ];
}

function login_reg(){
    return login_login();
}

function login_logout(){
    global $mysql, $authorize, $config;
    $token = $authorize['token'];

    if(!isset($_GET['confirm'])) {
        $status = "CONFIRM_PAGE";
    }else{
        $status = "SUCCESS_PAGE";
        $mysql->query("UPDATE users SET `username` = '', `password` = '', `authorize_token` = '' WHERE `authorize_token` = ?", [$token]);
    }

    return [
        "status"    => $status
    ];
}