<?php
function AESdecrypt($encryptedData, $Key, $iv){
    //Open module
    $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, $iv);

    mcrypt_generic_init($module, $Key, $iv);

    $encryptedData = mdecrypt_generic($module, $encryptedData);

    return $encryptedData;
}