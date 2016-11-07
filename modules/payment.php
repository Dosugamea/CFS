<?php
//payment.php 谁要敢把商品ID改成实际存在的我跟他没完——开发者

//payment/productList 获取商品列表
function payment_productList() {
  return json_decode('{
            "product_list": [{
                "apple_product_id": "fake.ident",
                "description": null,
                "google_product_id": "fake.ident",
                "insert_date": "2013-06-05 11:30:00",
                "name": "1 Love Gems",
                "price": 99,
                "product_id": "fake.ident",
                "sns_coin": 1,
                "update_date": "2013-06-05 11:30:00"
            }]
        }');
}

?>