<?php
function award_awardInfo() {
  global $user;
  if ($user['award'] == 0) {
    $user['award'] = 1;
  }
  $ret['award_info'] = [];
  for($i = 1; $i <= 29; $i++) {
    $ret['award_info'][]=[
      'award_id'=>$i,
      'is_set'=>$i==$user['award'],
      "insert_date"=>"2013-04-15 00:00:00"
    ];
  }
  for($i = 901; $i <= 902; $i++) {
    $ret['award_info'][]=[
      'award_id'=>$i,
      'is_set'=>$i==$user['award'],
      "insert_date"=>"2013-04-15 00:00:00"
    ];
  }
  return $ret;
}

function award_set($post) {
  global $user;
  $user['award'] = $post['award_id'];
  return [];
}
