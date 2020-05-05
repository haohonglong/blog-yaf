<?php

class GoodsController extends Base {
	public function indexAction() {
        $query = static::$DB->query("
SELECT b.bill_id as bill_id,b.price as total_price,b.discount,s.name as shop_name,g.id,g.name,g.final_price, g.create_by as create_at FROM bills as b 
RIGHT JOIN goods as g USING(bill_id)
LEFT JOIN shop as s ON g.shop_id = s.id
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
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        file_put_contents('/usr/local/nginx/html/lamborghiniJS/LAM2-demos/brandhall/data/goods.json',$json);
        exit;
    }


    public function addAction() {

        exit;
    }

    public function writeAction() {

    }
}
