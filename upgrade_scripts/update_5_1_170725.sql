-- --------------------------------------------------------

--
-- 表的结构 `tmp_duty_result`
--

CREATE TABLE `tmp_duty_result` (
  `user_id` int(11) NOT NULL,
  `duty_event_room_id` int(11) NOT NULL,
  `result` text NOT NULL,
  `reward` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `tmp_duty_room`
--

CREATE TABLE `tmp_duty_room` (
  `duty_event_room_id` int(11) NOT NULL,
  `difficulty` int(11) NOT NULL,
  `live_difficulty_id` int(11) NOT NULL,
  `player_ready_1` tinyint(1) NOT NULL DEFAULT '0',
  `player_ready_2` tinyint(1) NOT NULL DEFAULT '0',
  `player_ready_3` tinyint(1) NOT NULL DEFAULT '0',
  `player_ready_4` tinyint(1) NOT NULL DEFAULT '0',
  `start_flag` tinyint(1) NOT NULL DEFAULT '0',
  `player1` int(11) NOT NULL,
  `player2` int(11) NOT NULL DEFAULT '0',
  `player3` int(11) NOT NULL DEFAULT '0',
  `player4` int(11) NOT NULL DEFAULT '0',
  `full_flag` tinyint(1) NOT NULL DEFAULT '0',
  `event_chat_id_1` varchar(10) NOT NULL DEFAULT '',
  `event_chat_id_2` varchar(10) NOT NULL DEFAULT '',
  `event_chat_id_3` varchar(10) NOT NULL DEFAULT '',
  `event_chat_id_4` varchar(10) NOT NULL DEFAULT '',
  `ended_flag_1` tinyint(1) NOT NULL DEFAULT '0',
  `ended_flag_2` tinyint(1) NOT NULL DEFAULT '0',
  `ended_flag_3` tinyint(1) NOT NULL DEFAULT '0',
  `ended_flag_4` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp` int(11) NOT NULL,
  `card_switch` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `tmp_duty_user_room`
--

CREATE TABLE `tmp_duty_user_room` (
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `pos_id` int(11) NOT NULL,
  `deck_id` int(11) NOT NULL
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- --------------------------------------------------------