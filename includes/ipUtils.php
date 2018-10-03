<?php
//author: http://blog.ifeeline.com/285.html
//TODO:增加v6支持
//判断是否是合法IP
function isIp($str) {
	$ip = explode(".", $str);
	if (count($ip) < 4 || count($ip) > 4) return FALSE;
	foreach($ip as $ip_addr) {
		if ( !is_numeric($ip_addr) ) return FALSE;
		if ( $ip_addr < 0 || $ip_addr > 255 ) return FALSE;
	}
    return preg_match("/^([0-9]{1,3}\.){3}[0-9]{1,3}$/is", $str);
}

//根据给出的数字生成二进制掩码串
function mask2bin($n){ 
	$n = intval($n); 
	if($n < 0 || $n > 32) return FALSE; 
	return str_repeat("1", $n).str_repeat("0", 32-$n);
}
//反转掩码串
function revBin($s)   { 
	$p = array('0','1','2'); 
	$r = array('2','0','1'); 
	return  str_replace($p,$r,$s); 
} 

//根据IP和掩码得到网络地址 十进制
function getSubnet($ip, $mask){
        //这里的按为运算务必要保证长度一致，都是32位
	$bin_ip = str_pad(decbin(ip2long($ip)),32,'0',STR_PAD_LEFT);
	$msk = mask2bin($mask);
	
	return bindec($bin_ip & $msk);
}
//根据IP和掩码得到广播地址 十进制
function getBroadcast($ip, $mask){
	$bin_ip = str_pad(decbin(ip2long($ip)),32,'0',STR_PAD_LEFT);
	$msk = mask2bin($mask);
	return bindec($bin_ip | revBin($msk));
}

//检查IP是否在列表里
function ipInListCheck($passlist, $ip){
	foreach ($passlist as $pass){
		//$dec_ip = bindec(decbin(ip2long($ip)));
		$dec_ip = $ip;
		$tm = explode("/", $pass);
		if(count($tm) > 1){
			// 合法IP 和 掩码数
			if(isIp($tm[0]) === FALSE){ continue; }
			if( ((int)$tm[1] < 1) || ((int)$tm[1] > 32)){ continue; }
			
			$dec_from = getSubnet($tm[0],(int)$tm[1]);
			$dec_end = getBroadcast($tm[0],(int)$tm[1]);
			// 在段中
			if( (bccomp($dec_ip,$dec_from) == 1) && (bccomp($dec_ip,$dec_end) == -1) ){
				return TRUE;
				break;
			}
		}else if(trim($ip) == bindec(decbin(ip2long(trim($pass))))){
			return TRUE;
			break;
		}
	}
    return FALSE;
}

function isCloudFlareIp($ip){
    $table = [
        "103.21.244.0/22",
        "103.22.200.0/22",
        "103.31.4.0/22",
        "104.16.0.0/12",
        "108.162.192.0/18",
        "131.0.72.0/22",
        "141.101.64.0/18",
        "162.158.0.0/15",
        "172.64.0.0/13",
        "173.245.48.0/20",
        "188.114.96.0/20",
        "190.93.240.0/20",
        "197.234.240.0/22",
        "198.41.128.0/17",
    ];
    return ipInListCheck($table, $ip);
}

function isAliIp($ip){
    $table = [];//todo
    return ipInListCheck($table, $ip);
}

function isInExtraIp($ip){
	global $config;
    return $ip == $config->basic['proxy_ip'];
}