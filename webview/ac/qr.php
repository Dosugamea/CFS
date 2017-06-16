<!DOCTYPE html>
<html>
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

	<style>
	body{
		background-color: white
	}
	pre{
		font-size:90px;
		text-align: center;
	}
	</style>
  </head>
  <body>
	  <pre><?php
		//$assets='"Live_s0364.json","Live_s0391.json","Live_s0402.json","Live_s0526.json","Live_s0609.json"';//歌曲列表填在这里
		if(!isset($assets)||empty($assets))
			die();
		$count=count(explode(',',$assets)); 
		$infos=$mysql->query("SELECT user_id,hi_score from live_ranking WHERE notes_setting_asset IN ($assets) AND card_switch=0 AND random_switch=0")->fetchAll();

		$users=[];
		foreach($infos as $info){
			$id=(int)$info['user_id'];
			$users[$id]['id']=$id;

			if(!isset($users[$id]['count']))
			  $users[$id]['count']=0;
		  $users[$id]['count']++;

			if(!isset($users[$id]['score']))
			  $users[$id]['score']=0;
		  $users[$id]['score']+=(int)$info['hi_score'];
		}

		$scores=[];
		foreach($users as $user)
		  $scores[]=$user['score'];
    array_multisort($scores,SORT_DESC,$users);

		foreach($users as $p => $user){
			if($user['count']!=$count)
				continue;
			$user_info=$mysql->query("SELECT name FROM users WHERE user_id=".$user['id'])->fetch(PDO::FETCH_ASSOC);
			echo ($p+1)." - ".$user_info['name']." ".$user['score']."\n";
		}
		?></pre>
  </body>
</html>