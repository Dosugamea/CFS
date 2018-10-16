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
    $head = @file_get_contents(BASE_PATH.".git/HEAD");
    if($head == NULL){
        $result['branch'] = "Unknown";
    }else{
        $tmp = explode("/", $head);
        $result['branch'] = end($tmp);
    }
    $result['commit']   = $phraseCommand("cd .. && git rev-parse --short HEAD");
    $result['date']     = $phraseCommand("cd .. && git log -1 --pretty=format:\"%ai\"");
    $result['bundle']   = isset($_SESSION['server']["HTTP_BUNDLE_VERSION"]) ? $_SESSION['server']["HTTP_BUNDLE_VERSION"] : 'Unknown';
    $result['server']   = $config->basic['server_ver'];
    $result['support']  = $config->basic['support_mail'];

    return $result;
}