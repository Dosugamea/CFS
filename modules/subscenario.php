<?php
//subscenario.php 支线故事module

//subscenario/subscenarioStatus 获取支线故事列表
function subscenario_subscenarioStatus() {
  global $max_unit_id, $release_info;
  $release_info_ids = array_map(function ($e) {
    return $e['id'];
  }, $release_info);
  $ss = getSubscenarioDb();
  if (!$ss) {
    return ["subscenario_status_list" => []];
  }
  $unit_list = $ss->query('SELECT subscenario_id FROM subscenario_m where (_encryption_release_id is null or _encryption_release_id in ('.implode(',', $release_info_ids).')) and unit_id<'.$max_unit_id)->fetchAll(PDO::FETCH_COLUMN);
  $ret = [];
  foreach ($unit_list as $v) {
    $ret[] = [
      'subscenario_id' => (int)$v,
      'status' => 2
    ];
  }
  return ['subscenario_status_list'=>$ret];
}

?>
