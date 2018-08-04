<?php

function maintenance_bomb(){
    global $mysql, $uid;
    $error = $mysql->query("SELECT text, ID FROM error_report WHERE user_id = ? ORDER BY ID DESC LIMIT 1", [$uid])->fetch();
    if (!$error){
    	$error = "未知的错误（我们已经记录）";
    }
    return $error;
}