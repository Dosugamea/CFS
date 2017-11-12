<?php
//这个页面用来配置透传下载所需的账号和pw
$login_key = "";
$login_passwd = "";

//CDN反代所用的域名
$reverse_proxy = "";

//官方客户端目前的版本
$official_bundle_ver = "";
$official_client_ver = "";

//检测缓存中是否有所需的包，如果不需要检查可以留空
//如果检测到缓存里面有，则会去除请求链接中的参数，增加CDN命中率
$check_package_an = "/www/wwwroot/";
$check_package_ios = "/www/wwwroot/";