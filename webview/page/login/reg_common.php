<?php
require_once dirname(__FILE__).'/../../includes/present.php';

if(!$config->reg['allow_reg']) {
	echo '<h1>注册已关闭！</h1>';
	die();
}

if($config->reg['enable_ssl'] && $_SERVER['HTTPS'] != 'on') {
	header('Location: https://'.$ssl_domain.$_SERVER['REQUEST_URI']);
	exit();
}

$unit = getUnitDb();


if (!$tmp_authorize) {
	echo '<h1>出现了错误，请关闭此页面重新进入</h1>';
	die();
}



function genpassv2($_pass, $id) {
	$_pass .= $id;
	$pass = hash('sha512', $_pass);
	$pass .= hash('sha512', str_replace($_pass[0], 'RubyRubyRu', $_pass));
	$pass .= $pass;
	return substr($pass, hexdec(substr(md5($_pass), ord($_pass[0]) % 30, 2)), 32);
}

include_once(dirname(__FILE__)."/../../includes/unit.php");
if(isset($_POST['submit'])) {
	if (!is_numeric($_POST['id'])) {
		echo '<h3><font color="red">错误：ID必须是数字 Error: the ID must be a number</font></h3>';
	} elseif($_POST['id']>999999999) {
		echo '<h3><font color="red">错误：你输入的数太大了！Number is too large</font></h3>';
	} else if(!is_numeric($_POST['site'])){
		echo '<h3><font color="red">错误：提交数据异常</font></h3>';
	} else	{
		$check_uid = $mysql->prepare('SELECT user_id FROM users WHERE user_id=?');
		$check_uid->execute([$_POST['id']]);
		if ($check_uid->rowCount()) {
			echo '<h3><font color="red">错误：此ID已被注册 </font></h3>';
		} else {
			$password = genpassv2($_POST['password'], $_POST['id']);	
			$mysql->prepare('
				INSERT INTO `users` (`user_id`, `username`, `password`,`login_password`, `name`, `introduction`, `download_site`)
				VALUES (?, ?, ?, ?, ?, "", ?)
			')->execute([$_POST['id'], $tmp_authorize['username'], $tmp_authorize['password'], $password, $_POST['name'], $_POST['site']]);
			$param = $mysql->prepare('INSERT INTO user_params VALUES('.$_POST['id'].', ?, ?)');
			$param->execute(['enable_card_switch', $config->reg['disable_card_by_default'] ? 0 : 1]);
			$param->execute(['card_switch', $config->reg['disable_card_by_default'] ? 0 : 1]);
			$param->execute(['random_switch', 0]);
			$param->execute(['allow_test_func', 0]);
			$param->execute(['item1', 0]);
			$param->execute(['item2', 0]);
			$param->execute(['item3', 2525200]);
			$param->execute(['item4', 0]);
			$param->execute(['item5', 0]);
		
			//送三个初期宝石
			$mysql->query("INSERT INTO removable_skill (user_id, skill_id, amount, equipped) VALUES(".$_POST['id'].",1,1,0)");
			$mysql->query("INSERT INTO removable_skill (user_id, skill_id, amount, equipped) VALUES(".$_POST['id'].",2,1,0)");
			$mysql->query("INSERT INTO removable_skill (user_id, skill_id, amount, equipped) VALUES(".$_POST['id'].",3,1,0)");
			
			$uid = $_POST['id'];
			if($config->reg['all_card_by_default']) {
				$card_list=$unit->query('SELECT unit_id from unit_m where unit_id <= ? and unit_number > 0', [$config->basic['max_unit_id']])->fetchAll();
				//$query='INSERT INTO `unit_list` (`user_id`, `unit_id`) VALUES ';
				foreach($card_list as $v){
					addUnit($v[0]);
				}
				//防止劝退
				$mysql->query("UPDATE unit_list SET favorite_flag = 1 WHERE user_id = ?", [$_POST['id']]);
			//$query.='('.$_POST['id'].', '.$v[0].'),';
				//$query=substr($query, 0,strlen($query)-1);
				//$mysql->exec($query);
			}
			
			$position=1;
			foreach($config->reg['default_deck_web'] as $k=>$v) {
				$tmp['position'] = $position;
				$tmp['unit_owning_user_id'] = addUnit($v)[0]['unit_owning_user_id'];
				if($position == 5)
					$center = $tmp['unit_owning_user_id'];
				$unit_deck_detail[] = $tmp;
				$position++;
			}
			
			//$mysql->exec("INSERT INTO album (user_id,unit_id) SELECT DISTINCT {$_POST['id']}, unit_id FROM unit_list WHERE user_id = {$_POST['id']}");
			//修正特典卡的rank
			//$default_rankup = $unit->query('select unit_id from unit_m where unit_m.normal_icon_asset like "%rankup%"')->fetchAll(PDO::FETCH_COLUMN);
			//$mysql->exec('UPDATE unit_list SET rank=2 WHERE user_id='.$_POST['id'].' AND unit_id in('.implode(', ', $default_rankup).')');
			//$mysql->exec('UPDATE album SET rank_max_flag=1 WHERE user_id='.$_POST['id'].' AND unit_id in('.implode(', ', $default_rankup).')');
			
			$tmp2['unit_deck_detail']=$unit_deck_detail;
			$tmp2['unit_deck_id']=1;
			$tmp2['main_flag']=true;
			$tmp2['deck_name']='';
			$unit_deck_list[]=$tmp2;
			$json=json_encode($unit_deck_list);
			$mysql->exec("INSERT INTO user_deck (user_id,json,center_unit) VALUES ({$_POST['id']}, '$json', $center)");
			
			$mysql->query('delete from tmp_authorize where token=?', [$token]);
			$invite = (int)$_POST['invite'];
			if($invite > 0){
				$res = $mysql->query("SELECT user_id FROM users WHERE user_id = ?",[$invite])->fetch();
				if($res){
					$mysql->query("INSERT INTO invitation (user_id, from_user) VALUES(?,?)", [$uid, $invite]);
					add_present("loveca", 10, "安利新人奖励", $uid = $invite);
				}
			}
			echo '<h3>注册成功！请直接进入游戏。</h3>';
			die();
		}
	}
	
}