<?php
function login_doLogin($post){
    global $mysql, $authorize, $config;
    require_once(BASE_PATH."includes/RSA.php");
    
    if(!isset($post['userId']) || !isset($post['password'])){
        $result = [
            "status" => -4,
            "errmsg" => "参数错误"
        ];
        return $result;
    }

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

function login_reg($post){
    global $mysql, $authorize, $config, $logger, $uid;
    require_once(BASE_PATH."includes/present.php");
    require_once(BASE_PATH."includes/unit.php");
    require_once(BASE_PATH."includes/RSA.php");
    $unit = getUnitDb();
    $token = $authorize['token'];

    if(!isset($post['userId']) || !isset($post['nickname']) || !isset($post['inviter']) || !isset($post['password'])){
        $result = [
            "status" => -4,
            "errmsg" => "参数错误"
        ];
        return $result;
    }

    //解密密码
    @$post['password'] = RSAdecrypt($post['password']);
    if($post['password'] === NULL){
        $result = [
            "status" => 1,
            "errmsg" => "密码解密失败"
        ];
        return $result;
    }
    
    if (!is_numeric($post['userId'])) {
        $result = [
            "status" => 2,
            "errmsg" => "UID异常"
        ];
        return $result;
    }elseif($post['userId'] > 999999999){
        $result = [
            "status" => 3,
            "errmsg" => "UID过大"
        ];
        return $result;
    }else{
        $check_uid = $mysql->query('SELECT user_id FROM users WHERE user_id = ?', [$post['userId']])->fetch();
        $logger->d(json_encode($check_uid));
        if ($check_uid) {
            $result = [
                "status" => 4,
                "errmsg" => "此ID已被注册，请更换"
            ];
            return $result;
        }else{
            $uid = $post['userId'];
            $password = genpassv2($post['password'], $post['userId']);
            $username = $mysql->query('SELECT username, `password` FROM tmp_authorize WHERE token = ?', [$token])->fetch();
            $mysql->query('
                INSERT INTO `users` (`user_id`, `username`, `password`,`login_password`, `name`, `introduction`)
                VALUES (?, ?, ?, ?, ?, "")
            ', [$post['userId'], $username['username'], $username['password'], $password, $post['nickname']]);
            $param = $mysql->prepare("INSERT INTO user_params VALUES(".$post['userId'].", ?, ?)");
            $param->execute(['enable_card_switch', $config->reg['disable_card_by_default'] ? 0 : 1]);
            $param->execute(['card_switch', $config->reg['disable_card_by_default'] ? 0 : 1]);
            $param->execute(['random_switch', 0]);
            $param->execute(['allow_test_func', 0]);
            $param->execute(['item1', 0]);
            $param->execute(['item2', 0]);
            $param->execute(['item3', 2525200]);
            $param->execute(['item4', 0]);
            $param->execute(['item5', 0]);
    
            //送三个初期宝石
            $mysql->query("INSERT INTO removable_skill (user_id, skill_id, amount, equipped) VALUES(?,1,1,0)", [$post['userId']]);
            $mysql->query("INSERT INTO removable_skill (user_id, skill_id, amount, equipped) VALUES(?,2,1,0)", [$post['userId']]);
            $mysql->query("INSERT INTO removable_skill (user_id, skill_id, amount, equipped) VALUES(?,3,1,0)", [$post['userId']]);
        
            $uid = $post['userId'];
            if($config->reg['all_card_by_default']) {
                $card_list=$unit->query('SELECT unit_id from unit_m where unit_id <= ? and unit_number > 0', [$config->basic['max_unit_id']])->fetchAll();
                foreach($card_list as $v){
                    addUnit($v[0]);
                }
                //防止劝退
                $mysql->query("UPDATE unit_list SET favorite_flag = 1 WHERE user_id = ?", [$post['userId']]);
            }
        
            $position = 1;
            foreach($config->reg['default_deck_web'] as $k=>$v) {
                $tmp['position'] = $position;
                $tmp['unit_owning_user_id'] = addUnit($v)[0]['unit_owning_user_id'];
                if($position == 5)
                    $center = $tmp['unit_owning_user_id'];
                $unit_deck_detail[] = $tmp;
                $position++;
            }
        
            $tmp2['unit_deck_detail'] = $unit_deck_detail;
            $tmp2['unit_deck_id'] = 1;
            $tmp2['main_flag'] = true;
            $tmp2['deck_name'] = '';
            $unit_deck_list[] = $tmp2;
            $json = json_encode($unit_deck_list);
            $mysql->query("INSERT INTO user_deck (`user_id`, `json`, `center_unit`) VALUES (?,?,?)", [$post['userId'], $json, $center]);
        
            $mysql->query('delete from tmp_authorize where token = ?', [$token]);
            $invite = (int)$post['inviter'];
            if($invite > 0){
                $res = $mysql->query("SELECT user_id FROM users WHERE user_id = ?",[$invite])->fetch();
                if($res){
                    $mysql->query("INSERT INTO invitation (user_id, from_user) VALUES(?,?)", [$uid, $invite]);
                    add_present("loveca", 10, "安利新人奖励", $uid = $invite);
                }
            }
            $result = [
                "status" => 0,
                "errmsg" => ""
            ];
            return $result;
        }
    }
}