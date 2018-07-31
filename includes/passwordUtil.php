<?php

function genpassv2($_pass, $id) {
    $_pass .= $id;
    $pass = hash('sha512', $_pass);
    $pass .= hash('sha512', str_replace($_pass[0], 'RubyRubyRu', $_pass));
    $pass .= $pass;
    return substr($pass, hexdec(substr(md5($_pass), ord($_pass[0]) % 30, 2)), 32);
}

function login_v2($post) {
	global $mysql;
	$pass = hash('sha512', $post['login_passwd']);
	$uname = hash('sha512', str_replace('-', 'MakiMakiMa', $post['login_key'])).$pass;
	$pass .= hash('sha512', str_replace('-', 'NicoNicoNi', $post['login_key']));
	$uname .= $uname; //长度不够
	$pass .= $pass;
	$uname = substr($uname, hexdec(substr($post['login_key'], 0, 2)), 32);
	$pass = substr($pass, hexdec(substr($post['login_passwd'], 0, 2)), 32);
	return [$mysql->query("SELECT user_id FROM users WHERE username='$uname' and password='$pass'")->fetchColumn(), $uname, $pass];
}

function login_v1($post) {
	global $mysql;
	$uname = sha1($post['login_key']);
	$pass = sha1($post['login_passwd']);
	return [$mysql->query("SELECT user_id FROM users WHERE username='$uname' and password='$pass'")->fetchColumn(), $uname, $pass];
}