<?php
/* configManager.php
 * 管理各种配置
*/

class configManager{
	public $configList;
	public function __construct(){
		$this->configList = [
			"basic",
			"database",
			"event",
			"maintenance",
			"mail",
			"reg",
			"tos",
			"m_award",
			"m_battle",
			"m_battle_reward",
			"m_challenge",
			"m_download",
			"m_duty",
			"m_exchange",
			"m_festival",
			"m_lbonus",
			"m_login",
			"m_nlbonus",
			"m_login",
			"m_notice",
			"m_personalnotice"
		];
		foreach($this->configList as $i){
			$funcName = "_init_".$i;
			$this->$funcName();
		}
	}

	private function initCommon($defaultConfig, $name){
		global $logger;
		$confPath = __DIR__."/../config/".$name.".json";
		if(is_file($confPath)){
			$conf = json_decode(file_get_contents($confPath), true);
			if(count($conf) < count($defaultConfig)){
				$writeFlag = true;
			}else{
				$writeFlag = false;
			}

			foreach($conf as $k => $v){
				$defaultConfig[$k] = $v;
			}

			if($writeFlag){
				file_put_contents($confPath, json_encode($defaultConfig, JSON_PRETTY_PRINT));
			}
		}else{
			$logger->e("No config file found, use default config");
			file_put_contents($confPath, json_encode($defaultConfig, JSON_PRETTY_PRINT));
		}
		
		//防止有关键配置未初始化
		$confError = false;
		foreach($defaultConfig as $k => $v){
			if($v === "" || $v === NULL){
				$logger->f("配置未初始化： ".$name.".json -> ".$k);
				$confError = true;
			}
		}
		if($confError){
			header("HTTP/1.1 500 Internal Service Error");
			print("配置文件检查出错，请参考日志");
			die();
		}

		$this->$name = $defaultConfig;
		return $defaultConfig;
	}

	public function saveAll(){
		foreach($this->configList as $i){
			$confPath = __DIR__."/../config/".$i.".json";
			file_put_contents($confPath, json_encode($this->$i));
		}
	}

	private function _init_basic(){
		//基础配置
		$defaultConfig = [
			"server_ver"				=> "0.0",	//客户端版本号
			"release_info"				=> [],		//release key，解密db所需
			"pub_key"					=> "",		//RSA公钥，用于X-Message-Sign
			"priv_key"					=> "",		//RSA私钥，同上
			"bundle_ver"				=> "1.0",	//最低客户端版本
			"max_live_difficulty_id"	=> 9999,	//？？？
			"max_unit_id"				=> 9999,	//最大社员数（暂时没用上）
			"mdl_address"				=> "http://dl-plserver.lovelivesupport.com/external/",	//mdl的前缀域名
			"admin_pw"					=> ""		//后台管理密码
		];
		$this->initCommon($defaultConfig, "basic");
	}

	private function _init_database(){
		//数据库配置
		$defaultConfig = [
			"mysql_server"		=> "127.0.0.1",		//MySQL的服务器IP，默认为本地
			"mysql_user"		=> "root",			//MySQL用户名
			"mysql_pass"		=> "",				//MySQL密码
			"mysql_db"			=> "lovelive",		//数据库名

			//redis相关
			"redis_server"		=> "127.0.0.1",
			"redis_password"	=> "",
			"redis_number"		=> 1,				//redis数据库号

			//默认数据库目录，建议不要更改
			"unit_db"			=> "db/unit.db_",
			"live_db"			=> "db/live.db_",
			"marathon_db"		=> "db/marathon.db_",
			"battle_db"			=> "db/battle.db_",
			"festival_db"		=> "db/festival.db_",
			"scenario_db"		=> "db/scenario.db_",
			"subscenario_db"	=> "db/subscenario.db_",
			"secretbox_db"		=> "db/secretbox_svonly.db_",
			"event_db"			=> "db/event_common.db_",
		];
		$this->initCommon($defaultConfig, "database");
	}

	private function _init_event(){
		//活动配置，此处仅为模板
		$template = [
			"name"			=> "This is a template.",
			"start_date"	=> "2000-01-01 00:00:00",
			"end_date"		=> "2000-01-01 00:00:00",
			"event_id"		=> 0,
			"asset_path"	=> false,
			"asset_path_se"	=> false,
			"result_path"	=> false,
			"description"	=> "Fake event"
		];
		$defaultConfig = [
			"marathon"	=> $template,
			"battle"	=> $template,
			"festival"	=> $template,
			"challenge"	=> $template,
			"duty"		=> $template
		];
		$this->initCommon($defaultConfig, "event");
	}

	private function _init_maintenance(){
		$defaultConfig = [
			"maintenance"				=> false,	//现在是否在维护
			"bypass_maintenance"		=> [],		//数组中的用户略过维护
			"maintenance_start"			=> "2000-01-01 00:00:00",	//维护开始时间
			"maintenance_end"			=> "2000-01-01 00:00:01",	//维护结束时间
			"maintenance_info"			=> "现在正在进行维护<br>\n给您带来的不便，敬请谅解",
		];
		$this->initCommon($defaultConfig, "maintenance");
	}

	private function _init_mail(){
		$defaultConfig = [
			"smtp_host"					=> "smtp.qq.com",	//SMTP服务器域名
			"mail_sender"				=> "PCFServer",		//发件人名字
			"mail_account"				=> "mail@example.com",	//发件人账户
			"mail_password"				=> "Passw0rd",
		];
		$this->initCommon($defaultConfig, "mail");
	}

	private function _init_reg(){
		$defaultConfig = [
			"enable_ssl"				=> false,					//登录、注册页面是否使用SSL
			"ssl_domain"				=> "www.your-domain.com",	//SSL跳转到的域名，须绑定PLS所在网站
			"allow_reg"					=> true,					//是否允许注册
			"enable_web_reg"			=> true,					//【未完工，不会判断，强制弹窗注册】注册类型：true=弹窗注册 false=游戏内注册，游戏内注册的UID将从最大的UID处开始递增分配
			"disable_card_by_default"	=> true,					//新用户是否禁用卡片
			"all_card_by_default"		=> true,					//新用户是否送全套卡片
			"default_deck_web"			=> [50, 51, 52, 53, 49, 54, 55, 56, 57], //弹窗注册初始卡组列表
			"default_deck"				=> [						//游戏内注册初始卡组列表
				[13, 9, 8, 23, 49, 24, 21, 20, 19],
				[13, 9, 8, 23, 50, 24, 21, 20, 19],
				[13, 9, 8, 23, 51, 24, 21, 20, 19],
				[13, 9, 8, 23, 52, 24, 21, 20, 19],
				[13, 9, 8, 23, 53, 24, 21, 20, 19],
				[13, 9, 8, 23, 54, 24, 21, 20, 19],
				[13, 9, 8, 23, 55, 24, 21, 20, 19],
				[13, 9, 8, 23, 56, 24, 21, 20, 19],
				[13, 9, 8, 23, 57, 24, 21, 20, 19]
			],
		];
		$this->initCommon($defaultConfig, "reg");
	}

	private function _init_tos(){
		$defaultConfig = [
			"tos_id"	=> 1
		];
		$this->initCommon($defaultConfig, "tos");
	}

	private function _init_m_award(){
		$defaultConfig = [
			"default_awards"	=> []
		];
		$this->initCommon($defaultConfig, "m_awards");
	}

	private function _init_m_battle(){
		$defaultConfig = [
			"score_match_live_lifficulty_ids"	=> [
				1	=> [['Live_0804.json', 0]],
				2	=> [],
				3	=> [],
				4	=> [],
				5	=> [],
				6	=> [],
			]
		];
		$this->initCommon($defaultConfig, "m_battle");
	}

	private function _init_m_battle_reward(){
		$defaultConfig = [
			"battle_reward_info"	=> [
				['pt'=>20,'id'=>3000,'amount'=>5000],
				['pt'=>100,'id'=>3002,'amount'=>300],
				['pt'=>500,'id'=>3001,'amount'=>1],
			]
		];
		$this->initCommon($defaultConfig, "m_battle_reward");
	}

	private function _init_m_challenge(){
		$defaultConfig = [
			"challenge_live_difficulty_ids"	=> [
				1 => [
					1 => [],
					2 => [],
					3 => [],
					4 => [],
					5 => [],
				],
				2 => [
					1 => [],
					2 => [],
					3 => [],
					4 => [],
					5 => [],
				],
				3 => [
					1 => [],
					2 => [],
					3 => [],
					4 => [],
					5 => [],
				],
				4 => [
					1 => [],
					2 => [],
					3 => [],
					4 => [],
					5 => [],
				],
			]
		];
		$this->initCommon($defaultConfig, "m_challenge");
	}

	private function _init_m_download(){
		$defaultConfig = [

		];
		$this->initCommon($defaultConfig, "m_download");
	}

	private function _init_m_duty(){
		$defaultConfig = [
			"duty_live_lifficulty_ids"	=> [
				1 => [],
				2 => [],
				3 => [],
				4 => [],
				5 => [],
				6 => [],
			]
		];
		$this->initCommon($defaultConfig, "m_duty");
	}

	private function _init_m_exchange(){
		$defaultConfig = [
			"exchange"	=> [
				[
					"exchange_item_id"		=> 20001,
					"title"					=> "補助チケット",
					"cost_list"				=> [
						[
							"rarity"		=> 2,
							"cost_value"	=> 30
						],[
							"rarity"		=> 3,
							"cost_value"	=> 2
						],
					],
					"rank_max_flag"			=> false,
					"end"					=> false,	//结束时间，false为永久
					"max_item_count"		=> 0,		//最大兑换数量
					"item"					=> ["s_ticket", 1]
				],
			]
		];
		$this->initCommon($defaultConfig, "m_exchange");
	}

	private function _init_m_festival(){
		$defaultConfig = [
			"festival_live_lifficulty_ids"	=> [
				1 => [
					1 => [],
					2 => [],
					3 => [],
					4 => [],
				],
				2 => [
					1 => [],
					2 => [],
					3 => [],
					4 => [],
				],
				3 => [
					1 => [],
					2 => [],
					3 => [],
					4 => ['Live_0084.json', 'Live_0089.json', 'Live_0116.json', 'Live_0079.json', 'Live_0124.json', 'Live_0540.json', 'Live_0478.json', 'Live_0393.json'],
				],
			]
		];
		$this->initCommon($defaultConfig, "m_festival");
	}

	private function _init_m_lbonus(){
		$defaultConfig = [
			//普通登录奖励列表
			"login_bonus_list" => [
				['coin', 250000],
				['coin', 200000],
				['r_seal', 1],
				[386, 3],
				['loveca', 5],
				['coin', 150000],
				['r_seal', 1],
				['coin', 200000],
				[386, 3],
				['loveca', 5],
				['coin', 150000],
				['r_seal', 1],
				['coin', 200000],
				[386, 3],
				['loveca', 5],
				['coin', 150000],
				['r_seal', 1],
				['coin', 200000],
				[386, 3],
				['loveca', 5],
				['coin', 150000],
				['r_seal', 1],
				['coin', 200000],
				[386, 3],
				['loveca', 5],
				['coin', 150000],
				['r_seal', 1],
				['coin', 200000],
				[386, 3],
				['loveca', 5],
				['coin', 150000],
				['r_seal', 1],
				['coin', 200000],
				[386, 3],
				['loveca', 5],
			],
			//通算课题
			"total_login_bonus_list" => [
				1		=> ['ticket', 1],
				5		=> ['ticket', 1],
				10		=> ['ticket', 2],
				20		=> ['ticket', 2],
				30		=> ['ticket', 2],
				40		=> ['ticket', 2],
				50		=> ['ticket', 5],
				80		=> ['s_ticket', 2],
				100		=> ['s_ticket', 5],
				150		=> ['s_ticket', 2],
				200		=> ['s_ticket', 5],
				250		=> ['s_ticket', 2],
				300		=> ['s_ticket', 5],
				350		=> ['s_ticket', 2],
				365		=> ['s_ticket', 10],
				400		=> ['s_ticket', 5],
				500		=> ['s_ticket', 5],
				600		=> ['s_ticket', 5],
				700		=> ['s_ticket', 5],
				800		=> ['s_ticket', 5],
				900		=> ['s_ticket', 5],
				1000	=> ['s_ticket', 20]
			]
		];
		$this->initCommon($defaultConfig, "m_lbonus");
	}

	private function _init_m_login(){
		$defaultConfig = [
			"base_key"			=> "eit4Ahph4aiX4ohmephuobei6SooX9xo",
			"application_key"	=> ""
		];
		$this->initCommon($defaultConfig, "m_login");
	}

	private function _init_m_nlbonus(){
		$defaultConfig = [
			"nlbonus"			=> [
				[
					"nlbonus_id"	=> "((date('Y')-2000)*-18-10)",		//登录奖励的唯一ID，支持代码自动生成
					"detail_text"	=> "2月10日是松浦果南的生日！
					为了庆祝这一天，本日登陆的所有用户
					都能获得「ラブカストーン50個」哦♪",
					"bg_asset"		=> "assets/image/ui/login_bonus_extra/birthday_Aqours_3_Kanan.png",	//背景图
					"start"			=> "date('Y').'-02-10 00:00:00'",	//起始时间
					"end"			=> "date('Y').'-02-10 23:59:59'",	//中止时间
					"items"			=> [
						["loveca", 50, "松浦果南酱生日快乐！"]
					],
				],
			]
		];
		$this->initCommon($defaultConfig, "m_nlbonus");
	}

	private function _init_m_notice(){
		//滚动通知
		$defaultConfig = [
			"noticeMarquee"	=> [[
				"marquee_id"	=> 1,
				"text"			=> "第二次PCF全服大会开办中！详情请看公告。",
				"text_color"	=> 0,	//颜色，0为黑色，1为红色
				"display_place"	=> 0,	//没用
				"start_date"	=> "2017-12-09 00:00:00",
				"end_date"		=> "2017-12-31 00:00:00",
			]]
		];
		$this->initCommon($defaultConfig, "m_notice");
	}

	private function _init_m_personalnotice(){
		$defaultConfig = [
			//全服通用的个人通知
			"global_notice"	=> [[
				"notice_id"		=> 1,
				"type"			=> 1,	//1带勾选框，2不带
				"title"			=> "Tos更新公告",
				"contents"		=> "本服Tos已于2017/11/9更新，请在下一步提示的Tos界面认真阅读。",
			]]
		];
		$this->initCommon($defaultConfig, "m_personalnotice");
	}
}