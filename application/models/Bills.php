<?php
/**
 * Created by PhpStorm.
 * User: long
 * Date: 04/05/2020
 * Time: 11:24 AM
 */

use Yaf\Registry;

class BillsModel
{
    public $bill_id=0,$shop_id=0,$discount=0.00,$price=0.00,$create_at,$update_at,$tatal_price = 0;
    public function __construct()
    {


    }

    public static function tableName()
    {
        return 'bills';
    }
    public static function generateId()
    {
        return time();
    }
    public static function getByBillId($billId) {
        return Registry::get('db')->get(static::tableName(),"bill_id",["bill_id"=>$billId]);
    }

    public function create() {
        $sth  = Registry::get('db')->pdo->prepare("INSERT INTO ".static::tableName() ." SET 
        bill_id=:bill_id, 
        shop_id=:shop_id, 
        discount=:discount, 
        price=:price, 
        create_at=:create_at, 
        update_at=:update_at");

        $this->price = (double)$this->tatal_price - (double)$this->discount;

        $sth->bindParam(':bill_id', $this->bill_id, \PDO::PARAM_INT);
        $sth->bindParam(':shop_id', $this->shop_id, \PDO::PARAM_INT);
        $sth->bindParam(':discount', $this->discount, \PDO::PARAM_INT);
        $sth->bindParam(':price', $this->price, \PDO::PARAM_INT);
        $sth->bindParam(':create_at', $this->create_at, \PDO::PARAM_INT);
        $sth->bindParam(':update_at', $this->create_at, \PDO::PARAM_INT);
        if($sth->execute()){
            $data['status'] = 1;
            $data['message'] = '添加成功';
        }else{
            $data['status'] = 0;
            $data['message'] = $sth->errorInfo();
        }
        return $data;
    }

    public function edit($bill_id) {
        $sth  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET 
        shop_id=:shop_id, 
        discount=:discount, 
        price=:price, 
        update_at=:update_at,
        WHERE bill_id=:bill_id
        ");

        $sth->bindParam(':bill_id', $bill_id, \PDO::PARAM_INT);
        $sth->bindParam(':shop_id', $this->shop_id, \PDO::PARAM_INT);
        $sth->bindParam(':discount', $this->discount, \PDO::PARAM_INT);
        $sth->bindParam(':price', $this->price, \PDO::PARAM_INT);
        $sth->bindParam(':update_at', $this->update_at, \PDO::PARAM_INT);

        if($sth->execute()){
            $data['status'] = 1;
            $data['message'] = '修改成功';
        }else{
            $data['status'] = 0;
            $data['message'] = $sth->errorInfo();
        }
        return $data;
    }

    public static function delete($bill_id) {
        $sth  = Registry::get('db')->pdo->prepare("DELETE FROM ".static::tableName() ." WHERE bill_id=:bill_id limit 1");
        $sth->bindParam(':bill_id', $bill_id, \PDO::PARAM_INT);
        if($sth->execute()){
            $data['status'] = 1;
            $data['message'] = '删除成功';
        }else{
            $data['status'] = 0;
            $data['message'] = $sth->errorInfo();
        }
        return $data;
    }
}