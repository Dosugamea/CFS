<?php
//贴纸商店商品，请按照相同的格式填写
/* 以下是增加一项特殊登录奖励的格式：

$exchange[]=[
  'exchange_item_id'=>1, //贴纸商店商品的唯一编号【不能重复】
  'title'=>'介绍文字',
  'rarity'=>2, //消耗贴纸种类，2~4分别是R SR UR
  'cost_value'=>1, //花费数目
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