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

    //$live->query("")
    
    return $ret;
}