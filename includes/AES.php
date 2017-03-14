<?php
function AESdecrypt($encryptedData, $Key, $iv){
    //Open module
    $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, $iv);

    mcrypt_generic_init($module, $Key, $iv);

    $encryptedData = mdecrypt_generic($module, $encryptedData);

    return $encryptedData;
}

function AESencrypt($data, $Key, $iv) {
    //Open module
    $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, $iv);

    //print "module = $module <br/>" ;

    mcrypt_generic_init($module, $Key, $iv);

    //Padding
    $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $pad = $block - (strlen($data) % $block); //Compute how many characters need to pad
    $data .= str_repeat(chr($pad), $pad); // After pad, the str length must be equal to block or its integer multiples

    //encrypt
    $encrypted = mcrypt_generic($module, $data);

    //Close
    mcrypt_generic_deinit($module);
    mcrypt_module_close($module);

    return base64_encode($encrypted);
}