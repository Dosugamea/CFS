<?php
require_once('includes/unit.php');

$setting = false;
function getSecretboxSetting() {
	global $setting;
	if (!$setting) {
		require('config/modules_secretbox.php');
		$id = 0;
		$make_code = function (&$category) use (&$id) {
			foreach ($category as &$tab) {
				foreach ($tab[0] as &$page) {
					pl_assert(isset($page['page_layout']) && ($page['page_layout'] == 0 || $page['page_layout'] == 1), 'secretbox: 某一页面没有设置页面类型：'.print_r($page, true));
					pl_assert(isset($page['box']), 'secretbox: 某一页面没有设置抽卡信息：'.print_r($page, true));
					foreach($page['box'] as &$box) {
						$box['secret_box_id'] = ++$id;
					}
				}
			}
		};
		pl_assert($scout_muse, 'secretbox: $scout_muse未设置！');
		pl_assert($scout_aqours, 'secretbox: $scout_aqours未设置！');
		$make_code($scout_muse);
		$make_code($scout_aqours);
		$setting = [
			'modes' => [$scout_muse, $scout_aqours],
			'max_gauge_point' => $max_gauge_point,
			'max_gauge_award' => $max_gauge_award
		];
	}
	return $setting;
}

function scout($id, $count) {
	global $mysql, $uid;
	$unit = getUnitDb();
	$box = false;
	foreach (getSecretboxSetting()['modes'] as $category) {
		foreach($category as $tab) {
			foreach ($tab[0] as $page) {
				foreach ($page['box'] as $b) {
					if ($b['secret_box_id'] == $id) {
						$box	= $b;
						break 3;
					}
				}
			}
		}
	}
	pl_assert($box, 'secretbox: 找不到对应的box');
	pl_assert(isset($box['rule']), 'secretbox: 某项抽卡信息没有指定抽卡规则：'.print_r($box, true));
	if (!isset($box['special_rule'])) {
		$box['special_rule'] = ['trigger_rarity' => 0];
	}
	$ret = [];
	$rule = $box['rule'];
	$total_chance = array_reduce($rule, function ($sum, $next) {
		return $sum + $next;
	}, 0);
	$process_rule = function ($rule) {
		if (trim($rule) == 'all') {
			return 'select unit_id from unit_m';
		}
		$rule = preg_replace('/\bN\b/', 'rarity=1', $rule);
		$rule = preg_replace('/\bR\b/', 'rarity=2', $rule);
		$rule = preg_replace('/\bSR\b/', 'rarity=3', $rule);
		$rule = preg_replace('/\bSSR\b/', 'rarity=5', $rule);
		$rule = preg_replace('/\bUR\b/', 'rarity=4', $rule);
		$rule = preg_replace('/\bsmile\b/', 'attribute_id=1', $rule);
		$rule = preg_replace('/\bpure\b/', 'attribute_id=2', $rule);
		$rule = preg_replace('/\bcool\b/', 'attribute_id=3', $rule);
		$rule = preg_replace('/\bpurple\b/', 'attribute_id=5', $rule);
		$rule = preg_replace('/\bprintemps\b/', 'unit_type_id in (1,3,8)', $rule);
		$rule = preg_replace('/\bbibi\b/', 'unit_type_id in (2,6,9)', $rule);
		$rule = preg_replace('/\blilywhite\b/', 'unit_type_id in (4,5,7)', $rule);
		$rule = preg_replace('/\bgrade1\b/', 'unit_type_id in (5,6,8)', $rule);
		$rule = preg_replace('/\bgrade2\b/', 'unit_type_id in (1,3,4)', $rule);
		$rule = preg_replace('/\bgrade3\b/', 'unit_type_id in (2,7,9)', $rule);
		$rule = preg_replace('/\baq_grade1\b/', 'unit_type_id in (106,107,109)', $rule);
		$rule = preg_replace('/\baq_grade2\b/', 'unit_type_id in (101,102,105)', $rule);
		$rule = preg_replace('/\baq_grade3\b/', 'unit_type_id in (103,104,108)', $rule);
		$rule = preg_replace('/\bhonoka\b/', 'unit_type_id=1', $rule);
		$rule = preg_replace('/\beli\b/', 'unit_type_id=2', $rule);
		$rule = preg_replace('/\bkotori\b/', 'unit_type_id=3', $rule);
		$rule = preg_replace('/\bumi\b/', 'unit_type_id=4', $rule);
		$rule = preg_replace('/\brin\b/', 'unit_type_id=5', $rule);
		$rule = preg_replace('/\bmaki\b/', 'unit_type_id=6', $rule);
		$rule = preg_replace('/\bnozomi\b/', 'unit_type_id=7', $rule);
		$rule = preg_replace('/\bhanayo\b/', 'unit_type_id=8', $rule);
		$rule = preg_replace('/\bnico\b/', 'unit_type_id=9', $rule);
		$rule = preg_replace('/\bchika\b/', 'unit_type_id=101', $rule);
		$rule = preg_replace('/\briko\b/', 'unit_type_id=102', $rule);
		$rule = preg_replace('/\bkanan\b/', 'unit_type_id=103', $rule);
		$rule = preg_replace('/\bdia\b/', 'unit_type_id=104', $rule);
		$rule = preg_replace('/\byou\b/', 'unit_type_id=105', $rule);
		$rule = preg_replace('/\byoshiko\b/', 'unit_type_id=106', $rule);
		$rule = preg_replace('/\bhanamaru\b/', 'unit_type_id=107', $rule);
		$rule = preg_replace('/\bmari\b/', 'unit_type_id=108', $rule);
		$rule = preg_replace('/\bruby\b/', 'unit_type_id=109', $rule);
		$rule = preg_replace('/\bmuse\b/', 'unit_type_id in (1,2,3,4,5,6,7,8,9)', $rule);
		$rule = preg_replace('/\baqours\b/', 'unit_type_id in (101,102,103,104,105,106,107,108,109)', $rule);
		$rule = preg_replace('/\barise\b/', 'unit_type_id in (80,81,82)', $rule);
		$rule = preg_replace('/\bnot_tokuten\b/', 'normal_icon_asset not like "%rankup%" and rank_max_icon_asset not like "%normal%"', $rule);
		$rule = preg_replace('/\btokuten\b/', 'disable_rank_up != 1 and normal_icon_asset like "%rankup%"', $rule);
		$rule = preg_replace('/\bskillup\b/', 'default_unit_skill_id >= 190 and default_unit_skill_id <= 201', $rule);
		$rule = preg_replace('/\balpaca\b/', 'disable_rank_up = 1 and default_unit_skill_id is null', $rule);
		$rule = preg_replace('/\bno-skill\b/', 'default_unit_skill_id is null', $rule);
		$rule = preg_replace('/\bhantei-syo\b/', 'default_unit_skill_id in (select unit_skill_id from unit_skill_m where skill_effect_type=4)', $rule);
		$rule = preg_replace('/\bhantei-dai\b/', 'default_unit_skill_id in (select unit_skill_id from unit_skill_m where skill_effect_type=5)', $rule);
		$rule = preg_replace('/\bheal\b/', 'default_unit_skill_id in (select unit_skill_id from unit_skill_m where skill_effect_type=9)', $rule);
		$rule = preg_replace('/\bscoreup\b/', 'default_unit_skill_id in (select unit_skill_id from unit_skill_m where skill_effect_type=11)', $rule);
		$rule = preg_replace('/\bno-centerskill\b/', 'default_leader_skill_id is null', $rule);
		$rule = preg_replace('/\bpower\b/', 'default_leader_skill_id in (1,4,7)', $rule);
		$rule = preg_replace('/\bheart\b/', 'default_leader_skill_id in (2,5,8)', $rule);
		$rule = preg_replace('/\bprincess\b/', 'default_leader_skill_id in (3,33,35)', $rule);
		$rule = preg_replace('/\bangel\b/', 'default_leader_skill_id in (6,31,36)', $rule);
		$rule = preg_replace('/\bempress\b/', 'default_leader_skill_id in (9,32,34)', $rule);
		return 'select unit_id from unit_m where '.$rule;
	};
	$got_cards = [];
	
	for ($i = 1; $i <= $count; $i++) {
		if ($count > 1 && $i == $count) {
			$trigger = true;
			foreach ($got_cards as $v) {
				if ($v['rarity'] > $box['special_rule']['trigger_rarity']) {
					$trigger = false;
					break;
				}
			}
			if ($trigger) {
				$rule = $box['special_rule']['rule'];
				$total_chance = array_reduce($rule, function ($sum, $next) {
					return $sum + $next;
				}, 0);
			}
		}
		pl_assert($total_chance, 'secretbox: 抽卡几率合计为0，未指定规则？'.print_r($box, true));
		$got = mt_rand(1, $total_chance);
		$sum = 0;
		foreach ($rule as $k => $v) {
			$sum += $v;
			if ($got <= $sum) {
				$this_rule = $k;
				break;
			}
		}
		$unit_id = $unit->query($process_rule($this_rule).' order by random() limit 1')->fetchColumn();
		$got_cards[] = addUnit($unit_id, 1, true)[0];
	}
	if ($box['type'] == 4 && isset($box['once_per_day'])) {
		$got_free_gacha_list = explode(',', $mysql->query('select got_free_gacha_list from secretbox where user_id=?', [$uid])->fetchColumn());
		pl_assert(array_search($box['once_per_day'], $got_free_gacha_list) === false, 'secretbox: 重复抽取了每日仅限一次的卡片！');
		$got_free_gacha_list[] = $box['once_per_day'];
		$mysql->query('update secretbox set got_free_gacha_list=? where user_id=?', [implode(',', $got_free_gacha_list), $uid]);
	}
	return $got_cards;
}

function secretBox_all() {
	global $uid, $mysql, $params;
	$setting = getSecretboxSetting();
	$ret['use_cache'] = 1;
	$ret['is_unit_max'] = false;
	for ($i = 1; $i <= 5; $i++) {
		$ret['item_list'][] = [
			"item_id" => $i,
			"amount" => $params['item'.$i]
		];
	}
	$secretbox_info = $mysql->query('select *, to_days(CURRENT_TIMESTAMP) - to_days(last_scout_time) reset_free_gacha from secretbox where user_id=?', [$uid])->fetch();
	if (!$secretbox_info) {
		$mysql->query('insert into secretbox (user_id, got_free_gacha_list) values (?, "")', [$uid]);
		$secretbox_info = $mysql->query('select *, 0 as reset_free_gacha from secretbox where user_id=?', [$uid])->fetch();
	}
	if ($secretbox_info['reset_free_gacha']) {
		$mysql->query('update secretbox set got_free_gacha_list = "" where user_id=?', [$uid]);
		$secretbox_info['got_free_gacha_list'] = '';
	}
	$used_free_gacha_list = explode(',', $secretbox_info['got_free_gacha_list']);
	$ret['gauge_info'] = [
		'max_gauge_point' => $setting['max_gauge_point'],
		'gauge_point' => $secretbox_info['gauge']
	];
	/*if (!$params['card_switch']) {
		return $ret;
	}*/
	$page_id = 0;
	$processPage = function ($setting) use (&$page_id, $used_free_gacha_list) {
		$pages = [];
		foreach ($setting as $page) {
			$next_page = [
				'secret_box_page_id' => ++$page_id,
				'page_layout' => $page['page_layout'],
				'default_img_info' => [
					'banner_img_asset' => null,
					'banner_se_img_asset' => null,
					'img_asset' => isset($page['img_asset']) ? $page['img_asset'] : 'assets/image/secretbox/top/s_con_n_3_2.png',
					'url' => isset($page['url']) ? $page['url'] : '/webview.php/secretBox/default_page',
				],
				'limited_img_info' => [],
				'effect_list' => isset($page['effect']) ? array_map(function($e) {
					$unit = getUnitDb();
					return [
						'unit_id' => $e,
						'normal_unit_img_asset' => str_replace('_navi_', '_card_', $unit->query('select unit_navi_asset from unit_m left join unit_navi_asset_m on unit_m.normal_unit_navi_asset_id = unit_navi_asset_m.unit_navi_asset_id where unit_id=?', [$e])->fetchColumn()),
						'rankup_unit_img_asset' => str_replace('_navi_', '_card_', $unit->query('select unit_navi_asset from unit_m left join unit_navi_asset_m on unit_m.rank_max_unit_navi_asset_id = unit_navi_asset_m.unit_navi_asset_id where unit_id=?', [$e])->fetchColumn()),
						'type' => 1,
						'start_date' => date('Y-m-d').' 00:00:00',
						'end_date' => date('Y').'-12-31 23:59:59'
					];
				}, $page['effect']) : [],
				'secret_box_list' => []
			];
			$box_base = [
				'name' => '',
				'title_asset' => null,
				'description' => '',
				'start_date' => '2013-06-05 00:00:00',
				'end_date' => '2037-12-31 23:59:59',
				'add_gauge' => 0,
				'multi_type' => 0,
				'multi_count' => 11,
				'is_pay_cost' => true,
				'is_pay_multi_cost' => true,
				'pon_count' => 0,
				'pon_upper_limit' => 0,
				'display_type' => 0,
				'step' => null,
				'term_count' => null,
				'step_up_bonus_asset_path' => null,
				'step_up_bonus_bonus_item_list' => null
			];
			if ($page['page_layout'] == 0) {
				$box_base['title_asset'] = isset($page['title_asset']) ? $page['title_asset'] : 'assets/image/ui/secretbox/se_etc_11.png';
				$box_base['description'] = isset($page['description']) ? $page['description'] : '';
			}
			$userinfo = runAction('user', 'userInfo')['user'];
			$check_cost = function ($cost, $amount) use ($userinfo) {
				global $params;
				if (!$params['card_switch']) {
					//关卡的可以随便抽……
					return true;
				}
				switch($cost) {
					case 'ticket': return $params['item1'] >= $amount;
					case 'social': return $params['social_point'] >= $amount;
					case 'coin': return $params['coin'] >= $amount;
					case 'loveca': return $params['loveca'] >= $amount;
					case 'sticket': return $params['item5'] >= $amount;
				}
				return false;
			};
			$processBox = function ($boxes) use ($box_base, $check_cost, $page, $userinfo, $used_free_gacha_list) {
				global $params;
				foreach($boxes as $k => $box) {
					//关卡的可以随便抽……
					if (!$params['card_switch']) {
						$box['amount'] = 0;
						$box['multi_amount'] = 0;
					}
					pl_assert(isset($box['type']) && ($box['type'] > 0 && $box['type'] < 5), 'secretbox: 无法识别的抽卡类型：'.print_r($box, true));
					pl_assert(isset($box['rule']), 'secretbox: 某项抽卡信息没有指定抽卡规则：'.print_r($box, true));
					$ret_box = $box_base;
					$ret_box['secret_box_id'] = $box['secret_box_id'];
					$ret_box['add_gauge'] = isset($box['add_gauge']) ? $box['add_gauge'] : 0;
					if ($box['type'] == 1 || $box['type'] == 3) {
						pl_assert(isset($box['amount']), 'secretbox: 没有指定消费数目：'.print_r($box, true));
						$item = (($box['type'] == 1) ? 'loveca' : 'social');
						$check = $check_cost($item, $box['amount']);
						if (!$check && $k < count($boxes) - 1) {
							continue;
						}
						$ret_box['is_pay_cost'] = $check;
						if ($box['type'] == 3) {
							pl_assert(isset($box['multi']), 'secretbox: 没有指定连抽数目：'.print_r($box, true));
							$multi = $box['multi'];
						} else {
							$multi = 10;
						}
						$ret_box['multi_type'] = $box['type'] == 1 ? 1 : 0;
						$ret_box['multi_count'] = $multi;
						$ret_box['is_pay_multi_cost'] = $check_cost($item, $box['amount'] * $multi);
						if (!$ret_box['is_pay_multi_cost'] && $box['type'] == 3 && isset($box['allow_lesser_multi']) && $box['allow_lesser_multi']) {
							$count = floor($userinfo['social_point'] / $box['amount']);
							if ($count >= 2) {
								$ret_box['is_pay_multi_cost'] = true;
								$ret_box['multi_count'] = $count;
							} else {
								$ret_box['is_pay_multi_cost'] = false;
								$ret_box['multi_count'] = 2;
							}
						}
						$ret_box['cost'] = [
							"priority" => 1,
							"type" => $box['type'],
							"item_id" => null,
							"amount" => $box['amount'],
							"multi_amount" => $box['amount'] * $ret_box['multi_count']
						];
					} elseif ($box['type'] == 4) {
						if (isset($box['once_per_day']) && $box['once_per_day']) {
							if (!$params['card_switch'] || array_search($box['once_per_day'], $used_free_gacha_list) !== false) {
								continue;
							}
						}
						$ret_box['cost'] = [
							"priority" => 1,
							"type" => 4,
							"item_id" => null,
							"amount" => 0,
							"multi_amount" => 0
						];
					} elseif($box['type'] == 2) {
						if ($page['page_layout'] == 1) {
							if (!$check_cost('ticket', 1)) {
								continue;
							}
							$ret_box['cost'] = [
								"priority" => 1,
								"type" => 2,
								"item_id" => 1,
								"amount" => 1,
								"multi_amount" => 1
							];
						} else {
							$id = ['ticket' => 1, 'sticket' => 5, 'social' => 2, 'coin' => 3, 'loveca' => 4];
							pl_assert(isset($box['item']), 'secretbox: 没有指定消费物品种类：'.print_r($box, true));
							pl_assert(isset($id[$box['item']]), 'secretbox: 消费物品种类无法识别：'.print_r($box, true));
							pl_assert(isset($box['amount']), 'secretbox: 没有指定消费数目：'.print_r($box, true));
							$check = $check_cost($box['item'], $box['amount']);
							if (!$check && $k < count($boxes) - 1) {
								continue;
							}
							$ret_box['is_pay_cost'] = $check;
							$ret_box['cost'] = [
								"priority" => 1,
								"type" => 2,
								"item_id" => $id[$box['item']],
								"amount" => $box['amount'],
								"multi_amount" => $box['amount']
							];
						}
					}
					$ret_box['within_single_limit'] = 1;
					$ret_box['within_multi_limit'] = 1;
					$ret_box['pon_count'] = 0;
					$ret_box['pon_upper_limit'] = 0;
					$ret_box['display_type'] = 0;
					//先让它能进去，后做支持
					$ret_box['all_cost'] = [$ret_box['cost']];
					//unset($ret_box['cost']);
					$ret_box['all_cost'][0]['multi_type'] = $ret_box['multi_type'];
					$ret_box['all_cost'][0]['multi_count'] = $ret_box['multi_count'];
					$ret_box['all_cost'][0]['is_pay_cost'] = $ret_box['is_pay_cost'];
					$ret_box['all_cost'][0]['is_pay_multi_cost'] = $ret_box['is_pay_multi_cost'];
					$ret_box['all_cost'][0]['within_single_limit'] = $ret_box['within_single_limit'];
					$ret_box['all_cost'][0]['within_multi_limit'] = $ret_box['within_multi_limit'];
					return $ret_box;
				}
			};
			$next_page['secret_box_list'][] = $processBox(array_filter($page['box'], function ($e) {
				return $e['type'] <= 2;
			}));
			if ($page['page_layout'] == 1) {
				$next_page['secret_box_list'][] = $processBox(array_filter($page['box'], function ($e) {
					return $e['type'] > 2;
				}));
			}
			$pages[] = $next_page;
		}
		return $pages;
	};
	$ret['member_category_list'] = [
		['member_category' => 1, 'tab_list' => []],
		['member_category' => 2, 'tab_list' => []]
	];
	$id = 1;
	foreach($setting['modes'][0] as $t) {
		$ret['member_category_list'][0]['tab_list'][] = [
			'secret_box_tab_id' => $id++,
			'title_img_asset' => $t[1],
			'title_img_se_asset' => $t[2],
			'page_list' => $processPage($t[0])
		];
	}
	foreach($setting['modes'][1] as $t) {
		$ret['member_category_list'][1]['tab_list'][] = [
			'secret_box_tab_id' => $id++,
			'title_img_asset' => $t[1],
			'title_img_se_asset' => $t[2],
			'page_list' => $processPage($t[0])
		];
	}
	foreach($ret['member_category_list'] as &$i1){
		foreach($i1['tab_list'] as &$i2){
			foreach($i2['page_list'] as &$i3){
				foreach($i3['secret_box_list'] as &$i4){
					switch($i4['all_cost'][0]['type']){
						case 1:
							$i4['all_cost'][0]['type'] = 3001;
							break;
						case 2:
							$i4['all_cost'][0]['type'] = 1000;
							break;
						case 3:
							$i4['all_cost'][0]['type'] = 3002;
							break;
					}
				}
			}
		}
	}
	//var_dump(json_encode($ret));
	//die();
	return $ret;
}

function getBoxById($id, $no_trigger_error = false) {
	$all = secretBox_all();
	$max_gauge_point = $all['gauge_info']['max_gauge_point'];
	$gauge_point = $all['gauge_info']['gauge_point'];
	foreach ($all['member_category_list'] as $category) {
		foreach ($category['tab_list'] as $tab) {
			foreach ($tab['page_list'] as $page) {
				foreach($page['secret_box_list'] as $box) {
					if ($id == $box['secret_box_id']) {
						return [
							'box' => $box,
							'max_gauge_point' => $max_gauge_point,
							'gauge_point' => $gauge_point
						];
					}
				}
			}
		}
	}
	if ($no_trigger_error) {
		return false;
	}
	trigger_error('secretbox: 找不到对应的box：'.$id);
}

function secretBox_multi($post) {
	global $uid, $mysql, $params;
	$ret['is_unit_max'] = false;
	$ret['before_user_info'] = runAction('user', 'userInfo')['user'];
	$ret['secret_box_id'] = $post['secret_box_id'];
	$box = getBoxById($post['secret_box_id']);
	$ret['secret_box_info'] = $box['box'];
	if (!isset($post['pon'])) {
		pl_assert($ret['secret_box_info']['is_pay_multi_cost'], 'secretbox: 您的剩余道具不足！');
		switch ($ret['secret_box_info']['cost']['type']) {
			case 3001: $item = 'loveca'; $amount = $ret['secret_box_info']['cost']['amount'] * 10; $count = 11; break;
			case 3002: $item = 'social_point'; $count = $ret['secret_box_info']['multi_count']; $amount = $ret['secret_box_info']['cost']['amount'] * $count; break;
			default: trigger_error('secretbox: 此卡池不能连续抽卡！');
		}
	} else {
		pl_assert($ret['secret_box_info']['is_pay_cost'], 'secretbox: 您的剩余道具不足！');
		$count = 1;
		switch ($ret['secret_box_info']['cost']['type']) {
			case 3001: $item = 'loveca'; $amount = $ret['secret_box_info']['cost']['amount']; break;
			case 3002: $item = 'social_point'; $amount = $ret['secret_box_info']['cost']['amount']; break;
			case 4: $item = false; break;
			case 1000: {
				if (isset($ret['secret_box_info']['cost']['item_id'])) {
					$item = 'item'.$ret['secret_box_info']['cost']['item_id'];
					$amount = $ret['secret_box_info']['cost']['amount'];
				} else {
					$item = 'item1';
					$amount = 1;
				}
			}
		}
	}
	if ($item) {
		$params[$item] -= $amount;
	}
	for ($i = 1; $i <= 5; $i++) {
		$ret['item_list'][] = [
			"item_id" => $i,
			"amount" => $params['item'.$i]
		];
	}
	$gauge_award = 0;
	$add_gauge = $ret['secret_box_info']['add_gauge'] * $count;
	$gauge = $add_gauge + $box['gauge_point'];
	while ($gauge >= $box['max_gauge_point']) {
		$gauge_award++;
		$gauge -= $box['max_gauge_point'];
	}
	$mysql->query('update secretbox set gauge=? where user_id=?', [$gauge, $uid]);
	$ret['gauge_info'] = [
		'max_gauge_point' => $box['max_gauge_point'],
		'gauge_point' => $gauge,
		'added_gauge_point' => $add_gauge
	];
	$ret['secret_box_items'] = [
		'unit' => [],
		'item' => []
	];
	if ($gauge_award) {
		$max_gauge_award = getSecretboxSetting()['max_gauge_award'];
		$award_item = $max_gauge_award[0];
		switch($award_item) {
			case 'social': $award_item = 'social_point';break;
			case 'coin': $award_item = 'coin';break;
			case 'loveca': $award_item = 'loveca';break;
			case 'ticket': $award_item = 'item1';break;
			case 'sticket': $award_item = 'item5';break;
			default: trigger_error('secretbox: 无法识别特待生奖励类型');
		}
		$amount = $max_gauge_award[1] * $gauge_award;
		$params[$award_item] += $amount;
		$id = ['ticket' => 1, 'sticket' => 5, 'social' => 2, 'coin' => 3, 'loveca' => 4];
		$add = ['ticket' => 1000, 'sticket' => 1000, 'social' => 3002, 'coin' => 3000, 'loveca' => 3001];
		$ret['secret_box_items']['item'] = [[
			'item_id' => $id[$max_gauge_award[0]],
			'item_category_id' => $id[$max_gauge_award[0]],
			'amount' => $amount,
			'add_type' => $add[$max_gauge_award[0]],
			'owning_item_id' => 0,
			'reward_box_flag' => false
		]];
	}
	$ret['next_free_gacha_timestamp'] = strtotime(date('Y-m-d',strtotime('+1 day')));
	$ret['accomplished_achievement_list'] = [];
	$ret['new_achievement_cnt'] = 0;
	$ret['after_user_info'] = runAction('user', 'userInfo')['user'];
	$ret['secret_box_items']['unit'] = array_map(function ($unit) {
		$unit['description'] = '';
		$unit['comment'] = '';
		$unit['unit_rarity_id'] = $unit['rarity'];
		$unit['skill_level'] = 1;
		$unit['reward_box_flag'] = false;
		$unit['new_unit_flag'] = false;
		unset($unit['rarity'], $unit['attribute'], $unit['smile'], $unit['cute'], $unit['cool'], $unit['skill'], $unit['center_skill'], $unit['hp']);
		return $unit;
	}, scout($post['secret_box_id'], $count));
	$box = getBoxById($ret['secret_box_info']['secret_box_id'], true);
	if (isset($post['pon'])) {
		if (!$box) {
			$ret['secret_box_info']['is_pay_cost'] = false;
		} else {
			$ret['secret_box_info']['is_pay_cost'] = $box['box']['is_pay_cost'];
		}
		$ret['secret_box_info']['next_cost'] = $ret['secret_box_info']['cost'];
	} else {
		if (!$box) {
			$ret['secret_box_info']['is_pay_multi_cost'] = false;
		} else {
			$ret['secret_box_info']['multi_count'] = $box['box']['multi_count'];
			$ret['secret_box_info']['is_pay_multi_cost'] = $box['box']['is_pay_multi_cost'];
		}
	}
	if (!$params['card_switch']) {
		//关卡的可以随便抽……但是不保存……
		rollback();
	}
	return $ret;
}

function secretBox_pon($post) {
	$post['pon'] = true;
	$ret = secretBox_multi($post);
	return $ret;
}