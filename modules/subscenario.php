<?php
//subscenario.php 支线故事module

//subscenario/subscenarioStatus 获取支线故事列表
function subscenario_subscenarioStatus() {
  global $config;
  $ss = getSubscenarioDb();
  if (!$ss) {
    return ["subscenario_status_list" => []];
  }
  $unit_list = $ss->query('SELECT subscenario_id FROM subscenario_m WHERE _encryption_release_id IS null AND unit_id < ?', [$config->basic['max_unit_id']])->fetchAll();
  $ret = [];
  foreach ($unit_list as $v) {
    $ret[] = [
      'subscenario_id' => (int)$v,
      'status' => 2
    ];
  }
  return ['subscenario_status_list' => $ret];
}

?>
