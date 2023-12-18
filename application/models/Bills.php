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
    private $bill_id=0,
            $discount=0.00,
            $create_at,
            $update_at,
            $points = 0;
    public function __construct($bill_id, $points, $discount, $create_at)
    {
        $this->bill_id  = $bill_id;
        $this->points  = $points;
        $this->discount  = $discount;
        $this->create_at  = $create_at;
        $this->update_at  = $create_at;
    }



    public static function tableName()
    {
        return 'bills';
    }

    public static function getByBillId($bill_id) {
        return Registry::get('db')->get(static::tableName(),"bill_id",["bill_id"=>$bill_id]);
    }

    public static function geOnetByBillId($bill_id) {
        $db =  Registry::get('db');
        $data  = $db->get(static::tableName(),["bill_id", "discount", "points", "update_at"], ["bill_id"=>$bill_id]);
        $query = $db->query("
                                                SELECT 
                                                    g.code as code,
                                                    gn.goodsname as goodsname,
                                                    g.shop_id as shop_id,
                                                    u.unit_name as unit_name,
                                                    g.number as number,
                                                    g.weight as weight,
                                                    g.single_price  as single_price,
                                                    g.final_price as final_price
                                                FROM ". GoodsModel::tableName() ." as g 
                                                INNER JOIN ". GoodsnameModel::tableName(). " as gn ON g.goodsname_id = gn.goodsname_id
                                                INNER JOIN ". UnitModel::tableName() ." as u         ON g.unit_id      = u.unit_id
                                                WHERE g.bill_id = :bill_id
                                                
                                                ", [ ":bill_id" => $bill_id])->fetchAll();


        foreach($query as $item) {
            $goodses[] = [
                "shop_id" => $item["shop_id"],
                "goodsname" => $item["goodsname"],
                "code" => $item["code"],
                "unit_name" => $item["unit_name"],
                "number" => $item["number"],
                "weight" => $item["weight"],
                "single_price" => $item["single_price"],
                "final_price" => $item["final_price"],
            ];

        }
        
        if(isset($data)) {
            $data["shop_id"] = $goodses[0]['shop_id'];
            $data["update_at"] =  date('Y-m-d',$data['update_at']);
            $data["goodses"] =  $goodses;
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
            $data['message'] = 'there is not anything the bill_id';
        }


        return $data;

    }

    public function create() {
        $sth  = Registry::get('db')->pdo->prepare("INSERT INTO ".static::tableName() ." SET 
        bill_id=:bill_id, 
        discount=:discount,
        points=:points, 
        create_at=:create_at, 
        update_at=:update_at");

        $sth->bindParam(':bill_id', $this->bill_id, \PDO::PARAM_INT);
        $sth->bindParam(':discount', $this->discount, \PDO::PARAM_INT);
        $sth->bindParam(':points', $this->points, \PDO::PARAM_INT);
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
        discount=:discount,
        points=:points, 
        update_at=:update_at,
        WHERE bill_id=:bill_id
        ");

        $sth->bindParam(':bill_id', $bill_id, \PDO::PARAM_INT);
        $sth->bindParam(':points', $this->points, \PDO::PARAM_INT);
        $sth->bindParam(':discount', $this->discount, \PDO::PARAM_INT);
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
        if(1 == GoodsModel::deleteByBillId($bill_id)) {
            $sth  = Registry::get('db')->pdo->prepare("DELETE FROM ".static::tableName() ." WHERE bill_id=:bill_id limit 1");
            $sth->bindParam(':bill_id', $bill_id, \PDO::PARAM_INT);
            if($sth->execute()){
                $data['status'] = 1;
                $data['message'] = '删除成功';
            }else{
                $data['status'] = 0;
                $data['message'] = $sth->errorInfo();
            }
        } else {
            $data['status'] = 0;
            $data['message'] = "删除goods失败";
        }
        
        return $data;
    }
}