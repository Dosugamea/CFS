<?php
//banner.php 首页显示的banner相关module

//banner/bannerList 获取banner列表 不返回的话不影响使用，但教程会卡在第8步
function banner_bannerList() {
  return json_decode('{
    "time_limit": "'.Date('Y').'-12-31 23:59:59",
    "member_category_list": [{
      "member_category": 1,
      "banner_list": [{
        "banner_type": 0,
        "target_id": 39,
        "asset_path": "assets\/image\/event\/banner\/e_fs_04.png",
        "asset_path_se": "assets\/image\/event\/banner\/e_fs_04se.png",
        "master_is_active_event": true
      }, {
        "banner_type": 0,
        "target_id": 35,
        "asset_path": "assets\/image\/event\/banner\/e_bt_03.png",
        "asset_path_se": "assets\/image\/event\/banner\/e_bt_03se.png",
        "master_is_active_event": true
      }]
    }, {
      "member_category": 2,
      "banner_list": [{
        "banner_type": 0,
        "target_id": 39,
        "asset_path": "assets\/image\/event\/banner\/e_fs_04.png",
        "asset_path_se": "assets\/image\/event\/banner\/e_fs_04se.png",
        "master_is_active_event": true
      }, {
        "banner_type": 0,
        "target_id": 35,
        "asset_path": "assets\/image\/event\/banner\/e_bt_03.png",
        "asset_path_se": "assets\/image\/event\/banner\/e_bt_03se.png",
        "master_is_active_event": true
      }]
    }]
  }');
}

?>