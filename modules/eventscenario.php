<?php
//eventscenario.php 活动故事module

//eventscenario/status 获取活动故事列表 **没有抓包**
function eventscenario_status() {
  $current_id = 1;
  foreach ([24,26,28,30,32,34,36,38,40] as $event_id) {
    $chapters = [];
    for($chapter = 1; $chapter < 6; $chapter++) {
      $chapters[] = [
        "event_scenario_id" => $current_id++,
        "chapter" => $chapter,
        "chapter_asset" => "assets/image/ui/eventscenario/{$event_id}_se_ic_{$chapter}.png",
        "status" => 2,
        "is_reward" => false
      ];
    }
    $ret[] = [
      "event_id" => $event_id,
      "event_scenario_btn_asset" => "assets/image/ui/eventscenario/{$event_id}_se_ba_t.png",
      "event_scenario_se_btn_asset" => "assets/image/ui/eventscenario/{$event_id}_se_ba_tse.png",
      "open_date" => null,
      "chapter_list" => $chapters
    ];
  }
  return ['event_scenario_list' => array_reverse($ret)];
}

function eventscenario_startup($post) {
  return json_decode('{"event_scenario_list":{"event_id":40,"progress":5,"status":2,"event_scenario_id":'.$post['event_scenario_id'].'},"scenario_adjustment":50}');
}

?>