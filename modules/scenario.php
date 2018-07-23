<?php
//scenario.php 故事module

//scenario/scenarioStatus 获取故事列表 **有待进一步研究**
function scenario_scenarioStatus() {
  $ss = getScenarioDb();
  if (!$ss) {
    return ["scenario_status_list" => []];
  }
  $slist = $ss->query('SELECT scenario_id FROM scenario_m')->fetchAll(PDO::FETCH_COLUMN);
  $list = [];
  foreach ($slist as $i) {
    $list[] = [
      'scenario_id' => (int)$i,
      'status' => 2
    ];
  }
  
  return ["scenario_status_list" => $list];
}

?>