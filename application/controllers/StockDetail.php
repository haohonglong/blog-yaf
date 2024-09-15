<?php
use Yaf\Registry;

class StockDetailController extends Base {
	public function indexAction() {
		$stock_id   = $this->getRequest()->getQuery("stock_id", null);
		
        $data = StockDetailModel::getList($stock_id);
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
	}
	
	public function addAction() {
		$errors = [];
		$stock_id = $this->getRequest()->getPost("stock_id", null);
		$stock_type = (int)$this->getRequest()->getPost("stock_type", 0);
		$stock_price = $this->getRequest()->getPost("stock_price", 0);
		$stock_deal_total = (int)$this->getRequest()->getPost("stock_deal_total", 0);
		$created_at = $this->getRequest()->getPost("created_at", null);
		$stock_detail_remark = $this->getRequest()->getPost("stock_detail_remark", "");

		$open = $this->getRequest()->getPost("open", 0.00);
		$close = $this->getRequest()->getPost("close", 0.00);
		$lup = $this->getRequest()->getPost("lup", 0.00);
		$ldown = $this->getRequest()->getPost("ldown", 0.00);
		$hight = $this->getRequest()->getPost("hight", 0.00);
		$low = $this->getRequest()->getPost("low", 0.00);
		$average = $this->getRequest()->getPost("average", 0.00);
		$change = $this->getRequest()->getPost("change", 0.00);
		$amplitude = $this->getRequest()->getPost("amplitude", 0.00);
		
		if(!isset($stock_id) || empty($stock_id)) { $errors["stock_id"] = "stock_id 参数是必须的"; }
		if(!isset($stock_price) || empty($stock_price)) { $errors["stock_price"] = "stock_price 参数是必须的"; }
		if(!isset($created_at) || empty($created_at)) { $errors["created_at"] = "created_at 参数是必须的"; }

		if((float)$stock_price <= 0){
			$errors["stock_price"] = "股票价格必须大于0";
		}

		if($stock_type > 0 && $stock_deal_total < 1){
			$errors["stock_deal_total"] = "除无操作外股票数量最少为1";

		}
		if(0 == $stock_type && $stock_deal_total > 0){
			$errors["stock_deal_total"] = "无操作时股票数量必须为0";
		}

		if(2 == $stock_type && 0 == $stock_deal_total){
			$errors["stock_deal_total"] = "卖出时股票数量不能为0";
		}

		if(empty($errors)) {
			$Stock = new StockDetailModel($stock_id
			,$stock_price
			,$stock_type
			,$stock_deal_total
			,$created_at
			,$stock_detail_remark

			,$open
			,$close
			,$lup
			,$ldown
			,$hight
			,$low
			,$average
			,$amplitude
			,$change
		
		
		);
			$data = $Stock->create();
		} else {
			$data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '添加失败';
		}

		echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
	}

	public function deleteAction() {
        $message = "";
        $id   = $this->getRequest()->getQuery("id", "");
        if(empty($id)){
            $message = "请选择要删除的数据";
        }
        $data = [];
        if(empty($message)){
            $data = StockDetailModel::delete($id);
        }else{
            $data['status'] = 1;
            $data['message'] = $message;
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
    }
}