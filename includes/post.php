<?php
function curls($url, $headers, $data_string) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    $data = curl_exec($ch);
    curl_close($ch);
	$resp = explode("\r\n\r\n", $data);
	$header = explode("\r\n", $resp[0]);
	$resp[1] = json_decode($resp[1],true);
	$resp[0] = $header;
	//$resp[1] = $body;
    return $resp;
}

function login(){
	$time = time();
	include 'config/maintenance.php';
	//生成随机AES密钥
	$chars='ABDEFGHJKLMNPQRSTVWXYabdefghijkmnpqrstvwxy23456789#%*';
	mt_srand((double)microtime()*1000000*getmypid());
	$AES_token_client='';
	while(strlen($AES_token_client) < 32){
		$AES_token_client.=substr($chars,(mt_rand()%strlen($chars)),1);  
	}
	//生成IV
	$iv='';
	while(strlen($iv) < 16){
		$iv.=substr($chars,(mt_rand()%strlen($chars)),1);  
	}
	//计算HMAC密钥
	$xor_table = hex2bin("02081105200d161d0d040c0a5b0509050f440b100651150159620a0e02581c0e");
	for($i=0;$i < strlen($xor_table);$i++){
		$HMACKey[$i] = ($xor_table[$i] ^ $AES_token_client[$i % strlen($AES_token_client)]);
	}
	$HMACKey = implode("",$HMACKey);
	//RSA加密密钥
	include 'includes/RSA.php';
	$encrypted_AES_token_client = RSAencrypt($AES_token_client);
	//auth_data生成
	$device_data = '{
		"Hardware":"Qualcomm Technologies, Inc MSM8974",
		"adbEnbled":"NO",
		"basePath":"/data/user/0/klb.android.lovelive/files",
		"ro.build.fingerprint":"google/hammerhead/hammerhead:4.4.4/KTU84P/3582057:user/release-keys",
		"ro.build.tags":"release-keys",
		"ro.build.version.release":"4.4.4",
		"ro.product.board":"hammerhead",
		"ro.product.brand":"google",
		"ro.product.device":"hammerhead",
		"ro.product.manufacturer":"LG",
		"ro.product.model":"Nexus 5",
		"ro.product.name":"hammerhead",
		"signature":"ba5803d889a34bd5a974b7d43d688799c645a70f4aa86628198ccfe0e1df804dcc5b4a158920603a6f5e2a709c494aa49ae09e198c56b4a116bf7a1f6c21c2e8",
		"SuspiciousElement":[]
	}';
	include 'includes/AES.php';
	include 'config/modules_download.php';
	$auth_data = json_encode(["1"=>$login_key,"2"=>$login_passwd,"3"=>base64_encode($device_data)]);
	$auth_data_enc = AESencrypt($auth_data, substr($AES_token_client,0,16), $iv);
	//生成body
	$body = array(
		"request_data" => json_encode(array(
			"dummy_token" => $encrypted_AES_token_client,
			"auth_data" => base64_encode($iv.base64_decode($auth_data_enc))
	)));
	
	$headers = array(
			'Accept: */*',
			'Accept-Encoding: deflate',
			'API-Model: straightforward',
			'Debug: 1',
			"Bundle-Version: $bundle_ver",
			"Client-Version: $server_ver",
			'OS-Version: Nexus 5 google hammerhead 4.4.4',
			'OS: Android',
			'Platform-Type: 2',
			'Application-ID: 626776655',
			'Time-Zone: JST',
			'Reigion: 392',
			'Authorize: consumerKey=lovelive_test&timeStamp=$time&version=1.1&nonce=1',
			'Expect:'
		);
	$XMC = hash_hmac('sha1', $body['request_data'], $HMACKey);
	$headers[] = "X-Message-Code: $XMC";
	$r = curls("prod-jp.lovelive.ge.klabgames.net/main.php/login/authkey",$headers,$body);
	if(in_array("Maintenance: 1",$r[0])){
		return "Maintenance";
	}
	$r = $r[1];
	
	$token = $r['response_data']['authorize_token'];
	$AES_token_server = base64_decode($r['response_data']['dummy_token']);
	//计算新的密钥
	for($i=0;$i < strlen($AES_token_server);$i++){
		$sessionKey[$i] = ($AES_token_server[$i] ^ $AES_token_client[$i % strlen($AES_token_client)]);
	}
	$sessionKey = implode("",$sessionKey);
	
	$time = time();
	$headers[12] = "Authorize: consumerKey=lovelive_test&timeStamp=$time&version=1.1&token=$token&nonce=2";
	
	
	//这里的body一定要是"request_data" => json的形式！
	$body = array(
		"request_data" => json_encode(array(
			"login_key" => base64_encode($iv.base64_decode(AESencrypt($login_key,substr($sessionKey,0,16),$iv))),
			"login_passwd" => base64_encode($iv.base64_decode(AESencrypt($login_passwd,substr($sessionKey,0,16),$iv))),
			"devtoken" => "APA91bFb7105jsH-vKhCv1XCxJaj3e2g9sEpSCLWtZvWniRhpv-DvHI_pvj7jYGwCGPbVejKfDwXsHXzez3loz95HJoam8xpQnfo-pCHZEAhiBW9IWzfV5-__k3BS5_lRx9zLqJ-LGKq"
	)));
	$XMC = hash_hmac('sha1', $body['request_data'], $sessionKey);
	$headers[14] = "X-Message-Code: $XMC";
	$r = curls("prod-jp.lovelive.ge.klabgames.net/main.php/login/login",$headers,$body);
	//$r = json_decode(gzdecode($r));
	$r = $r[1]['response_data'];
	$r['sessionKey'] = $sessionKey;
	return $r;
}

?>
