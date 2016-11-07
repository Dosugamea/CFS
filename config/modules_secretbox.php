<?php
//抽卡设置
//您需要分别进行卡池、抽卡类型、抽卡页、选项卡四项设置。
//设置内容繁杂，请仔细阅读以下所有的说明文字，并仔细检查您的最终设置！
//若您需要测试设置，改好后点一下客户端的劝诱按钮就可以刷新了，不需要重启

//特待生奖励的总值
$max_gauge_point = 100;
//特待生奖励满后获得的奖励
$max_gauge_award = ['sticket', 1];

/* 此处设置卡池
下面是卡池的示例，后面会有说明：*/
$default_scout = [
	'R and not_tokuten and muse' => 80,
	'SR and not_tokuten and muse' => 15,
	'SSR and not_tokuten and muse' => 4,
	'UR and not_tokuten and muse' => 1
];
$default_scout_11 = [
	'SR and not_tokuten and muse' => 15,
	'SSR and not_tokuten and muse' => 4,
	'UR and not_tokuten and muse' => 1
];
$default_scout_normal = [
	'N' => 95,
	'R and not_tokuten and muse' => 5
];
$default_scout_aq = [
	'R and not_tokuten and aqours' => 80,
	'SR and not_tokuten and aqours' => 15,
	'SSR and not_tokuten and aqours' => 4,
	'UR and not_tokuten and aqours' => 1
];
$default_scout_11_aq = [
	'SR and not_tokuten and aqours' => 15,
	'SSR and not_tokuten and aqours' => 4,
	'UR and not_tokuten and aqours' => 1
];
$default_scout_normal_aq = [
	'N' => 95,
	'R and not_tokuten and aqours' => 5
];
/* 卡池中的每一项由filter和chance两个参数组成
箭头后面的数字为抽出的概率【和不必为100，请使用整数】
箭头前面为筛选卡片的条件，条件之间使用and或or连接，可以加括号。

服务器识别的筛选条件如下（区分大小写）：
团体：muse aqours arise （此条件很重要，否则会返回所有团体的卡片！）
稀有度：N R SR SSR UR （只有这一行是大写，其余全小写）
属性：smile pure cool purple （紫色卡）
小组：printemps bibi lilywhite
年级（μ's）：grade1 grade2 grade3 （为向前兼容，不带前缀的grade只筛选μ's角色）
年级（Aqours）：aq_grade1 aq_grade2 aq_grade3
角色（μ's）：honoka eli umi kotori hanayo rin maki nozomi nico
角色（Aqours）：chika riko kanan dia you yoshiko hanamaru mari ruby
卡片种类： not_tokuten （非特典卡） tokuten （特典卡，不包括特典羊驼王） skillup （升级技能卡） alpaca （经验卡，包括R白羊驼、R三个老师、特典SR羊驼王）
技能：no-skill hantei-syo hantei-dai heal scoreup
CENTER技能：no-centerskill power heart princess angel empress （取center技能的后半部分）
按相册ID指定卡片：unit_number=数字 或 unit_number in (逗号分隔的数字)
按内部ID指定卡片：unit_id=数字 或 unit_id in (逗号分隔的数字)
指定所有卡片：all

也可以直接在箭头前面写sql语句，或者把筛选条件和sql语句混写，语句执行时会先与"select unit_id from unit_m where "进行连接，然后把里面的关键字替换成对应的语句。

前面的示例为官服的一般抽卡，下面的默认设置为官服的辅助券抽卡。 */

$default_scout_srur = [
	'SR and not_tokuten and muse' => 4,
	'UR and not_tokuten and muse' => 1
];

$default_scout_support = [
	'R and skillup' => 6,
	'SR and skillup' => 3,
	'UR and skillup' => 1
];


/* 此处设置抽卡信息
抽卡类型有四种，其中某些依赖于正确的页面种类（见后面的页面部分）
所有类型通用的设置：
"type" => 抽卡类型
"rule" => 卡池
"special_rule" => 保底设置（可省略，见下）
"add_gauge" => 特待生奖励增加的数值（可省略，默认为0）

保底设置的格式：
"special_rule" => [
	"trigger_rarity" => 数字, //若前面抽出的卡片都不高于此稀有度，则触发保底（1~4分别是N到UR，写得更高则为一定触发）
	"rule" => 保底卡池
]

第一种类型：特待生招募【显示位置：上】
特点：cost强制显示为心，multi消费强制按10倍显示、11连几个字写在按钮上了所以不能改
示例：
$my_box = [
	"type" => 1, //特待生招募
	"amount" => 5, //消耗5个Loveca
	"rule" => $my_rule //前面指定的卡池
];

第二种类型：消耗道具招募（不可连抽），依赖于页面种类【显示位置：上】
若页面种类=1，显示为消耗1张招募券的抽卡，程序中也会按照此消耗处理
$my_box = [
	"type" => 2,
	"rule" => $my_rule
]; //此种情况这么写就可以了
若页面种类=0，消耗为指定的物品，显示为消耗amount个item的抽卡，【这是唯一可以自由指定消耗种类的抽卡类型】
$my_box = [
	"type" => 2,
	"item" => 'sticket', //服务器识别的道具种类：ticket（招募券） social coin loveca sticket（辅助券）
	"amount" => 5,
	"rule" => $my_rule
];

第三种类型：一般生招募【显示位置：下，需要页面种类=1】
特点：cost强制显示为友情点，可指定multi数目，multi消费按照 数目*消耗 计算
示例：
$my_box = [
	"type" => 3, //一般生招募
	"amount" => 100, //消耗100个友情点
	"multi" => 10, //最多10连
	"allow_lesser_multi" => true, //若为true，则会像官服那样，允许小于指定值的连抽，否则不允许
	"rule" => $my_rule //前面指定的卡池
];

第四种类型：免费招募（不可连抽）【显示位置：下，需要页面种类=1】
特点：显示为“1日1回无料一般生劝诱”
示例：
$my_box = [
	"type" => 4, //免费招募
	"once_per_day" => 1, //若为大于0的数字，则一天只能抽一次，【不同的免费招募使用这个数字区分】，否则（0或false或留空）为无限次
	"rule" => $my_rule //前面指定的卡池
];

下面的默认设置为官服有保底的一般抽卡和辅助券抽卡。 */
$default_box = [
	"type" => 1, //特待生招募
	"amount" => 5, //消耗5个Loveca
	"add_gauge" => 10, //增加10点特待生奖励
	"rule" => $default_scout, //前面指定的卡池
	"special_rule" => [
		"trigger_rarity" => 2, //若前面抽出的卡片都不高于此稀有度，则触发保底（1~4分别是N到UR，写得更高则为一定触发）
		"rule" => $default_scout_11
	]
];
$default_box_ticket = [
	"type" => 2,
	"add_gauge" => 10, //增加10点特待生奖励
	"rule" => $default_scout
];
$default_box_normal = [
	"type" => 3, //一般生招募
	"amount" => 100, //消耗100个友情点
	"multi" => 10, //最多10连
	"allow_lesser_multi" => true, //若为true，则会像官服那样，允许小于指定值的连抽，否则不允许
	"rule" => $default_scout_normal //前面指定的卡池
];
$default_box_free = [
	"type" => 4, //免费招募
	"once_per_day" => -1, //若为大于0的数字，则一天只能抽一次，【不同的免费招募使用这个数字区分】，否则（0或false或留空）为无限次
	"rule" => $default_scout_normal //前面指定的卡池
];

$default_box_aq = [
	"type" => 1, //特待生招募
	"amount" => 5, //消耗5个Loveca
	"add_gauge" => 10, //增加10点特待生奖励
	"rule" => $default_scout_aq, //前面指定的卡池
	"special_rule" => [
		"trigger_rarity" => 2, //若前面抽出的卡片都不高于此稀有度，则触发保底（1~4分别是N到UR，写得更高则为一定触发）
		"rule" => $default_scout_11_aq
	]
];
$default_box_ticket_aq = [
	"type" => 2,
	"add_gauge" => 10, //增加10点特待生奖励
	"rule" => $default_scout_aq
];
$default_box_normal_aq = [
	"type" => 3, //一般生招募
	"amount" => 100, //消耗100个友情点
	"multi" => 10, //最多10连
	"allow_lesser_multi" => true, //若为true，则会像官服那样，允许小于指定值的连抽，否则不允许
	"rule" => $default_scout_normal_aq //前面指定的卡池
];
$default_box_free_aq = [
	"type" => 4, //免费招募
	"once_per_day" => -1, //若为大于0的数字，则一天只能抽一次，【不同的免费招募使用这个数字区分】，否则（0或false或留空）为无限次
	"rule" => $default_scout_normal_aq //前面指定的卡池
];

$default_box_support = [
	"type" => 2,
	"item" => 'sticket', //服务器识别的道具种类：ticket（招募券） social coin loveca sticket（辅助券）
	"amount" => 1,
	"rule" => $default_scout_support
];
$default_box_srur = [
	"type" => 2,
	"item" => 'sticket', //服务器识别的道具种类：ticket（招募券） social coin loveca sticket（辅助券）
	"amount" => 5,
	"rule" => $default_scout_srur
];

/* 此处设置抽卡页面
抽卡页面有两种
种类0：只有上方的抽卡，需要指定标题图片（不指定的话会崩溃），可以指定说明文字，此种抽卡页面下第二种抽卡类型可消耗任意道具
种类1：有上方和下方的抽卡，标题图片固定为“特待生劝诱”，不显示说明文字（哪怕不指定下方的抽卡），此种抽卡页面下第二种抽卡类型只能消耗1个招募券

由于涉及到数据包中的图片比较多，这个设置项服务器会做容错处理（见下方“可省略”的项）。若没有把握，请省略这些项以免客户端崩溃。
下面的默认设置作为示例，此设置为官服的一般抽卡和辅助券抽卡。*/
$default_page = [
	"page_layout" => 1, //种类1的页面
	"img_asset" => 'assets/image/secretbox/top/s_con_n_3_2.png', //左侧显示的图片（可省略，若省略会显示官服特待生劝诱的图片）
	"url" => '/webview.php/secretBox/default_page', //点击“劝诱详情”弹出的网页地址（可省略，若省略会显示本示例中的网址）
	"effect" => [728, 724, 725, 726, 727], //左侧滚动播出的卡片（可省略）【此处使用的是卡片内部ID(unit_id)，不是相册ID(unit_number)】
	"box" => [$default_box_ticket, $default_box, $default_box_free, $default_box_normal] //指定本页显示的抽卡类型。若前一种类型的条件不足，会自动显示后一种类型，直到剩下一种。特待生和一般生在程序中会分开处理，故其先后顺序无要求。
];
$default_page_aq = [
	"page_layout" => 1, //种类1的页面
	"img_asset" => 'assets/image/secretbox/top/s_con_n_62_1.png', //左侧显示的图片（可省略，若省略会显示官服特待生劝诱的图片）
	"url" => '/webview.php/secretBox/default_page', //点击“劝诱详情”弹出的网页地址（可省略，若省略会显示本示例中的网址）
	"effect" => [], //左侧滚动播出的卡片（可省略）【此处使用的是卡片内部ID(unit_id)，不是相册ID(unit_number)】
	"box" => [$default_box_ticket_aq, $default_box_aq, $default_box_free_aq, $default_box_normal_aq] //指定本页显示的抽卡类型。若前一种类型的条件不足，会自动显示后一种类型，直到剩下一种。特待生和一般生在程序中会分开处理，故其先后顺序无要求。
];

$default_page_srur = [
	"page_layout" => 0, //种类0的页面
	"img_asset" => 'assets/image/secretbox/top/s_con_n_22_1.png', //左侧显示的图片
	"url" => '/webview.php/secretBox/default_page_srur', //点击“劝诱详情”弹出的网页地址
	"title_asset" => 'assets/image/secretbox/title/22.png', //标题图片（可省略，若省略会显示“特待生劝诱”）
	"description" => "SR部員かUR部員を勧誘できる\n補助チケット勧誘です！", //说明文字
	"box" => [$default_box_srur]
];

$default_page_support = [
	"page_layout" => 0, //种类0的页面
	"img_asset" => 'assets/image/secretbox/top/s_con_n_23_1.png', //左侧显示的图片
	"url" => '/webview.php/secretBox/default_page_support', //点击“劝诱详情”弹出的网页地址
	"title_asset" => 'assets/image/secretbox/title/23.png', //标题图片
	"description" => "特技アップサポートメンバーだけを\n勧誘できる補助チケット勧誘です！", //说明文字
	"box" => [$default_box_support]
];

/* 此处设置选项卡
4.0可以准备任意多选项卡，只是每个分类最大3个
把要加的page放进去就可以了 */

$scout_first_tab = [
	$default_page
];
$scout_first_tab_aq = [
	$default_page_aq
];
$scout_third_tab = [
	$default_page_srur,
	$default_page_support
];

/* 此处设置分类
把选项卡按照图中的格式放进去
后面两个路径分别是选项卡没选中和选中的图片 */

$scout_muse = [
  [$scout_first_tab, 'assets/image/secretbox/tab/s_tab_01.png', 'assets/image/secretbox/tab/s_tab_01se.png'],
  //[$scout_second_tab, 'assets/image/secretbox/tab/s_tab_02.png', 'assets/image/secretbox/tab/s_tab_02se.png'],
  [$scout_third_tab, 'assets/image/secretbox/tab/s_tab_03.png', 'assets/image/secretbox/tab/s_tab_03se.png']
];
$scout_aqours = [
  [$scout_first_tab_aq, 'assets/image/secretbox/tab/s_tab_01.png', 'assets/image/secretbox/tab/s_tab_01se.png']
];