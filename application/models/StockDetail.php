<?php

use Yaf\Registry;
use base\model\StockModelBase;



class StockDetailModel extends StockModelBase
{

    private  $userid, $stock_type, $stock_deal_total, $created_at, $stock_detail_remark="";

    public function __construct($userid, $stock_id, $data = [])
    {
        $redis = Registry::get('redis');
        $user = "user:{$userid}";
        
        parent::__construct($stock_id, $data);
        $this->userid = $userid;
        $this->stock_type = $data['stock_type'];
        $this->stock_deal_total = $data['stock_deal_total'];
        $this->created_at = $data['created_at'];
        $this->stock_detail_remark = $data['stock_detail_remark'] ?? "";
        $this->user = json_decode($redis->get($user), true);

    }

    public static function tableName()
    {
        return 'stock_detail';
    }



    public static function getById($id) {
        return Registry::get('db')->get(static::tableName(),"id",["id"=>$id]);
    }

    public static function hasBothStockIdAndDate($stock_id, $created_at) {
        return Registry::get('db')->has(static::tableName(),["stock_id"=>$stock_id, 'created_at'=> $created_at]);
    }


    /**
     * @author: lhh
     * 创建日期：2024-5-06
     * 修改日期：2025-11-6
     * 名称： getList
     * 功能：fetching something from stock_detail
     * 说明：如果是测试数据就把匹配条件的所有数据获取(stock.stock_code == stock_detail.stock_id) && stock_detail.stock_type == 0
     * 注意：
     * @return mixed
     */
    public static function getList($stock_id=null, $size = 1, $rows = 0) {


        // $rows = 300;
        // $size = ($size-1) * $rows;

        
        if(!is_null($stock_id)){
            $stock = StockModel::getById($stock_id);
            $stock_code = $stock["stock_code"];

            
            $select = "SELECT 
                a.id,
                a.stock_number,
                a.stock_price,
                a.stock_deal_total,
                a.stock_type,
                a.stock_date_at,
                a.stock_time_at,
                CONCAT(a.stock_date_at,' ', a.stock_time_at) as created_at,
                a.open,
                a.close,
                a.lup,
                a.ldown,
                a.highest,
                a.lowest,
                a.average,
                a.change,
                a.amplitude,
                a.volume,
                a.amount,
                a.gone,
                a.stock_detail_remark,
                a.profit,
                b.cost,
                b.tax
                
            FROM ".static::tableName()." as a 
            LEFT JOIN ".StockHistoryModel::tableName()." as b ON a.id = b.stock_detail_id
            ";
            
            

            $sql = "";
            if($rows > 0 && $size > 0){
                $size = ($size-1) * $rows;
                $sql = " LIMIT {$size}, {$rows}";
            }

            
            

            $query = Registry::get('db')->query($select. " WHERE a.stock_id = '{$stock_id}' ORDER by a.id DESC ". $sql)->fetchAll();

            if(1 == $stock['flag']){
                $datas = Registry::get('db')->query($select. " WHERE a.stock_id ='". $stock_code ."' AND a.stock_type = 0  ORDER by a.id DESC ". $sql)->fetchAll();
                $query = array_merge($query, $datas);
                
            }
        // LIMIT {$size}, {$rows}

        } else {
            // $query = Registry::get('db')->query("
            //                                         SELECT 
            //                                             sd.id,
            //                                             sd.stock_id,
            //                                             s.stock_name,
            //                                             sd.stock_number,
            //                                             sd.stock_price,
            //                                             sd.stock_deal_total,
            //                                             sd.stock_type,
            //                                             sd.stock_date_at,
            //                                             sd.stock_time_at,
            //                                             sd.open,
            //                                             sd.close,
            //                                             sd.lup,
            //                                             sd.ldown,
            //                                             sd.highest,
            //                                             sd.lowest,
            //                                             sd.average,
            //                                             sd.change,
            //                                             sd.amplitude,
            //                                             CONCAT(sd.stock_date_at,' ', sd.stock_time_at) as created_at,
            //                                             sd.stock_detail_remark
                                                        
            //                                         FROM ".static::tableName()." as sd 
            //                                         INNER JOIN ". StockModel::tableName()." as s USING(stock_id)
            //                                         ORDER by sd.id DESC 
            //                                         ")->fetchAll();

        }
        

        return $query;
    }


    /**
     * @author: lhh
     * 创建日期：2025-11-30
     * 修改日期：2025-12-3
     * 名称： calculateStockCost
     * 功能：计算股票多次买或卖后的平均持仓成本价
     * 说明：
     * 注意：
     * @param  array  $stock  最近一次持仓数据
     * @param  float  $tax  税
     * @param  float  $commissionRate  佣金费率，独立计算
     * @return float  平均成本价
     */
    private  function calculateStockCost($stock = [], $tax = 5, $commissionRate = 0.0002) {
        $stock_id = $this->stock_id;
        $stock_price = $this->stock_price;
        $type = (int)$this->stock_type;
        $stock_deal_total = (int)$this->stock_deal_total;

        $trades = Registry::get('db')->select(static::tableName(), ["stock_price", "stock_deal_total", "stock_type"], ["stock_id"=>$stock_id, "gone"=> 0, "stock_type" => [1, 2]]);
        $trades[] = [
            "stock_price" => $stock_price,
            "stock_deal_total" => $stock_deal_total,
            "stock_type" => $type,
        ];

        // 初始化变量
        $totalShares = 0;      // 累计股数
        $totalCost = 0;        // 总成本
        $cumulativeProfit = 0; // 累计收益

        $totalAmount = 0; // 总成交金额
        $totalFee = 0;    // 总买入费用
        $transferFeeRate = 0.00001; // 过户费费率（双向收取）

        foreach ($trades as $trade) {
            $price = $trade['stock_price'];
            $type = $trade['stock_type'];
            $shares = $trade['stock_deal_total'];
            $market = $trade['market'] ?? 'sh';
            $amount = $price * $shares;

            // 计算佣金（最低5元）
            $commission = max($amount * $commissionRate, $tax);
            // 计算过户费（仅沪市收取，最低0.01元）
            $transferFee = $market === 'sh' ? max($amount * $transferFeeRate, 0.01) : 0;
            $totalFee += $commission + $transferFee;
            if(1 == $type){
                $totalAmount += $amount;
                $totalShares += $shares;

            }else{
                $totalAmount -= $amount;
                $totalShares -= $shares;

            }

            
        }

        // 平均成本价 = (总成交金额+总费用) / 总持股数量
        $newCostPrice = round(($totalAmount + $totalFee) / $totalShares, 4);
        $profit = $totalAmount - $newCostPrice;
        
        return [$newCostPrice, 0];
    }

    /**
     * @author: lhh
     * 创建日期：2024-5-06
     * 修改日期：2025-12-19
     * 名称： create
     * 功能：
     * 说明：stock_detail 成功添加一条后，然后在操作stock和stock_daily表。 只有买入和卖出时才操作stock_history表
     * 注意：从2026-1-10起非买卖时不再往流水账存数据了
     * @return mixed
     */
    public function create() {
        $gone = 0;
        $stock_id = $this->stock_id;
        $type = (int)$this->stock_type;
        $stock_deal_total = (int)$this->stock_deal_total;
        $date_and_time = explode(' ',$this->created_at);
        $stock = StockModel::getById($stock_id);
        $userAndStockResult = UserAndStockModel::getByUseridAndStockId($this->userid, $this->stock_id);

        if(is_null($stock)){ //  
            $data['status'] = 0;
            $data['message'] = "股票代码{$stock_id}不存在";
            return $data;
        }

        
        // 简单成本计算
        $number = (int)$stock["stock_number"];
        $tax = $stock["tax"] ?? 5.00;
        $stock_id_ = $stock_id;
        if(1 == $stock['flag']){
            $stock_id_ = Registry::get('db')->get(StockModel::tableName(),"stock_id",["stock_code"=>$stock["stock_code"], "flag"=>0]);
        }
        
        if(static::hasBothStockIdAndDate($stock_id_, $this->created_at)){
            $data['status'] = 3;
            $data['message'] = $stock_id ."-". $stock['stock_name'] ." ". $this->created_at ." 同一时间不能再次写入同一个股票数据";
            return $data;
        }
        
        $stockModelDatas = [
            'stock_price' => $this->stock_price,
            'open' => $this->open,
            'close' => $this->close,
            'lup' => $this->lup,
            'ldown' => $this->ldown,
            'highest' => $this->highest,
            'lowest' => $this->lowest,
            'average' => $this->average,
            'amplitude' => $this->amplitude,
            'volume' => $this->volume,
            'amount' => $this->amount,
            'change' => $this->change,
            'updated_at' => $this->created_at,
        ];

        $userAndStockDatas = [
            'created_at' => $this->created_at,
            'flag' => $stock["flag"],
        ];

        $database = Registry::get('db');

        try {
            $database->pdo->beginTransaction();

            if($type > 0 || 0 == $stock["flag"]){// 为了避免数据冗余，只有非测试数据，或测试股票只在买卖时才往stock_detail里存储数据
                if($type > 0){
                    if(1 == $type){// 买入时
                        $number += $stock_deal_total; 

                    }else if(2 == $type) {// 卖出时 
                        $number -= $stock_deal_total;

                        if($number < 0){//检查剩余股票数量是否够卖
                            $data['status'] = 0;
                            $data['message'] = "剩股票数量不够";
                            return $data;

                        }else if(0 == $number){ // 清仓时
                            $gone = 1;
                            $userAndStockDatas["gone"] = $gone;
                            if(!static::liquidate($stock_id, $this->userid)) {
                                $data['status'] = 0;
                                $data['message'] = "清仓时异常";
                                $database->pdo->rollBack();
                                return $data;

                            }
                            $stockModelDatas["stock_cost"] = null;
                            $this->profit = 0;

                        }

                    } else {
                        $data['status'] = 0;
                        $data['message'] = "非法交易类别！必须是1或2";
                        $database->pdo->rollBack();
                        return $data;
                    }

                    if(0 === $gone){ // 不清仓时计算成本价
                        // 交易时要计算当前股票成本价须先获取它的历史买卖记录（非清仓）
                        list($costPrice, $profit) = $this->calculateStockCost($stock, $tax);
                        $stockModelDatas["stock_cost"] = $costPrice;
                        $this->profit = $profit;
                    }

                    
                    $stockModelDatas["bought"] = $this->stock_price;
                    $stockModelDatas["stock_number"] = $number;
                    $userAndStockDatas["stock_remain"] = $number;
                    $userAndStockDatas["stock_deal_total"] = $stock_deal_total;
                    $userAndStockDatas["created_at"] = $this->created_at;
                    $userAndStockDatas["bought"] = $stockModelDatas["bought"];
                    $userAndStockDatas["stock_cost"] = $stockModelDatas["stock_cost"];



                    $userAndStockModel = new UserAndStockModel($this->userid, $stock_id, $userAndStockDatas);

                    if(is_null($userAndStockResult)){
                        $userAndStockModelData = $userAndStockModel->create();
                        
                    }else{
                        $userAndStockModelData = $userAndStockModel->update();
                        
                    }
                    
                    if(0  == $userAndStockModelData['status']){
                        $database->pdo->rollBack();
                        return $userAndStockModelData;
                    }

                    // 已经到了百万数据了，所有从2026-1-10起只有买卖时才写入流水账
                    $stockDetailData = 
                    //  insert a data to the stock_detail
                    $database->insert(static::tableName(), [
                        'stock_id' => $stock_id,
                        'stock_price' => $this->stock_price,
                        'stock_deal_total' => $stock_deal_total,
                        'stock_type' => $type,
                        'stock_number' => $number,
                        'stock_date_at' => $date_and_time[0],
                        'stock_time_at' => $date_and_time[1],
                        'created_at' => $this->created_at,
                        'open' => $this->open,
                        'close' => $this->close,
                        'lup' => $this->lup,
                        'ldown' => $this->ldown,
                        'highest' => $this->highest,
                        'lowest' => $this->lowest,
                        'average' => $this->average,
                        'change' => $this->change,
                        'amplitude' => $this->amplitude,
                        'volume' => $this->volume,
                        'amount' => $this->amount,
                        'gone' => $gone,
                        'stock_detail_remark' => $this->stock_detail_remark,
                        'profit' => $this->profit,
                    ]);
                    $lastInsertId = $database->id();
                    $lastInsertId = (int)$lastInsertId;

                    if($lastInsertId){
                        // 交易历史
                        $theDataOfstockDateModel = (new StockHistoryModel($stock_id, [
                            'stock_price' => $this->stock_price,
                            'tax' => $tax,
                            'stock_detail_id' => $lastInsertId,
                            'stock_deal_total' => $this->stock_deal_total,
                            'stock_remain' => $number,
                            'stock_type' => $type,
                            'created_at' => $this->created_at,
                            'date_at' => $date_and_time[0],
                            'stock_cost' => $stockModelDatas["stock_cost"],
                        ]))->create();
                        if(0  == $theDataOfstockDateModel['status']){
                            $database->pdo->rollBack();
                            return $theDataOfstockDateModel;
                        }
                    }else{
                        $database->pdo->rollBack();
                        $data['status'] = 0;
                        $data['message'] = "failed: insert a data to the stock_detail" . " in " . __FILE__ . " on line " . __LINE__;
                    }
                }

                

                // var_dump($lastInsertId);

                $query = StockDateModel::getByIdAndDate($stock_id, $date_and_time[0]);
                        
                $stockDateModel = new StockDateModel($stock_id, $date_and_time[0], $this->created_at, [
                    'stock_price' => $this->stock_price,
                    'open' => $this->open,
                    'close' => $this->close,
                    'lup' => $this->lup,
                    'ldown' => $this->ldown,
                    'highest' => $this->highest,
                    'lowest' => $this->lowest,
                    'average' => $this->average,
                    'amplitude' => $this->amplitude,
                    'volume' => $this->volume,
                    'amount' => $this->amount,
                    'change' => $this->change,
                ]);

                
                
                
                if(!isset($query)){// create
                    $data = $stockDateModel->create();
                    if(0  == $data['status']){
                        $database->pdo->rollBack();
                        return $data;
                    }
        
                }else {// update
                    if($query["open"]  != $this->open 
                    || $query["highest"]  != $this->highest   
                    || $query["lowest"]  != $this->lowest   
                    || $query["average"]  != $this->average   
                    || $query["amplitude"]  != $this->amplitude   
                    || $query["change"]  != $this->change   
                    ){
                        $data = $stockDateModel->update();
                        if(0  == $data['status']){
                            $database->pdo->rollBack();
                            return $data;
                        }
                    }
                    
                }

            }

            $stockModel = new StockModel($this->userid, $stock_id, null, null, $stockModelDatas);
            $data2 = $stockModel->update();
            if(0  == $data2['status']){
                $database->pdo->rollBack();
                return $data2;
            }

            
            // $userAndStockModel = new UserAndStockModel($this->userid, $stock_id, $userAndStockDatas);
            // if(is_null($userAndStockResult)){
            //     $userAndStockModelData = $userAndStockModel->create();
                
            // }else{
            //     $userAndStockModelData = $userAndStockModel->update();
                
            // }
            // if(0  == $userAndStockModelData['status']){
            //     $database->pdo->rollBack();
            //     return $userAndStockModelData;
            // }



            $data['status'] = 1;
            $data['message'] = '数据存储成功';
            $database->pdo->commit();

        } catch (Exception $e) {
            $database->pdo->rollBack();
            $data['status'] = 0;
            $data['message'] = $e->getMessage() . " in " . __FILE__ . " on line " . __LINE__;
			
        }
        
        return $data;
    }

     /**
     * @author: lhh
     * 创建日期：2024-5-06
     * 修改日期：2025-12-11
     * 名称： delete
     * 功能：
     * 说明：除了非买卖数据外禁止删除
     * 注意：
     * @return mixed
     */
    public static function delete($id) {
        $database = Registry::get('db');
        // if(is_array($id)) {
            $sth  = $database->pdo->prepare("DELETE FROM ".static::tableName() ." WHERE stock_type = 0 AND FIND_IN_SET(id, :ids)");
            $ids = implode(",", $id);
            $sth->bindParam(':ids', $ids, \PDO::PARAM_STR);
        // } else {
        //     $sth  = $database->pdo->prepare("DELETE FROM ".static::tableName() ." WHERE stock_type = 0 AND id = :id limit 1");
        //     $sth->bindParam(':id', $id, \PDO::PARAM_STR);
        // }
        
        if($sth->execute()){
            $data['status'] = 0;
            $data['message'] = '删除成功';
        }else{
            $data['status'] = 1;
            $data['message'] = $sth->errorInfo();
        }
        return $data;
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
     * 创建日期：2025-11-19
     * 修改日期：2025-11-19
     * 名称： liquidate
     * 功能：清仓
     * 说明：
     * 注意：
     * @param $stock_id
     * @return boolean
     */
    private static function liquidate($stock_id, $userid = 1) {
        $stock  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET gone = 1 WHERE stock_id = :stock_id AND gone = 0 AND stock_type IN (1, 2)");
        $stock->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);

        $stockHistory  = Registry::get('db')->pdo->prepare("UPDATE ".StockHistoryModel::tableName() ." SET gone = 1 WHERE stock_id = :stock_id AND gone = 0");
        $stockHistory->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);
        
        // $userAndStock  = Registry::get('db')->pdo->prepare("UPDATE ".UserAndStockModel::tableName() ." SET gone = 1 WHERE userid = :userid, stock_id = :stock_id AND gone = 0");
        // $userAndStock->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);
        // $userAndStock->bindParam(':userid', $userid, \PDO::PARAM_STR);

        if($stock->execute() && $stockHistory->execute()){
            return true;
        }

        return false;
    }

    

}