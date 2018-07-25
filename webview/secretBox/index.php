<?php
if(!is_numeric($_GET['secret_box_id'])){
	print("非法访问！");
	die();
}
include_once("../includes/db.php");
$db = getSecretboxDb();
$unit = getUnitDb();
$name = $db->query("SELECT name FROM secret_box_m WHERE secret_box_id = ".$_GET['secret_box_id'])->fetchColumn();
$precentage = $db->query("SELECT * FROM secret_box_unit_group_m WHERE secret_box_id = ".$_GET['secret_box_id']." ORDER BY weight ASC")->fetchAll(PDO::FETCH_ASSOC);
if(!$precentage){
	print("找不到对应的概率！");
	die();
}
$count = $db->query("SELECT COUNT(*) FROM secret_box_unit_m WHERE secret_box_id = ".$_GET['secret_box_id'])->fetchColumn();
$ur = $db->query("SELECT * FROM secret_box_unit_m WHERE unit_group_id = 4 AND secret_box_id = ".$_GET['secret_box_id'])->fetchAll(PDO::FETCH_ASSOC);
$ssr = $db->query("SELECT * FROM secret_box_unit_m WHERE unit_group_id = 5 AND secret_box_id = ".$_GET['secret_box_id'])->fetchAll(PDO::FETCH_ASSOC);
$sr = $db->query("SELECT * FROM secret_box_unit_m WHERE unit_group_id = 3 AND weight != 1 AND secret_box_id = ".$_GET['secret_box_id'])->fetchAll(PDO::FETCH_ASSOC);
$sr_ev = $db->query("SELECT * FROM secret_box_unit_m WHERE unit_group_id = 3 AND weight = 1 AND secret_box_id = ".$_GET['secret_box_id'])->fetchAll(PDO::FETCH_ASSOC);
$r = $db->query("SELECT * FROM secret_box_unit_m WHERE unit_group_id = 2 AND secret_box_id = ".$_GET['secret_box_id'])->fetchAll(PDO::FETCH_ASSOC);
$n = $db->query("SELECT * FROM secret_box_unit_m WHERE unit_group_id = 1 AND secret_box_id = ".$_GET['secret_box_id'])->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>勧誘詳細</title>
  <meta name="viewport" content="width=960, target-densitydpi=device-dpi, user-scalable=no">
  <link rel="stylesheet" href="//cf-static-prod.lovelive.ge.klabgames.net/resources/css1.3/bstyle.css?r=20170115">
  <style>
    h1 {
      background-image: url('//cf-static-prod.lovelive.ge.klabgames.net/resources/img/help/bg01_01.png');
      background-repeat: no-repeat;
      color: white;
      height: 58px;
      width: 900px;
      padding-top: 15px;
      font-size: 40px;
      font-weight: bold;
      text-align: center;
      margin: 0 auto;
    }

    .container {
      background: url('//cf-static-prod.lovelive.ge.klabgames.net/resources/img/help/bg02.png') repeat-y;
      padding: 5px 20px;
      width: 860px;
      margin: 0 auto;
    }

    h2, h3, h4, h5 {
      font-size: 27px;
      margin-top: 1em;
    }

    h2 {
      margin-top: 2em;
      text-align: center;
    }

    h2:first-child {
      margin-top: 1em;
    }

    h4 {
      font-weight: bold;
      margin-top: 2em;
      width: auto;
    }

    h3 + h4 {
      margin-top: 0.5em;
    }

    h5 {
      font-weight: normal;
      margin-top: 1em;
    }

    h4 + h5 {
      margin-top: 0.5em;
    }

    h5 + ul {
      margin-bottom: 0.5em;
    }

    p {
      margin-left: 1em;
      margin-bottom: 0.5em;
    }

    ul li {
      background-color: white;  /* to avoid Android 4.[0-3] bug */
      margin-left: 1em;
      text-indent: -1em;
    }

    ul li:before {
      content: '・';
    }

    ul.notice li {
      margin-bottom: 8px;
    }

    table {
      border: 1px solid #fc6399;
      border-collapse: collapse;
    }

    thead tr {
      background-color: #fc6399;
    }

    tbody tr:nth-child(even) {
      background-color: #ddd;
    }

    th, td {
      border: 1px solid #fc6399;
      padding: 2px 5px;
    }

    td.skill-name {
      width: 350px;
    }

    td.unit-name {
      width: 200px;
    }

    td.unit-attribute {
      width: 150px;
    }

    .limited {
      color: red;
    }

    .event {
      color: blue;
    }

    footer {
      background-image: url("//cf-static-prod.lovelive.ge.klabgames.net/resources/img/help/bg03.png");
      margin: 0 auto;
      width: 900px;
      height: 30px;
    }
  </style>
</head>

<body>
  <h1>
    <?php print($name)?>的概率
  </h1>

  <div class="container">
          
    <h3>■各稀有度的概率</h3>
    <ul>
		<?php
		foreach($precentage as $i){
			switch((int)$i['unit_group_id']){
				case 4:
					print("<li>UR: ".$i['weight']."%</li>");break;
				case 5:
					print("<li>SSR: ".$i['weight']."%</li>");break;
				case 3:
					print("<li>SR: ".$i['weight']."%</li>");break;
				case 2:
					print("<li>R: ".$i['weight']."%</li>");break;
				case 1:
					print("<li>N: ".$i['weight']."%</li>");break;
				default:
					print("系统出错辣！");
			}
		}
		?>
        </ul>
	<?php if(isset($precentage[0]['weight_extra'])){
	print("<h3>■开卡时的概率</h3>
    <ul>");
		foreach($precentage as $i){
			switch((int)$i['unit_group_id']){
				case 4:
					print("<li>UR: ".$i['weight_extra']."%</li>");break;
				case 5:
					print("<li>SSR: ".$i['weight_extra']."%</li>");break;
				case 3:
					print("<li>SR: ".$i['weight_extra']."%</li>");break;
				case 2:
					print("<li>R: ".$i['weight_extra']."%</li>");break;
				case 1:
					print("<li>N: ".$i['weight_extra']."%</li>");break;
				default:
					print("系统出错辣！");
			}
		}
		print("</ul>");
	}
		?>
        

    <h3>■注意事项</h3>
    <ul class="notice">
      <li>概率经过了四舍五入，所以总概率加起来可能达不到100%</li>
      <li>可获得的社员有时会重复</li>
      <li>抽取时首先按照上述的“各稀有度的概率”抽取稀有度，<br>然后，再根据该稀有度抽取社员</li>
          </ul>

    <h3>■可以抽到的社员一览（共<?php print($count)?>种）</h3>
	<?php if(!empty($ur)){ ?>
            <h4>&lt;UR&gt;（<?php print(count($ur));?>张）</h4>
                <h5>[UR普通社员]</h5>
    <p>
      每位社员的平均出现率相等
    </p>
    <p>
      UR社员中每张卡的出现率：<?php print(100/count($ur))?>%
    </p>
        <table>
      <thead>
        <tr>
          <th class="skill-name">技能</th>          <th class="unit-name">名字</th>
          <th class="unit-attribute">属性</th>
        </tr>
      </thead>
      <tbody>
			<?php
			foreach($ur as $i){
				$card = $unit->query("SELECT name, default_unit_skill_id, attribute_id FROM unit_m WHERE unit_id = ".$i['unit_id'])->fetch(PDO::FETCH_ASSOC);
				$skill = $unit->query("SELECT name FROM unit_skill_m WHERE unit_skill_id = ".$card['default_unit_skill_id'])->fetchColumn();
				print('
              <tr>
			<td class="skill-name">【'.$skill.'】</td>          <td class="unit-name">'.$card['name'].'</td>
			<td class="unit-attribute">');
				switch((int)$card['attribute_id']){
					case 1:print("スマイル</td>");break;
					case 2:print("ピュア</td>");break;
					case 3:print("クール</td>");break;
					case 5:print("全</td>");break;
				}
			}
		  print("</tr>");
		  ?>
            </tbody>
    </table>
	<?php }?>
	
    <?php if(!empty($ssr)){ ?>
            <h4>&lt;SSR&gt;（<?php print(count($ssr));?>张）</h4>
                <h5>[SSR普通社员]</h5>
    <p>
      每位社员的平均出现率相等
    </p>
    <p>
      SSR社员中每张卡的出现率：<?php print(100/count($ssr))?>%
    </p>
        <table>
      <thead>
        <tr>
          <th class="skill-name">技能</th>          <th class="unit-name">名字</th>
          <th class="unit-attribute">属性</th>
        </tr>
      </thead>
      <tbody>
			<?php
			foreach($ssr as $i){
				$card = $unit->query("SELECT name, eponym, attribute_id FROM unit_m WHERE unit_id = ".$i['unit_id'])->fetch(PDO::FETCH_ASSOC);
				print('
              <tr>
			<td class="skill-name">【'.$card['eponym'].'】</td>          <td class="unit-name">'.$card['name'].'</td>
			<td class="unit-attribute">');
				switch((int)$card['attribute_id']){
					case 1:print("スマイル</td>");break;
					case 2:print("ピュア</td>");break;
					case 3:print("クール</td>");break;
					case 5:print("全</td>");break;
				}
			}
		  print("</tr>");
		  ?>
            </tbody>
    </table>
	<?php }?>
	
	<?php if(!empty($sr)){ ?>
            <h4>&lt;SR&gt;（<?php print(count($sr) + count($sr_ev));?>张）</h4>
                <h5>[SR普通社员]</h5>
    <p>
      每位社员的平均出现率相等
    </p>
    <p>
      SR社员中每张卡的出现率：<?php print(100/(count($sr) + count($sr_ev)))?>%
    </p>
        <table>
      <thead>
        <tr>
          <th class="skill-name">技能</th>          <th class="unit-name">名字</th>
          <th class="unit-attribute">属性</th>
        </tr>
      </thead>
      <tbody>
			<?php
			foreach($sr as $i){
				$card = $unit->query("SELECT name, default_unit_skill_id, attribute_id FROM unit_m WHERE unit_id = ".$i['unit_id'])->fetch(PDO::FETCH_ASSOC);
				$skill = $unit->query("SELECT name FROM unit_skill_m WHERE unit_skill_id = ".$card['default_unit_skill_id'])->fetchColumn();
				print('
              <tr>
			<td class="skill-name">【'.$skill.'】</td>          <td class="unit-name">'.$card['name'].'</td>
			<td class="unit-attribute">');
				switch((int)$card['attribute_id']){
					case 1:print("スマイル</td>");break;
					case 2:print("ピュア</td>");break;
					case 3:print("クール</td>");break;
					case 5:print("全</td>");break;
				}
			}
		  print("</tr>");
		  ?>
            </tbody>
    </table>
	<?php if(!empty($sr_ev)){?>
	 <h5>[SR活动先行配信社员]</h5>
    <p>
      每位社员的出现率是普通社员的1/5
    </p>
    <p>
      SR社员中每张卡的出现率：<?php print(20/(count($sr) + count($sr_ev)))?>%
    </p>
        <table>
      <thead>
        <tr>
          <th class="skill-name">技能</th>          <th class="unit-name">名字</th>
          <th class="unit-attribute">属性</th>
        </tr>
      </thead>
      <tbody>
			<?php
			foreach($sr_ev as $i){
				$card = $unit->query("SELECT name, eponym, attribute_id FROM unit_m WHERE unit_id = ".$i['unit_id'])->fetch(PDO::FETCH_ASSOC);
				print('
              <tr>
			<td class="skill-name">【'.$card['eponym'].'】</td>          <td class="unit-name">'.$card['name'].'</td>
			<td class="unit-attribute">');
				switch((int)$card['attribute_id']){
					case 1:print("スマイル</td>");break;
					case 2:print("ピュア</td>");break;
					case 3:print("クール</td>");break;
					case 5:print("全</td>");break;
				}
			}
		  print("</tr>");
		  ?>
            </tbody>
    </table>
	<?php }}?>
	
	<?php if(!empty($r)){ ?>
            <h4>&lt;R&gt;（<?php print(count($r));?>张）</h4>
                <h5>[R普通社员]</h5>
    <p>
      每位社员的平均出现率相等
    </p>
    <p>
      R社员中每张卡的出现率：<?php print(100/count($r))?>%
    </p>
        <table>
      <thead>
        <tr>
          <th class="skill-name">技能</th>          <th class="unit-name">名字</th>
          <th class="unit-attribute">属性</th>
        </tr>
      </thead>
      <tbody>
			<?php
			foreach($r as $i){
				$card = $unit->query("SELECT name, default_unit_skill_id, attribute_id FROM unit_m WHERE unit_id = ".$i['unit_id'])->fetch(PDO::FETCH_ASSOC);
				$skill = $unit->query("SELECT name FROM unit_skill_m WHERE unit_skill_id = ".$card['default_unit_skill_id'])->fetchColumn();
				print('
              <tr>
			<td class="skill-name">【'.$skill.'】</td>          <td class="unit-name">'.$card['name'].'</td>
			<td class="unit-attribute">');
				switch((int)$card['attribute_id']){
					case 1:print("スマイル</td>");break;
					case 2:print("ピュア</td>");break;
					case 3:print("クール</td>");break;
					case 5:print("全</td>");break;
				}
			}
		  print("</tr>");
		  ?>
            </tbody>
    </table>
	<?php }?>
	
	<?php if(!empty($n)){ ?>
            <h4>&lt;N&gt;（<?php print(count($n));?>张）</h4>
                <h5>[N普通社员]</h5>
    <p>
      每位社员的平均出现率相等
    </p>
    <p>
      R社员中每张卡的出现率：<?php print(100/count($n))?>%
    </p>
        <table>
      <thead>
        <tr>
          <th class="unit-name">名字</th>
          <th class="unit-attribute">属性</th>
        </tr>
      </thead>
      <tbody>
			<?php
			foreach($n as $i){
				$card = $unit->query("SELECT name, attribute_id FROM unit_m WHERE unit_id = ".$i['unit_id'])->fetch(PDO::FETCH_ASSOC);
				print('
              <tr>
			<td class="unit-name">'.$card['name'].'</td>
			<td class="unit-attribute">');
				switch((int)$card['attribute_id']){
					case 1:print("スマイル</td>");break;
					case 2:print("ピュア</td>");break;
					case 3:print("クール</td>");break;
					case 5:print("全</td>");break;
				}
			}
		  print("</tr>");
		  ?>
            </tbody>
    </table>
	<?php }?>
            </div>
  <footer></footer>
</body>
</html>
