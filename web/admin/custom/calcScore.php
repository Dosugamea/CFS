<title>>_<</title>
<?php 
  if(!isset($_POST['maps']) || ($_POST['maps'] == NULL)){
    print("请求有误 <a href='javascript:history.go(-1);'>返回上一页</a>");
    die();
  }

include('../../../includes/live.php');
$map = $_POST['maps'];
print("计算结果:<br>");
$total=calcScore(60500, json_decode($map, true));
$out='[{"rank":5,"rank_min":0},{"rank":4,"rank_min":'.floor($total*0.7).'},{"rank":3,"rank_min":'.floor($total*0.8).'},{"rank":2,"rank_min":'.floor($total*0.9).'},{"rank":1,"rank_min":'.floor($total*0.975).'}]';
print($out);
print("<br><br>计算成功 <a href='javascript:history.go(-1);'>返回上一页</a>");

?>