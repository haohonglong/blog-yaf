<?php

use Yaf\Registry;
use base\model\StockModelBase;

class StockDateModel extends StockModelBase
{

    private $stock_date_at, $created_at;

    public function __construct($stock_id, $stock_date_at, $created_at, $data = [])
    {
        parent::__construct($stock_id, $data);
        $this->stock_date_at = $stock_date_at;
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
     * 创建日期：2024-5-06
     * 修改日期：2024-5-06
     * 名称： getList
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public static function getListByStockId($stock_id) {
        $query = Registry::get('db')->select(static::tableName(),"*",["stock_id"=>$stock_id]);

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
        WHERE `stock_id`=:stock_id AND `stock_date_at`=:stock_date_at");
        $sth->bindParam(':stock_id', $this->stock_id, \PDO::PARAM_STR);
        $sth->bindParam(':stock_price', $this->stock_price, \PDO::PARAM_INT);
        $sth->bindParam(':stock_date_at', $this->stock_date_at, \PDO::PARAM_STR);
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
        `stock_price`=:stock_price,  
        `open`=:open,
        `close`=:close,
        `lup`=:lup,
        `ldown`=:ldown,
        `highest`=:highest,  
        `lowest`=:lowest,
        `average`=:average,  
        `change`=:change,
        `created_at`=:created_at,  
        `updated_at`=:updated_at,
        `volume`=:volume,
        `amount`=:amount,  
        `amplitude`=:amplitude");
        $sth->bindParam(':stock_id', $this->stock_id, \PDO::PARAM_STR);
        $sth->bindParam(':stock_date_at', $this->stock_date_at, \PDO::PARAM_STR);
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