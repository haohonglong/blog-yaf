<?php

use Yaf\Registry;

class StockModel
{

    private $stock_id, $stock_name, $stock_price, $stock_remark
    ,$open
    ,$close
    ,$lup
    ,$ldown
    ,$hight
    ,$low
    ,$average
    ,$change
    ,$amplitude
    ,$updated_at;

    public function __construct($stock_id, $stock_name, $stock_remark="",
    $stock_price=0.00, 
    $open=0.00,
    $close=0.00,
    $lup=0.00,
    $ldown=0.00,
    $hight=0.00, 
    $low=0.00, 
    $average=0.00,
    $amplitude=0.00,
    $change=0.00,
    $updated_at=null)
    {
        $this->stock_id = $stock_id;
        $this->stock_name = $stock_name;
        $this->stock_price = $stock_price;
        $this->stock_remark = $stock_remark;

        $this->open = $open;
        $this->close = $close;
        $this->lup = $lup;
        $this->ldown = $ldown;
        $this->hight = $hight;
        $this->low = $low;
        $this->average = $average;
        $this->amplitude = $amplitude;
        $this->change = $change;
        $this->updated_at = $updated_at;
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
        return Registry::get('db')->get(static::tableName(),["stock_name", "stock_number"],["stock_id"=>$stock_id]);
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
        $query = Registry::get('db')->query("SELECT `stock_id`, `stock_name`, `stock_price`, `bought`, `stock_number`, `open`, `close`, `lup`, `ldown`, `hight`, `low`, `average`, `change`, `amplitude`, `updated_at` FROM ".static::tableName())->fetchAll(\PDO::FETCH_ASSOC);
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
        `hight`=:hight, 
        `low`=:low, 
        `average`=:average, 
        `change`=:change, 
        `amplitude`=:amplitude,
        `updated_at`=:updated_at
        WHERE `stock_id`=:stock_id");
        $sth->bindParam(':stock_id', $this->stock_id, \PDO::PARAM_STR);
        $sth->bindParam(':stock_price', $this->stock_price, \PDO::PARAM_INT);
        $sth->bindParam(':open', $this->open, \PDO::PARAM_INT);
        $sth->bindParam(':close', $this->close, \PDO::PARAM_INT);
        $sth->bindParam(':lup', $this->lup, \PDO::PARAM_INT);
        $sth->bindParam(':ldown', $this->ldown, \PDO::PARAM_INT);
        $sth->bindParam(':hight', $this->hight, \PDO::PARAM_INT);
        $sth->bindParam(':low', $this->low, \PDO::PARAM_INT);
        $sth->bindParam(':average', $this->average, \PDO::PARAM_INT);
        $sth->bindParam(':change', $this->change, \PDO::PARAM_INT);
        $sth->bindParam(':amplitude', $this->amplitude, \PDO::PARAM_INT);
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
        $sth  = Registry::get('db')->pdo->prepare("INSERT INTO ".static::tableName() ." SET stock_id=:stock_id, stock_name=:stock_name, stock_remark=:stock_remark, created_at=:created_at, updated_at=:created_at");
        $sth->bindParam(':stock_id', $this->stock_id, \PDO::PARAM_STR);
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

    

}