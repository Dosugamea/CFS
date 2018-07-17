<?php
function throw403($err) {
    header('HTTP/1.1 403 Forbidden');
    print($err);
    exit();
}

function throw400($err) {
    header('HTTP/1.1 400 Bad request');
    print($err);
    exit();
}

function throw500($err) {
    header('HTTP/1.1 500 Internal Server Error');
    print($err);
    exit();
}


function pl_assert($condition, $err = '') {
    if (!$condition) {
        trigger_error("Assert failure:\n$err");
    }
}