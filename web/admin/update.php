<?php
include("../../includes/configManager.php");
$config = new configManager;
include("../../includes/db.php");
$live = getLiveDb();
$all = $live->query("SELECT notes_setting_asset FROM live_setting_m")->fetchAll(PDO::FETCH_ASSOC);
$all_live = [];
foreach($all as $i)
	$all_live[] = $i['notes_setting_asset'];
//var_dump($all_live);
$b = $mysql->query("SELECT notes_setting_asset FROM notes_setting")->fetchAll(PDO::FETCH_ASSOC);
$present = [];
foreach($b as $i)
	$present[] = $i['notes_setting_asset'];
//var_dump($present);
$result = array_diff($all_live,$present);
foreach($result as $i){
	$url = "http://a.llsif.win/live/json/".$i;
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_NOBODY, FALSE);
  
    $response = curl_exec($ch);
    //分离header与body  
    $header = '';
    $body = '';
    if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200') {
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE); //头信息size  
        $header = substr($response, 0, $headerSize);  
        $body = substr($response, $headerSize);
		if(json_decode($body, true) != Null){
			$mysql->query("INSERT INTO notes_setting VALUES('".$i."', '".$body."')");
			print "Sync success ".$i."<br>";
		}
		else
			print "Invalid json ".$i."<br>";
    }else
		print "Invalid data ".$i." status ".curl_getinfo($ch, CURLINFO_HTTP_CODE)."<br>";
    curl_close($ch);
	flush();
}
?>