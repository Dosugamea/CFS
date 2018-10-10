<?php
function genDuelUserInfoFake(){
    return [
        "duel_energy_max"               => 5,
        "duel_energy_full_time"         => "2000-01-01 00:00:00",
        "duel_energy_full_need_time"    => 0,
        "over_max_duel_energy"          => 0
    ];
}

//约战信息，API以及进入约战界面时请求
function duel_duelInfo(){
    global $config;
    $ret = [
        "member_category_list" => [
            [
                "member_category"       => 1,
                "is_open"               => false,
                "asset_bgm_id"          => $config->m_duel['muse']['bgm_id']
            ],[
                "member_category"       => 2,
                "is_open"               => false,
                "asset_bgm_id"          => $config->m_duel['aqours']['bgm_id']
            ]
        ],
        "duel_user_info" => genDuelUserInfoFake(),
        "difficulty_list" => [
            [
                "difficulty"    => 1,
                "capital_type"  => 4,
                "capital_value" => 1
            ],[
                "difficulty"    => 2,
                "capital_type"  => 4,
                "capital_value" => 1
            ],[
                "difficulty"    => 3,
                "capital_type"  => 4,
                "capital_value" => 1
            ],[
                "difficulty"    => 4,
                "capital_type"  => 4,
                "capital_value" => 1
            ],[
                "difficulty"    => 6,
                "capital_type"  => 4,
                "capital_value" => 1
            ],
        ]
    ];
    if($config->m_duel['muse']['is_open']){
        $ret['member_category_list'][0]['is_open'] = true;
    }
    if($config->m_duel['aqours']['is_open']){
        $ret['member_category_list'][1]['is_open'] = true;
    }
    return $ret;
}

//约战页面
function duel_top(){
    global $mysql, $config;
    $allLiveList = getAllLiveList([]);
    foreach($allLiveList as &$i){
        foreach($i as &$j){
            unset($j['notes_setting_asset']);
        }
    }
    $ret = [
        "is_open"                   => $config->m_duel['muse']['is_open'] || $config->m_duel['aqours']['is_open'],
        "duel_id"                   => $config->m_duel['duel_id'],
        "term_id"                   => $config->m_duel['term_id'],
        "gps_is_open"               => $config->m_duel['gps_is_open'],
        "difficulty_live_list"      => [
            "1" => [
                "difficulty"    => 1,
                "live_list"     => $allLiveList[1]
            ],
            "2" => [
                "difficulty"    => 2,
                "live_list"     => $allLiveList[2]
            ],
            "3" => [
                "difficulty"    => 3,
                "live_list"     => $allLiveList[3]
            ],
            "4" => [
                "difficulty"    => 4,
                "live_list"     => $allLiveList[4]
            ],
            "5" => [
                "difficulty"    => 5,
                "live_list"     => $allLiveList[5]
            ],
            "6" => [
                "difficulty"    => 6,
                "live_list"     => $allLiveList[6]
            ]
        ],
        "server_timestamp"          => time()
    ];
    
    return $ret;
}

//GPS匹配
function duel_gpsMatch(){
    //ERROR_CODE_LIVE_GPS_MATCHING_NOT_OPENED
    return retError(3455);
}

//普通匹配
function duel_matching($post){
    global $redis, $redLock, $uid, $mysql, $envi;
    //需要的参数
    if(!isset($post['difficulty']) || !isset($post['live_difficulty_id']) ||
    !is_numeric($post['difficulty']) || !is_numeric($post['live_difficulty_id'])){
        throw403("INVALID_ARGUMENTS");
    }
    //参数预处理
    $post['difficulty']         = (int)$post['difficulty'];
    $post['live_difficulty_id'] = (int)$post['live_difficulty_id'];

    //检查live是否存在
    $allLiveList = getAllLiveList([]);
    $found = false;
    foreach($allLiveList[$post['difficulty']] as $i){
        if($i['live_difficulty_id'] == $post['live_difficulty_id']){
            $found = true;
            break;
        }
    }
    if(!$found && $post['live_difficulty_id'] != 0){
        //ERROR_CODE_LIVE_NOT_FOUND
        return retError(3400);
    }

    //检查是否有未满员的房间
    $lock = $redLock->lock("Duel:room:notFull");
    if($lock){
        //没有非空房间
        if(!$redis->exists("Duel:room:notFull")){
            $re_enter = false;
            $mysql->query("INSERT INTO tmp_duel_room (users) VALUES(?)", [json_encode([$uid])]);
            $room_id = $mysql->lastInsertId();
            $redis->set("Duel:room:notFull", $room_id);
            $redis->set("Duel:room:{$room_id}:createTime", time());
            $redis->set("Duel:room:{$room_id}:lastJoinTime", time());
        }else{
            //有非空房间时
            $room_id = (int)$redis->get("Duel:room:notFull");
            //先检查自己是否在里面
            $room_users = $mysql->query("SELECT users FROM tmp_duel_room WHERE room_id = ?", [$room_id])->fetchColumn();
            $room_users = json_decode($room_users);
            //自己不在房间里时插入数据
            if(!in_array($uid, $room_users)){
                $re_enter = false;
                $room_users[] = $uid;
                $mysql->query("UPDATE tmp_duel_room SET users = ? WHERE room_id = ?", [json_encode($room_users), $room_id]);
            }else{
                $re_enter = true;
            }

            $redis->set("Duel:room:{$room_id}:lastJoinTime", time());

            if(count($room_users) == 4){
                $redis->del("Duel:room:notFull"); //TODO:BOT
            }
        }
    }else{
        pl_assert("Duel:room:notFull 上锁失败！");
    }
    $redLock->unlock($lock);

    //处理房间信息
    if(!$re_enter){
        $lock = $redLock->lock("Duel:room:{$room_id}:selectedLive");
        if($lock){
            $redis->rpush("Duel:room:{$room_id}:selectedLive", $post['live_difficulty_id']);
        }else{
            pl_assert("Duel:room:{$room_id}:selectedLive 上锁失败！");
        }
        $redLock->unlock($lock);
    }

    //建立用户信息缓存
    if(!$re_enter){
        $lock = $redLock->lock("Duel:room:{$room_id}:userInfoCache");
        if($lock){
            //生成个人用户信息
            $userInfo = [
                "user_info"         => [
                    "user_id"   => $uid,
                    "name"      => $envi->user['name'],
                    "level"     => $envi->user['level']
                ],
                "center_unit_info"  => GetUnitDetail($mysql->query('SELECT center_unit FROM user_deck WHERE user_id = ?', [$uid])->fetchColumn()),
                "setting_award_id"  => (int)$envi->user['award'],
                "chat_id"           => "0-0",
                "room_user_status"  => [
                    "selected_live_difficulty_id"   => $post['live_difficulty_id']
                ]
            ];
            $redis->rpush("Duel:room:{$room_id}:userInfoCache", json_encode($userInfo));
        }else{
            pl_assert("Duel:room:{$room_id}:userInfoCache 上锁失败！");
        }
        $redLock->unlock($lock);
    }

    $energy = getCurrentEnergy();
    $ret = [
        "room_id"           => $room_id,
        "energy_full_time"  => $energy['energy_full_time'],
        "over_max_energy"   => $energy['over_max_energy'],
        "duel_user_info"    => genDuelUserInfoFake(),
        "server_timestamp"  => time()
    ];
    return $ret;
}

function duel_startWait($post){
    global $redis, $redLock, $uid, $mysql, $logger;
    //TODO:随机铺面支持
    //需要的参数
    if(!isset($post['room_id']) || !isset($post['chat_id']) ||
    !is_numeric($post['room_id'])){
        throw403("INVALID_ARGUMENTS");
    }
    //参数预处理
    $post['room_id']    = (int)$post['room_id'];

    //MySQL获取房间信息
    $room_id = $post['room_id'];
    $room_info = $mysql->query("SELECT * FROM tmp_duel_room WHERE room_id = ?", [$room_id])->fetch();
    $users = json_decode($room_info['users']);

    //检查用户是否在房间里
    if(!in_array($uid, $users)){
        throw403("USER_NOT_IN_ROOM");
    }

    //处理用户信息
    $lock = $redLock->lock("Duel:room:{$room_id}:userInfoCache");
    if($lock){
        $usersInfo = $redis->lrange("Duel:room:{$room_id}:userInfoCache", 0, 3);
        $redis->del("Duel:room:{$room_id}:userInfoCache");
        foreach($usersInfo as &$i){
            $i = json_decode($i, true);
            if($i['user_info']['user_id'] == $uid){
                if($post['chat_id'] == 0){
                    //客户端提交bug非得叫服务器背锅
                    $i['chat_id'] = "0-0";
                }else{
                    $i['chat_id'] = $post['chat_id'];
                }
            }
            $redis->rpush("Duel:room:{$room_id}:userInfoCache", json_encode($i));
        }
    }else{
        pl_assert("Duel:room:{$room_id}:userInfoCache 上锁失败！");
    }
    $redLock->unlock($lock);

    //处理开车相关信息
    $DEFAULT_COUNTDOWN = 30;
    $lock = $redLock->lock("Duel:room:{$room_id}:lastJoinTime");
    if(!$lock){
        pl_assert("Duel:room:{$room_id}:lastJoinTime 上锁失败！");
    }
    $last_join_time = (int)$redis->get("Duel:room:{$room_id}:lastJoinTime");
    if(count($users) == 4){
        //4人立即开车！
        if($redis->exists("Duel:room:{$room_id}:startTime")){
            $start_time = (int)$redis->get("Duel:room:{$room_id}:startTime");
        }else{
            $start_time = time() + 5;
            $redis->set("Duel:room:{$room_id}:startTime", $start_time);
            $redis->del("Duel:room:notFull");
        }
        $remain_time = $start_time - time();
        $capacity = 4;
        $start_flag = true;
    }
    
    if(!isset($remain_time) && time() - $last_join_time < $DEFAULT_COUNTDOWN){
        //还在倒计时的情况
        if(count($users) >= 2){
            //有两个用户，等超时再开车
            $remain_time = $last_join_time - time() + $DEFAULT_COUNTDOWN;
        }else if(count($users) == 1){
            //只有一个用户，等超时再计算（我™为啥要写个if）
            $remain_time = $last_join_time - time() + $DEFAULT_COUNTDOWN;
        }
        $capacity = 4;
        $start_flag = false;
    }else{
        //倒计时到了的情况
        if(count($users) >= 2){
            //有两个用户直接开车
            if($redis->exists("Duel:room:{$room_id}:startTime")){
                $start_time = (int)$redis->get("Duel:room:{$room_id}:startTime");
            }else{
                $start_time = time() + 5;
                $redis->set("Duel:room:{$room_id}:startTime", $start_time);
                $redis->del("Duel:room:notFull");
            }

            $remain_time = $start_time - time();
            $capacity = count($users);
            $start_flag = true;
        }else if(count($users) == 1){
            //只有一个用户，继续等车.jpg
            $redis->set("Duel:room:{$room_id}:lastJoinTime", time());
            $remain_time = $DEFAULT_COUNTDOWN;
            $capacity = 4;
            $start_flag = false;
        }
    }
    $redLock->unlock($lock);

    //时间到，抽选歌曲
    if($start_flag){
        $logger->d($redis->exists("Duel:room:{$room_id}:chosenLive"));
        if(!$redis->exists("Duel:room:{$room_id}:chosenLive")){
            //redis里面没有的话进行抽选
            $lock = $redLock->lock("Duel:room:{$room_id}:chosenLive");
            if($lock){
                $pick = array_rand($usersInfo, 1);
                $selectedLive = $usersInfo[$pick]['room_user_status']['selected_live_difficulty_id'];
                $redis->set("Duel:room:{$room_id}:chosenLive", $selectedLive);
            }else{
                pl_assert("Duel:room:{$room_id}:chosenLive 上锁失败！");
            }
            $redLock->unlock($lock);
        }else{
            $selectedLive = (int)$redis->get("Duel:room:{$room_id}:chosenLive");
        }
    }else{
        $selectedLive = 0;
    }

    //防止客户端bug
    if($remain_time < 0){
        $remain_time = 0;
    }

    //返回值
    $ret = [
        "polling_interval"      => 2, //两次取数据的间隔，单位秒
        "player_num"            => count($users),
        "start_wait_time"       => $remain_time,
        "start_flag"            => $start_flag,
        "capacity"              => $capacity,
        "room_id"               => $room_id,
        "matching_user"         => $usersInfo,
        "live_difficulty_id"    => $selectedLive,
        "server_timestamp"      => time()
    ];
    return $ret;
}

//live开始！
function duel_liveStart($post){
    global $redis, $redLock, $uid, $mysql, $logger;
    //需要的参数
    if(!isset($post['room_id']) || !isset($post['deck_id']) ||
    !is_numeric($post['room_id']) || !is_numeric($post['deck_id'])){
        throw403("INVALID_ARGUMENTS");
    }
    //参数预处理
    $post['room_id']    = (int)$post['room_id'];
    $post['deck_id']    = (int)$post['deck_id'];
    $room_id            = $post['room_id'];
    
    //读取房间信息
    $room = $mysql->query("SELECT * FROM tmp_duel_room WHERE room_id = ?", [$post['room_id']])->fetch();
    $users = json_decode($room['users']);
    if(!in_array($uid, $users)){
        throw403("DUEL_USER_NOT_IN_ROOM");
    }

    //检查用户是否在房间里
    if(!in_array($uid, $users)){
        throw403("USER_NOT_IN_ROOM");
    }

    //读取live信息
    $selectedLive = (int)$redis->get("Duel:room:{$room_id}:chosenLive");
    if(!$selectedLive){
        throw403("DUEL_ROOM_NOT_START");
    }
    $post['live_difficulty_id'] = $selectedLive;

    //防重放
    $startedUser = $redis->lrange("Duel:room:{$room_id}:startedUser", 0, 3);
    foreach($startedUser as $i){
        if((int)$i == $uid){
            throw403("DUEL_USER_ALREADY_STARTED");
        }
    }
    $redis->rpush("Duel:room:{$room_id}:startedUser", $uid);

    //获得房间用户
    $usersInfo = $redis->lrange("Duel:room:{$room_id}:userInfoCache", 0, 3);
    foreach($usersInfo as &$i){
        $i = json_decode($i, true);
    }
    
    //开始live
    $post['unit_deck_id']   = $post['deck_id'];
    $ret = runAction("live", "play", $post, ["free" => true]);
    $ret['matching_user_info']  = $usersInfo;
    $ret['duel_user_info']      = genDuelUserInfoFake();
    $energy = getCurrentEnergy();
    $ret["energy_full_time"]    = $energy['energy_full_time'];
    $ret["over_max_energy"]     = $energy['over_max_energy'];
    $ret['server_timestamp']    = time();

    return $ret;
}

//live结束！
function duel_liveEnd($post){
    global $uid, $redis, $redLock, $mysql;
    //需要的参数
    if(!isset($post['room_id']) || !is_numeric($post['room_id'])){
        throw403("INVALID_ARGUMENTS");
    }
    //参数预处理
    $post['room_id']    = (int)$post['room_id'];
    $room_id            = $post['room_id'];
    
    //MySQL获取房间信息
    $room_info = $mysql->query("SELECT * FROM tmp_duel_room WHERE room_id = ?", [$room_id])->fetch();
    $users = json_decode($room_info['users']);

    //检查用户是否在房间里
    if(!in_array($uid, $users)){
        throw403("USER_NOT_IN_ROOM");
    }

    //写入结果缓存
    $lock = $redLock->lock("Duel:room:{$room_id}:userResultCache");
    if($lock){
        $redis->hset("Duel:room:{$room_id}:userResultCache", $uid, json_encode($post));
        $redis->set("Duel:room:{$room_id}:lastClear", time());
    }else{
        pl_assert("Duel:room:{$room_id}:userResultCache 上锁失败！");
    }

    //清空聊天表情
    $lock = $redLock->lock("Duel:room:{$room_id}:userInfoCache");
    if($lock){
        $usersInfo = $redis->lrange("Duel:room:{$room_id}:userInfoCache", 0, 3);
        $redis->del("Duel:room:{$room_id}:userInfoCache");
        foreach($usersInfo as &$i){
            $i = json_decode($i, true);
            if($i['user_info']['user_id'] == $uid){
                $i['chat_id'] = "0-0";
            }
            $redis->rpush("Duel:room:{$room_id}:userInfoCache", json_encode($i));
        }
    }else{
        pl_assert("Duel:room:{$room_id}:userInfoCache 上锁失败！");
    }
    
    return [];
}

function duel_endWait($post){
    global $uid, $redis, $redLock, $mysql;
    //需要的参数
    if(!isset($post['room_id']) || !isset($post['chat_id']) ||
    !is_numeric($post['room_id'])){
        throw403("INVALID_ARGUMENTS");
    }
    //参数预处理
    $post['room_id']    = (int)$post['room_id'];
    $room_id            = $post['room_id'];
    
    //读取房间信息
    $room = $mysql->query("SELECT * FROM tmp_duel_room WHERE room_id = ?", [$post['room_id']])->fetch();
    $users = json_decode($room['users']);
    if(!in_array($uid, $users)){
        throw403("DUEL_USER_NOT_IN_ROOM");
    }

    //检查用户是否在房间里
    if(!in_array($uid, $users)){
        throw403("USER_NOT_IN_ROOM");
    }

    //读取live信息
    $selectedLive = (int)$redis->get("Duel:room:{$room_id}:chosenLive");
    if(!$selectedLive){
        throw403("DUEL_ROOM_NOT_START");
    }
    
    //检查是否结算过
    $resultCache = $redis->hget("Duel:room:{$room_id}:userResultCache", $uid);
    if(!$resultCache){
        throw403("DUEL_STATUS_ERROR");
    }

    //检查了一大堆终于可以开始处理数据了= =！
    //2018-08-28 22:30然后突然忘了要怎么写
    //2018-08-28 22:48好的想起来了（。
    $lastClear = (int)$redis->get("Duel:room:{$room_id}:lastClear");
    if(!$lastClear){
        $logger->f("【协力】无法获取房间{$room_id}结算信息");
        throw500("ERROR_DUEL_ROOM_DATA_UNDEFINED");
    }
    //2018-08-28 22:56 诶我又双叒叕忘了要写啥

    //处理用户信息和聊天信息
    $lock = $redLock->lock("Duel:room:{$room_id}:userInfoCache");
    if($lock){
        $usersInfo = $redis->lrange("Duel:room:{$room_id}:userInfoCache", 0, 3);
        $redis->del("Duel:room:{$room_id}:userInfoCache");
        foreach($usersInfo as &$i){
            $i = json_decode($i, true);
            if($i['user_info']['user_id'] == $uid){
                if($post['chat_id'] == 0){
                    //客户端提交bug非得叫服务器背锅
                    $i['chat_id'] = "0-0";
                }else{
                    $i['chat_id'] = $post['chat_id'];
                }
            }
            $redis->rpush("Duel:room:{$room_id}:userInfoCache", json_encode($i));
        }
    }else{
        pl_assert("Duel:room:{$room_id}:userInfoCache 上锁失败！");
    }
    $redLock->unlock($lock);

    //已结算用户计算
    $userResultCache = $redis->hgetall("Duel:room:{$room_id}:userResultCache");
    $DEFAULT_COUNTDOWN = 30;
    if(count($userResultCache) == count($users) || time() - $lastClear > $DEFAULT_COUNTDOWN){
        //所有用户全部结算完或者超时的情况，结算
        $endFlag = true;
    }else{
        $endFlag = false;
    }

    $waitTime = $DEFAULT_COUNTDOWN - time() + $lastClear;
    if($waitTime < 0){
        $waitTime = 0;
    }

    $result = [
        "room_id"           => $room_id,
        "capacity"          => count($users),
        "end_flag"          => $endFlag,
        "player_num"        => count($users),
        "end_wait_time"     => $waitTime,
        "matching_user"     => $usersInfo,
        "polling_interval"  => 2,
        "server_timestamp"  => time()
    ];
    return $result;
}

function duel_endRoom($post){
    global $uid, $redis, $redLock, $mysql, $logger;
    //需要的参数
    if(!isset($post['room_id']) ||
    !is_numeric($post['room_id'])){
        throw403("INVALID_ARGUMENTS");
    }
    //参数预处理
    $post['room_id']    = (int)$post['room_id'];
    $room_id            = $post['room_id'];
    
    //读取房间信息
    $room = $mysql->query("SELECT * FROM tmp_duel_room WHERE room_id = ?", [$post['room_id']])->fetch();
    $users = json_decode($room['users']);
    if(!in_array($uid, $users)){
        throw403("DUEL_USER_NOT_IN_ROOM");
    }

    //检查用户是否在房间里
    if(!in_array($uid, $users)){
        throw403("USER_NOT_IN_ROOM");
    }

    //读取live信息
    $selectedLive = (int)$redis->get("Duel:room:{$room_id}:chosenLive");
    if(!$selectedLive){
        throw403("DUEL_ROOM_NOT_START");
    }
    
    //检查是否结算过
    $resultCache = $redis->hget("Duel:room:{$room_id}:userResultCache", $uid);
    $userStatus = $redis->hget("Duel:room:{$room_id}:userEndRoomFlag", $uid);
    if(!$resultCache || $userStatus){
        throw403("DUEL_STATUS_ERROR");
    }
    $resultCache = json_decode($resultCache, true);
    
    //获得房间用户
    $usersInfo = $redis->lrange("Duel:room:{$room_id}:userInfoCache", 0, 3);
    foreach($usersInfo as &$i){
        $i = json_decode($i, true);
    }
    unset($i);
    
    //直接调live模块
    //TODO:过滤参数防止搞事情（好像要搞事情就在前面搞了）
    $post = array_merge($post, $resultCache);
    $post['live_difficulty_id'] = $selectedLive;
    $ret = runAction("live", "reward", $post);
    if(isset($ret['errorCode'])){
        $logger->e("Duel/endRoom: Error on live/reward, returning error");
        return $ret;
    }
    $resultCacheAll = $redis->hgetall("Duel:room:{$room_id}:userResultCache");
    $uidCache = [];
    $scoreCache = [];
    //TODO:Key element cannot be a reference &$k
    foreach($resultCacheAll as $k => &$v){
        $k = (int)$k;
        $v = json_decode($v, true);
        $uidCache[] = (int)$k;
        $scoreCache[] = $v['score_smile'] + $v['score_cute'] + $v['score_cool'];
    }
    //防止数据被覆盖
    unset($k);
    unset($v);
    array_multisort($scoreCache, SORT_DESC, $uidCache);
    foreach($scoreCache as $k => $v){
        if($uidCache[$k] == $uid){
            $rank = $k + 1;
        }
    }
    //填充未结算的用户
    foreach($usersInfo as $i){
        if(!in_array($i['user_info']['user_id'], $uidCache)){
            $scoreCache[] = 0;
            $uidCache[] = $i['user_info']['user_id'];
        }
    }
    /* $scoreCache = [12345, 12344, 12343, 12000];
     * $uidCache   = [3,     4,     8,     7    ];
     * 分数按从高到低排列，uid跟随分数
     */
    if(!isset($rank)){
        $logger->e("[Duel]Failed to calculate rank.");
        $rank = 4;
    }
    $logger->d("Users Info before add result: ".json_encode($usersInfo));
    //计算房间结果
    $live_info = getLiveSettings((int)$redis->get("Duel:room:{$room_id}:chosenLive"), 's_rank_combo');
    foreach($usersInfo as &$i){
        $curUid = $i['user_info']['user_id'];
        $curRank = array_search($curUid, $uidCache);
        if($scoreCache[$curRank] == 0){
            $result = [
                "rank"          => $curRank + 1,
                "score"         => 0,
                "status"        => 30,
                "time_up"       => true,
                "max_combo"     => 0,
                "is_full_combo" => false
            ];
        }else{
            $result = [
                "rank"          => $curRank + 1,
                "score"         => $scoreCache[$curRank],
                "status"        => 50,
                /*  MATCHED = 1,
                 *  START_WAIT = 2,
                 *  PLAYED = 3,
                 *  PLAY_END = 4,
                 *  REWARD = 5,
                 *  GAMEOVER = 6
                 * 这里应该是30（未结算）、50（已结算）或者10（NPC）
                 */
                //TODO:数据库加入各个玩家状态
                "time_up"       => false,
                "max_combo"     => $resultCacheAll[(string)$uid]['max_combo'],
                "is_full_combo" => $live_info['s_rank_combo'] == $resultCacheAll[(string)$uid]['max_combo'] ? true : false
            ];
        }
        $i['result'] = $result;
    }

    //追加duty特定参数
    $ret['room_id']         = $room_id;
    $ret['rank']            = $rank;
    $ret['matching_user']   = $usersInfo;
    return $ret;
}