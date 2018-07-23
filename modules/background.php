<?php
function background_backgroundInfo() {
  global $envi;
  if ($envi->user['background'] == 0) {
    $envi->user['background'] = 1;
  }
  $ret['background_info'] = [];
  for($i = 1; $i <= 24; $i++) {
    $ret['background_info'][]=[
      'background_id'=>$i,
      'is_set'=>$i==$envi->user['background'],
      "insert_date"=>"2013-04-15 00:00:00"
    ];
  }
  for($i = 901; $i <= 902; $i++) {
    $ret['background_info'][]=[
      'background_id'=>$i,
      'is_set'=>$i==$envi->user['background'],
      "insert_date"=>"2013-04-15 00:00:00"
    ];
  }
  return $ret;
}

function background_set($post) {
  global $envi;
  $envi->user['background'] = $post['background_id']; //TODO
  return [];
}
