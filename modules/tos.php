<?php
//tos.php 用户协议module

//tos/tosCheck 返回所有用户协议的接受情况
function tos_tosCheck() {
  return json_decode('{"tos_id":1,"is_agreed":true}');
}

//tos/read 阅读用户协议
//tos/tosAgree 同意用户协议
?>