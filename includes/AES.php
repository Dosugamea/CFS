<?php
function AESdecrypt($encryptedData, $Key, $iv){
	$result = openssl_decrypt($encryptedData, 'aes-128-cbc', $Key, OPENSSL_RAW_DATA, $iv);
	return $result;
}

function AESencrypt($data, $Key, $iv) {
	$newdata = openssl_encrypt($data, 'aes-128-cbc', $Key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($newdata);
}