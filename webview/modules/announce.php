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
    $result['commit']   = $phraseCommand("cd .. && git rev-parse --short HEAD");
    $result['date']     = $phraseCommand("cd .. && git log -1 --pretty=format:\"%ai\"");
    $result['branch']   = "Unknown";
    $result['branch']   = file_get_contents(BASE_PATH.".git/HEAD");

    return $result;
}