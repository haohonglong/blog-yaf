<?php


class GoodsController extends Base {
	public function indexAction() {
        $data = GoodsModel::listAll();
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        return false;
    }

    public function generateIdAction() {
	    echo BillsModel::generateId();
	    exit;
    }


    public function addAction() {
	    $errors = [];
        $shop_id = $this->getRequest()->getPost("shop_id", "");
        $bill_id = $this->getRequest()->getPost("bill_id", "");
        $discount = $this->getRequest()->getPost("discount", 0);
        $create_by = $this->getRequest()->getPost("create_by", "");

        $name = $this->getRequest()->getPost("name", "");
        $number = $this->getRequest()->getPost("number", "");
        $weight = $this->getRequest()->getPost("weight", "");
        $single_price = $this->getRequest()->getPost("single_price", "");
        $final_price = $this->getRequest()->getPost("final_price", "");

        

        if(empty($shop_id)){
            $errors["shop_id"] = "shop_id 是必须的";
        }
        if(!is_numeric($discount)){
            $errors["discount"] = "discount 是必须的";
        }
        if(empty($bill_id)){
            $errors["bill_id"] = "bill_id 是必须的";
        }
        if(empty($create_by)){
            $errors["create_by"] = "create_by 是必须的";
        }



        foreach ($name as $k => $v) {
            if(empty($name[$k])) {
                $errors['name'][$k] = "name 是必须的";
            }
            if(empty($number[$k])) {
                $errors['number'][$k] = "number 是必须的";
            }
            if(empty($weight[$k])){
                $errors['weight'][$k] = "weight 是必须的";
            }
            if(empty($single_price[$k])){
                $errors['single_price'][$k] = "single_price 是必须的";
            }
            if(empty($final_price[$k])){
                $errors['final_price'][$k] = "final_price 是必须的";
            }
        }
        $create_by = strtotime($create_by);
        $update_by = $create_by;

        $data = [];
        if(empty($errors)){


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
