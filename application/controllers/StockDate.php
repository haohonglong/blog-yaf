<?php
use base\controller\ControllerBase;
use Yaf\Registry;

class StockDateController extends ControllerBase {
	
	public function indexAction() {
		$errors = [];
		$stock_id = $this->getRequest()->getQuery("stock_id", null);
		if(!isset($stock_id) || empty($stock_id)) { $errors["stock_id"] = "stock_id 参数是必须的"; }

        if(empty($errors)){
			$query = StockDateModel::getListByStockId($stock_id);
			if(!isset($query)){
				$data['status'] = 1;
				$data['message'] = '数据不存在';
			}else{
				$data['status'] = 0;
				$data['message'] = '数据获取成功';
				$data['data'] = $query;
			}

		}else{
			$data['status'] = 1;
            $data['errors'] = $errors;
            $data['message'] = '错误';
		}
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
	}


	public function getOneAction() {
		$errors = [];
		$stock_id = $this->getRequest()->getPost("stock_id", null);
		$stock_date_at = $this->getRequest()->getPost("stock_date_at", null);

		if(!isset($stock_id) || empty($stock_id)) { $errors["stock_id"] = "stock_id 参数是必须的"; }

		if(empty($errors)){
			$query = StockModel::getLastOneByStockId($stock_id);
			if(!isset($query)){
				$data['status'] = 0;
				$data['message'] = '数据不存在';
			}else{
				$data['status'] = 1;
				$data['message'] = '数据获取成功';
				$data['data'] = $query;
			}

		}else{
			$data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '添加失败';
		}
		
        

        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
	}
	
}