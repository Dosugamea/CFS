<?php
function RSAdecrypt($encrypted){
	include('config/RSA_KeyPair.php');
	$priv_key =  openssl_pkey_get_private($priv_key);
	openssl_private_decrypt(base64_decode($encrypted),$decrypted,$priv_key);
	return $decrypted;
}