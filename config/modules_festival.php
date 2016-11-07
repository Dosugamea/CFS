<?php
//指定FESTIVAL的默认曲库。
//外层的1~3分别代表三种颜色（即三个大的曲库），当然也可以增加更多项。
//内层的1~4分别代表当前曲库里的E到EX四个难度
//括号里的字符串是谱面ID，谱面ID与实际谱面的对应关系可到“公告页-游戏设置-自定义组曲”中的选曲页查看。

$festival_live_lifficulty_ids=[
  1=>[
    1=>['Live_0013.json', 'Live_0028.json', 'Live_0037.json', 'Live_0069.json', 'Live_0125.json', 'Live_0835.json', 'Live_0270.json', 'Live_0678.json'],
    2=>['Live_0014.json', 'Live_0029.json', 'Live_0038.json', 'Live_0070.json', 'Live_0126.json', 'Live_0836.json', 'Live_0271.json', 'Live_0679.json'],
    3=>['Live_0015.json', 'Live_0030.json', 'Live_0039.json', 'Live_0071.json', 'Live_0127.json', 'Live_0837.json', 'Live_0272.json', 'Live_0680.json'],
    4=>['Live_0077.json', 'Live_0099.json', 'Live_0088.json', 'Live_0115.json', 'Live_0262.json', 'Live_s0391.json', 'Live_0416.json', 'Live_0834.json']
  ],
  2=>[
    1=>['Live_0004.json', 'Live_0019.json', 'Live_0080.json', 'Live_0520.json', 'Live_0706.json', 'Live_0148.json', 'Live_0702.json', 'Live_0831.json'],
    2=>['Live_0005.json', 'Live_0020.json', 'Live_0081.json', 'Live_0521.json', 'Live_0707.json', 'Live_0149.json', 'Live_0703.json', 'Live_0832.json'],
    3=>['Live_0006.json', 'Live_0021.json', 'Live_0082.json', 'Live_0522.json', 'Live_0708.json', 'Live_0150.json', 'Live_0704.json', 'Live_0833.json'],
    4=>['Live_0068.json', 'Live_0078.json', 'Live_0128.json', 'Live_0651.json', 'Live_0838.json', 'Live_0306.json', 'Live_0852.json', 'Live_0659.json']
  ],
  3=>[
    1=>['Live_0016.json', 'Live_0025.json', 'Live_0040.json', 'Live_0061.json', 'Live_0072.json', 'Live_0402.json', 'Live_0314.json', 'Live_0259.json'],
    2=>['Live_0017.json', 'Live_0026.json', 'Live_0041.json', 'Live_0062.json', 'Live_0073.json', 'Live_0403.json', 'Live_0315.json', 'Live_0260.json'],
    3=>['Live_0018.json', 'Live_0027.json', 'Live_0042.json', 'Live_0063.json', 'Live_0074.json', 'Live_0404.json', 'Live_0316.json', 'Live_0261.json'],
    4=>['Live_0084.json', 'Live_0089.json', 'Live_0116.json', 'Live_0079.json', 'Live_0124.json', 'Live_0540.json', 'Live_0478.json', 'Live_0393.json']
  ]
];
