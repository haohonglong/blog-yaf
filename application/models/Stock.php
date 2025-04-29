<?php

use Yaf\Registry;
use base\model\StockModelBase;

class StockModel extends StockModelBase
{

    private $stock_code, $stock_name,  $stock_remark, $updated_at;

    public function __construct($stock_id, $stock_code, $stock_name, $data)
    {
        parent::__construct($stock_id, $data);
        $this->stock_code = $stock_code;
        $this->stock_name = $stock_name;
        $this->stock_remark = $data['stock_remark'] ?? "";
        $this->updated_at = $data['updated_at'] ?? null;

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
        return Registry::get('db')->get(static::tableName(),"stock_name",["stock_name"=>$name]);
    }

    public static function getById($stock_id) {
        return Registry::get('db')->get(static::tableName(), "*", ["stock_id"=>$stock_id]);
    }


    public static function getLastOneByStockId($stock_id) {
        $query = Registry::get('db')->get(static::tableName(),"*",["stock_id"=>$stock_id]);
        return $query;
    }


    /**
     * @author: lhh
     * 创建日期：2024-5-06
     * 修改日期：2024-5-06
     * 名称： getList
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public static function getList() {
        $query = Registry::get('db')->query("SELECT * FROM ".static::tableName()." ORDER BY level DESC")->fetchAll(\PDO::FETCH_ASSOC);
        foreach($query as $k => $v){
            $query[$k]['level'] = (int)$v['level'];
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
        $sth  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET stock_number=:stock_number WHERE stock_id = :stock_id limit 1");
        $sth->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);
        $sth->bindParam(':stock_number', $stock_number, \PDO::PARAM_INT);
        if($sth->execute()){
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
        $sth  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET level=:level WHERE stock_id = :stock_id limit 1");
        $sth->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);
        $sth->bindParam(':level', $level, \PDO::PARAM_INT);
        if($sth->execute()){
            return true;
        }

        return false;
        
    }

    /**
     * @author: lhh
     * 创建日期：2024-12-10
     * 修改日期：2024-12-10
     * 名称： setCost
     * 功能：修改股票成本
     * 说明：
     * 注意：
     * @param $id
     * @return mixed
     */
    public static function setCost($stock_id, $cost=null) {
        if('' == $cost) $cost = null;
        $sth  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET cost=:cost WHERE stock_id = :stock_id limit 1");
        $sth->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);
        $sth->bindParam(':cost', $cost, \PDO::PARAM_INT);
        if($sth->execute()){
            return true;
        }

        return false;
        
    }

    /**
     * @author: lhh
     * 创建日期：2024-5-21
     * 修改日期：2024-5-21
     * 名称： update
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public function update(){
        $sth  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET 
        `stock_price`=:stock_price, 
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
        `updated_at`=:updated_at
        WHERE `stock_id`=:stock_id");
        $sth->bindParam(':stock_id', $this->stock_id, \PDO::PARAM_STR);
        $sth->bindParam(':stock_price', $this->stock_price, \PDO::PARAM_INT);
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
        $sth->bindParam(':updated_at', $this->updated_at, \PDO::PARAM_STR);
        if($sth->execute()){
            $data['status'] = 1;
            $data['message'] = '更新成功';
        }else{
            $data['status'] = 0;
            $data['message'] = $sth->errorInfo();
        }
        return $data;
    }

    /**
     * @author: lhh
     * 创建日期：2024-5-06
     * 修改日期：2024-5-06
     * 名称： create
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public function create() {
        $created_at = date("Y-m-d H:i:s");
        $sth  = Registry::get('db')->pdo->prepare("INSERT INTO ".static::tableName() ." SET stock_id=:stock_id, stock_code=:stock_code, stock_name=:stock_name, stock_remark=:stock_remark, created_at=:created_at, updated_at=:created_at");
        $sth->bindParam(':stock_id', $this->stock_id, \PDO::PARAM_STR);
        $sth->bindParam(':stock_code', $this->stock_code, \PDO::PARAM_STR);
        $sth->bindParam(':stock_name', $this->stock_name, \PDO::PARAM_STR);
        $sth->bindParam(':stock_remark', $this->stock_remark, \PDO::PARAM_STR);
        $sth->bindParam(':created_at', $created_at, \PDO::PARAM_STR);
        if($sth->execute()){
            $data['status'] = 1;
            $data['message'] = '添加成功';
        }else{
            $data['status'] = 0;
            $data['message'] = $sth->errorInfo();
        }
        return $data;
    }

    public static function delete($stock_id) {
        $database = Registry::get('db');

        try {

            $database->pdo->beginTransaction();
            
            $stockDetail  = $database->pdo->prepare("DELETE FROM ".StockDetailModel::tableName() ." WHERE stock_id = :stock_id");
            $stockDetail->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);
            if($stockDetail->execute()) {

                $stockDate  = $database->pdo->prepare("DELETE FROM ".StockDateModel::tableName() ." WHERE stock_id = :stock_id");
                $stockDate->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);
                if($stockDate->execute()) {

                    $sth  = $database->pdo->prepare("DELETE FROM ".static::tableName() ." WHERE stock_id = :stock_id limit 1");
                    $sth->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);
                    if($sth->execute()){

                        $database->pdo->commit();
                        $data['status'] = 0;
                        $data['message'] = '删除成功';
                    }else {
                        $database->pdo->rollBack();
                        $data['status'] = 1;
                        $data['message'] = $sth->errorInfo();
                    }
                }else {

                    $database->pdo->rollBack();
                    $data['status'] = 1;
                    $data['message'] = $stockDate->errorInfo();
                }
            }else{

                $database->pdo->rollBack();
                $data['status'] = 1;
                $data['message'] = $stockDetail->errorInfo();
            }
            
        } catch (Exception $e) {

            $database->pdo->rollBack();
            $data['status'] = 1;
            $data['message'] = $e->getMessage();
        }
        
        return $data;
    }

    

}