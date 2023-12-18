<?php
/**
 * Created by PhpStorm.
 * User: long
 * Date: 04/05/2020
 * Time: 11:24 AM
 */


use Yaf\Registry;

class GoodsModel
{
    private $shop_id=0, $bill_id, $create_at,$update_at;
    private $codes         = [],
            $goodsname_ids = [],
            $unit_ids      = [],
            $numbers       = [],
            $weights       = [],
            $single_prices = [],
            $final_prices = [];
    private $Bill=null;

    public function __construct(BillsModel $bill, $shop_id, $bill_id, $codes, 
                                $goodsname_ids, $numbers, $weights, $unit_ids, 
                                $single_prices, $final_prices, $create_at) {
        $this->Bill = $bill;
        $this->shop_id = $shop_id;
        $this->bill_id = $bill_id;
        $this->codes = $codes;
        $this->goodsname_ids = $goodsname_ids;
        $this->numbers = $numbers;
        $this->weights = $weights;
        $this->unit_ids = $unit_ids;
        $this->single_prices = $single_prices;
        $this->final_prices = $final_prices;
        $this->create_at = $create_at;
        $this->update_at = $create_at;
    }

    public static function tableName() {
        return 'goods';
    }

    public static function listAll() {
        $query = Registry::get('db')->query("
                                                SELECT 
                                                    g.id,
                                                    gn.goodsname as name,
                                                    g.goodsname_id as goodsname_id,
                                                    g.code,
                                                    g.final_price,
                                                    g.create_at as create_at, 
                                                    g.shop_id as shop_id,
                                                    s.name as shop_name,
                                                    g.bill_id as bill_id,
                                                    b.points,
                                                    b.discount
                                                    
                                                FROM ".static::tableName()." as g 
                                                INNER JOIN ". BillsModel::tableName()." as b USING(bill_id)
                                                INNER JOIN ". GoodsnameModel::tableName(). " as gn USING(goodsname_id)
                                                INNER JOIN ".ShopModel::tableName()." as s ON s.id = g.shop_id
                                                ORDER by g.id DESC 
                                                ")->fetchAll();

        $data = [];
        foreach ($query as $k => $v) {
            if(!isset($data[$v['shop_id']]))  {
                $data[$v['shop_id']] = [
                    'shop_id' => $v['shop_id'],
                    'shop_name'=>$v['shop_name'],
                    'childs' => [],
                ];
            }

            

            // if(!(isset($v['bill_id']) && !empty($v['bill_id']))){
            //     $v['bill_id'] = 0;
            // }

            $v['create_at'] = date('Y-m-d',$v['create_at']);


            if(!isset($data[$v['shop_id']]['childs'][$v['bill_id']])){
                $data[$v['shop_id']]['childs'][$v['bill_id']] = [
                    'bill_id'=>$v['bill_id'],
                    'points'=>$v['points'],
                    'discount'=>$v['discount'],
                    'create_at'=>$v['create_at'],
                    'childs'=>[],
                ];
            }
            $data[$v['shop_id']]['childs'][$v['bill_id']]['childs'][] = [
                'id'=>$v['id'],
                'name'=>$v['name'],
                'code'=>$v['code'],
                'final_price'=>$v['final_price'],
                'create_at'=>$v['create_at'],
            ];
        }
//        echo "<pre>";
//        print_r($data);
//        echo "</pre>";

        $data = array_values($data);
        return $data;
    }

    public function create() {
        $datas = [];
        $data = [];

        foreach ($this->goodsname_ids as $i => $v) {
            
            $datas[$i]['shop_id']       = $this->shop_id;
            $datas[$i]['bill_id']       = $this->bill_id;
            $datas[$i]['code']          = $this->codes[$i];
            $datas[$i]['number']        = $this->numbers[$i];
            $datas[$i]['weight']        = $this->weights[$i];
            $datas[$i]['goodsname_id']  = $this->goodsname_ids[$i];
            $datas[$i]['unit_id']       = $this->unit_ids[$i];
            $datas[$i]['single_price']  = $this->single_prices[$i];
            $datas[$i]['final_price']   = $this->final_prices[$i];
            $datas[$i]['create_at']     = $this->create_at;
            $datas[$i]['update_at']     = $this->create_at;
        }

        $n = BillsModel::getByBillId($this->bill_id);
        $bill = $this->Bill;
        Registry::get('db')->action(function($database) use($datas, $bill, &$data, $n) {
            
            if(!isset($n)){
                $data = $bill->create();
                if(0 === $data['status']){
                    return false;
                }
            }

            $database->insert(static::tableName(), $datas);
            if($database->id()) {
                $data['status'] = 1;
                $data['message'] = '创建成功';
            }else {
                $data['status'] = 0;
                $data['message'] = '创建失败';
                return false;
            }
        });



        return $data;
    }

    public function edit($id,$data = [
        'shop_id'=>0,
        'bill_id'=>0,
        'create_at'=>'',
        'update_at'=>'',
        'goodsname_id' => 0,
        'unit_id'      => 0,
        'number'       => 0,
        'code'       => '',
        'weight'       => '',
        'single_price' => 0,
    ]) {
        $sth  = Yaf_Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET 
            shop_id=:shop_id, 
            bill_id = :bill_id, 
            number = :number, 
            weight = :weight, 
            goodsname_id = :goodsname_id, 
            unit_id = :unit_id, 
            single_price = :single_price, 
            final_price = :final_price, 
            create_at = :create_at, 
            update_at = :update_at, 
            code = :code
            WHERE id=:id
            ");
        $sth->bindParam(':id', $id, \PDO::PARAM_INT);
        $sth->bindParam(':update_at', $data['update_at'], \PDO::PARAM_INT);
        $sth->bindParam(':final_price', $data['final_price'], \PDO::PARAM_INT);
        $sth->bindParam(':single_price', $data['single_price'], \PDO::PARAM_INT);
        $sth->bindParam(':number', $data['number'], \PDO::PARAM_INT);
        $sth->bindParam(':weight', $data['weight'], \PDO::PARAM_STR);
        $sth->bindParam(':shop_id', $data['shop_id'], \PDO::PARAM_INT);
        $sth->bindParam(':bill_id', $data['bill_id'], \PDO::PARAM_STR);
        $sth->bindParam(':code', $data['code'], \PDO::PARAM_STR);
        $sth->bindParam(':goodsname_id', $data['goodsname_id'], \PDO::PARAM_INT);
        $sth->bindParam(':unit_id', $data['unit_id'], \PDO::PARAM_INT);
        $data = [];
        if($sth->execute()){
            $data['status'] = 1;
            $data['message'] = '修改成功';
        }else{
            $data['status'] = 0;
            $data['message'] = $sth->errorInfo();
        }
        return $data;
    }

    public static function deleteByBillId($bill_id) {
        $sth  = Registry::get('db')->pdo->prepare("DELETE FROM ".static::tableName() ." WHERE bill_id = :bill_id");
        $sth->bindParam(':bill_id', $bill_id, \PDO::PARAM_STR);
        if($sth->execute()){
            $data['status'] = 1;
            $data['message'] = '删除成功';
        }else{
            $data['status'] = 0;
            $data['message'] = $sth->errorInfo();
        }
        return $data;
    }

    public static function delete($id) {
        if(is_array($id)) {
            $sth  = Registry::get('db')->pdo->prepare("DELETE FROM ".static::tableName() ." WHERE id IN (:ids)");
            $sth->bindParam(':ids', implode(',',$id), \PDO::PARAM_STR);
        } else {
            $sth  = Registry::get('db')->pdo->prepare("DELETE FROM ".static::tableName() ." WHERE id = :id limit 1");
            $sth->bindParam(':id', $id, \PDO::PARAM_INT);
        }
        
        if($sth->execute()){
            $data['status'] = 1;
            $data['message'] = '删除成功';
        }else{
            $data['status'] = 0;
            $data['message'] = $sth->errorInfo();
        }
        return $data;
    }


    /**
     * 创建日期：2023-12-05
     * 修改日期：2023-12-05
     * 名称： statistics
     * 功能：统计每个店铺的同类商品
     * 说明：根据选定的日期范围显示每个商店同类商品的价格
     * 注意：
     */
    public static function statistics($goodsname_id = 0, $start_date = 0, $end_date = 0, $G = false) {
        $query = Registry::get('db')->query("
        SELECT g.id, g.shop_id, s.name, g.single_price as price, DATE_FORMAT(FROM_UNIXTIME(g.create_at), '%Y-%m') as date
        FROM ".static::tableName()." as g
        INNER JOIN ".ShopModel::tableName()." as s ON s.id = g.shop_id
        WHERE g.goodsname_id = {$goodsname_id} AND g.create_at BETWEEN {$start_date} AND {$end_date}

        ORDER BY g.create_at ASC
        ")->fetchAll();

        $data =  [];
        $dates = [];

        foreach($query as $k => $v) {
            $name = $v['name'];
            $shop_id = $v['shop_id'];
            if(!isset($data[$shop_id])) {
                $data[$shop_id]= [
                    "name" => $name,
                    // "childs" => [],
                    "prices" => [],
                ];

            }

            if($G) {
                $price = $v["price"] / 2;
            } else {
                $price = $v["price"];
            }

            $data[$shop_id]["prices"][] = (float)(number_format($price , 2));
            if(!in_array($v["date"], $dates)) {
                $dates[] = $v["date"];
            }

            // $data[$shop_id]["childs"][] = [
            //     "id" => $v["id"],
            //     "price" => number_format($v["price"] / 2, 2),
            //     "date" => $v["date"],
            // ];


        }
        

        // $data = array_values($data);
        if(!empty($data)){
            $result['status'] = 1;
            $result['list'] = $data;
            $result['dates'] = $dates;
        }else{
            $result['status'] = 0;
            $result['message'] = "没有数据";
        }
        return $result;
    }
}