<?php
//指定协力活动的默认曲库。
//最外层的1~4分别是E到EX的四个难度
//内层数组是曲目ID，曲目ID与实际谱面的对应关系可到“公告页-自定义组曲”中的选曲页查看。

//示例曲库内容为：
//Easy：第22回SM NORMAL
//Normal：第22回SM HARD
//Hard：第22回SM EX
//Expert：第22回SM TECHNICAL
$duty_lifficulty_ids=[
  1=>['Live_0804.json', 'Live_0182.json', 'Live_s0389.json', 'Live_0590.json', 'Live_0649.json', 'Live_0873.json', 'Live_0562.json', 'Live_0719.json', 'Live_0359.json', 'Live_0538.json', 'Live_s0372.json', 'Live_0304.json', 'Live_0867.json', 'Live_0754.json', 'Live_0850.json', 'Live_s0393.json'],
  2=>['Live_0805.json', 'Live_0183.json', 'Live_s0390.json', 'Live_0591.json', 'Live_0650.json', 'Live_0874.json', 'Live_0563.json', 'Live_0720.json', 'Live_0360.json', 'Live_0539.json', 'Live_s0373.json', 'Live_0305.json', 'Live_0868.json', 'Live_0755.json', 'Live_0851.json', 'Live_s0394.json'],
  3=>['Live_0806.json', 'Live_0349.json', 'Live_0752.json', 'Live_0810.json', 'Live_0709.json', 'Live_0510.json', 'Live_0690.json', 'Live_0439.json', 'Live_0601.json', 'Live_s0402.json'],
  4=>['Live_0806.json', 'Live_0349.json', 'Live_0752.json', 'Live_0810.json', 'Live_0709.json', 'Live_0510.json', 'Live_0690.json', 'Live_0439.json', 'Live_0463.json', 'Live_0567.json', 'Live_0601.json', 'Live_s0402.json'],
  5=>['Live_0807.json', 'Live_0350.json', 'Live_0753.json', 'Live_0811.json', 'Live_0709.json', 'Live_0510.json', 'Live_0690.json', 'Live_0439.json', 'Live_0463.json', 'Live_0567.json', 'Live_0601.json', 'Live_s0402.json'],
  6=>['Live_0809.json', 'Live_0351.json', 'Live_0754.json', 'Live_0812.json', 'Live_0709.json', 'Live_0510.json', 'Live_0690.json', 'Live_0439.json', 'Live_0463.json', 'Live_0567.json', 'Live_0601.json', 'Live_s0402.json']
];
