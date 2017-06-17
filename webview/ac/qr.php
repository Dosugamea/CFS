<!DOCTYPE html>
<html>
  <head>
	<meta charset="utf-8">
	<meta name="GENERATOR" content="MSHTML 11.00.10011.0">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="viewport" content="width=880px, target-densitydpi=device-dpi, user-scalable=no">
	<style>
	body{min-width: 870px;width: 870px;background-color: #ffffff;border:4px solid #FF679A;border-radius: 15px;position: absolute;top: 0px;left: 0px;margin: 0;}
	.user_frame{width: 800px;height: 80px; margin: 0 auto;margin-top:30px;margin-bottom:10px;background-color: #ffffff;border:3px solid #FF679A;border-radius: 20px;box-shadow: 4px 4px 4px #cccccc;}
	.user_icon{position: absolute;width: 120px;height: 120px;margin-top: -18px;margin-left: -30px;}
	.user_rank{position:absolute;color: #FF679A;font-size: 25px;font-weight: 800;text-align: center;    width: 100px;height: 40px;font-family: 'microsoft yahei';margin-top: 20px;margin-left: 120px; }
	.user_award{position: absolute;width: 100px;height: 70px;margin-left: 230px;margin-top: 3px;}
	.user_info{position: absolute;font-family: 'microsoft yahei';margin-left: 380px;margin-top: 3px;}
		.info_rk{color: #FF679A;font-weight: 500;font-size:25px; margin-right: 10px;}
		.info_rank{color: #000000;font-weight: 800;font-size: 30px;margin-right: 30px;}
		.info_name{color: #000000;font-weight: 400;font-size:23px;}
	.user_score{position: absolute;background-color: #FF679A;color: #ffffff;border-radius: 30px;height: 30px;width: 440px;margin-left: 350px;margin-top: 45px;font-family: 'microsoft yahei'}
		.pt_title{font-size: 24px;font-weight: 500;margin-left: 10px;margin-right: 50px}
		.pt_s{font-size: 25px;font-weight: 600}
	pre{
		font-size:30px;
		text-align: center;
	}
	</style>
	<?php 
		$assets_base_url ='https://card.lovelivesupport.com';
	?>
</head>
<body>
	<div class="user_frame">
		<div class="user_icon"><img src="<?=$assets_base_url?>/asset/assets/image/units/u_rankup_icon_41105001.png" width="100%" height="anto"></div>
		<div class="user_rank">第 1 位</div>
		<div class="user_award"><img src="<?=$assets_base_url?>/asset/assets/image/award/award_42.png" width="100%" height="anto"></div>
		<div class="user_info">
			<span class="info_rk">Rank.</span>
			<span class="info_rank">89</span>
			<span class="info_name">NOAHLUALU</span>
		</div>
		<div class="user_score">
			<span class="pt_title">SCORE</span>
			<span class="pt_s">691191</span>
		</div>
	</div>
	

	  <!--<?php
		$assets='"Live_s0364.json","Live_s0391.json","Live_s0402.json","Live_s0526.json","Live_s0609.json"';//歌曲列表填在这里
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
		?></pre>-->
</body>
</html>