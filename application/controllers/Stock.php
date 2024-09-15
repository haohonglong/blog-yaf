<?php
use Yaf\Registry;

class StockController extends Base {

	private function feach($item){
		$result = [];
		$stock_id = $item['stock_id'];
		$stock_name = $item['stock_name'];
		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => 'http://api.mairui.club/hsrl/ssjy/'. $stock_id .'/2436F02E-99D9-49C4-B1B5-BE4D187AF029',
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
		$data = json_decode($data, true, 512);
		
		if(!is_array($data)){
			switch ($data) {
				case 101:
					$result['status'] = 1;
					$result['message'] = '当日的请求已超过版本限制，次日将会自动恢复';
					break;
				
				default:
					$result['status'] = 1;
					$result['message'] = 'url地址错误';
					break;
			}
			
		} else {
			try {
				$data = [
					'stock_price' => $data['p'],
					'created_at' => $data['t'],
					'open' => $data['o'],
					'close' => $data['yc'],
					'lup' => $item['lup'],
					'ldown' => $item['ldown'],
					'hight'     => $data['h'],
					'low'       => $data['l'],
					'average'   => $item['average'],
					'change'    => $data['hs'],
					'amplitude' => $data['zf'],
				];


				$stock_type = 0;
				$stock_price = $data['stock_price'];
				$stock_deal_total = 0;
				$created_at = $data['created_at'];
				$stock_detail_remark = "";
		
				$open      = $data['open'];
				$close     = $data['close'];
				$lup       = $data['lup'];
				$ldown     = $data['ldown'];
				$hight     = $data['hight'];
				$low       = $data['low'];
				$average   = $data['average'];
				$change    = $data['change'];
				$amplitude = $data['amplitude'];

				$StockDetailModel = new StockDetailModel($stock_id
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
				$data2 = $StockDetailModel->create();
				switch ($data2['status']) {
					case 0:
					case 3:
						$result['status'] = 1;
						$result['message'] = $data2['message'];
						break;
					
					case 1:
						$result['status'] = 0;
						$result['message'] = $stock_id. '-' .$stock_name.' 股票信息已更新完成';
						break;

					default:


				}

				
			} catch (Exception $e) {
				$result['status'] = 1;
				$result['message'] = $e->getMessage();
			}

			
		}
		
		return $result;
	}

	private function feach2($item){
		
		$result = [];
		$stock_id = $item['stock_id'];
		$stock_name = $item['stock_name'];
		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => 'https://finance.pae.baidu.com/vapi/v1/getquotation?all=1&srcid=5353&pointType=string&group=quotation_minute_ab&market_type=ab&new_Format=1&finClientType=pc&query='.$stock_id.'&code='.$stock_id,
			// CURLOPT_URL => 'https://finance.pae.baidu.com/api/getbanner?marketType=ab&code=000875&financeType=stock&finClientType=pc',
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
		var_dump($data);exit;
		$data = json_decode($data, true, 512);
		if(!isset($data)){
			$result['status'] = 1;
			$result['message'] = 'url地址错误';
		} else {
			try {
				$date = $data['Result']['update']['text'];
				$y = date('Y-');
				$created_at = $y.$date;
				$stock_price = $data['Result']['buyinfos'][0]['bidprice'];
				$data = $data['Result']['pankouinfos']['list'];
				$data = [
					'stock_price' => $stock_price,
					'created_at' => $created_at,
					'open' => $data[0]['value'],
					'close' => $data[3]['value'],
					'lup' => $data[15]['value'],
					'ldown' => $data[18]['value'],
					'hight'     => $data[1]['value'],
					'low'       => $data[4]['value'],
					'average'   => $data[13]['value'],
					'change'    => $data[6]['originValue'],
					'amplitude' => $data[19]['originValue'],
				];

				$stock_type = 0;
				$stock_price = $data['stock_price'];
				$stock_deal_total = 0;
				$created_at = $data['created_at'];
				$stock_detail_remark = "";
		
				$open      = $data['open'];
				$close     = $data['close'];
				$lup       = $data['lup'];
				$ldown     = $data['ldown'];
				$hight     = $data['hight'];
				$low       = $data['low'];
				$average   = $data['average'];
				$change    = $data['change'];
				$amplitude = $data['amplitude'];

				$StockDetailModel = new StockDetailModel($stock_id
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
				$data2 = $StockDetailModel->create();
				switch ($data2['status']) {
					case 0:
					case 3:
						$result['status'] = 1;
						$result['message'] = $data2['message'];
						break;
					
					case 1:
						$result['status'] = 0;
						$result['message'] = $stock_id. '-' .$stock_name.' 股票信息已更新完成';
						break;
						
					default:
				}

				
			} catch (Exception $e) {
				$result['status'] = 1;
				$result['message'] = $e->getMessage();
			}
			

		}

		return $result;
	}
	/**
	 * 获取API 接口数据并存入数据库
	 */
	public function feachAction(){
		$datas = [];
		$stock_id = $this->getRequest()->getQuery("stock_id", null);
		if(isset($stock_id) && !empty($stock_id)){
			$total = 1;
			$one = StockModel::getLastOneByStockId($stock_id);
			$data = $this->feach2($one);
			$datas['datas'][] = $data;
		} else {
			$total = 0;
			$list = StockModel::getList();
			foreach ($list as $key => $item) {
				$data = $this->feach2($item);
				$datas['datas'][] = $data;
				if($data['status'] > 0){
					break;
				}
				$total = $key+1;

			}
			$datas['total'] = $total;
			
		}

		$datas['status'] = 0;
		
		echo json_encode($datas, JSON_UNESCAPED_UNICODE);
		exit;
		


	}


	public function indexAction() {
        $data = StockModel::getList();
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
	}
	public function addAction() {
		$errors = [];
		$stock_id = $this->getRequest()->getPost("stock_id", null);
		$stock_name = $this->getRequest()->getPost("stock_name", null);
		$stock_remark = $this->getRequest()->getPost("stock_remark", "");
		if(!isset($stock_id) || empty($stock_id)) { $errors["stock_id"] = "stock_id 参数是必须的"; }
		if(!isset($stock_name) || empty($stock_name)) { $errors["stock_name"] = "stock_name 参数是必须的"; }
		if(empty($errors)) {
			$Stock = new StockModel($stock_id, $stock_name, $stock_remark);
			$data = $Stock->create();
		} else {
			$data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '添加失败';
		}

		echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
	}
}