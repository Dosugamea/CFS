<?php 
function addExchangePoint($arr) {
  global $params;
  foreach(['seal1', 'seal2', 'seal3', 'seal4'] as $v) {
    if (!isset($params[$v])) {
      $params[$v] = 0;
    }
  }
  if ($arr !== false) {
    $before_seal = $params;
    foreach ($arr as $rarity) {
      switch ($rarity) {
        case 2: $params['seal1']++;break;
        case 3: $params['seal2']++;break;
        case 4: $params['seal4']++;break;
        case 5: $params['seal3']++;break;
      }
    }
    $ret = [];
    foreach([2 => 'seal1', 3 => 'seal2', 4 => 'seal4', 5 => 'seal3'] as $k => $v) {
      if ($params[$v] - $before_seal[$v] > 0) {
        $ret[] = ['rarity' => $k, 'exchange_point' => $seal1[$v] - $before_seal[$v]];
      }
    }
    return $ret;
  }
  return [
    ['rarity' => 2, 'exchange_point' => $params['seal1']],
    ['rarity' => 3, 'exchange_point' => $params['seal2']],
    ['rarity' => 4, 'exchange_point' => $params['seal4']],
    ['rarity' => 5, 'exchange_point' => $params['seal3']]
  ];
}