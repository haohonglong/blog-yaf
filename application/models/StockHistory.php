<?php

use Yaf\Registry;
use base\model\StockModelBase;

class StockHistoryModel extends StockModelBase
{

    private $stock_deal_total, $stock_type, $created_at, $stock_detail_id;

    public function __construct($stock_id, $data)
    {
        parent::__construct($stock_id, $data);
        $this->stock_deal_total = $data['stock_deal_total'] ?? 0;
        $this->stock_type = $data['stock_type'] ?? 1;
        $this->created_at = $data['created_at'] ?? date("Y-m-d H:i:s");
        $this->stock_detail_id = $data['stock_detail_id'];
        $this->date_at = $data['date_at'];

    }

    public static function tableName()
    {
        return 'stock_history';
    }





    /**
     * @author: lhh
     * 创建日期：2024-12-26
     * 修改日期：2024-12-26
     * 名称： getList
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public static function getList() {
        $query = Registry::get('db')->query("SELECT * FROM ".static::tableName()." ")->fetchAll(\PDO::FETCH_ASSOC);
        
        return $query;
    }



    /**
     * @author: lhh
     * 创建日期：2025-6-28
     * 修改日期：2025-6-28
     * 名称： setCost
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public static function setCost($stock_id, $cost) {
        $date = date("Y-m-d");
        $sth  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET cost=:cost WHERE stock_id=:stock_id AND date_at=:date_at");
        $sth->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);
        $sth->bindParam(':cost', $cost, \PDO::PARAM_INT);
        $sth->bindParam(':date_at', $data, \PDO::PARAM_STR);
        if($sth->execute()){
            return true;
        }
        return $data;
    }







    /**
     * @author: lhh
     * 创建日期：2024-12-26
     * 修改日期：2024-12-26
     * 名称： create
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public function create() {
        $sth  = Registry::get('db')->pdo->prepare("INSERT INTO ".static::tableName() ." SET stock_id=:stock_id, stock_price=:stock_price, stock_deal_total=:stock_deal_total, stock_type=:stock_type, created_at=:created_at");
        $sth->bindParam(':stock_id', $this->stock_id, \PDO::PARAM_STR);
        $sth->bindParam(':stock_price', $this->stock_price, \PDO::PARAM_INT);
        $sth->bindParam(':stock_deal_total', $this->stock_deal_total, \PDO::PARAM_INT);
        $sth->bindParam(':stock_type', $this->stock_type, \PDO::PARAM_INT);
        $sth->bindParam(':created_at', $this->created_at, \PDO::PARAM_STR);
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