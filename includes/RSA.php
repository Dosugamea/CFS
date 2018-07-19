<?php
function RSAdecrypt($encrypted){
	global $config;
	$priv_key =  openssl_pkey_get_private($config->basic['priv_key']);
	openssl_private_decrypt(base64_decode($encrypted), $decrypted, $priv_key);
	return $decrypted;
}

function RSAsign($data){
	global $config;
	$priv_key =  openssl_pkey_get_private($config->basic['priv_key']);
	openssl_sign($data, $signature, $priv_key, OPENSSL_ALGO_SHA1);
	return base64_encode($signature);
}