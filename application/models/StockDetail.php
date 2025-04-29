<?php

use Yaf\Registry;
use base\model\StockModelBase;

class StockDetailModel extends StockModelBase
{

    private  $stock_type, $stock_deal_total, $created_at, $stock_detail_remark="";

    public function __construct($stock_id, $data = [])
    {
        parent::__construct($stock_id, $data);
        $this->stock_type = $data['stock_type'];
        $this->stock_deal_total = $data['stock_deal_total'];
        $this->created_at = $data['created_at'];
        $this->stock_detail_remark = $data['stock_detail_remark'] ?? "";

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
     * 创建日期：2024-12-11
     * 修改日期：2024-12-11
     * 名称： setGone
     * 功能：清仓
     * 说明：
     * 注意：
     * @param $id
     * @return mixed
     */
    public static function setGone($stock_id, $date) {
        $sth  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET gone=:gone WHERE stock_id = :stock_id AND stock_date_at < :date");
        $sth->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);
        $sth->bindParam(':date', $date, \PDO::PARAM_STR);
        $sth->bindParam(':gone', 1, \PDO::PARAM_INT);
        if($sth->execute()){
            return true;
        }

        return false;
        
    }

    


    /**
     * @author: lhh
     * 创建日期：2024-5-06
     * 修改日期：2024-5-06
     * 名称： getList
     * 功能：fetching something from stock_detail
     * 说明：
     * 注意：
     * @return mixed
     */
    public static function getList($stock_id=null, $size=1) {
        // $rows = 300;
        // $size = ($size-1) * $rows;

        if(isset($stock_id)){
            $query = Registry::get('db')->query("SELECT 
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
            a.stock_detail_remark
            
        FROM ".static::tableName()." as a 
        WHERE a.stock_id = '{$stock_id}'
        ORDER by a.id DESC 
        ")->fetchAll();
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
     * 创建日期：2024-5-06
     * 修改日期：2024-12-26
     * 名称： create
     * 功能：
     * 说明：stock_detail 成功添加一条后，然后在操作stock和stock_date表。 只有买入和卖出时才操作stock_history表
     * 注意：
     * @return mixed
     */
    public function create() {
        $stock_id = $this->stock_id;
        $type = (int)$this->stock_type;
        $stock_deal_total = (int)$this->stock_deal_total;
        $date_and_time = explode(' ',$this->created_at);
        $result = StockModel::getById($stock_id);
        if(!isset($result)){ //  is the stock_id whether legal 
            $data['status'] = 0;
            $data['message'] = "股票代码{$stock_id}不存在";
            return $data;
        }

        
        if(static::hasBothStockIdAndDate($stock_id, $this->created_at)){
            $data['status'] = 3;
            $data['message'] = $stock_id ."-". $result['stock_name'] ." ". $this->created_at ." 同一时间不能再次写入同一个股票数据";
            return $data;
        }
        
        $number = (int)$result["stock_number"];
        $database = Registry::get('db');

        try {

            $database->pdo->beginTransaction();

            if($type > 0){
                if(1 == $type){// 买入时
                    $number += $stock_deal_total; 
                    $sthStockModel  = $database->pdo->prepare("UPDATE ".StockModel::tableName() ." SET 
                    `bought`=:bought 
                    WHERE `stock_id`=:stock_id");
                    $sthStockModel->bindParam(':stock_id', $this->stock_id, \PDO::PARAM_STR);
                    $sthStockModel->bindParam(':bought', $this->stock_price, \PDO::PARAM_INT);
                    $sthStockModel->execute();

                }else if(2 == $type) {// 卖出时 
                    $number -= $stock_deal_total;
                    if($number < 0){//检查剩余股票数量是否够卖
                        $data['status'] = 0;
                        $data['message'] = "剩股票数量不够";
                        return $data;
                    } elseif (0 == $number) {// 清仓时
                        static::setGone($this->stock_id, $date_and_time[0]);
                    }
                } else {
                    return false;
                }
    
                if(!StockModel::setStockNumber($stock_id, $number)){ // 
                    $database->pdo->rollBack();
                    $data['status'] = 0;
                    $data['message'] = "stock表更新stock_number时失败";
                    return $data;
                }

                // 交易历史
                $theDataOfstockDateModel = (new StockHistoryModel($stock_id, [
                    'stock_price' => $this->stock_price,
                    'stock_deal_total' => $this->stock_deal_total,
                    'stock_type' => $type,
                    'created_at' => $this->created_at,
                ]))->create();
                if(0  == $theDataOfstockDateModel['status']){
                    $database->pdo->rollBack();
                    return $theDataOfstockDateModel;
                }
                
            }

            $sql = " SET `stock_id`=:stock_id, 
            `stock_price`=:stock_price, 
            `stock_deal_total`=:stock_deal_total,
            `stock_type`=:stock_type,
            `stock_number`=:stock_number,
            `stock_date_at`=:stock_date_at,
            `stock_time_at`=:stock_time_at,
            `created_at`=:created_at,
            
            `open`=:open,
            `close`=:close,
            `lup`=:lup,
            `ldown`=:ldown,
            `highest`=:highest,  
            `lowest`=:lowest,
            
            `average`=:average,  
            `change`=:change,  
            `amplitude`=:amplitude,
            `volume`=:volume,
            `amount`=:amount,
            `stock_detail_remark`=:stock_detail_remark
            ";


            $sth  = $database->pdo->prepare("INSERT INTO ".static::tableName() . $sql);

            $sth->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);
            $sth->bindParam(':stock_price', $this->stock_price, \PDO::PARAM_INT);
            $sth->bindParam(':stock_deal_total', $stock_deal_total, \PDO::PARAM_INT);
            $sth->bindParam(':stock_type', $type, \PDO::PARAM_INT);
            $sth->bindParam(':stock_number', $number, \PDO::PARAM_INT);
            $sth->bindParam(':stock_date_at', $date_and_time[0], \PDO::PARAM_STR);
            $sth->bindParam(':stock_time_at', $date_and_time[1], \PDO::PARAM_STR);
            $sth->bindParam(':created_at', $this->created_at, \PDO::PARAM_STR);
            $sth->bindParam(':open', $this->open, \PDO::PARAM_INT);
            $sth->bindParam(':close', $this->close, \PDO::PARAM_INT);
            $sth->bindParam(':lup', $this->lup, \PDO::PARAM_INT);
            $sth->bindParam(':ldown', $this->ldown, \PDO::PARAM_INT);
            $sth->bindParam(':highest', $this->highest, \PDO::PARAM_INT);
            $sth->bindParam(':lowest', $this->lowest, \PDO::PARAM_INT);
            $sth->bindParam(':average', $this->average, \PDO::PARAM_INT);
            $sth->bindParam(':change', $this->change, \PDO::PARAM_INT);
            $sth->bindParam(':amplitude', $this->amplitude, \PDO::PARAM_INT);
            $sth->bindParam(':volume', $this->volume, \PDO::PARAM_INT);
            $sth->bindParam(':amount', $this->amount, \PDO::PARAM_INT);
            $sth->bindParam(':stock_detail_remark', $this->stock_detail_remark, \PDO::PARAM_STR);
            
            if($sth->execute()){
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

                
                $stockModel = new StockModel($stock_id, null, null, [
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
                ]);
        
                $data2 = $stockModel->update();
                if(0  == $data2['status']){
                    $database->pdo->rollBack();
                    return $data2;
                }
                
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
                $data['status'] = 1;
                $data['message'] = '添加成功';
                $database->pdo->commit();
            }else{
                $database->pdo->rollBack();
                $data['status'] = 0;
                $data['message'] = $sth->errorInfo();
            }
        } catch (Exception $e) {
            $database->pdo->rollBack();
            $data['status'] = 0;
            $data['message'] = $e->getMessage();
			
        }
        
        return $data;
    }

    public static function delete($id) {
        $database = Registry::get('db');
        if(is_array($id)) {
            $sth  = $database->pdo->prepare("DELETE FROM ".static::tableName() ." WHERE FIND_IN_SET(id, :ids)");
            $ids = implode(",", $id);
            $sth->bindParam(':ids', $ids, \PDO::PARAM_STR);
        } else {
            $sth  = $database->pdo->prepare("DELETE FROM ".static::tableName() ." WHERE id = :id limit 1");
            $sth->bindParam(':id', $id, \PDO::PARAM_INT);
        }
        
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

    

}