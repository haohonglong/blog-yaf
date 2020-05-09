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
    public $shop_id=0,$bill_id,$create_by,$update_by;
    public  $name         = [],
            $number       = [],
            $weight       = [],
            $single_price = [];
    public $bill=null;

    public function __construct(BillsModel $bill) {
        $this->bill = $bill;
    }

    public static function tableName() {
        return 'goods';
    }

    public static function listAll() {
        $query = Registry::get('db')->query("
SELECT b.bill_id as bill_id,b.price as total_price,b.discount,s.name as shop_name,g.id,g.name,g.final_price, g.create_by as create_at FROM ".BillsModel::tableName()." as b 
RIGHT JOIN ".static::tableName()." as g USING(bill_id)
LEFT JOIN ".ShopModel::tableName()." as s ON g.shop_id = s.id
ORDER by g.create_by DESC 
")->fetchAll();

        $data = [];
        foreach ($query as $k => $v) {
            if(!(isset($v['bill_id']) && !empty($v['bill_id']))){
                $v['bill_id'] = 0;

            }

            $v['create_at'] = date('Y-m-d',$v['create_at']);

            if(!isset($data[$v['bill_id']])){
                $data[$v['bill_id']] = [
                    'bill_id'=>$v['bill_id'],
                    'shop_name'=>$v['shop_name'],
                    'total_price'=>$v['total_price'],
                    'discount'=>$v['discount'],
                    'create_at'=>$v['create_at'],
                    'childs'=>[],
                ];
            }
            $data[$v['bill_id']]['childs'][] = [
                'id'=>$v['id'],
                'name'=>$v['name'],
                'shop_name'=>$v['shop_name'],
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

        $total = 0;
        foreach ($this->name as $i => $v) {
            $datas[$i]['shop_id']       = $this->shop_id;
            $datas[$i]['bill_id']       = $this->bill_id;
            $datas[$i]['name']          = $this->name[$i];
            $datas[$i]['number']        = $this->number[$i];
            $datas[$i]['weight']        = $this->weight[$i];
            $datas[$i]['single_price']  = $this->single_price[$i];
            $datas[$i]['create_by']     = $this->create_by;
            $datas[$i]['update_by']     = $this->create_by;
            $total += (double)$this->single_price[$i];
        }

        $n = BillsModel::getByBillId($this->bill_id);
        $bill = $this->bill;
        if(!isset($n)){
            $bill->tatal_price = $total;
            $data = $bill->create();

        }else{
            $data = $bill->edit($this->bill_id);

        }
        if(1 === $data['status']){
            $last_user_id = Registry::get('db')->insert(static::tableName(), $datas);

        }

        return $data;
    }

    public function edit($id,$data = [
        'shop_id'=>0,
        'bill_id'=>0,
        'create_by'=>time(),
        'update_by'=>time(),
        'name'         => 0,
        'number'       => 0,
        'weight'       => '',
        'single_price' => 0,
    ]) {
        $sth  = Yaf_Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET 
            shop_id=:shop_id, 
            bill_id = :bill_id, 
            number = :number, 
            weight = :weight, 
            single_price = :single_price, 
            final_price = :final_price, 
            create_by = :create_by, 
            update_by = :update_by, 
            name = :name
            WHERE id=:id
            ");
        $sth->bindParam(':id', $id, \PDO::PARAM_INT);
        $sth->bindParam(':update_by', $data['update_by'], \PDO::PARAM_INT);
        $sth->bindParam(':final_price', $data['final_price'], \PDO::PARAM_INT);
        $sth->bindParam(':single_price', $data['single_price'], \PDO::PARAM_INT);
        $sth->bindParam(':number', $data['number'], \PDO::PARAM_INT);
        $sth->bindParam(':weight', $data['weight'], \PDO::PARAM_STR);
        $sth->bindParam(':shop_id', $data['shop_id'], \PDO::PARAM_INT);
        $sth->bindParam(':bill_id', $data['bill_id'], \PDO::PARAM_STR);
        $sth->bindParam(':name', $data['name'], \PDO::PARAM_STR);
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

    public static function delete($id) {
        $sth  = Registry::get('db')->pdo->prepare("DELETE FROM ".static::tableName() ." WHERE id = :id limit 1");
        $sth->bindParam(':id', $id, \PDO::PARAM_INT);
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