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

		$StockDetailModel = new StockDetail2Model($userid, $stock_id, $data);
		$data2 = $StockDetailModel->create();
		
		

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
		
		$result = [];
		$stock_code = $item['stock_code'];
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
            'query' => $stock_code,
            'code' => $stock_code,
            'word' => $stock_code,
            'resource_id' => '5429',
            'ma_ver' => '4',
            'finClientType' => 'pc',
		];
		$params_str = http_build_query($params);
		$url = 'https://gushitong.baidu.com/opendata?'. $params_str;
		// var_dump($url);
		// file_put_contents(APPLICATION_PATH . '/application/data/gushitong_url.txt', $url . "\n", FILE_APPEND);

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
			$result['message'] = "{$stock_code}-{$stock_name} url地址错误或者是休市日";
			return $result;
		} else {
			try {
				$minute_data = $data['Result'][1]['DisplayData']['resultData']['tplData']['result']['minute_data'];
				$date       = $minute_data['update']['text'];
				$y = date('Y-');
				$date = $y.$date;
				// var_dump(explode(" ", $date)[0], date('Y-m-d'));
				if(date('Y-m-d') != explode(" ", $date)[0]){
					$result['status'] = 2;
					$result['message'] = "服务器现在是测试时间";
					return $result;	
				}

				$priceinfo  = $minute_data['priceinfo'];
				$origin_pankou       = $minute_data['pankouinfos']['origin_pankou'];
				$average    = $minute_data['pankouinfos']['list'][13]['value'];
				$open = isset($origin_pankou['open']) ? $origin_pankou['open'] : 0;
				$preClose = isset($origin_pankou['preClose']) ? $origin_pankou['preClose'] : 0;
				$limitUp = isset($origin_pankou['limitUp']) ? $origin_pankou['limitUp'] : 0;
				$limitDown = isset($origin_pankou['limitDown']) ? $origin_pankou['limitDown'] : 0;
				$high = isset($origin_pankou['high']) ? $origin_pankou['high'] : 0;
				$low = isset($origin_pankou['low']) ? $origin_pankou['low'] : 0;
				$turnoverRatio = isset($origin_pankou['turnoverRatio']) ? $origin_pankou['turnoverRatio'] : 0;
				$amplitudeRatio = isset($origin_pankou['amplitudeRatio']) ? $origin_pankou['amplitudeRatio'] : 0;
				$volume = isset($origin_pankou['volume']) ? $origin_pankou['volume'] : 0;
				$inside = isset($origin_pankou['inside']) ? $origin_pankou['inside'] : 0;
				$outside = isset($origin_pankou['outside']) ? $origin_pankou['outside'] : 0;
				$amount = isset($origin_pankou['amount']) ? $origin_pankou['amount'] : 0;
				$weibiRatio = isset($origin_pankou['weibiRatio']) ? $origin_pankou['weibiRatio'] : 0;
				$volumeRatio = isset($origin_pankou['volumeRatio']) ? $origin_pankou['volumeRatio'] : 0;
				$currencyValue = isset($origin_pankou['currencyValue']) ? $origin_pankou['currencyValue'] : 0;
				$capitalization = isset($origin_pankou['capitalization']) ? $origin_pankou['capitalization'] : 0;
				$peratio = isset($origin_pankou['peratio']) ? $origin_pankou['peratio'] : 0;
				$lyr = isset($origin_pankou['lyr']) ? $origin_pankou['lyr'] : 0;
				$bvRatio = isset($origin_pankou['bvRatio']) ? $origin_pankou['bvRatio'] : 0;
				$perShareEarn = isset($origin_pankou['perShareEarn']) ? $origin_pankou['perShareEarn'] : 0;
				$netAssetsPerShare = isset($origin_pankou['netAssetsPerShare']) ? $origin_pankou['netAssetsPerShare'] : 0;
				$circulatingCapital = isset($origin_pankou['circulatingCapital']) ? $origin_pankou['circulatingCapital'] : 0;
				$totalShareCapital = isset($origin_pankou['totalShareCapital']) ? $origin_pankou['totalShareCapital'] : 0;
				$priceLimit = isset($origin_pankou['priceLimit']) ? $origin_pankou['priceLimit'] : 0;
				$w52_low = isset($origin_pankou['w52_low']) ? $origin_pankou['w52_low'] : 0;
				$w52_high = isset($origin_pankou['w52_high']) ? $origin_pankou['w52_high'] : 0;

				$result = [
					'stock_price' => $origin_pankou['currentPrice'],
					'created_at' => $date,
					'open' => $open ,
					'close' => $preClose,
					'lup' => $limitUp,
					'ldown' => $limitDown,
					'highest'     => $high,
					'lowest'       => $low,
					'average'   => $average,
					'change'    => $turnoverRatio, //换手率
					'amplitude' => $amplitudeRatio, //振幅
					'volume' => $volume, //成交量
					'inside' => $inside, //内盘
					'outside' => $outside, //外盘
					'amount' => $amount, //成交额
					'weibiRatio' => $weibiRatio, //委比
					'volumeRatio' => $volumeRatio, //量比
					'currencyValue' => $currencyValue, //流通值
					'capitalization' => $capitalization, //总市值
					'peratio' => $peratio, //市盈(TTM) string
					'lyr' => $lyr, //市盈(静) string
					'bvRatio' => $bvRatio, //市净率
					'perShareEarn' => $perShareEarn, //
					'netAssetsPerShare' => $netAssetsPerShare, //
					'circulatingCapital' => $circulatingCapital, //流通股
					'totalShareCapital' => $totalShareCapital, //总股本
					'priceLimit' => $priceLimit, //涨跌幅
					'w52_low' => $w52_low, //52周低
					'w52_high' => $w52_high, //52周高
				];

				
			} catch (Exception $e) {
				$result['status'] = 1;
				$result['message'] = $e->getMessage();
			}
			

		}

		return $result;
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
			// switch ($way) {
			// 	case '2':
			// 		$data = $this->feach2($one);
			// 		break;
			// 	case '3':
			// 		$data = $this->feach3($one);
			// 		break;
				
			// 	default:
				
			// }
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

					// switch ($way) {
					// 	case '2':
					// 		$data = $this->feach2($item);
					// 		break;
					// 	case '3':
					// 		$data = $this->feach3($item);
					// 		break;
						
					// 	default:
						
					// }

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

