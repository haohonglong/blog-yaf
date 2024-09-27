<?php

use Yaf\Registry;

class StockController extends Base {

	private function putTheDataToTheStockDetailModel($stock_id
		,$stock_name
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
		,$change){

		if(0 == $open){
			$result['status'] = 3;
			$result['message'] = $stock_id. '-' .$stock_name.': 开盘不能是零';
			return $result;
		}

		if(0 == $close){
			$result['status'] = 3;
			$result['message'] = $stock_id. '-' .$stock_name.': 收盘不能是零';
			return $result;
		}

		$StockDetailModel = new StockDetailModel(
			 $stock_id
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
				$result['status'] = 3;
				$result['message'] = $data2['message'];
				break;
			
			case 1:
				$result['status'] = 0;
				$result['message'] = $stock_id. '-' .$stock_name.': 已更新完成';
				break;
				
			default:
		}

		return $result;


	}
	
	private function feach($item){
		
		$result = [];
		$stock_id = $item['stock_id'];
		$stock_name = $item['stock_name'];

		$headers = [
			'Connection' => 'keep-alive',
			'Cache-Control' => 'max-age=0',
			'Upgrade-Insecure-Requests' => '1',
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36',
			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
			'Sec-Fetch-Site' => 'none',
			'Sec-Fetch-Mode' => 'navigate',
			'Sec-Fetch-User' => '?1',
			'Sec-Fetch-Dest' => 'document',
			'Accept-Language' => 'zh-CN,zh;q=0.9',
		];

		$cookies = [
			'PSTM' => '1635248519',
			'BIDUPSID' => '90EF3BD78F53BC8C96DF84CD3854CA2D',
			'__yjs_duid' => '1_cd247776bc887ee300105fb75c8c2a331635258445589',
			'BDUSS' => '1oWEtxQkpPR25ySTgtSHRHb0JOR2VXcm12MEk4V3ZBZ2VkOWZSVFI2QTBlWE5pRVFBQUFBJCQAAAAAAAAAAAEAAACRJsY-cGlwacnxu7AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADTsS2I07EticS',
			'BDUSS_BFESS' => '1oWEtxQkpPR25ySTgtSHRHb0JOR2VXcm12MEk4V3ZBZ2VkOWZSVFI2QTBlWE5pRVFBQUFBJCQAAAAAAAAAAAEAAACRJsY-cGlwacnxu7AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADTsS2I07EticS',
			'BDORZ' => 'B490B5EBF6F3CD402E515D22BCDA1598',
			'MCITY' => '-158%3A',
			'BA_HECTOR' => '8h242g8hah002l0g0g1h9pekn15',
			'ZFY' => 'uYCFmlJSV5rn3KHYBSLi6naqucpmiTVS5c4ql8gHf3c:C',
			'BAIDUID_V4' => '59DEA2219CA3CC71798923390803C00A:FG=1',
			'RT' => '"z=1&dm=baidu.com&si=xgb0bofv4d&ss=l41exipa&sl=3&tt=jbz&bcn=https%3A%2F%2Ffclog.baidu.com%2Flog%2Fweirwood%3Ftype%3Dperf&ld=12pi&ul=1jdc&hd=1jej"',
			'BDRCVFR[feWj1Vr5u3D]' => 'I67x6TjHwwYf0',
			'delPer' => '0',
			'PSINO' => '2',
			'BAIDUID_BFESS' => '488CA1A354CAFF05B0D67E0E09E83335:FG=1',
			'H_PS_PSSID' => '36426_36549_36465_36455_36512_36452_36167_36488_36517_36074_36519_26350_36467_36314',
			'BAIDUID' => 'B0C47089A4FF26A4CB78746AB1FD2529:FG=1',
			'Hm_lvt_c8bd3584daa59ca83c2ec1247d343576' => '1654438355,1654506317',
			'Hm_lpvt_c8bd3584daa59ca83c2ec1247d343576' => '1654506958',
		];

		$params = [
			'openapi' => '1',
            'dspName' => 'iphone',
            'tn' => 'tangram',
            'client' => 'app',
            'query' => $stock_id,
            'code' => $stock_id,
            'word' => $stock_id,
            'resource_id' => '5429',
            'ma_ver' => '4',
            'finClientType' => 'pc',
		];
		$params_str = http_build_query($params);
		$url = 'https://gushitong.baidu.com/opendata?'. $params_str;
		// echo $url;exit;

		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			// CURLOPT_NOSIGNAL => 1,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_HTTPHEADER => $headers,
			// CURLOPT_COOKIE => $cookies,
			CURLOPT_CUSTOMREQUEST => 'GET',
		]);
		$data = curl_exec($curl);
		curl_close($curl);
		// echo $data;exit;
		$data = json_decode($data, true);
		// print_r($data);exit;
		
		if(!isset($data)){
			$result['status'] = 2;
			$result['message'] = "{$stock_id}-{$stock_name} url地址错误或者是休市日";
		} else {
			try {
				$minute_data = $data['Result'][1]['DisplayData']['resultData']['tplData']['result']['minute_data'];
				$date       = $minute_data['update']['text'];
				$y = date('Y-');
				$date = $y.$date;
				$priceinfo  = $minute_data['priceinfo'];
				$origin_pankou       = $minute_data['pankouinfos']['origin_pankou'];
				$average    = $minute_data['pankouinfos']['list'][13]['value'];
				
				$data = [
					'stock_price' => $origin_pankou['currentPrice'],
					'created_at' => $date,
					'open' => $origin_pankou['open'],
					'close' => $origin_pankou['preClose'],
					'lup' => $origin_pankou['limitUp'],
					'ldown' => $origin_pankou['limitDown'],
					'hight'     => $origin_pankou['high'],
					'low'       => $origin_pankou['low'],
					'average'   => $average,
					'change'    => $origin_pankou['turnoverRatio'],
					'amplitude' => $origin_pankou['amplitudeRatio'],
					'volume' => $origin_pankou['volume'], //成交量
					'inside' => $origin_pankou['inside'], //内盘
					'outside' => $origin_pankou['outside'], //外盘
					'amount' => $origin_pankou['amount'], //成交额
					'weibiRatio' => $origin_pankou['weibiRatio'], //委比
					'volumeRatio' => $origin_pankou['volumeRatio'], //量比
					'currencyValue' => $origin_pankou['currencyValue'], //流通值
					'capitalization' => $origin_pankou['capitalization'], //总市值
					'peratio' => $origin_pankou['peratio'], //市盈(TTM)
					'lyr' => $origin_pankou['lyr'], //市盈(静)
					'bvRatio' => $origin_pankou['bvRatio'], //市净率
					'perShareEarn' => $origin_pankou['perShareEarn'], //成交量
					'netAssetsPerShare' => $origin_pankou['netAssetsPerShare'], //
					'circulatingCapital' => $origin_pankou['circulatingCapital'], //流通股
					'totalShareCapital' => $origin_pankou['totalShareCapital'], //总股本
					'priceLimit' => $origin_pankou['priceLimit'], //涨跌幅
					'w52_low' => $origin_pankou['w52_low'], //52周低
					'w52_high' => $origin_pankou['w52_high'], //52周高
				];
				// print_r($data);exit;

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

				$result = $this->putTheDataToTheStockDetailModel($stock_id
				   ,$stock_name
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
			$result['message'] = "{$stock_id}-{$stock_name} url地址错误或者是休市日";
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
				// print_r($data);exit;

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

				$result = $this->putTheDataToTheStockDetailModel($stock_id
				   ,$stock_name
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

				
			} catch (Exception $e) {
				$result['status'] = 1;
				$result['message'] = $e->getMessage();
			}
			

		}

		return $result;
	}

	private function feach3($item){
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
					$result['message'] = "{$stock_id}-{$stock_name} url地址错误或者是休市日";
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

				$result = $this->putTheDataToTheStockDetailModel($stock_id
				   ,$stock_name
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

				
			} catch (Exception $e) {
				$result['status'] = 1;
				$result['message'] = $e->getMessage();
			}

			
		}
		
		return $result;
	}


	public function getProgressAction(){
		header("X-Accel-Buffering: no");
		header("Content-Type: text/event-stream");
		header("Cache-Control: no-cache");

		$redis = Registry::get('redis');
		//发送消息
		
		sleep(2);
		$progress = 1;
		while($progress)
		{
			$progress = $redis->get("progress");
			
			// $c = "event:" . PHP_EOL; //定义事件
			$c = "data: " . $progress . PHP_EOL; //推送内容
			echo $c . PHP_EOL;

			while (ob_get_level() > 0) {
				ob_end_flush();
			}
			flush();
			if ( connection_aborted() ) break;
			sleep(1);
		}
		
		exit;

	}

	/**
	 * 获取API 接口数据并存入数据库
	 */
	public function feachAction(){
		$datas = [];
		$stock_id = $this->getRequest()->getQuery("stock_id", null);
		$way = $this->getRequest()->getQuery("way", '1');
		if(isset($stock_id) && !empty($stock_id)){
			$total = 1;
			$one = StockModel::getLastOneByStockId($stock_id);
			switch ($way) {
				case '2':
					$data = $this->feach2($one);
					break;
				case '3':
					$data = $this->feach3($one);
					break;
				
				default:
					$data = $this->feach($one);

			}
			
			$datas['datas'][] = $data;
		} else {
			$total = 0;
			$list = StockModel::getList();
			$redis = Registry::get('redis');
			try {
				$i = 0;
				$len = count($list);
				
				foreach ($list as $item) {
					$progress = intval((++$i)/$len * 100);
					$redis->set('progress', $progress);

					switch ($way) {
						case '2':
							$data = $this->feach2($item);
							break;
						case '3':
							$data = $this->feach3($item);
							break;
						
						default:
							$data = $this->feach($item);
	
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
				$data=[];
				$data['status'] = 1;
				$data['message'] = $e->getMessage();
				$datas['datas'][] = $data;
				echo json_encode($datas, JSON_UNESCAPED_UNICODE);
				exit;
				
			} finally {
				// $redis->set('progress', 0);
			}
			
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