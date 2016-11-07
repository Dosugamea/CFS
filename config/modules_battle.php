<?php
//指定SCORE MATCH的默认曲库。
//最外层的1~4分别是E到EX的四个难度
//内层数组第一个数字是曲目ID，曲目ID与实际谱面的对应关系可到“公告页-自定义组曲”中的选曲页查看。
//内层数组第二个数字是模式，0=正常 1=新随机 2=旧随机

//示例曲库内容为：
//Easy：第22回SM NORMAL
//Normal：第22回SM HARD
//Hard：第22回SM EX
//Expert：第22回SM TECHNICAL
//Technical：全部日替曲EX（跳绳除外）
$score_match_live_lifficulty_ids=[
  1=>[['Live_0804.json', 0], ['Live_0182.json', 0], ['Live_s0389.json', 0], ['Live_0590.json', 0], ['Live_0649.json', 0], ['Live_0873.json', 0], ['Live_0562.json', 0], ['Live_0719.json', 0], ['Live_0359.json', 0], ['Live_0538.json', 0], ['Live_s0372.json', 0], ['Live_0304.json', 0], ['Live_0867.json', 0], ['Live_0754.json', 0], ['Live_0850.json', 0], ['Live_s0393.json', 0]],
  2=>[['Live_0805.json', 0], ['Live_0183.json', 0], ['Live_s0390.json', 0], ['Live_0591.json', 0], ['Live_0650.json', 0], ['Live_0874.json', 0], ['Live_0563.json', 0], ['Live_0720.json', 0], ['Live_0360.json', 0], ['Live_0539.json', 0], ['Live_s0373.json', 0], ['Live_0305.json', 0], ['Live_0868.json', 0], ['Live_0755.json', 0], ['Live_0851.json', 0], ['Live_s0394.json', 0]],
  3=>[['Live_0806.json', 0], ['Live_0349.json', 0], ['Live_0752.json', 0], ['Live_0810.json', 0], ['Live_0709.json', 0], ['Live_0510.json', 0], ['Live_0690.json', 0], ['Live_0439.json', 0], ['Live_0601.json', 0], ['Live_s0402.json', 0]],
  4=>[['Live_0806.json', 1], ['Live_0349.json', 1], ['Live_0752.json', 1], ['Live_0810.json', 1], ['Live_0709.json', 1], ['Live_0510.json', 1], ['Live_0690.json', 1], ['Live_0439.json', 1], ['Live_0463.json', 0], ['Live_0567.json', 0], ['Live_0601.json', 1], ['Live_s0402.json', 1]],
  5=>[['Live_0567.json', 0], ['Live_0569.json', 0], ['Live_0458.json', 0], ['Live_0459.json', 0], ['Live_0593.json', 0], ['Live_0446.json', 0], ['Live_0566.json', 0], ['Live_0594.json', 0], ['Live_0463.json', 0]],
];
