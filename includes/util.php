<?php
function xor_($str1, $str2){
	for($i=0; $i < strlen($str1);$i++){
		$result[$i] = ($str1[$i] ^ $str2[$i % strlen($str2)]);
	}
	$result = implode("", $result);
	return $result;
}