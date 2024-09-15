<?php

use Yaf\Registry;

class StockDateModel
{

    private $stock_id,
            $stock_date_at, 
            $open,
            $close,
            $lup,
            $ldown,
            $hight, 
            $low, 
            $average,
            $amplitude,
            $change,
            $created_at;

    public function __construct($stock_id,
    $stock_date_at, 
    $open=0.00,
    $close=0.00,
    $lup=0.00,
    $ldown=0.00,
    $hight=0.00, 
    $low=0.00, 
    $average=0.00,
    $amplitude=0.00,
    $change=0.00,
    $created_at)
    {
        $this->stock_id = $stock_id;
        $this->stock_date_at = $stock_date_at;
        $this->open = $open;
        $this->close = $close;
        $this->lup = $lup;
        $this->ldown = $ldown;
        $this->hight = $hight;
        $this->low = $low;
        $this->average = $average;
        $this->amplitude = $amplitude;
        $this->change = $change;
        $this->created_at = $created_at;
    }

    public static function tableName()
    {
        return 'stock_date';
    }



    public static function getByIdAndDate($stock_id, $stock_date_at) {
        $query = Registry::get('db')->get(static::tableName(),"*",["stock_id"=>$stock_id, "stock_date_at"=>$stock_date_at]);
        return $query;
    }


    /**
     * @author: lhh
     * 创建日期：2024-5-16
     * 修改日期：2024-5-16
     * 名称： update
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public function update() {
        $sth  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET 
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
        WHERE `stock_id`=:stock_id AND `stock_date_at`=:stock_date_at");
        $sth->bindParam(':stock_id', $this->stock_id, \PDO::PARAM_STR);
        $sth->bindParam(':stock_date_at', $this->stock_date_at, \PDO::PARAM_STR);
        $sth->bindParam(':open', $this->open, \PDO::PARAM_INT);
        $sth->bindParam(':close', $this->close, \PDO::PARAM_INT);
        $sth->bindParam(':lup', $this->lup, \PDO::PARAM_INT);
        $sth->bindParam(':ldown', $this->ldown, \PDO::PARAM_INT);
        $sth->bindParam(':hight', $this->hight, \PDO::PARAM_INT);
        $sth->bindParam(':low', $this->low, \PDO::PARAM_INT);
        $sth->bindParam(':average', $this->average, \PDO::PARAM_INT);
        $sth->bindParam(':change', $this->change, \PDO::PARAM_INT);
        $sth->bindParam(':amplitude', $this->amplitude, \PDO::PARAM_INT);
        $sth->bindParam(':updated_at', $this->created_at, \PDO::PARAM_STR);
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
     * 创建日期：2024-5-16
     * 修改日期：2024-5-16
     * 名称： create
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public function create() {
        $sth  = Registry::get('db')->pdo->prepare("INSERT INTO ".static::tableName() ." SET 
        `stock_date_at`=:stock_date_at, 
        `stock_id`=:stock_id,  
        `open`=:open,
        `close`=:close,
        `lup`=:lup,
        `ldown`=:ldown,
        `hight`=:hight,  
        `low`=:low,
        `average`=:average,  
        `change`=:change,  
        `created_at`=:created_at,  
        `updated_at`=:updated_at,  
        `amplitude`=:amplitude");
        $sth->bindParam(':stock_id', $this->stock_id, \PDO::PARAM_STR);
        $sth->bindParam(':stock_date_at', $this->stock_date_at, \PDO::PARAM_STR);
        $sth->bindParam(':open', $this->open, \PDO::PARAM_INT);
        $sth->bindParam(':close', $this->close, \PDO::PARAM_INT);
        $sth->bindParam(':lup', $this->lup, \PDO::PARAM_INT);
        $sth->bindParam(':ldown', $this->ldown, \PDO::PARAM_INT);
        $sth->bindParam(':hight', $this->hight, \PDO::PARAM_INT);
        $sth->bindParam(':low', $this->low, \PDO::PARAM_INT);
        $sth->bindParam(':average', $this->average, \PDO::PARAM_INT);
        $sth->bindParam(':change', $this->change, \PDO::PARAM_INT);
        $sth->bindParam(':amplitude', $this->amplitude, \PDO::PARAM_INT);
        $sth->bindParam(':created_at', $this->created_at, \PDO::PARAM_STR);
        $sth->bindParam(':updated_at', $this->created_at, \PDO::PARAM_STR);

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