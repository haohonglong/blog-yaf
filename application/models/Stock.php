<?php

use Yaf\Registry;
use base\model\StockModelBase;

class StockModel extends StockModelBase
{

    private $stock_code, $stock_name,  $stock_remark, $updated_at, $stock_cost, $flag, $bought, $stock_number, $userid, $tax;

    public function __construct($userid, $stock_id, $stock_code, $stock_name, $data)
    {
        parent::__construct($stock_id, $data);
        $this->stock_code = $stock_code;
        $this->stock_name = $stock_name;
        $this->userid = $userid;
        $this->stock_remark = $data['stock_remark'] ?? "";
        $this->updated_at = $data['updated_at'] ?? null;
        $this->stock_cost = $data['stock_cost'] ?? null;
        $this->tax = $data['tax'] ?? 5.00;
        $this->bought = $data['bought'] ?? null;
        $this->stock_number = $data['stock_number'] ?? null;
        $this->flag = $data['flag'] ?? 0;
        $this->flag = (int)($this->flag);

    }

    public static function tableName()
    {
        return 'stock';
    }


    /**
     * @author: lhh
     * 创建日期：2024-5-06
     * 修改日期：2024-5-06
     * 名称： getByName
     * 功能：
     * 说明：
     * 注意：
     * @param $name
     * @return mixed
     */
    public static function getByName($name) {
        return Registry::get('db')->get(static::tableName(),"stock_id",["stock_name"=>$name]);
    }

    public static function getById($stock_id) {
        return Registry::get('db')->get(static::tableName(), "*", ["stock_id"=>$stock_id]);
    }
    
    public static function geCodetById($stock_id) {
        return Registry::get('db')->get(static::tableName(), "stock_code", ["stock_id"=>$stock_id]);
    }

    public static function getByCode($stock_code) {
        return Registry::get('db')->get(static::tableName(), "stock_code", ["stock_code"=>$stock_code]);
    }


    public static function getLastOneByStockId($stock_id) {
        $query = Registry::get('db')->get(static::tableName(), "*", ["stock_id"=>$stock_id]);
        return $query;
    }
    /**
     * @author: lhh
     * 创建日期：2026-2-09
     * 修改日期：2026-2-09
     * 名称： feach
     * 功能：从股市百事通获取数据
     * 说明：
     * 注意：
     */
    public static function feach($item){
		
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
				$name = $data['Result'][0]['DisplayData']['resultData']['tplData']['result']['name'];
				$code = $data['Result'][0]['DisplayData']['resultData']['tplData']['result']['code'];
				$exchange = $data['Result'][0]['DisplayData']['resultData']['tplData']['result']['exchange'];

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
                    'code' => $code,
                    'name' => $name,
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


    /**
     * @author: lhh
     * 创建日期：2024-5-06
     * 修改日期：2025-8-21
     * 名称： getList
     * 功能：只列出属于当前用户的股票
     * 说明：
     * 注意：
     * @return mixed
     */
    public static function getList($userid, $size = 1, $rows = 0) {
        $sql = "";
        if($rows > 0 && $size > 0){
            $size = ($size-1) * $rows;
            $sql = " LIMIT {$size}, {$rows}";
        }


        $select = "SELECT 
                a.amount, a.amplitude, a.average, a.change, a.close, a.created_at, a.flag, a.highest, a.ldown, a.level, a.lowest, a.lup, a.open, a.stock_code, a.stock_id, a.stock_name, a.stock_number, a.stock_price, a.stock_remark, a.updated_at, a.volume,
                b.id, b.bought, b.cost, b.tax
            FROM ".static::tableName()." as a 
            LEFT JOIN ".UserAndStockModel::tableName()." as b ON a.stock_id = b.stock_id
            ";
        $query = Registry::get('db')->query($select. " WHERE b.userid = {$userid} ORDER BY created_at DESC ". $sql)->fetchAll(\PDO::FETCH_ASSOC);
        foreach($query as $k => $v){
            $query[$k]['level'] = +$v['level'];
            $query[$k]['bought'] = +$v['bought'];
            $query[$k]['tax'] = +$v['tax'];
            $query[$k]['stock_number'] = +$v['stock_number'];
            
            
        }
        return $query;
    }

    /**
     * @author: lhh
     * 创建日期：2025-12-19
     * 修改日期：2025-12-19
     * 名称： getAllList
     * 功能：获取所有股票
     * 说明：
     * 注意：
     * @return mixed
     */
    public static function getAllList() {

        $all = "amount, amplitude, average, bought, change, close, cost, created_at, flag, highest, ldown, level, lowest, lup, open, stock_code, stock_id, stock_name, stock_number, stock_price, stock_remark, tax, updated_at, volume";

        $query = Registry::get('db')->query("SELECT * FROM ".static::tableName()." WHERE flag = 0 ORDER BY created_at DESC")->fetchAll(\PDO::FETCH_ASSOC);
        foreach($query as $k => $v){
            $query[$k]['level'] = +$v['level'];
            $query[$k]['bought'] = +$v['bought'];
            $query[$k]['tax'] = +$v['tax'];
            $query[$k]['stock_number'] = +$v['stock_number'];
            
            
        }
        return $query;
    }


    
    /**
     * @author: lhh
     * 创建日期：2024-5-08
     * 修改日期：2024-5-09
     * 名称： setStockNumbers
     * 功能：
     * 说明：
     * 注意：
     * @param $id
     * @return mixed
     */
    public static function setStockNumber($stock_id, $stock_number) {
        $stock  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET stock_number=:stock_number WHERE stock_id = :stock_id");
        $stock->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);
        $stock->bindParam(':stock_number', $stock_number, \PDO::PARAM_STR);
        if($stock->execute()){
            return true;
        }

        return false;
        
    }

    /**
     * @author: lhh
     * 创建日期：2024-11-03
     * 修改日期：2024-11-03
     * 名称： search
     * 功能：搜索值按照指定的字段
     * 说明：
     * 注意：
     * @param $id
     * @return mixed
     */
    public static function search($key, $value) {
        $query = Registry::get('db')->query("SELECT * FROM ".static::tableName()." WHERE {$key} = {$value}")->fetchAll(\PDO::FETCH_ASSOC);
        return $query;
        
    }

    /**
     * @author: lhh
     * 创建日期：2024-10-29
     * 修改日期：2024-10-29
     * 名称： setLevel
     * 功能：五星推荐
     * 说明：
     * 注意：
     * @param $id
     * @return mixed
     */
    public static function setLevel($stock_id, $level=0) {
        $stock  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET level=:level WHERE stock_id = :stock_id limit 1");
        $stock->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);
        $stock->bindParam(':level', $level, \PDO::PARAM_STR);
        if($stock->execute()){
            return true;
        }

        return false;
        
    }



    /**
     * @author: lhh
     * 创建日期：2024-5-21
     * 修改日期：2025-7-30
     * 名称： update
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public function update(){
        $setFields = [
            "`stock_price`=:stock_price",
            "`open`=:open",
            "`close`=:close", 
            "`lup`=:lup",
            "`ldown`=:ldown",
            "`highest`=:highest",
            "`lowest`=:lowest", 
            "`average`=:average",
            "`change`=:change",
            "`amplitude`=:amplitude",
            "`volume`=:volume",
            "`amount`=:amount",
            "`updated_at`=:updated_at"
        ];

        // 动态添加可选字段
        if(!is_null($this->bought)){
            $setFields[] = "`bought`=:bought";
            $setFields[] = "`cost`=:cost";
        }

        if(!is_null($this->stock_number)){
            $setFields[] = "`stock_number`=:stock_number";
        }

        $setClause = implode(", ", $setFields);

        $sql = "UPDATE ".static::tableName() ." SET " . $setClause . " WHERE `stock_id`=:stock_id";
        // error_log("执行的SQL: " . $sql . " in " . __FILE__ . " on line " . __LINE__); // 调试用
        

        $stock = Registry::get('db')->pdo->prepare($sql);

        if(!is_null($this->bought)){
            $stock->bindParam(':bought', $this->bought, \PDO::PARAM_STR);
            $stock->bindParam(':cost', $this->stock_cost, \PDO::PARAM_STR);
        }

        if(!is_null($this->stock_number)){
            $stock->bindParam(':stock_number', $this->stock_number, \PDO::PARAM_STR);
        }

        // 绑定固定参数
        $stock->bindParam(':stock_id', $this->stock_id, \PDO::PARAM_STR);
        $stock->bindParam(':stock_price', $this->stock_price, \PDO::PARAM_STR);
        $stock->bindParam(':open', $this->open, \PDO::PARAM_STR);
        $stock->bindParam(':close', $this->close, \PDO::PARAM_STR);
        $stock->bindParam(':lup', $this->lup, \PDO::PARAM_STR);
        $stock->bindParam(':ldown', $this->ldown, \PDO::PARAM_STR);
        $stock->bindParam(':highest', $this->highest, \PDO::PARAM_STR);
        $stock->bindParam(':lowest', $this->lowest, \PDO::PARAM_STR);
        $stock->bindParam(':average', $this->average, \PDO::PARAM_STR);
        $stock->bindParam(':change', $this->change, \PDO::PARAM_STR);
        $stock->bindParam(':amplitude', $this->amplitude, \PDO::PARAM_STR);
        $stock->bindParam(':volume', $this->volume, \PDO::PARAM_STR);
        $stock->bindParam(':amount', $this->amount, \PDO::PARAM_STR);
        $stock->bindParam(':updated_at', $this->updated_at, \PDO::PARAM_STR);

        if($stock->execute()){
            $data['status'] = 1;
            $data['message'] = '更新成功';
        }else{
            $error = $stock->errorInfo();
            error_log("SQL错误详情: " . print_r($error, true));
            $data['status'] = 0;
            $data['message'] = $error . " in " . __FILE__ . " on line " . __LINE__;
        }

        return $data;
    }
    /**
     * @author: lhh
     * 创建日期：2025-12-22
     * 修改日期：2025-12-22
     * 名称： updateTests
     * 功能：只更新flag = 1，批量更新
     * 说明：交易时不会执行，只有请求api更新股票信息时才执行，考虑到网络性能问题，自动更新股票信息时每个股票代码只更新一次,其余同名股票代码数据更新，通过这条更新过的数据复制来更新它们
     * 注意：
     * @return mixed
     */
    public function updateTests(){
        $setFields = [
            "`stock_price`=:stock_price",
            "`open`=:open",
            "`close`=:close", 
            "`lup`=:lup",
            "`ldown`=:ldown",
            "`highest`=:highest",
            "`lowest`=:lowest", 
            "`average`=:average",
            "`change`=:change",
            "`amplitude`=:amplitude",
            "`volume`=:volume",
            "`amount`=:amount",
            "`updated_at`=:updated_at"
        ];


        $setClause = implode(", ", $setFields);

        $sql = "UPDATE ".static::tableName() ." SET " . $setClause . " WHERE `flag` = 1 AND `stock_code`=:stock_code";
        // error_log("执行的SQL: " . $sql . " in " . __FILE__ . " on line " . __LINE__); // 调试用
        

        $stock = Registry::get('db')->pdo->prepare($sql);


        // 绑定固定参数
        $stock->bindParam(':stock_code', $this->stock_code, \PDO::PARAM_STR);
        $stock->bindParam(':stock_price', $this->stock_price, \PDO::PARAM_STR);
        $stock->bindParam(':open', $this->open, \PDO::PARAM_STR);
        $stock->bindParam(':close', $this->close, \PDO::PARAM_STR);
        $stock->bindParam(':lup', $this->lup, \PDO::PARAM_STR);
        $stock->bindParam(':ldown', $this->ldown, \PDO::PARAM_STR);
        $stock->bindParam(':highest', $this->highest, \PDO::PARAM_STR);
        $stock->bindParam(':lowest', $this->lowest, \PDO::PARAM_STR);
        $stock->bindParam(':average', $this->average, \PDO::PARAM_STR);
        $stock->bindParam(':change', $this->change, \PDO::PARAM_STR);
        $stock->bindParam(':amplitude', $this->amplitude, \PDO::PARAM_STR);
        $stock->bindParam(':volume', $this->volume, \PDO::PARAM_STR);
        $stock->bindParam(':amount', $this->amount, \PDO::PARAM_STR);
        $stock->bindParam(':updated_at', $this->updated_at, \PDO::PARAM_STR);

        if($stock->execute()){
            $data['status'] = 1;
            $data['message'] = '更新成功';
        }else{
            $error = $stock->errorInfo();
            error_log("SQL错误详情: " . print_r($error, true));
            $data['status'] = 0;
            $data['message'] = $error . " in " . __FILE__ . " on line " . __LINE__;
        }

        return $data;
    }

    /**
     * @author: lhh
     * 创建日期：2024-5-06
     * 修改日期：2026-2-7
     * 名称： create
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public function create() {
        $created_at = date("Y-m-d H:i:s");
        $database = Registry::get('db');
        $api = static::feach(['stock_code' => $this->stock_code, 'stock_name' => $this->stock_name]);
        
        if(isset($api['status']) || (!isset($api['status']) && '' == $api['name'])){
            $errors["stock_code"] = "股票代码错误";
            $data['status'] = 0;
            $data['errors'] = $errors;
            return $data;

        }

        if(0 == $this->flag && !is_null(static::getByCode($this->stock_code))){
            $this->flag = 1;
        }

        try{
            $database->pdo->beginTransaction();
            $stock  = $database->pdo->prepare("INSERT INTO ".static::tableName() ." SET stock_id=:stock_id, stock_code=:stock_code, stock_name=:stock_name, stock_remark=:stock_remark, created_at=:created_at, updated_at=:created_at, flag=:flag, tax=:tax");
            $stock->bindParam(':stock_id', $this->stock_id, \PDO::PARAM_STR);
            $stock->bindParam(':stock_code', $this->stock_code, \PDO::PARAM_STR);
            $stock->bindParam(':stock_name', $this->stock_name, \PDO::PARAM_STR);
            $stock->bindParam(':stock_remark', $this->stock_remark, \PDO::PARAM_STR);
            $stock->bindParam(':created_at', $created_at, \PDO::PARAM_STR);
            $stock->bindParam(':flag', $this->flag, \PDO::PARAM_STR);
            $stock->bindParam(':tax', $this->tax, \PDO::PARAM_STR);
            if($stock->execute()){

                $userAndStockModel = new UserAndStockModel($this->userid, $this->stock_id, [
                    'created_at' => $created_at,
                    'flag' => $this->flag,
                ]);
                $userAndStockModelData = $userAndStockModel->create();

                if(0  == $userAndStockModelData['status']){
                    $database->pdo->rollBack();
                    return $userAndStockModelData;
                }
                $data['status'] = 1;
                $data['message'] = '添加成功';
                $logModel = new LogModel($this->userid, "股票-添加", "添加了股票,stock_id:" . $this->stock_id, $created_at);
                $log = $logModel->create();
                if(0 == $log['status']){
                    $database->pdo->rollBack();
                    $data['status'] = 0;
                    $data['message'] = $log['message'];
                    return $data;
                }

                $database->pdo->commit();
                
            }else{
                $data['status'] = 0;
                $data['message'] = $stock->errorInfo();
                $database->pdo->rollBack();
            }

        }catch (Exception $e) {
            $database->pdo->rollBack();
            $data['status'] = 0;
            $data['message'] = $e->getMessage() . " in " . __FILE__ . " on line " . __LINE__;
			
        }
        
        return $data;
    }

    /**
     * @author: lhh
     * 创建日期：2025-6-27
     * 修改日期：2026-2-7
     * 名称： edit
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public function edit() {
        $updated_at = date("Y-m-d H:i:s");
        $old_stock_code = static::geCodetById($this->stock_id);

        if($old_stock_code != $this->stock_code){
            $api = static::feach(['stock_code' => $old_stock_code, 'stock_name' => $this->stock_name]);
            
            if(!isset($api['status']) && $api['name'] != ''){ 
                // 如果原股票代码工作正常，就说明是恶意修改股票代码
                $errors["stock_code"] = "非法操作";
                $data['status'] = 0;
                $data['errors'] = $errors;
                return $data;
    
            } else {
                $api = static::feach(['stock_code' => $this->stock_code, 'stock_name' => $this->stock_name]);
                
                if(isset($api['status']) || (!isset($api['status']) && '' == $api['name'])){
                    $errors["stock_code"] = "股票代码错误";
                    $data['status'] = 0;
                    $data['errors'] = $errors;
                    return $data;
        
                }

            }


        }

        $database = Registry::get('db');

        try {
            $database->pdo->beginTransaction();

            $stock  = $database->pdo->prepare("UPDATE ".static::tableName() ." SET stock_code=:stock_code, stock_name=:stock_name, cost=:stock_cost, tax=:tax, stock_remark=:stock_remark, updated_at=:updated_at WHERE stock_id=:stock_id");
            $stock->bindParam(':stock_id', $this->stock_id, \PDO::PARAM_STR);
            $stock->bindParam(':stock_code', $this->stock_code, \PDO::PARAM_STR);
            $stock->bindParam(':stock_name', $this->stock_name, \PDO::PARAM_STR);
            $stock->bindParam(':stock_remark', $this->stock_remark, \PDO::PARAM_STR);
            $stock->bindParam(':updated_at', $updated_at, \PDO::PARAM_STR);
            $stock->bindParam(':stock_cost', $this->stock_cost, \PDO::PARAM_STR);
            $stock->bindParam(':tax', $this->tax, \PDO::PARAM_STR);
            if($stock->execute()){
                $data['status'] = 1;
                $data['message'] = '修改成功';
                $cause = "修改了股票,stock_id:" . $this->stock_id;

                $history = StockHistoryModel::setCost($this->stock_id, $this->stock_cost, $this->tax, $updated_at);
                $userAndStock = UserAndStockModel::setCost($this->userid, $this->stock_id, $this->stock_cost, $this->tax, $updated_at);
                if(0 == $history['status']){
                    $database->pdo->rollBack();
                    $data = $history;
                    return $data;
                }

                if(0 == $userAndStock['status']){
                    $database->pdo->rollBack();
                    $data = $userAndStock;
                    return $data;
                }
                if($old_stock_code != $this->stock_code){
                    //如果修改了stock_code ，那么所有的stock_code 都要改变, 但为了安全问题防止恶意修改，不要这样操作
                    $stock2  = $database->pdo->prepare("UPDATE ".static::tableName() ." SET stock_code=:stock_code, updated_at=:updated_at WHERE stock_code=:old_stock_code");
                    $stock2->bindParam(':old_stock_code', $old_stock_code, \PDO::PARAM_STR);
                    $stock2->bindParam(':stock_code', $this->stock_code, \PDO::PARAM_STR);
                    $stock2->bindParam(':updated_at', $updated_at, \PDO::PARAM_STR);
                    if(!$stock2->execute()){
                        $data['status'] = 0;
                        $data['message'] = $stock2->errorInfo();
                        $database->pdo->rollBack();
                        return $data;
                    }
                    $cause .= "，把股票代码" . $old_stock_code . "改为了" . $this->stock_code;
                    
                }

                $logModel = new LogModel($this->userid, "股票-修改", $cause, $updated_at);
                $log = $logModel->create();
                if(0 == $log['status']){
                    $database->pdo->rollBack();
                    $data['status'] = 0;
                    $data['message'] = $log['message'];
                    return $data;
                }

                $database->pdo->commit();
            }else{
                $database->pdo->rollBack();
                $data['status'] = 0;
                $data['message'] = $stock->errorInfo();
            }
        }catch (Exception $e) {
            $database->pdo->rollBack();
            $data['status'] = 0;
            $data['message'] = $e->getMessage() . " in " . __FILE__ . " on line " . __LINE__;
			
        }

        

        
        return $data;
    }



    public static function delete($userid, $stock_id) {
        $updated_at = date("Y-m-d H:i:s");
        $database = Registry::get('db');
        if(0  == static::getById($stock_id)['flag']){ //非测试股票不能被删除
            return [
                'status' => 1,  // 1=失败
                'message' => '非测试股票不能被删除',
            ];
        }
        
        // 添加调试信息
        error_log("第 " . __LINE__ . " 行: 开始删除股票数据, stock_id: " . $stock_id);

        try {
            $database->pdo->beginTransaction();
            
            // 定义删除顺序（根据外键约束调整）
            $tablesToDelete = [
                StockDetailModel::tableName(),
                StockDateModel::tableName(), 
                StockHistoryModel::tableName(),
                UserAndStockModel::tableName(),
                static::tableName() // 主表最后删除
            ];
            
            foreach ($tablesToDelete as $table) {
                if(UserAndStockModel::tableName() == $tabl){ // 只有user_stock 表需要userid
                    $stmt = $database->pdo->prepare("DELETE FROM {$table} WHERE userid=:userid AND stock_id = :stock_id");
                    $stmt->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);
                    $stmt->bindParam(':userid', $userid, \PDO::PARAM_STR);
                }else {
                    $stmt = $database->pdo->prepare("DELETE FROM {$table} WHERE stock_id = :stock_id");
                    $stmt->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);

                }
                
                if (!$stmt->execute()) {
                    $error = $stmt->errorInfo();
                    error_log("第 " . __LINE__ . " 行: 删除 {$table} 失败 - " . print_r($error, true));
                    throw new Exception("删除 {$table} 失败: " . $error[2]);
                }
                
                $affectedRows = $stmt->rowCount();
                error_log("第 " . __LINE__ . " 行: 从 {$table} 删除了 {$affectedRows} 条记录");
            }

            $logModel = new LogModel($userid, "股票-删除", "删除了股票,stock_id:" . $stock_id, $updated_at);
            $log = $logModel->create();
            if(0 == $log['status']){
                $database->pdo->rollBack();
                $data['status'] = 1;
                $data['message'] = $log['message'];
                return $data;
            }
            
            $database->pdo->commit();
            
            error_log("第 " . __LINE__ . " 行: 股票数据删除成功");
            return [
                'status' => 0,  // 0=成功
                'message' => '删除成功'
            ];
            
        } catch (Exception $e) {
            $database->pdo->rollBack();
            
            error_log("第 " . __LINE__ . " 行: 删除过程发生异常 - " . $e->getMessage());
            return [
                'status' => 1,  // 1=失败
                'message' => '删除失败: ' . $e->getMessage()
            ];
        }
    }

    

}