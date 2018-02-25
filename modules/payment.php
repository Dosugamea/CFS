<?php
//payment.php 谁要敢把商品ID改成实际存在的我跟他没完——开发者

//payment/productList 获取商品列表
function payment_productList() {
  return json_decode('{
			"sns_product_list":[],
            "product_list": []
        }');
}
//payment/month 购买历史
function payment_month() {
	$ret['item_count'] = 0;
	$ret['payment_month_list'] = [];
	return $ret;
}
?>