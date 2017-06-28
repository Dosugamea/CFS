<title>>_<</title>
<?php 
  if(!isset($_POST['maps']) || ($_POST['maps'] == NULL)){
    print("请求有误 <a href='javascript:history.go(-1);'>返回上一页</a>");
    die();
  }
function calcScore($base, $map) {
  $total = 0;
  $combo = 0;
  $rate = 1;
  $total += array_reduce($map, function ($sum, $next) use (&$combo, $base, &$rate) {
    $combo++;
    switch($combo) {
    case 51:$rate = 1.1;break;
    case 101:$rate = 1.15;break;
    case 201:$rate = 1.2;break;
    case 401:$rate = 1.25;break;
    case 601:$rate = 1.3;break;
    case 801:$rate = 1.35;break;
    }
    $score = $base * 1.25 * $rate;
    if ($next['effect'] == 3) {
      $score *= 1.25;
    }
    return $sum + floor($score / 100);
  }, 0);
  return '[{"rank":5,"rank_min":0},{"rank":4,"rank_min":'.floor($total*0.7).'},{"rank":3,"rank_min":'.floor($total*0.8).'},{"rank":2,"rank_min":'.floor($total*0.9).'},{"rank":1,"rank_min":'.floor($total*0.975).'}]';
}
$map = $_POST['maps'];
print("计算结果:<br>");
print(calcScore(60500, json_decode($map, true)));
print("<br><br>计算成功 <a href='javascript:history.go(-1);'>返回上一页</a>");

?>