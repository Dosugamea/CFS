<?php
//课题界面 ！待修复！
function achievement_unaccomplishList() {
	$ret = [];
	$ret[] = json_decode('{
			"achievement_category_id": 1,
			"count": 0,
			"achievement_list": []
		}',true);
	return $ret;
}
?>