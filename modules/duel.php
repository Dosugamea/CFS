<?php
//约战
function duel_duelInfo(){
    //TODO!!!
    $ret = [
        "member_category_list" => [
            [
                "member_category_id"    => 1,
                "is_open"               => false
            ],[
                "member_category_id"    => 2,
                "is_open"               => false
            ]
        ],
        "duel_user_info" => [
            "duel_energy_max"               => 5,
            "duel_energy_full_time"         => "2000-01-01 00:00:00",
            "duel_energy_full_need_time"    => 0,
            "over_max_duel_energy"          => 0
        ],
        "difficulty_list" => [
            [
                "difficulty"    => 1,
                "capital_type"  => 4,
                "capital_value" => 1
            ]
        ]
    ];
    return $ret;
}