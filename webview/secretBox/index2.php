<head>
  <meta charset="utf-8">
  <meta name="GENERATOR" content="MSHTML 11.00.10011.0">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="viewport" content="width=880, target-densitydpi=device-dpi, user-scalable=no">

  <link rel="stylesheet" href="/resources/things/detail.css?">
  <link rel="stylesheet" href="/resources/things/perfect-scrollbar.css">
  <link rel="stylesheet" href="/resources/things/list2.css">

  <script src="/resources/things/perfect-scrollbar.min.js"></script>
  <script src="/resources/things/button.js"></script>

</head>
<body>
  <div id="outer">
  <div id="inner">
    <div id="header">
      <h2>招募详情</h2>
    </div>

<div id="body">
<div id="container">
<ul id="list">
  <li class="entry"">
    <div class="entry-container">
      <h2 class="text"></h2>
      <div class="summary">
        <!--<del>此功能尚未完成，需要亲亲抱抱么么才能继续淦</del></br>-->
        <pre><?php
        require 'includes/errorUtil.php'; 
        if(!isset($_SERVER["QUERY_STRING"])||empty($_SERVER["QUERY_STRING"])){
          header('HTTP/1.1 403 Forbidden');
          echo '<h1>出现了一些问题，请尝试关闭页面重新打开 (Query String不存在)</h1>';
          die();
        }
        
        require 'modules/secretbox.php';
        function get_text($rule,$index,$total){
          if(empty($rule)||$rule<=0)
            return "";
          switch($index){
            case 0:$pre="N&nbsp;&nbsp;&nbsp;";break;
            case 1:$pre="R&nbsp;&nbsp;&nbsp;";break;
            case 2:$pre=     "SR&nbsp;&nbsp;";break;
            case 3:$pre=          "SSR&nbsp;";break;
            case 4:$pre=     "UR&nbsp;&nbsp;";break;
          }
          return $pre.sprintf("%.1f", ($rule/$total)*100)."%\n";
        }
        
        $id_list=explode('&', $_SERVER["QUERY_STRING"]);
        $last_index=count($id_list)-1;
        global $params;
        $params['card_switch']=($id_list[$last_index]=="true");
        
        for($i=0;$i<$last_index;$i++){
          $id=$id_list[$i];
          $box=getBoxObjectById($id);
          //echo "Box ".$id."\n";
          if(isset($box['rule'])&&!empty($box['rule'])){
            $total_chance = array_reduce($box['rule'], function ($sum, $next) {
              return $sum + $next;
              }, 0);
            $counter=0;
            foreach($box['rule'] as $r)
              echo get_text($r,$counter++,$total_chance);
          }
          break;//仅输出第一个Box
        }
        ?>
        </pre>
      </div>
      <div class="clearfix"></div>
    </div>
  </li>
</ul>

</div>
 </div>
  </div>
</div>
</div>
  </div>
</div>

<script>
  Ps.initialize(document.getElementById('body'), {suppressScrollX: true});
</script>
</body>
</body>