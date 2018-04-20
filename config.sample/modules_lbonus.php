<?php
//登录奖励
//每行代表一个奖励
//括号内左侧为物品种类，右侧为物品数量

/* 可用的物品种类：
loveca:心
coin:金币
social:友情点
ticket:单抽券
s_ticket:辅助券
数字:以该数字为编号的卡片
*/

//PCF的默认设置为官服的登录奖励
$login_bonus_list=[
  ['loveca', 3],
  ['coin', 3000],
  ['social', 100],
  ['coin', 3000],
  ['social', 100],
  ['loveca', 3],
  ['coin', 3000],
  ['social', 100],
  ['coin', 10000],
  ['social', 100],
  ['loveca', 3],
  ['coin', 3000],
  ['social', 500],
  ['coin', 3000],
  ['social', 100],
  ['loveca', 3],
  ['coin', 3000],
  ['social', 100],
  ['coin', 10000],
  ['social', 100],
  ['loveca', 3],
  ['coin', 3000],
  ['social', 100],
  ['coin', 3000],
  ['social', 500],
  ['loveca', 3],
  ['social', 100],
  ['coin', 3000],
  ['social', 100],
  ['loveca', 3],
  ['social', 1000]
];

//通算课题奖励
$total_login_bonus_list = [
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
];