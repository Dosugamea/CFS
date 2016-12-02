<?php
//特殊登录奖励，请按照相同的格式填写
/* 以下是增加一项特殊登录奖励的格式：

$nlbonus[]=[
  'nlbonus_id'=>1, //特殊登录奖励的唯一编号【不能重复，否则无法正常领取】
  'detail_text'=>'领取画面显示的文字',
  'bg_asset'=>'背景图片（列表见下）',
  'start'=>'开始时间(yyyy-mm-dd hh:ss:mm)',
  'end'=>'结束时间',
  'items'=>[
    ['物品种类（见下）',物品数量, '礼物说明']
  ]
];

可用的物品种类：
loveca:心
coin:金币
social:友情点
ticket:单抽券
s_ticket:辅助券
数字:以该数字为编号的卡片

截至15年4月18日，可用的图片列表如下：
开头的路径一律为：assets/image/ui/login_bonus_extra/
（例：'bg_asset'=>'assets/image/ui/login_bonus_extra/default_1_1.png'）

default_1_1.png：通常背景（1个物品），图片上方有START UP！字样，右侧为南小鸟
default_1_2.png 到 default_1_7.png：通常背景（2~7个物品，修改第二个数字），图中为Q版的μ's 9人

birthday_1_1.png：西木野真姬的生日（1个物品）
birthday_2_1.png：东条希的生日（1个物品）
birthday_3_1.png：矢泽妮可的生日（1个物品）
birthday_4_1.png：高坂穗乃果的生日（1个物品）
birthday_5_1.png：南小鸟的生日（1个物品）
birthday_6_1.png：绚濑绘里的生日（1个物品）
birthday_7_1.png：星空凛的生日（1个物品）
birthday_8_1.png：小泉花阳的生日（1个物品）
birthday_9_1.png：园田海未的生日（1个物品）

tv_1_1.png 到 tv_12_1.png：动画二期放送纪念（1个物品）
图中人物（从1到12）依次为：二年组、一年组、果、三年组、绘、鸟、海、凛、希、姬、花、妮

movie_1_1.png：电影公开倒计时，图中为妮可（1个物品）
movie_2_1.png：电影公开倒计时，图中为花阳（1个物品）

limit_1_5.png：特别登录奖励 小鸟式雪人（5个物品）
limit_2_3.png：恭贺新年（全员，3个物品）
limit_3_2.png：4th开催纪念（全员10月篇卡面，2个物品）
limit_4_7.png：手游一周年纪念（全员2月篇卡面，7个物品）
limit_5_7.png：300万人突破纪念（全员4月篇卡面，7个物品）
limit_6_5.png：剧场版制作决定（类似于旧标题画面，5个物品）
limit_7_5.png：特别登录奖励 SUMMER GIRL决定战 绚濑绘里（5个物品）
limit_8_7.png：400万人突破纪念（全员9月篇卡面，7个物品）
limit_9_7.png：500万人突破纪念（全员汉服卡面，7个物品）
limit_10_7.png：600万人突破纪念（全员5月篇卡面，7个物品）
limit_11_5.png：特别登录奖励 圣诞礼物大作战 西木野真姬（5个物品）
limit_12_7.png：1000万人突破纪念（全员婚纱卡面，7个物品）
limit_13_3.png：恭贺新年2015（全员巫女服，3个物品）
limit_14_2.png：5th开催纪念（全员10月篇卡面，2个物品）
limit_15_7.png：700万人突破纪念（全员3月篇卡面，7个物品）
limit_16_7.png：800万人突破纪念（全员11月篇卡面，7个物品）
limit_17_7.png：手游二周年纪念（全员白色情人节卡面，7个物品）
*/

//LLSP的默认设置为：全员生日奖励，已配置为每年均可领取
//关于奖励编号：自带生日奖励的编号是负的，如要新增奖励可以直接从1开始编号
$nlbonus[]=[
  'nlbonus_id'=>((date('Y')-2000)*-9-1),
  'detail_text'=>'４月１９日は西木野真姫ちゃんの誕生日です！
お祝いとして、本日ログインした皆さんに
「ラブカストーン5個」をプレゼントいたいます♪',
  'bg_asset'=>'assets/image/ui/login_bonus_extra/birthday_1_1.png',
  'start'=>date('Y').'-04-19 00:00:00',
  'end'=>date('Y').'-04-19 23:59:59',
  'items'=>[
    ['loveca', 5, '真姫ちゃんの誕生日のお祝いです']
  ]
];
$nlbonus[]=[
  'nlbonus_id'=>((date('Y')-2000)*-9-2),
  'detail_text'=>'６月９日は東條希ちゃんの誕生日です！
お祝いとして、本日ログインした皆さんに
「ラブカストーン5個」をプレゼントいたいます♪',
  'bg_asset'=>'assets/image/ui/login_bonus_extra/birthday_2_1.png',
  'start'=>date('Y').'-06-09 00:00:00',
  'end'=>date('Y').'-06-09 23:59:59',
  'items'=>[
    ['loveca', 5, '希ちゃんの誕生日のお祝いです']
  ]
];
$nlbonus[]=[
  'nlbonus_id'=>((date('Y')-2000)*-9-3),
  'detail_text'=>'７月２２日は矢澤にこちゃんの誕生日です！
お祝いとして、本日ログインした皆さんに
「ラブカストーン5個」をプレゼントいたいます♪',
  'bg_asset'=>'assets/image/ui/login_bonus_extra/birthday_3_1.png',
  'start'=>date('Y').'-07-22 00:00:00',
  'end'=>date('Y').'-07-22 23:59:59',
  'items'=>[
    ['loveca', 5, 'にこちゃんの誕生日のお祝いです']
  ]
];
$nlbonus[]=[
  'nlbonus_id'=>((date('Y')-2000)*-9-4),
  'detail_text'=>'８月３日は高坂穂乃果ちゃんの誕生日です！
お祝いとして、本日ログインした皆さんに
「ラブカストーン5個」をプレゼントいたいます♪',
  'bg_asset'=>'assets/image/ui/login_bonus_extra/birthday_4_1.png',
  'start'=>date('Y').'-08-03 00:00:00',
  'end'=>date('Y').'-08-03 23:59:59',
  'items'=>[
    ['loveca', 5, '穂乃果ちゃんの誕生日のお祝いです']
  ]
];
$nlbonus[]=[
  'nlbonus_id'=>((date('Y')-2000)*-9-5),
  'detail_text'=>'９月１２日は南ことりちゃんの誕生日です！
お祝いとして、本日ログインした皆さんに
「ラブカストーン5個」をプレゼントいたいます♪',
  'bg_asset'=>'assets/image/ui/login_bonus_extra/birthday_5_1.png',
  'start'=>date('Y').'-09-12 00:00:00',
  'end'=>date('Y').'-09-12 23:59:59',
  'items'=>[
    ['loveca', 5, 'ことりちゃんの誕生日のお祝いです']
  ]
];
$nlbonus[]=[
  'nlbonus_id'=>((date('Y')-2000)*-9-6),
  'detail_text'=>'１０月２１日は絢瀬絵里ちゃんの誕生日です！
お祝いとして、本日ログインした皆さんに
「ラブカストーン5個」をプレゼントいたいます♪',
  'bg_asset'=>'assets/image/ui/login_bonus_extra/birthday_6_1.png',
  'start'=>date('Y').'-10-21 00:00:00',
  'end'=>date('Y').'-10-21 23:59:59',
  'items'=>[
    ['loveca', 5, '絵里ちゃんのお誕生日のお祝いです']
  ]
];
$nlbonus[]=[
  'nlbonus_id'=>((date('Y')-2000)*-9-7),
  'detail_text'=>'１１月１日は星空凛ちゃんのお誕生日です！
お祝いとして、本日ログインした皆さんに
「ラブカストーン5個」をプレゼントいたいます♪',
  'bg_asset'=>'assets/image/ui/login_bonus_extra/birthday_7_1.png',
  'start'=>date('Y').'-11-01 00:00:00',
  'end'=>date('Y').'-11-01 23:59:59',
  'items'=>[
    ['loveca', 5, '凛ちゃんのお誕生日のお祝いです']
  ]
];
$nlbonus[]=[
  'nlbonus_id'=>((date('Y')-2000)*-9-8),
  'detail_text'=>'１月１７日は小泉花陽ちゃんのお誕生日です！
お祝いとして、本日ログインした皆さんに
「ラブカストーン5個」をプレゼントいたいます♪',
  'bg_asset'=>'assets/image/ui/login_bonus_extra/birthday_8_1.png',
  'start'=>date('Y').'-01-17 00:00:00',
  'end'=>date('Y').'-01-17 23:59:59',
  'items'=>[
    ['loveca', 5, '花陽ちゃんのお誕生日のお祝いです']
  ]
];
$nlbonus[]=[
  'nlbonus_id'=>((date('Y')-2000)*-9-9),
  'detail_text'=>'３月１５日は園田海未ちゃんのお誕生日です！
お祝いとして、本日ログインした皆さんに
「ラブカストーン5個」をプレゼントいたいます♪',
  'bg_asset'=>'assets/image/ui/login_bonus_extra/birthday_9_1.png',
  'start'=>date('Y').'-03-15 00:00:00',
  'end'=>date('Y').'-03-15 23:59:59',
  'items'=>[
    ['loveca', 5, '海未ちゃんのお誕生日のお祝いです']
  ]
];