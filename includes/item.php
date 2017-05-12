<?php
//用来处理物品对应的add_type、item_category_id等关系
$items = [];
$items["ticket"] = ["incentive_item_id"  => 1, //勧誘チケット
                    "item_category_id"   => 1,
					"add_type"           => 1000,
					"incentive_type"     => null];
					
$items["social"] = ["incentive_item_id"  => 2, //友情pt
                    "item_category_id"   => 2,
					"add_type"           => 3002,
					"incentive_type"     => null];
					
$items["gold"] = ["incentive_item_id"  => 3, //G
                  "item_category_id"   => 3,
			      "add_type"           => 3000,
				  "incentive_type"     => 6000];

$items["lovaca"] = ["incentive_item_id"  => 4, //ラブカストーン
                    "item_category_id"   => 4,
			        "add_type"           => 3001,
				    "incentive_type"     => null];
					
$items["sticket"] = ["incentive_item_id"  => 5, //補助チケット
                    "item_category_id"   => 5,
			        "add_type"           => 1000,
				    "incentive_type"     => 8000];

$items["srticket"] = ["incentive_item_id"  => 6, //SR以上確定勧誘チケット
                    "item_category_id"   => 6,
			        "add_type"           => 1000,
				    "incentive_type"     => null];
					
$items["11ticket1"] = ["incentive_item_id"  => 7, //11連勧誘補助チケット
                    "item_category_id"   => 7,
			        "add_type"           => 1000,
				    "incentive_type"     => null];

$items["11ticket"] = ["incentive_item_id"  => 8, //11連特待生勧誘チケット
                    "item_category_id"   => 8,
			        "add_type"           => 1000,
				    "incentive_type"     => null];

$items["srticket1"] = ["incentive_item_id"  => 9, //SR以上確定勧誘チケット〜μ's〜
                    "item_category_id"   => 9,
			        "add_type"           => 1000,
				    "incentive_type"     => null];
					
$items["srticket2"] = ["incentive_item_id"  => 10, //SR以上確定勧誘チケット〜Aqours〜
                    "item_category_id"   => 10,
			        "add_type"           => 1000,
				    "incentive_type"     => null];
					
$items["ssrticket1"] = ["incentive_item_id"  => 11, //SSR以上確定勧誘チケット〜μ's〜
                    "item_category_id"   => 11,
			        "add_type"           => 1000,
				    "incentive_type"     => null];
					
$items["ssrticket2"] = ["incentive_item_id"  => 12, //SSR以上確定勧誘チケット〜Aqours〜
                    "item_category_id"   => 12,
			        "add_type"           => 1000,
				    "incentive_type"     => null];

$items["urticket1"] = ["incentive_item_id"  => 13, //選べるメンバー！UR確定勧誘チケット〜μ's〜
                    "item_category_id"   => 13,
			        "add_type"           => 1000,
				    "incentive_type"     => null];
					
$items["urticket1"] = ["incentive_item_id"  => 14, //選べるメンバー！UR確定勧誘チケット〜Aqours〜
                    "item_category_id"   => 14,
			        "add_type"           => 1000,
				    "incentive_type"     => null];
	
$items["member"] = ["incentive_item_id"  => null, //部員，incentive_item_id为社员的unit_id
                    "item_category_id"   => 0,
			        "add_type"           => 1001,
				    "incentive_type"     => 15000,
					"rank_max_flag"      => false];
					
$items["exp"] =    ["incentive_item_id"  => 0, //EXP
                    "item_category_id"   => 0,
			        "add_type"           => 3004,
				    "incentive_type"     => null];
					
$items["memberMax"] = ["incentive_item_id"  => 0, //部員枠
                    "item_category_id"   => 0,
			        "add_type"           => 3005,
				    "incentive_type"     => null];
					
$items["sticker"] = ["incentive_item_id"  => null, //シール，incentive_item_id为贴纸序号，2为R贴纸
                    "item_category_id"   => 0,
			        "add_type"           => 3006,
				    "incentive_type"     => 6100];
					
$items["friendMax"] = ["incentive_item_id"  => 0, //友達枠
                    "item_category_id"   => 0,
			        "add_type"           => 3007,
				    "incentive_type"     => null];
					
$items["song"] =  ["incentive_item_id"  => null, //楽曲，猜测incentive_item_id为歌曲ID
                    "item_category_id"   => 0,
			        "add_type"           => 5000,
				    "incentive_type"     => null];
					
$items["award"] = ["incentive_item_id"  => null, //称号，incentive_item_id为award_id
                    "item_category_id"   => 0,
			        "add_type"           => 5100,
				    "incentive_type"     => null];
					
$items["background"] = ["incentive_item_id"  => null, //背景，incentive_item_id为背景id
                    "item_category_id"   => 0,
			        "add_type"           => 5200,
				    "incentive_type"     => 6100];
					
$items["mainstory"] = ["incentive_item_id"  => null, //メインストーリー，incentive_item_id可能是啥剧情ID
                    "item_category_id"   => 0,
			        "add_type"           => 5300,
				    "incentive_type"     => null];
					
$items["eventstory"] = ["incentive_item_id"  => null, //イベントストーリー，incentive_item_id可能是啥剧情ID
                    "item_category_id"   => 0,
			        "add_type"           => 5330,
				    "incentive_type"     => null];
					
$items["livetap"] = ["incentive_item_id"  => null, //ライブタップ音，incentive_item_id为99是羊驼（
                    "item_category_id"   => 0,
			        "add_type"           => 5400,
				    "incentive_type"     => null];
					
$items["skill"] = ["incentive_item_id"  => null, //スクールアイドルスキル，incentive_item_id是对应技能宝石的ID
                    "item_category_id"   => 0,
			        "add_type"           => 5500,
				    "incentive_type"     => null];