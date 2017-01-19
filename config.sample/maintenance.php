<?php

//是否启用维护（禁止访问）
$maintenance=false;
//此数组中的UID无视维护状态
$bypass_maintenance[]=1;
//维护信息
$maintenance_info='现在正在进行维护。<br /><br />
服务停止时间<br />
我也不知道（滑稽）<br />
<br />
带来的不便我们深表歉意，敬请谅解。';

//是否提供自动更新
$update_for_android=true;
$update_for_ios=true;
//是否提供小包下载
$additional_for_android=true;
$additional_for_ios=true;
//允许的最低数据包版本（低于会触发自动更新）
$server_ver='21.1';
//允许的最低客户端版本
$bundle_ver='4.2';
$restrict_ver=false; //此版本号的用户必须拥有allow_test_func权限才能登录（为了防止开发版客户端泄露）
//最大LIVE数
$max_live_difficulty_id=9999;
//最大UNIT数
$max_unit_id=9999;
//游戏内DL地址
$getUrl_address = 'http://60.205.189.255/update/extracted/';

