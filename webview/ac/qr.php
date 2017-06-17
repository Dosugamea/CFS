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
	.user_icon_bg{position: absolute;width: 120px;height: 120px;margin-top: -18px;margin-left: -30px;}
	.user_icon_f{position: absolute;width: 120px;height: 120px;margin-top: -18px;margin-left: -30px;}
	.user_rank{position:absolute;color: #FF679A;font-size: 25px;font-weight: 800;text-align: center;    width: 100px;height: 40px;font-family: 'microsoft yahei';margin-top: 20px;margin-left: 120px; }
	.user_award{position: absolute;width: 100px;height: 70px;margin-left: 230px;margin-top: 3px;}
	.user_info{position: absolute;font-family: 'microsoft yahei';margin-left: 380px;margin-top: 3px;}
		.info_rk{color: #FF679A;font-weight: 500;font-size:25px; margin-right: 10px;}
		.info_rank{color: #000000;font-weight: 800;font-size: 30px;margin-right: 30px;}
		.info_name{color: #000000;font-weight: 400;font-size:23px;}
	.user_score{position: absolute;background-color: #FF679A;color: #ffffff;border-radius: 30px;height: 30px;width: 440px;margin-left: 350px;margin-top: 45px;font-family: 'microsoft yahei'}
		.pt_title{font-size: 24px;font-weight: 500;margin-left: 10px;margin-right: 30px}
		.pt_s{font-size: 25px;font-weight: 600;display:inline-block;width:125px}
		.pt_r{font-size: 24px;font-weight: 500;display:inline-block;width:40px;margin-right:15px;}
		.pt_p{font-size: 24px;font-weight: 500;display:inline-block;}
		img{width:95%;height:auto;}
		.avatar{width:95%;height:auto;}
	</style>
	<?php 
		
	?>
</head>
<body>
	  <?php
		$assets='"Live_s0732.json"';
		//$assets='"Live_s0743.json","custom_chrono_diver_ex","Live_s0732.json","Live_s0526.json","Live_s0609.json"';//歌曲列表填在这里
		//告白日和-MA Chrono-MA ? ? ?

		require("includes/live.php");
		if(!isset($assets)||empty($assets))
			die();
		$assets_list=explode(',',$assets);
		$count=count($assets_list); 
		$score_max=0;
		foreach($assets_list as $asset){
			$notes=$mysql->query("SELECT notes_list FROM notes_setting WHERE notes_setting_asset=$asset")->fetch(PDO::FETCH_ASSOC)['notes_list'];
			//print_r($notes);
			$score_max+=calcScore(60500,json_decode($notes,true));
			echo $score_max;
		}

		//获取有参与的用户并初始化用户资料
		$infos=$mysql->query("SELECT user_id,hi_score,mx_perfect_cnt from live_ranking WHERE notes_setting_asset IN ($assets) AND card_switch=0 AND random_switch=0")->fetchAll();
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

			if(!isset($users[$id]['perfect']))
			  $users[$id]['perfect']=0;
		  $users[$id]['perfect']+=(int)$info['mx_perfect_cnt'];
		}

		//以分数进行排序
		$scores=[];
		foreach($users as $user)
		  $scores[]=$user['score'];
    array_multisort($scores,SORT_DESC,$users);

		//输出
		foreach($users as $p => $user){
			if($user['count']!=$count)
				continue;
			$id=$user['id'];
			$user_info=$mysql->query("SELECT name,level,award FROM users WHERE user_id=$id")->fetch(PDO::FETCH_ASSOC);

			$user_avatar=(int)$mysql->query("SELECT value FROM user_params WHERE param='extend_avatar' AND user_id=$id")->fetch(PDO::FETCH_ASSOC)['value'];
			if($user_avatar==0)
				$user_avatar=49;
			$user_avatar_rankup=(int)$mysql->query("SELECT value FROM user_params WHERE param='extend_avatar_is_rankup' AND user_id=$id")->fetch(PDO::FETCH_ASSOC)['value'];
			$unit=getUnitDb();
			$card_info=$unit->query("SELECT normal_icon_asset,rank_max_icon_asset,rarity,attribute_id FROM unit_m WHERE unit_id=$user_avatar")->fetch(PDO::FETCH_ASSOC);
			$user_avatar=$card_info[$user_avatar_rankup==0?'normal_icon_asset':'rank_max_icon_asset'];
			$rarity=['','N','R','SR','UR','SSR'];
			$attribute=[1=>'smile',2=>'pure',3=>'cool',9=>'all'];
			$user_avatar_bg=$attribute[(int)$card_info['attribute_id']]."_".$rarity[(int)$card_info['rarity']]."_00".($user_avatar_rankup+1);
			$user_avatar_f=$rarity[(int)$card_info['rarity']]."_".$card_info['attribute_id'];
			
			$award=(int)$user_info['award'];
			$award=($award>=100||(42<=$award&&$award<=46))?$award:($award<10?"00".$award:"0".$award);

			$position=$p+1;
			$name=$user_info['name'];
			$level=$user_info['level'];
			$score=$user['score'];
			$rate=number_format($score/$score_max*100, 1, '.', '')."%";
			$perfect=$user['perfect']>0?$user['perfect'].'P':'';
			echo 
	"<div class='user_frame'>
	  <div class='user_icon_bg'><img src='https://card.lovelivesupport.com/asset/assets/image/cards/icon/b_$user_avatar_bg.png' class='avatar'></div>
		<div class='user_icon'><img src='https://card.lovelivesupport.com/asset/$user_avatar' class='avatar'></div>
		<div class='user_icon_f'><img src='https://card.lovelivesupport.com/asset/assets/image/cards/icon/f_$user_avatar_f.png' class='avatar'></div>
		<div class='user_rank'>第 $position 位</div>
		<div class='user_award'><img src='https://card.lovelivesupport.com/asset/assets/image/award/award_$award.png'></div>
		<div class='user_info'>
			<span class='info_rk'>Rank.</span>
			<span class='info_rank'>$level</span>
			<span class='info_name'>$name</span>
		</div>
		<div class='user_score'>
			<span class='pt_title'>SCORE</span>
			<span class='pt_s'>$score</span>
			<span class='pt_r'>$rate</span>
			<span class='pt_p'>$perfect</span>
		</div>
	</div>";
		}
		?></pre>
</body>
</html>