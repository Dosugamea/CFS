<?php
function login_doLogin($post){
    global $mysql, $authorize, $config;
    require_once(BASE_PATH."includes/RSA.php");
    
    //解密密码
    @$post['password'] = RSAdecrypt($post['password']);
    if($post['password'] === NULL){
        $result = [
            "status" => 1,
            "errmsg" => "密码解密失败"
        ];
        return $result;
    }

    $token = $authorize['token'];
    $username = $mysql->query('SELECT username, password FROM tmp_authorize WHERE token = ?', [$token])->fetch();

    $pass_v2 = genpassv2($post['password'], $post['userId']);
    $success = $mysql->query('SELECT user_id FROM users WHERE login_password = ? AND user_id = ?', [$pass_v2, $post['userId']])->fetch();
    //二代密码加盐错误，尝试一代
    if($success === false) {
        $pass_v1 = sha1($post['password']);
        $success = $mysql->query('SELECT user_id FROM users WHERE login_password = ? AND user_id = ?', [$pass_v1, $post['userId']])->fetch();
        if($success === false) {
            print("<h3><font color=\"red\">错误：您输入的ID或密码有误 <br> Error: You Input The Wrong UserID or Password</font></h3>");
        }else{
            $mysql->query("UPDATE users SET login_password = ? WHERE login_password = ? AND user_id = ?", [$pass_v2, $pass_v1, $post['userId']]);
        }
    }
    if ($success !== false) {
        $mysql->query("
            UPDATE users SET username = ?, password = ?
            WHERE login_password=? AND user_id=?",
            [$username['username'], $username['password'], $pass_v2, $post['userId']]);
        $mysql->query("DELETE FROM tmp_authorize WHERE token = ?", [$token]);
        $result = [
            "status" => 0,
            "errmsg" => ""
        ];
        return $result;
    }
}