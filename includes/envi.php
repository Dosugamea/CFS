<?php
/* envi.php
 * 负责检查环境
 * 以及访问的合法性
 */

class envi{
    public $uid;
    public $item;
    public $authorize;
    public $platform;
    public $sessionKey;
    public $path;
    public $params;

    public function __construct(){
        $this->authorize = [];
        foreach (explode('&', $_SERVER['HTTP_AUTHORIZE']) as $i) {
            $i = explode('=', $i);
            $this->authorize[$i[0]] = $i[1];
        }

        //真实IP判断
        if(isCloudFlareIp($_SERVER["REMOTE_ADDR"]) || isAliIp($_SERVER["REMOTE_ADDR"]) || isInExtraIp($_SERVER["REMOTE_ADDR"])){
            $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $this->ip = $_SERVER["REMOTE_ADDR"];
        }
    }

    public function checkAll(){
        global $logger, $mysql;
        //检查header是否缺东西
        if(!isset($_SERVER['HTTP_API_MODEL']) || !isset($_SERVER['HTTP_DEBUG']) || !isset($_SERVER['HTTP_BUNDLE_VERSION'])
        || !isset($_SERVER['HTTP_CLIENT_VERSION']) || !isset($_SERVER['HTTP_OS_VERSION']) || !isset($_SERVER['HTTP_OS'])
        || !isset($_SERVER['HTTP_PLATFORM_TYPE']) || !isset($_SERVER['HTTP_APPLICATION_ID']) || !isset($_SERVER['HTTP_REGION'])){
            $logger->f("INVALID HEADER triggered");
            throw403("INVALID_HEADER");
        }

        //检查平台
        if($_SERVER['HTTP_PLATFORM_TYPE'] == "1" && $_SERVER['HTTP_OS'] == "iOS"){
            $this->platform = 1;
        }elseif($_SERVER['HTTP_PLATFORM_TYPE'] == "2" && $_SERVER['HTTP_OS'] == "Android"){
            $this->platform = 2;
        }else{
            $logger->f("INVALID DEVICE: ".$_SERVER['HTTP_OS']." - ".$_SERVER['HTTP_PLATFORM_TYPE']);
            throw403("INVALID_DEVICE");
        }

        //检查authorize
        if(!isset($this->authorize['consumerKey']) || $this->authorize['consumerKey'] != "lovelive_test"){
            $logger->f("INVALID CONSUMER KEY: ".isset($this->authorize['consumerKey']) ? $this->authorize['consumerKey'] : "Undefined");
            throw403("INVALID_CONSUMER_KEY");
        }
        if(!isset($this->authorize['version']) || $this->authorize['version'] != "1.1"){
            $logger->f("INVALID VERSION: ".isset($this->authorize['version']) ? $this->authorize['version'] : "Undefined");
            throw403("INVALID_VERSION");
        }
        if(!isset($this->authorize['timeStamp']) || abs((int)$this->authorize['timeStamp'] - time()) > 60){
            $logger->f("INVALID TIME: ".isset($this->authorize['timeStamp']) ? $this->authorize['timeStamp'] : "Undefined");
            throw403("INVALID_TIME");
        }
        if(!isset($this->authorize['nonce'])){
            $logger->f("INVALID nonce: Undefined");
            throw403("INVALID_NONCE");
        }

        //往公用变量里面塞东西
        $this->deviceModel      = $_SERVER['HTTP_OS_VERSION'];
        $this->bundleVersion    = $_SERVER['HTTP_BUNDLE_VERSION'];
        $this->clientVersion    = $_SERVER['HTTP_CLIENT_VERSION'];


        //检查访问的接口
        $tmp = explode("/", $_SERVER['PATH_INFO']);
        $this->path = array_splice($tmp, 1);

        //检查用户是否存在
        if(isset($this->authorize['token'])){
            if(isset($_SERVER['HTTP_USER_ID'])){
                //注册/登录用
                if($_SERVER['HTTP_USER_ID'] == -1){
                    header('Maintenance: 1');
                	exit();
                }
                //token和uid都存在，用户应该存在
                $user = $mysql->query("SELECT * FROM users WHERE user_id = ? AND authorize_token = ?", [(int)$_SERVER['HTTP_USER_ID'], $this->authorize['token']])->fetch();
                if($user){
                    $this->uid          = (int)$user['user_id'];
                    $this->sessionKey   = base64_decode($user['sessionKey']);
                    $mysql->query("UPDATE users SET nonce = ? WHERE authorize_token = ?", [$this->authorize['nonce'], $this->authorize['token']]);

                    //检查是否被封禁
                    $banned = $mysql->query("SELECT msg FROM banned_user WHERE user = ? or user = ?", [$this->uid, $user['username']])->fetchColumn(); //检查UID和username（LOVELIVE_ID，keychain）是否在封禁列表
                    if($banned){
                        header('HTTP/1.1 423 USER BANNED');
                        header('Content-Type: application/json');
                        $ret = [];
                        $ret['response_data'] = [];
                        $ret['status_code'] = 423;
                        print(json_encode($ret));
                        exit();
                    }
                }else{
                    $logger->f("Cannot find correspond user with token: ".$this->authorize['token']." and uid: ".$_SERVER['HTTP_USER_ID']);
                    throw403("INVALID_USER");
                }
            }else{
                //有token没有uid，可能当前请求的是login/login
                $user = $mysql->query("SELECT * FROM tmp_authorize WHERE token = ?", [$this->authorize['token']])->fetch();
                if($user){
                    $this->uid          = 0;
                    $this->sessionKey   = base64_decode($user['sessionKey']);
                }else{
                    $logger->f("Cannot find correspond tmp_user with token: ".$this->authorize['token']);
                    throw403("INVALID_USER");
                }
            }
        }else{
            //token和uid都没有，应该是authkey
            if($this->path[0] == "login" && $this->path[1] == "authkey"){
                $logger->i("Didn't find uid and token, authorize.");
            }else{
                $logger->d(json_encode($this->path));
                throw403("NO_AUTHORIZE");
                $logger->f("No authorize and uid, and not authkey, forbidden.");
            }
        }
        $this->checkXMC();
    }

    //检查X-Message-Code
    public function checkXMC(){
        global $logger, $config;
        $specialApi = [
            "lbonus/execute",
            "live/play",
            "ranking/player"
        ];

        $api = implode("/", $this->path);
        $xor_pad = xor_($config->m_login['base_key'], $config->m_login['application_key']);
        if(!file_exists(__DIR__."/../"."XMCWrong.log")){
            $XMCLOG = fopen(__DIR__."/../"."XMCWrong.log","w");
        }else{
            $XMCLOG = fopen(__DIR__."/../"."XMCWrong.log","a");
        }

        if(in_array($api, $specialApi)){
            //特殊API有特殊的XMC算法
            $hmacKey = xor_(substr($config->m_login['base_key'], 16), substr($config->m_login['application_key'], 0, 16)).
            xor_(substr($config->m_login['base_key'], 0, 16), substr($config->m_login['application_key'], 16));
            $xmc = hash_hmac('sha1', $_POST['request_data'], $hmacKey);
            if(!isset($_SERVER['HTTP_X_MESSAGE_CODE']) || $xmc != $_SERVER['HTTP_X_MESSAGE_CODE']){
                fwrite($XMCLOG, date("Y-m-d H:i:s"));
                fwrite($XMCLOG, " ".$_SERVER['HTTP_USER_ID']);
                fwrite($XMCLOG, " ".$_SERVER['PATH_INFO']);
                fwrite($XMCLOG, " ".$_SERVER['HTTP_X_MESSAGE_CODE']);
                fwrite($XMCLOG, "\r\n");
                //throw400('X-MESSAGE-CODE-WRONG');
            }
        }elseif($api != "login/authkey"){ //跳过authkey（modules里面检查）
            $xmc = hash_hmac('sha1', $_POST['request_data'], $this->sessionKey);
            if(!isset($_SERVER['HTTP_X_MESSAGE_CODE']) || $xmc != $_SERVER['HTTP_X_MESSAGE_CODE']){
                fwrite($XMCLOG, date("Y-m-d H:i:s"));
                fwrite($XMCLOG, " ".$_SERVER['HTTP_USER_ID']);
                fwrite($XMCLOG, " ".$_SERVER['PATH_INFO']);
                fwrite($XMCLOG, " ".$_SERVER['HTTP_X_MESSAGE_CODE']);
                fwrite($XMCLOG, "\r\n");
                throw400('X-MESSAGE-CODE-WRONG');
            }
        }
        fclose($XMCLOG);
    }

    public function initItem(){
        global $mysql;
        if(!$this->uid){
            throw500("FAILED TO INITLIZE ITEM: NO USER FOUND.");
        }
        $paramList = [
            "enable_card_switch",
            "card_switch",
            "random_switch",
            "extend_mods_key",
            "allow_test_func",
            "item1",
            "item2",
            "item3",
            "item4",
            "item5",
            "item6",
            "item7",
            "item8",
            "item9",
            "item10",
            "item11",
            "item12",
            "item13",
            "item14",
            "item15",
            "seal1",
            "seal2",
            "seal3",
            "seal4",
            "aqours_flag",
        ];

        $params_ = $mysql->query("SELECT * FROM user_params WHERE user_id = ?", [$this->uid])->fetchAll();
        $params = [];
        foreach($params_ as $i){
            $params[$i['param']] = (int)$i['value'];
        }
        $this->_paramsAppend = [];
        //数据库不存在某样物品的时候设0
        foreach($paramList as $i){
            if(!isset($params[$i])){
                $params[$i] = 0;
                $this->_paramsAppend[] = $i; //并把他添加到要用INSERT的列表里
            }
        }
        //额外的卡组权限设定表
        $cardSwitch = $mysql->query("SELECT stat FROM user_card_switch WHERE user_id = ?", [$this->uid])->fetch();
        if($cardSwitch && $cardSwitch['stat'] == "1"){
            $params['enable_card_switch'] = 1;
        }

        //为了方便访问的别名
        $params['social_point'] = &$params['item2'];
        $params['coin']         = &$params['item3'];
        $params['loveca']       = &$params['item4'];

        $this->params = $params;
    }

    public function initUser(){
        global $mysql;
        $user = $mysql->query("SELECT name, introduction, level, exp, award, background FROM users WHERE user_id = ?", [$this->uid])->fetch();
        $this->user = $user;
        $this->user['level']            = (int)$this->user['level'];
        $this->user['exp']              = (int)$this->user['exp'];
        $this->user['award']            = (int)$this->user['award'];
        $this->user['background']       = (int)$this->user['background'];
    }

    public function saveAll(){
        global $mysql;
        $mysql->query("UPDATE users SET name=?, introduction=?, level=?, exp=?, award=?, background=? WHERE user_id=?", [
            $this->user['name'], $this->user['introduction'], $this->user['level'], $this->user['exp'], $this->user['award'], $this->user['background'], $this->uid
        ]);
        foreach($this->params as $k => $v){
            if(in_array($k, ["social_point", "coin", "loveca"])){
                continue;
            }
            if(in_array($k, $this->_paramsAppend)){
                $mysql->query("INSERT INTO user_params VALUES(?, ?, ?)", [$this->uid, $k, $v]);
            }else{
                $mysql->query("UPDATE user_params SET value = ? WHERE user_id = ? AND param = ?", [$v, $this->uid, $k]);
            }
        }
    }
}