<?php
//抽卡设置
//您需要分别进行卡池、抽卡类型、抽卡页、选项卡四项设置。
//设置内容繁杂，请仔细阅读以下所有的说明文字，并仔细检查您的最终设置！
//若您需要测试设置，改好后点一下客户端的劝诱按钮就可以刷新了，不需要重启

//特待生奖励的总值
$max_gauge_point = 100;
//特待生奖励满后获得的奖励
$max_gauge_award = ['sticket', 1];

class __scout {
  public $n = 0, $r = 0, $sr = 0, $ur = 0, $ssr = 0, $filter = '';
  public function __construct($def) {
    if (is_array($def)) {
      $this->n = $def[0];
      $this->r = $def[1];
      $this->sr = $def[2];
      $this->ur = $def[3];
      $this->ssr = isset($def[4]) ? $def[4] : 0;
    } elseif (is_object($def)) {
      $this->n = $def->n;
      $this->r = $def->r;
      $this->sr = $def->sr;
      $this->ur = $def->ur;
      $this->ssr = $def->ssr;
      $this->filter = $def->filter;
    }
  }
  public function _and($s) {
    $this->filter .= ' and '.$s;
    return $this;
  }
  public function ret() {
    $filter = $this->filter . ' and not aqours and not arise';
    return [
      'N'.$filter => $this->n,
      'R'.$filter => $this->r,
      'SR'.$filter => $this->sr,
      'SSR'.$filter => $this->ssr,
      'UR'.$filter => $this->ur
    ];
  }
  public function ret_aq() {
    $filter = $this->filter . ' and not muse and not arise';
    return [
      'N'.$filter => $this->n,
      'R'.$filter => $this->r,
      'SR'.$filter => $this->sr,
      'SSR'.$filter => $this->ssr,
      'UR'.$filter => $this->ur
    ];
  }
  public function ret_both() {
    $filter = $this->filter . ' and (muse or aqours)';
    return [
      'N'.$filter => $this->n,
      'R'.$filter => $this->r,
      'SR'.$filter => $this->sr,
      'SSR'.$filter => $this->ssr,
      'UR'.$filter => $this->ur
    ];
  }
};
function makeRule($def = false) {
  return new __scout($def);
}

class __box {
  private $box = [
    'type' => false,
    'rule' => false
  ];
  public $aq = 0;
  public function __construct($type, $aq = 0) {
    switch ($type) {
      case 1: case 'loveca': $this->box['type'] = 1; break;
      case 2: case 'custom': $this->box['type'] = 2; break;
      case 3: case 'social': $this->box['type'] = 3; break;
      case 4: case 'free': $this->box['type'] = 4; break;
    }
    $this->aq = $aq;
  }
  public function rule($rule) {
    if (is_object($rule)) {
		  switch($this->aq) {
			  case 0:$rule=$rule->ret();break;
			  case 1:$rule=$rule->ret_aq();break;
			  case 2:$rule=$rule->ret_both();break;
		  }
    }
    $this->box['rule'] = $rule;
    return $this;
  }
  public function cost($r) {
    $this->box['amount'] = $r;
    return $this;
  }
  public function item($r) {
    if ($this->box['type'] == 2)
      $this->box['item'] = $r;
    return $this;
  }
  public function gauge($r) {
    $this->box['add_gauge'] = $r;
    return $this;
  }
  public function multi($r, $lesser = true) {
    if ($this->box['type'] == 3) {
      $this->box['multi'] = $r;
      $this->box['allow_lesser_multi'] = $lesser;
    }
    return $this;
  }
  public function once($r) {
    if ($this->box['type'] == 4)
      $this->box['once_per_day'] = $r;
    return $this;
  }
  public function special($r, $rule) {
    if ($this->box['type'] == 3 || $this->box['type'] == 1) {
      switch ($r) {
        case 'N': $r = 1; break;
        case 'R': $r = 2; break;
        case 'SR': $r = 3; break;
        case 'UR': $r = 4; break;
        case 0: case false: $r = 5; break;
      }
      if (is_object($rule)) {
		  switch($this->aq) {
			  case 0:$rule=$rule->ret();break;
			  case 1:$rule=$rule->ret_aq();break;
			  case 2:$rule=$rule->ret_both();break;
		  }
      }
      $this->box["special_rule"] = [
        "trigger_rarity" => $r,
        "rule" => $rule
      ];
    }
    return $this;
  }
  public function ret() {
    return $this->box;
  }
};
function makeBox($type, $aq = 0) {
  return new __box($type, $aq);
}

function makePage($boxes, $url = false, $desc = false, $title = false, $img = false) {
  $page['page_layout'] = $desc ? 0 : 1;
  $page['img_asset'] = $img ?: 'assets/image/secretbox/top/s_con_n_3_2.png';
  $page['url'] = $url ? '/webview.php/secretBox/index2?'.$url : '/webview.php/secretBox/index';
  $page['box'] = $boxes;
  if ($desc) {
    $page['description'] = $desc;
  }
  if ($title) {
    $page['title_asset'] = $title;
  }
  return $page;
}

$base_n = makeRule([95,5,0,0])->_and('not_tokuten');
$base_r = makeRule([0,80,15,1,4]);
$base_srur = makeRule([0,0,4,1]);

if (isset($params) && $params['card_switch']) {
  $pl_r = makeRule([0,2,5,2,1]);
  $pl_srur = makeRule([0,0,3,2]);
  $baodi = 'SR';
  $ur = makeRule([0,0,0,2,1]);
  $pl_skillup = makeRule($pl_r);
} else {
  $pl_r = $base_r;
  $pl_srur = $base_srur;
  $baodi = 'R';
  $ur = makeRule([0,0,15,1,4]);
  $pl_skillup = makeRule([0,6,3,1]);
}
$pl_r = $pl_r->_and('not_tokuten');
$pl_srur = $pl_srur->_and('not_tokuten');
$ur = $ur->_and('not_tokuten');

//普通抽卡
$allcard_loveca = makeBox(1)->rule($pl_r)->gauge(10)->cost(1)->special($baodi, $ur)->ret();
$allcard_n = makeBox(3)->rule($base_n)->cost(50)->multi(10, true)->ret();
$allcard_free = makeBox(4)->rule($base_n)->once(1)->ret();
$scout_first_tab[] = makePage([$allcard_loveca, $allcard_free, $allcard_n], 'all');
//普通抽卡（水团）
$allcard_loveca_aq = makeBox(1, 1)->rule($pl_r)->gauge(10)->cost(1)->special($baodi, $ur)->ret();
$allcard_n_aq = makeBox(3, 1)->rule($base_n)->cost(50)->multi(10, true)->ret();
$allcard_free_aq = makeBox(4, 1)->rule($base_n)->once(1)->ret();
$scout_first_tab_aq[] = makePage([$allcard_loveca_aq, $allcard_free_aq, $allcard_n_aq], 'all', false, false, 'assets/image/secretbox/top/s_con_n_62_1.png');

//限定
$makeLimitedPage = function($rule, $desc, $title = false, $img = false) use ($baodi, $ur, $pl_r) {
  $m = makeBox(1)->gauge(10)->cost(1)->special($baodi, makeRule($ur)->_and($rule))->rule(makeRule($pl_r)->_and($rule))->ret();
  return makePage([$m], $rule, $desc, 'assets/image/secretbox/title/'.$title, 'assets/image/secretbox/top/'.$img);
};
$makeLimitedPage2 = function($rule, $desc, $title = false, $img = false) use ($baodi, $ur, $pl_r) {
  global $params;
  $m = makeBox(1)->gauge(10)->cost(2)->special($baodi, makeRule($ur)->_and($rule))->rule(makeRule($pl_r)->_and($rule))->ret();
  if ($params['card_switch']) {
    $m2 = makeBox(2)->gauge(10)->item('sticket')->cost(1)->rule(makeRule($pl_r)->_and($rule))->ret();
    return makePage([$m2, $m], $rule, $desc, 'assets/image/secretbox/title/'.$title, 'assets/image/secretbox/top/'.$img);
  } else {
    return makePage([$m], $rule, $desc, 'assets/image/secretbox/title/'.$title, 'assets/image/secretbox/top/'.$img);
  }
};

$scout_second_tab[] = $makeLimitedPage('printemps', 'Printemps限定勧誘', '18.png', 's_con_n_18_1.png');
$scout_second_tab[] = $makeLimitedPage('bibi', 'BiBi限定勧誘', '20.png', 's_con_n_20_1.png');
$scout_second_tab[] = $makeLimitedPage('lilywhite', 'lily white限定勧誘', '16.png', 's_con_n_16_1.png');
$scout_second_tab[] = $makeLimitedPage('grade1', '1年生限定勧誘', '4.png', 's_con_n_4_1.png');
$scout_second_tab[] = $makeLimitedPage('grade2', '2年生限定勧誘', '6.png', 's_con_n_6_1.png');
$scout_second_tab[] = $makeLimitedPage('grade3', '3年生限定勧誘', '8.png', 's_con_n_8_1.png');
$scout_second_tab[] = $makeLimitedPage('smile', 'Smile限定勧誘', '10.png', 's_con_n_10_1.png');
$scout_second_tab[] = $makeLimitedPage('pure', 'Pure限定勧誘', '12.png', 's_con_n_12_1.png');
$scout_second_tab[] = $makeLimitedPage('cool', 'Cool限定勧誘', '14.png', 's_con_n_14_1.png');
$scout_second_tab[] = makePage([makeBox(1)->gauge(10)->cost(1)->rule(makeRule([0,1,4,2])->_and('tokuten'))->ret()], 'tokuten', '特典カード限定勧誘');

$srur1 = makeBox(1)->gauge(10)->cost(2)->rule(makeRule($pl_srur))->ret();
$srur2 = makeBox(2)->gauge(10)->item('sticket')->cost(1)->rule(makeRule($pl_srur))->ret();
$srur1_aq = makeBox(1, true)->gauge(10)->cost(2)->rule(makeRule($pl_srur))->ret();
$srur2_aq = makeBox(2, true)->gauge(10)->item('sticket')->cost(1)->rule(makeRule($pl_srur))->ret();
$scout_third_tab[] = makePage([$srur2, $srur1], 'srur', 'SR部員かUR部員を勧誘できる\n補助チケット勧誘です！', 'assets/image/secretbox/title/22.png', 'assets/image/secretbox/top/s_con_n_22_1.png');
$scout_third_tab_aq[] = makePage([$srur2_aq, $srur1_aq], 'srur', 'SR部員かUR部員を勧誘できる\n補助チケット勧誘です！', 'assets/image/secretbox/title/22.png', 'assets/image/secretbox/top/s_con_n_62_1.png');

$support = makeBox(2)->item('coin')->cost(100000)->rule(makeRule($pl_skillup)->_and('skillup'))->ret();
$scout_third_tab[] = makePage([$support], 'support', '特技アップサポートメンバーだけを\n勧誘できる補助チケット勧誘です！', 'assets/image/secretbox/title/23.png', 'assets/image/secretbox/top/s_con_n_23_1.png');
if (isset($params) && $params['card_switch']) {
  $alpaca = makeBox(2)->item('coin')->cost(20000)->rule(makeRule([0,9,1,0])->_and('alpaca'))->ret();
  $scout_third_tab[] = makePage([$alpaca], 'support', 'Buy an EXP card for 20000 coins', 'assets/image/secretbox/title/23.png', 'assets/image/secretbox/top/s_con_n_23_1.png');
}

$scout_second_tab[] = $makeLimitedPage2('honoka', 'キャラ限定勧誘「高坂穂乃果」', '40.png', 's_con_n_40_1.png');
$scout_second_tab[] = $makeLimitedPage2('eli', 'キャラ限定勧誘「絢瀬絵里」', '42.png', 's_con_n_42_1.png');
$scout_second_tab[] = $makeLimitedPage2('kotori', 'キャラ限定勧誘「南ことり」', '44.png', 's_con_n_44_1.png');
$scout_second_tab[] = $makeLimitedPage2('umi', 'キャラ限定勧誘「園田海未」', '46.png', 's_con_n_46_1.png');
$scout_second_tab[] = $makeLimitedPage2('rin', 'キャラ限定勧誘「星空凛」', '48.png', 's_con_n_48_1.png');
$scout_second_tab[] = $makeLimitedPage2('maki', 'キャラ限定勧誘「西木野真姫」', '50.png', 's_con_n_50_1.png');
$scout_second_tab[] = $makeLimitedPage2('nozomi', 'キャラ限定勧誘「東條希」', '52.png', 's_con_n_52_1.png');
$scout_second_tab[] = $makeLimitedPage2('hanayo', 'キャラ限定勧誘「小泉花陽」', '54.png', 's_con_n_54_1.png');
$scout_second_tab[] = $makeLimitedPage2('nico', 'キャラ限定勧誘「矢澤にこ」', '56.png', 's_con_n_56_1.png');


//普通抽卡（大卡池）
$allcard_loveca_aq2 = makeBox(1, 2)->rule($pl_r)->gauge(10)->cost(1)->special($baodi, $ur)->ret();
$scout_first_tab_aq[] = makePage([$allcard_loveca_aq2], false, '18人勧誘');

$scout_muse = [
  [$scout_first_tab, 'assets/image/secretbox/tab/s_tab_01.png', 'assets/image/secretbox/tab/s_tab_01se.png'],
  [$scout_second_tab, 'assets/image/secretbox/tab/s_tab_02.png', 'assets/image/secretbox/tab/s_tab_02se.png'],
  [$scout_third_tab, 'assets/image/secretbox/tab/s_tab_03.png', 'assets/image/secretbox/tab/s_tab_03se.png']
];
$scout_aqours = [
  [$scout_first_tab_aq, 'assets/image/secretbox/tab/s_tab_01.png', 'assets/image/secretbox/tab/s_tab_01se.png'],
  [$scout_third_tab_aq, 'assets/image/secretbox/tab/s_tab_03.png', 'assets/image/secretbox/tab/s_tab_03se.png']
];
