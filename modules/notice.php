<?php

//notice/noticeMarquee 应该是主界面滚动显示的通知（比如继承码到期）
function notice_noticeMarquee() {
  return json_decode('{
            "item_count": 0,
            "marquee_list": []
        }');
}
//notice/noticeFriendVariety 新着信息 返回空
function notice_noticeFriendVariety() {
	  return json_decode('{
            "item_count": 0,
            "notice_list": []
        }');
}
//notice/noticeFriendGreeting 新着信息 返回空
function notice_noticeFriendGreeting() {
	  return json_decode('{
            "item_count": 0,
            "notice_list": []
        }');
}
//notice/noticeUserGreetingHistory 好友PY历史 返回空
function notice_noticeUserGreetingHistory() {
	  return json_decode('{
            "item_count": 0,
			"has_next": false,
            "notice_list": []
        }');
}
?>