<?php
use base\controller\ControllerBase;
use Yaf\Registry;

class StockController extends ControllerBase {

	private function putTheDataToTheStockDetailModel($userid, $stock_id, $stock_code, $stock_name, $data){
		
		if(0 == $data['open']){
			$result['status'] = 3;
			$result['message'] = $stock_code. '-' .$stock_name.': 开盘不能是零';
			return $result;
		}

		if(0 == $data['close']){
			$result['status'] = 3;
			$result['message'] = $stock_code. '-' .$stock_name.': 收盘不能是零';
			return $result;
		}

		if(0 == $data['volume']){
			$result['status'] = 3;
			$result['message'] = $stock_code. '-' .$stock_name.': 成交量不能是零';
			return $result;
		}

		if(0 == $data['highest']){
			$result['status'] = 3;
			$result['message'] = $stock_code. '-' .$stock_name.': 最高值不能是零';
			return $result;
		}

		if(0 == $data['lowest']){
			$result['status'] = 3;
			$result['message'] = $stock_code. '-' .$stock_name.': 最低值不能是零';
			return $result;
		}



		$data['stock_type'] = $data['stock_type'] ?? 0;
		$data['stock_deal_total'] = $data['stock_deal_total'] ?? 0;
		$data['stock_detail_remark'] = $data['stock_detail_remark'] ?? "";

		$StockDetailModel = new StockDetailModel($userid, $stock_id, $data);
		$data2 = $StockDetailModel->create2();
		
		

		switch ($data2['status']) {
			case 0:
			case 3:
				$result['status'] = 3;
				$result['message'] = $data2['message'];
				break;
			
			case 1:
				$result['status'] = 0;
				$result['message'] = "$stock_code($stock_id)-$stock_name: 已更新完成";
				$data['updated_at'] = $data['created_at'];
				(new StockModel(null, null, $stock_code, null, $data))->updateTests();
				
				break;
				
			default:
		}

		return $result;


	}
	
	private function feach($item){
		return StockModel::feach($item);
	}
/*
	private function feach2($item){
		
		$result = [];
		$stock_code = $item['stock_code'];
		$stock_name = $item['stock_name'];
		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => 'https://finance.pae.baidu.com/vapi/v1/getquotation?all=1&srcid=5353&pointType=string&group=quotation_minute_ab&market_type=ab&new_Format=1&finClientType=pc&query='.$stock_code.'&code='.$stock_code,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			// CURLOPT_NOSIGNAL => 1,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
		]);
		$data = curl_exec($curl);
		curl_close($curl);
		$data = json_decode($data, true);
		// var_dump($data);
		// print_r($data);
		
		if(!isset($data)){
			$result['status'] = 2;
			$result['message'] = "{$stock_code}-{$stock_name} url地址错误或者是休市日";
		} else {
			try {
				$date = $data['Result']['update']['text'];
				$y = date('Y-');
				$created_at = $y.$date;
				$stock_price = $data['Result']['buyinfos'][0]['bidprice'];
				$data = $data['Result']['pankouinfos']['list'];
				// print_r($data);exit;


				$result = [
					'stock_price' => $stock_price,
					'created_at' => $created_at,
					'open' => $data[0]['value'],
					'close' => $data[3]['value'],
					'lup' => $data[15]['value'],
					'ldown' => $data[18]['value'],
					'highest'     => $data[1]['value'],
					'lowest'       => $data[4]['value'],
					'average'   => $data[13]['value'],
					'change'    => $data[6]['originValue'],
					'amplitude' => $data[19]['originValue'],
					'volume' => $data[2]['originValue'],
					'amount' => $data[5]['originValue'],
				];

				
			} catch (Exception $e) {
				$result['status'] = 1;
				$result['message'] = $e->getMessage();
			}
			

		}

		return $result;
	}

	private function feach3($item){
		$result = [];
		$stock_code = $item['stock_code'];
		$stock_name = $item['stock_name'];
		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => 'http://api.mairui.club/hsrl/ssjy/'. $stock_code .'/2436F02E-99D9-49C4-B1B5-BE4D187AF029',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
		]);
		$data = curl_exec($curl);
		curl_close($curl);
		$data = json_decode($data, true);
		// var_dump($data);
		// print_r($data);
		
		if(!is_array($data)){
			switch ($data) {
				case 101:
					$result['status'] = 1;
					$result['message'] = '当日的请求已超过版本限制，次日将会自动恢复';
					break;
				
				default:
					$result['status'] = 2;
					$result['message'] = "{$stock_code}-{$stock_name} url地址错误或者是休市日";
					break;
			}
			
		} else {
			try {

				$result = [
					'stock_price' => $data['p'],
					'created_at' => $data['t'],
					'open' => $data['o'],
					'close' => $data['yc'],
					'lup' => $item['lup'],
					'ldown' => $item['ldown'],
					'highest'     => $data['h'],
					'lowest'       => $data['l'],
					'average'   => $item['average'],
					'change'    => $data['hs'],
					'amplitude' => $data['zf'],
					'volume' => $data['v'],
					'amount' => $data['cje'],
				];

				
			} catch (Exception $e) {
				$result['status'] = 1;
				$result['message'] = $e->getMessage();
			}

			
		}
		
		return $result;
	}

*/
	public function getProgressAction(){
		header("X-Accel-Buffering: no");
		header("Content-Type: text/event-stream");
		header("Cache-Control: no-cache");
		$progress_id = $this->getRequest()->getQuery("progress_id", "123");
		$progress_id = "progress:{$progress_id}";


		$redis = Registry::get('redis');
		//发送消息
		
		$progress = 1;
		try {
			while($progress)
			{
				usleep(10000); // 0.01秒

				$progress = (int)$redis->get($progress_id);
				
				// $c = "event:" . PHP_EOL; //定义事件
				$c = "data: " . $progress . PHP_EOL; //推送内容
				echo $c . PHP_EOL;

				while (ob_get_level() > 0) {
					ob_end_flush();
				}
				flush();
				if (connection_aborted()) break;
            	if ($progress >= 100 || $progress == 0) break;
				
			}
			
		} catch (Exception $e) {
			$data=[];
			$data['status'] = 1;
			$data['message'] = $e->getMessage();
			echo json_encode($data, JSON_UNESCAPED_UNICODE);
		} finally {
			$redis->del($progress_id);
		}
		
		
		exit;

	}


	

	/**
	 * 获取API 接口数据并存入数据库
	 */
	public function feachAction(){
		$datas = [];
		$userid = $this->getRequest()->getPost("userid", 1);
		$stock_id = $this->getRequest()->getPost("stock_id", null);
		$progress_id = $this->getRequest()->getPost("progress_id", "123");
		$progress_id = "progress:{$progress_id}";
		
		$way = $this->getRequest()->getPost("way", '1');
		if(isset($stock_id) && !empty($stock_id)){
			$total = 1;
			$one = StockModel::getLastOneByStockId($stock_id);

			$data = $this->feach($one);

			if(!isset($data['status'])){
				$data = $this->putTheDataToTheStockDetailModel($userid, $stock_id, $one['stock_code'], $one['stock_name'], $data);
			}
			
			$datas['datas'][] = $data;
		} else {
			$redis = Registry::get('redis');
			
			try {
				$total = 0;
				$list = StockModel::getAllList();
				$i = 0;
				$len = count($list);
				
				foreach ($list as $item) {
					$redis->set($progress_id, intval((++$i)/$len * 100));

					$data = $this->feach($item);

					if(!isset($data['status'])){
						$data = $this->putTheDataToTheStockDetailModel($userid, $item['stock_id'], $item['stock_code'], $item['stock_name'], $data);
					}

					$datas['datas'][] = $data;
					
					if(2 == $data['status']){
						break;
					}
					if(3 == $data['status']){
						continue;
					}
					$total++;

					
					
	
				}
				$datas['total'] = $total;
				
			} catch (Exception $e) {
				$redis->del($progress_id);

				$data=[];
				$data['status'] = 1;
				$data['message'] = $e->getMessage();
				$datas['datas'][] = $data;
				echo json_encode($datas, JSON_UNESCAPED_UNICODE);
				exit;
				
			} finally {
				$redis->set($progress_id, 0);
				$redis->expire($progress_id, 60); // 1分钟后自动删除
				
			}

			// 	sleep($redis->get('auto_feach_stocks_delaytime'));

			

			

			
			
		}

		$datas['status'] = 0;
		echo json_encode($datas, JSON_UNESCAPED_UNICODE);
		exit;
		


	}


	public function indexAction() {
		$userid = $this->getRequest()->getPost("userid", 1);
		$size = $this->getRequest()->getPost("size", 1);
		$rows = $this->getRequest()->getPost("rows", 0);

        $query = StockModel::getList($userid, $size, $rows);
		
		if($query){
            $data['status'] = 0;
            $data['data'] = $query;
            $data['message'] = '获取成功';
        }else{
            $data['status'] = 1;
            $data['message'] = "没有数据";
        }

        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
	}
	
	public function addAction() {
		$errors = [];
		$userid = $this->getRequest()->getPost("userid", 1);
		$stock_id = $this->getRequest()->getPost("stock_id", null);
		$stock_code = $this->getRequest()->getPost("stock_code", null);
		$stock_name = $this->getRequest()->getPost("stock_name", null);
		$stock_remark = $this->getRequest()->getPost("stock_remark", "");
		$flag = $this->getRequest()->getPost("flag", 0);
		$tax = $this->getRequest()->getPost("tax", 5.00);
		if(!isset($stock_id) || empty($stock_id)) { $errors["stock_id"] = "stock_id 参数是必须的"; }
		if(!isset($stock_code) || empty($stock_code)) { $errors["stock_code"] = "stock_code 参数是必须的"; }
		if(!isset($stock_name) || empty($stock_name)) { $errors["stock_name"] = "stock_name 参数是必须的"; }

		if(!empty($stock_id) && StockModel::getById($stock_id)){
			$errors["stock_id"] = "已存在";
		}
		if(!empty($stock_name) && StockModel::getByName($stock_name)){
			$errors["stock_name"] = "已存在";
		}
		
		if(empty($errors)) {
			$Stock = new StockModel($userid, $stock_id, $stock_code, $stock_name, [
				'stock_remark' => $stock_remark,
				'flag' => $flag,
				'tax' => $tax,
			]);
			$data = $Stock->create();
		} else {
			$data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '添加失败';
		}

		echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
	}

	public function editAction() {
		$errors = [];
		$userid = $this->getRequest()->getPost("userid", 1);
		$stock_id = $this->getRequest()->getPost("stock_id", null);
		$stock_code = $this->getRequest()->getPost("stock_code", null);
		$stock_name = $this->getRequest()->getPost("stock_name", null);
		$stock_cost   = $this->getRequest()->getPost("stock_cost", null);
		$stock_remark = $this->getRequest()->getPost("stock_remark", "");
		$tax = $this->getRequest()->getPost("tax", 5.00);
		if(!isset($stock_id) || empty($stock_id)) { $errors["stock_id"] = "stock_id 参数是必须的"; }
		if(!isset($stock_code) || empty($stock_code)) { $errors["stock_code"] = "stock_code 参数是必须的"; }
		if(!isset($stock_name) || empty($stock_name)) { $errors["stock_name"] = "stock_name 参数是必须的"; }

		$stock_id_ = StockModel::getByName($stock_name);
		
		if(!is_null($stock_id_) &&  !empty($stock_name) && $stock_id != $stock_id_){
			$errors["stock_name"] = "已存在";
		}
		if(empty($errors)) {
			$Stock = new StockModel($userid, $stock_id, $stock_code, $stock_name, [
				'stock_remark' => $stock_remark,
				'stock_cost' => $stock_cost,
				'tax' => $tax,
			]);
			$data = $Stock->edit();
		} else {
			$data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '添加失败';
		}

		echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
	}

	public function deleteAction() {
		$userid = $this->getRequest()->getQuery("userid", 0);
        $message = "";
        $stock_id   = $this->getRequest()->getQuery("stock_id", "");
        if(empty($stock_id)){
            $message = "请选择要删除的数据";
        }
        $data = [];
        if(empty($message)){
            $data = StockModel::delete($userid, $stock_id);
        }else{
            $data['status'] = 1;
            $data['message'] = $message;
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
    }

	public function setLevelAction() {
        $message = "";
        $stock_id   = $this->getRequest()->getQuery("stock_id", "");
        $level   = $this->getRequest()->getQuery("level", 0);
        if(empty($stock_id)){
            $message = "请选择stock_id";
        }
        $data = [];
        if(empty($message)){
			if(StockModel::setLevel($stock_id, $level)){
				$data['status'] = 0;
            	$data['message'] = "设置五星推荐成功";
			}else {
				$data['status'] = 1;
            	$data['message'] = "设置五星推荐失败";
			}
        }else{
            $data['status'] = 1;
            $data['message'] = $message;
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
    }


	public function searchAction(){
		$userid = $this->getRequest()->getQuery("userid", 1);
		$key = $this->getRequest()->getQuery("key", "");
		$value = $this->getRequest()->getQuery("value", "");
		$query = StockModel::search($key, $value, $userid);
		
		echo json_encode($query,JSON_UNESCAPED_UNICODE);
		exit;

		
	}

	public function tradeAction(){
		$errors = [];
		$userid = $this->getRequest()->getPost("userid", 1);
		$stock_id = $this->getRequest()->getPost("stock_id", null);
		$stock_type = (int)$this->getRequest()->getPost("stock_type", 0);
		$stock_price = $this->getRequest()->getPost("stock_price", 0);
		$stock_deal_total = (int)$this->getRequest()->getPost("stock_deal_total", 0);
		$created_at = $this->getRequest()->getPost("created_at", null);
		$stock_detail_remark = $this->getRequest()->getPost("stock_detail_remark", "");

		$one = StockModel::getLastOneByStockId($stock_id);

		$data = $this->feach($one);
		if(!isset($data['status'])){
			$data['stock_type'] = $stock_type;
			$resul = $this->putTheDataToTheStockDetailModel($userid, $stock_id, $one['stock_code'], $one['stock_name'], $data);

		}
	}
}

