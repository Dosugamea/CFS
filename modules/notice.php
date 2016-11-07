<?php

//notice/noticeMarquee 应该是主界面滚动显示的通知（比如继承码到期）
function notice_noticeMarquee() {
  return json_decode('{
            "item_count": 0,
            "marquee_list": []
        }');
}

?>