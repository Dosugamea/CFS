<?php
function RSAdecrypt($encrypted){
	include('../config/RSA_KeyPair.php');
	$priv_key =  openssl_pkey_get_private($priv_key);
	openssl_private_decrypt(base64_decode($encrypted),$decrypted,$priv_key);
	return $decrypted;
}

function RSAencrypt($data){
	include('../config/RSA_KeyPair.php');
	$official_pub_key =  openssl_pkey_get_public($official_pub_key);
	openssl_public_encrypt($data, $encrypted, $official_pub_key);
	return base64_encode($encrypted);
}

function RSAsign($data){
	include('../config/RSA_KeyPair.php');
	$priv_key =  openssl_pkey_get_private($priv_key);
	openssl_sign($data, $signature, $priv_key, OPENSSL_ALGO_SHA1);
	return base64_encode($signature);
}