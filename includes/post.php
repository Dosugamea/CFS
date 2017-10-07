<?php
class poster{
	private $login_key;
	private $login_passwd;
	private $application_key = "gae1aefu9eeRojahj4cei3pho1eaZada";
	private $xor_base = "eit4Ahph4aiX4ohmephuobei6SooX9xo";
	private $cache;
	private $sessionKey = "";
	private $uri = "http://prod-jp.lovelive.ge.klabgames.net/main.php";
	private $device_data = '{
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
		"signature":"b3dc6cc00b7dec39fcdf40a5ea1c986f2b3c0ce05c50120cc3f5d17e47916bcd730bbdead2c5fbaf14fc0b3e199f48f9a8483164776226310b8089aeb0bbdbca",
		"SuspiciousElement":[]
	}';
	private $common_headers;
	
	public function __construct(){
		global $official_bundle_ver, $server_ver;
		include("../config/modules_download.php");
		$time = time();
		$this->common_headers = [
			'Accept: */*',
			'Accept-Encoding: deflate',
			'API-Model: straightforward',
			'Debug: 1',
			"Bundle-Version: $official_bundle_ver",
			"Client-Version: $server_ver",
			'OS-Version: Nexus 5 google hammerhead 4.4.4',
			'OS: Android',
			'Platform-Type: 2',
			'Application-ID: 626776655',
			'Time-Zone: JST',
			'Reigion: 392',
			"Authorize: consumerKey=lovelive_test&timeStamp=$time&version=1.1&nonce=1",
			'Expect:',
			'X-Message-Code: '
		];
		if(is_file("../auth.cache") && filesize("../auth.cache") > 0){
			$cache_file = fopen("../auth.cache", "r+");
			$cache_file_ = fread($cache_file, filesize("../auth.cache"));
		}else{
			$cache_file = fopen("../auth.cache", "w");
		}
		
		if(!isset($cache_file_) || !$cache_file_){
			$this->cache = ["token" => "", "time" => 0, "sessionKey" => "", "uid" => 0];
		}else{
			$this->cache = json_decode($cache_file_, true);
			$this->common_headers[12] = "Authorize: consumerKey=lovelive_test&timeStamp=".time()."&version=1.1&token=".$this->cache['token']."&nonce=2";
		}
		$sessionKey = $this->cache['sessionKey'];
		$this->sessionKey = base64_decode($sessionKey);
		$this->login_key = $login_key;
		$this->login_passwd = $login_passwd;
		$this->common_headers[] = "User-ID: ".$this->cache['uid'];
		if(time() - $this->cache['time'] > 43200){ //每12小时刷新token
			$this->login();
			$this->cache['sessionKey'] = base64_encode($this->sessionKey);
			rewind($cache_file);
			fwrite($cache_file, json_encode($this->cache));
		}
		fclose($cache_file);
	}
	private function curls($url, $headers, $data_string) {
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
		return $resp;
	}
	
	public function post($body, $url){
		$full_url = $this->uri.$url;
		$req_body = ["request_data" => json_encode($body)];//这里的body一定要是"request_data" => json的形式！
		$headers = $this->common_headers;
		$XMC = hash_hmac('sha1', $req_body['request_data'], $this->sessionKey);
		$headers[14] = "X-Message-Code: $XMC";
		return $this->curls($full_url, $headers, $req_body);
	}
	
	private function login(){
		unset($this->common_headers[15]);
		$time = time();
		include '../config/maintenance.php';
		//生成随机AES密钥
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890/*-+!@#$%^&*()_=';
		mt_srand((double)microtime() * 1000000 * getmypid());
		$AES_token_client = '';
		while(strlen($AES_token_client) < 32){
			$AES_token_client .= substr($chars,(mt_rand()%strlen($chars)),1);  
		}
		//生成IV
		$iv = '';
		while(strlen($iv) < 16){
			$iv .= substr($chars, (mt_rand() % strlen($chars)), 1);  
		}
		//计算HMAC密钥
		for($i = 0;$i < 32;$i++){
			$xor_table[$i] = $this->application_key[$i] ^ $this->xor_base[$i];
		}
		$xor_table = implode("", $xor_table);
		for($i = 0;$i < strlen($xor_table);$i++){
			$HMACKey[$i] = $xor_table[$i] ^ $AES_token_client[$i % strlen($AES_token_client)];
		}
		$this->sessionKey = implode("",$HMACKey);
		//RSA加密密钥
		$encrypted_AES_token_client = RSAencrypt($AES_token_client);
		//auth_data生成
		$auth_data = json_encode(["1"=>$this->login_key,"2"=>$this->login_passwd,"3"=>base64_encode($this->device_data)]);
		$auth_data_enc = AESencrypt($auth_data, substr($AES_token_client,0,16), $iv);
		//生成body
		$body = [
			"dummy_token" => $encrypted_AES_token_client,
			"auth_data" => base64_encode($iv.base64_decode($auth_data_enc))
		];
		$r = $this->post($body, "/login/authkey");
		if(in_array("Maintenance: 1",$r[0])){
			return "Maintenance";
		}
		$r = $r[1];
		
		$token = $r['response_data']['authorize_token'];
		$AES_token_server = base64_decode($r['response_data']['dummy_token']);
		//计算新的密钥
		for($i = 0;$i < strlen($AES_token_server);$i++){
			$sessionKey[$i] = ($AES_token_server[$i] ^ $AES_token_client[$i % strlen($AES_token_client)]);
		}
		$sessionKey = implode("",$sessionKey);
		$this->sessionKey = $sessionKey;
		
		$time = time();
		$this->common_headers[12] = "Authorize: consumerKey=lovelive_test&timeStamp=$time&version=1.1&token=$token&nonce=2";
		
		$devtoken = "";
		for($i = 0; $i < 64; $i++){
			$devtoken .= str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
			$devtoken .= ":";
		}
		$devtoken = substr($devtoken, 0, -1);
		$body = [
				"login_key" => base64_encode($iv.base64_decode(AESencrypt($this->login_key, substr($sessionKey,0,16),$iv))),
				"login_passwd" => base64_encode($iv.base64_decode(AESencrypt($this->login_passwd, substr($sessionKey,0,16),$iv))),
				"devtoken" => strtoupper($devtoken)
		];
		$r = $this->post($body, "/login/login");
		$r = $r[1]['response_data'];
		@$this->cache['token'] = $r['authorize_token'];
		@$token = $r['authorize_token'];
		@$user_id = $r['user_id'];
		$time = time();
		$this->common_headers[12] = "Authorize: consumerKey=lovelive_test&timeStamp=$time&version=1.1&token=$token&nonce=3";
		
		@$this->common_headers[] = "User-ID: $user_id";
		@$this->cache['uid'] = $user_id;
		$this->cache['time'] = time();
		return;
	}
}
?>
