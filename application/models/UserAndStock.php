<?php

use Yaf\Registry;


class UserAndStockModel
{

    private $userid, $stock_id, $bought, $tax, $stock_deal_total, $stock_remain, $gone, $created_at, $stock_cost, $flag;

    public function __construct($userid=1, $stock_id, $data=[])
    {
        $this->userid = $userid;
        $this->stock_id = $stock_id;
        $this->bought = $data['bought'] ?? 0;
        $this->tax = $data['tax'] ?? 5.00;
        $this->stock_deal_total = $data['stock_deal_total'] ?? 0;
        $this->stock_remain = $data['stock_remain'] ?? 0;
        $this->gone = $data['gone'] ?? 0;
        $this->created_at = $data['created_at'];
        $this->stock_cost = $data['stock_cost'] ?? null;
        $this->flag = $data['flag'] ?? 0;
        $this->flag = (int)($this->flag);

    }
    public static function tableName() {
        return 'user_stock';
    }

    public static function getByUseridAndStockId($userid, $stock_id) {
        return Registry::get('db')->get(static::tableName(), "*", ["userid"=>$userid, "stock_id"=>$stock_id]);
    }

    /**
     * @author: lhh
     * 创建日期：2025-9-2
     * 修改日期：2025-9-2
     * 名称： setCost
     * 功能：设定成本价
     * 说明：
     * 注意：
     * @return mixed
     */
    public static function setCost($userid = 1, $stock_id, $cost, $tax=5.00, $updated_at) {
        $updated_at = $updated_at ?? date("Y-m-d H:i:s");

        if(is_null(static::getByUseridAndStockId($userid, $stock_id))){
            $data['status'] = 1;
            
        }else{
            $sth  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET cost=:cost, tax=:tax, updated_at=:updated_at WHERE userid=:userid AND stock_id=:stock_id");
            $sth->bindParam(':userid', $userid, \PDO::PARAM_STR);
            $sth->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);
            $sth->bindParam(':cost', $cost, \PDO::PARAM_STR);
            $sth->bindParam(':tax', $tax, \PDO::PARAM_STR);
            $sth->bindParam(':updated_at', $updated_at, \PDO::PARAM_STR);
            if($sth->execute()){
                $data['status'] = 1;
                $data['message'] = '修改账单的成本价成功';
            }else{
                $data['status'] = 0;
                $data['message'] = $sth->errorInfo() . " in " . __FILE__ . " on line " . __LINE__;
            }
        }
        
        return $data;
    }



    /**
     * @author: lhh
     * 创建日期：2025-9-01
     * 修改日期：2025-12-19
     * 名称： create
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public function create() {
        // var_dump($this);exit;
        $stock  = Registry::get('db')->pdo->prepare("INSERT INTO ".static::tableName() ." SET userid=:userid, stock_id=:stock_id, bought=:bought, cost=:cost, tax=:tax, stock_deal_total=:stock_deal_total, stock_remain=:stock_remain, flag=:flag, gone=:gone, created_at=:created_at, updated_at=:created_at");
        $stock->bindParam(':userid', $this->userid, \PDO::PARAM_STR);
        $stock->bindParam(':stock_id', $this->stock_id, \PDO::PARAM_STR);
        $stock->bindParam(':bought', $this->bought, \PDO::PARAM_STR);
        $stock->bindParam(':cost', $this->stock_cost, \PDO::PARAM_STR);
        $stock->bindParam(':tax', $this->tax, \PDO::PARAM_STR);
        $stock->bindParam(':stock_deal_total', $this->stock_deal_total, \PDO::PARAM_STR);
        $stock->bindParam(':stock_remain', $this->stock_remain, \PDO::PARAM_STR);
        $stock->bindParam(':flag', $this->flag, \PDO::PARAM_STR);
        $stock->bindParam(':gone', $this->gone, \PDO::PARAM_STR);
        $stock->bindParam(':created_at', $this->created_at, \PDO::PARAM_STR);
        
        
        if($stock->execute()){
            $data['status'] = 1;
            $data['message'] = '添加成功';
        }else{
            $data['status'] = 0;
            $errorInfo = $stock->errorInfo();
            if (is_array($errorInfo)) {
                $data['message'] = implode(' | ', $errorInfo) . " in " . __FILE__ . " on line " . __LINE__;
            } else {
                $data['message'] = (string)$errorInfo . " in " . __FILE__ . " on line " . __LINE__;
            }
        }
        return $data;
    }

    /**
     * @author: lhh
     * 创建日期：2025-9-01
     * 修改日期：2025-9-01
     * 名称： update
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public function update(){
        $others = "";
        if(!is_null($this->bought)  && $this->bought > 0){
            $others .= ", bought=:bought, `cost`=:cost";
        }

        if(!is_null($this->stock_remain)){
            $others .= ", stock_remain=:stock_remain";
        }

        $stock  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET 
         
        `tax`=:tax, 
        `stock_deal_total`=:stock_deal_total, 
        `gone`=:gone,  
        `updated_at`=:updated_at". $others ." 
        WHERE userid=:userid AND stock_id=:stock_id");

        if(!is_null($this->bought) && $this->bought > 0){
            $stock->bindParam(':bought', $this->bought, \PDO::PARAM_STR);
            $stock->bindParam(':cost', $this->stock_cost, \PDO::PARAM_STR);
        }

        
        if(!is_null($this->stock_remain)){
            $stock->bindParam(':stock_remain', $this->stock_remain, \PDO::PARAM_STR);
            
        }

        $stock->bindParam(':userid', $this->userid, \PDO::PARAM_STR);
        $stock->bindParam(':stock_id', $this->stock_id, \PDO::PARAM_STR);
        $stock->bindParam(':tax', $this->tax, \PDO::PARAM_STR);
        $stock->bindParam(':stock_deal_total', $this->stock_deal_total, \PDO::PARAM_STR);
        $stock->bindParam(':gone', $this->gone, \PDO::PARAM_STR);
        $stock->bindParam(':updated_at', $this->created_at, \PDO::PARAM_STR);

        if($stock->execute()){
            $data['status'] = 1;
            $data['message'] = '更新成功';
        }else{
            $data['status'] = 0;
            $data['message'] = $stock->errorInfo() . " in " . __FILE__ . " on line " . __LINE__;
        }
        return $data;
    }

    

}