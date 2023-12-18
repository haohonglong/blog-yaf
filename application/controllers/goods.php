<?php


class GoodsController extends Base {
	public function indexAction() {
        $data = GoodsModel::listAll();
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        return false;
    }

    public function statisticsAction() {
        $goodsname_id = $this->getRequest()->getPost("goodsname_id", 0);
        $start_date = $this->getRequest()->getPost("start_date", null);
        $end_date = $this->getRequest()->getPost("end_date", null);
        $G = $this->getRequest()->getPost("G", false);
        if('true' == $G) {
            $G = true;
        } else {
            $G = false;
        }
        
        
        $data = GoodsModel::statistics($goodsname_id, strtotime($start_date), strtotime($end_date), $G);
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        return false;
    }


    public function addAction() {
	    $errors = [];
        $shop_id = $this->getRequest()->getPost("shop_id", "");
        $bill_id = $this->getRequest()->getPost("bill_id", "");
        $points = $this->getRequest()->getPost("points", 0);
        $discount = $this->getRequest()->getPost("discount", 0);
        $create_at = $this->getRequest()->getPost("create_at", "");

        $goodsname_ids = $this->getRequest()->getPost("goodsname_ids", null);
        $codes = $this->getRequest()->getPost("codes", null);
        $unit_ids = $this->getRequest()->getPost("unit_ids", null);
        $numbers = $this->getRequest()->getPost("numbers", null);
        $weights = $this->getRequest()->getPost("weights", null);
        $single_prices = $this->getRequest()->getPost("single_prices", null);
        $final_prices = $this->getRequest()->getPost("final_prices", null);
        

        if(!isset($goodsname_ids)) {
            $errors["goodsname_id"] = "商品名必选的";
        }

        if(!is_array($goodsname_ids)) {
            $errors["goodsname_id"] = "goodsname_ids 必须是一个数组";
        }
        if(!isset($unit_ids)) {
            $errors["unit_id"] = "计量单位是必须的";
        }

        if(!isset($numbers)) {
            $errors["number"] = "numbers 参数是必须的";
        }

        if(!isset($weights)) {
            $errors["weight"] = "weights 参数是必须的";
        }

        if(!isset($single_prices)) {
            $errors["single_prices"] = "single_prices 参数是必须的";
        }

        if(!isset($final_prices)) {
            $errors["final_prices"] = "final_prices 参数是必须的";
        }
        

        if(empty($shop_id)){
            $errors["shop_id"] = "是必须的";
        }
        if(!is_numeric($discount)){
            $discount = 0;
        }
        if(empty($bill_id)){
            $errors["bill_id"] = "是必须的";
        }
        if(empty($create_at)){
            $errors["create_at"] = "是必须的";
        }

        if(empty($errors)){
            foreach ($goodsname_ids as $k => $v) {
                if(empty($goodsname_ids[$k])) {
                    $errors['goodsname_id'][$k] = "是必须的";
                }
                if(empty($numbers[$k])) {
                    $errors['number'][$k] = "是必须的";
                }
                if(empty($weights[$k])){
                    $errors['weight'][$k] = "是必须的";
                }
                if(empty($unit_ids[$k])) {
                    $errors['unit_id'][$k] = "是必须的";
                }
                if(empty($single_prices[$k]) || !(floatval($single_prices[$k]) > 0)){
                    $errors['single_price'][$k] = "请填写金额，且不能为0";
                }
                if(!(floatval($final_prices[$k]) > 0)){
                    $errors['final_price'][$k] = "不能为零";
                }
            }
            $create_at = strtotime($create_at);
        }
        
        $data = [];
        if(empty($errors)){
            $bill = new BillsModel($bill_id, $points, $discount, $create_at);
            $goods = new GoodsModel(
                                    $bill,
                                    $shop_id, $bill_id,
                                    $codes, $goodsname_ids, $numbers, $weights,
                                    $unit_ids, $single_prices, $final_prices, $create_at
                                );
            $data = $goods->create();

        }else{
            $data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '添加失败';
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function editAction() {

    }
    public function deleteAction() {

    }

}
