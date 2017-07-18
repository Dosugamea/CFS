<?php
function generateRandomLiveSevenKey($note) {
	$decoded=mapSort($note);
	$max_combo=count($decoded);
	for($i=2;$i<=8;$i++){
		$latest[$i]=-0.2;
		$toput[$i]=0;
	}
	$change=0;
	//latest用来存储每个键位最后一个note的结束时间
	//toput用来存储这个note是否可以放在这个位置上
	//change用来交换两个note（需要交换的情况在下面会看到）
	for($note=0;$note<$max_combo;$note++){
		$start[$note]=$decoded[$note]["timing_sec"];
		$slide_group[$decoded[$note]["notes_level"]][0]=0;
	}
	for($note=0;$note<$max_combo;$note++){
		if($note<$max_combo-1&&$start[$note+1]==$start[$note]&&($decoded[$note]["effect"]<10||$slide_group[$decoded[$note]["notes_level"]][0]==0)&&$decoded[$note+1]["effect"]>10||$change==1){
			$change++;
			if($change==1)$note++;
		}
		//滑点双押优先处理滑键
		$end[$note]=$decoded[$note]["timing_sec"];
		if($decoded[$note]["effect"]%10==3){
			$end[$note]+=$decoded[$note]["effect_value"];
			$longnote=1;
		}
		else $longnote=0;
		$last=0;
		$singlelast=0;
		if($note<$max_combo-1&&$start[$note+1]==$start[$note])$equalnext=1;
		else $equalnext=0;
		//判断是否为长条，双键的前半
		for($i=2;$i<=8;$i++){
			if($latest[$i]<$start[$note]-0.2)$toput[$i]=1;//当且仅当这个这个键位前面0.2s内没有note存在时这个键位可以放这个note
			else if($latest[$i]>=$start[$note]){
				$singlelast=10;
				$last=$i;
			}
			//判断是否另一手有长条或为双键后一半，锁定singlelast（唯一最后一键）为10
			else if($singlelast<10){
				$singlelast++;
				if($singlelast==1)$last=$i;
				else{
					if($latest[$i]>$latest[$last])$last=$i;//如果没有检测到它是长条或双键后一半，找到前面离它最近的note，并令singlelast不为0
					else if($latest[$i]==$latest[$last])$singlelast=0;//但是如果这样的note至少有两个，那么令singlelast为0
				}
			}
		}
		//此时有：一个note为双键或长条或另一手为长条等价于singlelast=10或longnote=1或equalnext=1
		//一个note不能放5等价于singlelast>=1或longnote=1或equalnext=1
		if($change==1)$equalnext=1;//处理滑单交换的特殊情形
		if($decoded[$note]["effect"]>10){
			$group=$decoded[$note]["notes_level"];
			$slide_group[$group][0]++;
			$num=$slide_group[$group][0];
			$slide_group[$group][$num]=$note;//滑键的分组
			$last1=$slide_group[$group][$num-1];
			if($num>1){
				if($decoded[$last1]["position"]==2)$decoded[$note]["position"]=3;
				else if($decoded[$last1]["position"]==8)$decoded[$note]["position"]=7;
				else if($decoded[$last1]["position"]==4&&($singlelast==10||$equalnext==1||$longnote==1||$num==2))$decoded[$note]["position"]=3;//干掉_45 _65
				else if($decoded[$last1]["position"]==6&&($singlelast==10||$equalnext==1||$longnote==1||$num==2))$decoded[$note]["position"]=7;//双手原则优先
				else if($num==2)$decoded[$note]["position"]=2*(rand(0,1))-1+$decoded[$last1]["position"];//第二个note随机取滑向
				else{
					$last2=$slide_group[$group][$num-2];
					if($num==3)$decoded[$note]["position"]=2*$decoded[$last1]["position"]-$decoded[$last2]["position"];//第三个note尽可能保滑向
					else if($decoded[$last1]["position"]==4&&$decoded[$last2]["position"]==5)$decoded[$note]["position"]=3;
					else if($decoded[$last1]["position"]==6&&$decoded[$last2]["position"]==5)$decoded[$note]["position"]=7;//干掉565 545
					else{
						$last3=$slide_group[$group][$num-3];
						if($decoded[$last1]["position"]==$decoded[$last3]["position"])$decoded[$note]["position"]=2*$decoded[$last1]["position"]-$decoded[$last2]["position"];//连续两个note不同时转滑向
						else{
							$i=rand(0,99);
							if($i<21)$decoded[$note]["position"]=$decoded[$last2]["position"];
							else $decoded[$note]["position"]=2*$decoded[$last1]["position"]-$decoded[$last2]["position"];//以上情况都不满足的话，79%概率保划向
						}
					}
				}
			}
			else{
				if($toput[3]+$toput[4]+$toput[6]+$toput[7]+$toput[5]+$toput[2]+$toput[8]==0){
					for($i=2;$i<=8;$i++){
						if($latest[$i]<$start[$note])$toput[$i]=1;
					}
				}
				if($equalnext==1||$longnote==1)$toput[5]=0;
				for($i=2;$i<=8;$i++){
					$toput1[$i]=$toput[$i];
				}
				if($singlelast>=1){
					$toput[5]=0;
					if($last<=4)$toput[3]=$toput[4]=$toput[2]=0;
					if($last>=6)$toput[6]=$toput[7]=$toput[8]=0;
				}
				if($toput[3]+$toput[4]+$toput[6]+$toput[7]+$toput[5]+$toput[2]+$toput[8]==0){
					for($i=2;$i<=8;$i++){
						$toput[$i]=$toput1[$i];
					}
				}
				for($j=0;$j==0;){
					$i=rand(2,8);
					if($toput[$i]==1){
						$j++;
						$decoded[$note]["position"]=$i;
					}
				}
			}
		}
		else{
			if($toput[3]+$toput[4]+$toput[6]+$toput[7]+$toput[5]+$toput[2]+$toput[8]==0){
				for($i=2;$i<=8;$i++){
					if($latest[$i]<$start[$note])$toput[$i]=1;
				}
			}
			if($equalnext==1||$longnote==1)$toput[5]=0;
			for($i=2;$i<=8;$i++){
				$toput1[$i]=$toput[$i];
			}
			if($singlelast>=1){
				$toput[5]=0;
				if($last<=4)$toput[3]=$toput[4]=$toput[2]=0;
				if($last>=6)$toput[6]=$toput[7]=$toput[8]=0;
			}
			if($toput[3]+$toput[4]+$toput[6]+$toput[7]+$toput[5]+$toput[2]+$toput[8]==0){
				for($i=2;$i<=8;$i++){
					$toput[$i]=$toput1[$i];
				}
			}
			for($j=0;$j==0;){
				$i=rand(2,8);
				if($toput[$i]==1){
					$j++;
					$decoded[$note]["position"]=$i;
				}
			}
		}
		for($i=2;$i<=8;$i++){
			$toput[$i]=0;
		}
		$latest[$decoded[$note]["position"]]=$end[$note];
		if($change==1)$note-=2;
		if($change==2){
			$change=0;
			$note++;
		}//滑单交换
	}
	$live_notes = $decoded;
	return $decoded;
}


function generateRandomLiveOldSevenKey($note) {
	$timing=[];
	foreach($note as $v)
		$timing[]=$v['timing_sec'];
	array_multisort($timing,SORT_ASC,$note);
	$holding=false;
	$holdend=0;
	$lasttime=0;
	$holdingg=[0,-0.1,-0.1,-0.1,-0.1,-0.1,-0.1,-0.1,-0.1,-0.1];
	foreach($note as $k=>&$v) {
		if($v['position']<2)$v['position']+=2;
		else if($v['position']>8)$v['position']-=2;
		if($v['timing_sec']<$holdingg[$v['position']]+0.01){
			if($v['position']<5)$v['position']+=2;
			else $v['position']-=2;
		}
		if($v['timing_sec']>$holdend+0.1)
			$holding=false;
		if(!$holding && $v['effect']%10==3) {
			//长条，什么都不做
			$holdend=$v['timing_sec']+$v['effect_value'];
			$holding=true;
		}
		elseif($holding) {
			//长按中，什么都不做
			if($v['effect']%10==3) {
				$holdend=max($holdend,$v['timing_sec']+$v['effect_value']);
			}
		}
		elseif($v['timing_sec']==$lasttime || (isset($note[$k+1]['timing_sec']) && $v['timing_sec']==$note[$k+1]['timing_sec'])) {
			//双押，什么都不做
		}
		elseif($v['effect'] >= 10) {
			//滑键，什么都不做
		}
		else $v['position']=rand(2,8); //单点
		$lasttime=$v['timing_sec'];
		if($v['effect']%10==3) //长条
			$holdingg[$v['position']]=$v['timing_sec']+$v['effect_value'];
		else
			$holdingg[$v['position']]=$v['timing_sec'];
	}
	return $note;
}



function generateLiveSevenKey($note) {
	$timing=[];
	foreach($note as $v)
		$timing[]=$v['timing_sec'];
	array_multisort($timing,SORT_ASC,$note);
	$holding=false;
	$holdend=0;
	$lasttime=0;
	$holdingg=[0,-0.1,-0.1,-0.1,-0.1,-0.1,-0.1,-0.1,-0.1,-0.1];
	foreach($note as $k=>&$v) {
		if($v['position']<2)$v['position']+=2;
		else if($v['position']>8)$v['position']-=2;
		if($v['timing_sec']<$holdingg[$v['position']]+0.01){
			if($v['position']<5)$v['position']+=2;
			else $v['position']-=2;
		}
		if($v['effect']%10==3) //长条
			$holdingg[$v['position']]=$v['timing_sec']+$v['effect_value'];
		else
			$holdingg[$v['position']]=$v['timing_sec'];
	}
	return $note;
}


function generateRandomLiveLimitlessSevenKey($note) {
	$timing=[];
	foreach($note as $v)
		$timing[]=$v['timing_sec'];
	array_multisort($timing,SORT_ASC,$note);
    
  $holding=[0,-0.1,-0.1,-0.1,-0.1,-0.1,-0.1,-0.1,-0.1,-0.1];
    
	foreach($note as $k=>&$v){
	$v['effect']%=10;//去除划键，防止箭头乱飞
    while(true){
      $v['position']=rand(2,8);
      if($v['timing_sec']>$holding[$v['position']]+0.05)
        break;
    }
    if($v['effect']%10==3) //长条
			$holding[$v['position']]=$v['timing_sec']+$v['effect_value'];
    else
      $holding[$v['position']]=$v['timing_sec'];
  }
	return $note;
}