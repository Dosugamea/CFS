<?php
//贴纸商店商品，请按照相同的格式填写
/* 以下是增加一项特殊登录奖励的格式：

$exchange[]=[
  'exchange_item_id'=>1, //贴纸商店商品的唯一编号【不能重复】
  'title'=>'介绍文字',
  'cost_list'=>[
    [
      'rarity'=>2,//消耗贴纸种类，2~4分别是R SR UR
      'cost_value'=>1//花费数目
    ]
  ],
  'rank_max_flag'=>false,//是否为觉醒卡片
  'end'=>'结束时间(yyyy-mm-dd hh:ss:mm)', //false为不结束
  'max_item_count'=>0, //领取限额，0为不限
  'item'=>['物品种类（见下）',物品数量]
];


可用的物品种类：
loveca:心
coin:金币
social:友情点
ticket:单抽券
s_ticket:辅助券
数字:以该数字为编号的卡片

PLS默认设置为空。*/
$exchange = [];
$exchange[]=[
  'exchange_item_id'=>20001,
  'title'=>'補助チケット',
  'cost_list'=>[
    [
      'rarity'=>2,
      'cost_value'=>30
    ],
    [
      'rarity'=>3,
      'cost_value'=>2
    ]
  ],
  'rank_max_flag'=>false,
  'end'=>false,
  'max_item_count'=>0,
  'item'=>['s_ticket',1]
];
$exchange[]=[
  'exchange_item_id'=>10001,
  'title'=>'ラブカストーン x1',
  'cost_list'=>[
    [
      'rarity'=>2,
      'cost_value'=>12
    ]
  ],
  'rank_max_flag'=>false,
  'end'=>false,
  'max_item_count'=>0,
  'item'=>['loveca',1]
];
$exchange[]=[
  'exchange_item_id'=>10002,
  'title'=>'ラブカストーン x5',
  'cost_list'=>[
    [
      'rarity'=>3,
      'cost_value'=>4
    ]
  ],
  'rank_max_flag'=>false,
  'end'=>false,
  'max_item_count'=>0,
  'item'=>['loveca',5]
];
$exchange[]=[
  'exchange_item_id'=>10003,
  'title'=>'ラブカストーンx10',
  'cost_list'=>[
    [
      'rarity'=>5,
      'cost_value'=>1
    ]
  ],
  'rank_max_flag'=>false,
  'end'=>false,
  'max_item_count'=>0,
  'item'=>['loveca',10]
];
$exchange[]=[
  'exchange_item_id'=>10004,
  'title'=>'ラブカストーンx20',
  'rarity'=>4,
  'cost_value'=>1,
  'cost_list'=>[
    [
      'rarity'=>4,
      'cost_value'=>1
    ]
  ],
  'rank_max_flag'=>false,
  'end'=>false,
  'max_item_count'=>0,
  'item'=>['loveca',20]
];
$exchange[]=[
  'exchange_item_id'=>30001,
  'title'=>'UR 穂乃果の母x2',
  'cost_list'=>[
    [
      'rarity'=>4,
      'cost_value'=>1
    ]
  ],
  'rank_max_flag'=>true,
  'end'=>false,
  'max_item_count'=>0,
  'item'=>[390,2]
];
$exchange[]=[
  'exchange_item_id'=>30002,
  'title'=>'UR 穂乃果の母',
  'cost_list'=>[
    [
      'rarity'=>3,
      'cost_value'=>8
    ],
    [
      'rarity'=>5,
      'cost_value'=>1
    ]
  ],
  'rank_max_flag'=>true,
  'end'=>false,
  'max_item_count'=>0,
  'item'=>[390,1]
];
$exchange[]=[
  'exchange_item_id'=>30003,
  'title'=>'SR アルパカ',
  'cost_list'=>[
    [
      'rarity'=>2,
      'cost_value'=>1
    ]
  ],
  'rank_max_flag'=>true,
  'end'=>false,
  'max_item_count'=>0,
  'item'=>[632,1]
];