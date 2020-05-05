<?php

use app\models\Shop;

class ShopController extends Base {
	public function indexAction() {
        $query = static::$DB->query("SELECT id,name FROM ".Shop::tableName())->fetchAll(PDO::FETCH_ASSOC);
        $data = [
            "data"=>$query,
            "status"=>1,
        ];
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
    }


    public function addAction() {
        $errors = [];
        $name = $this->getRequest()->getPost("name", "");
        if(empty($name)){
            $errors['name'] = "请填写名称";
        }
        if(empty($errors)) {
            $n = static::$DB->get(Shop::tableName(),"id",["name"=>$name]);
            if ($n > 0) {
                $errors['name'] = "名称已存在";
            }
        }

        $data = [];
        if(empty($errors)){
            $sth  = static::$DB->pdo->prepare("INSERT INTO ".Shop::tableName() ." SET name=:name");
            $sth->bindParam(':name', $name, PDO::PARAM_STR);
            if($sth->execute()){
                $data['status'] = 1;
                $data['message'] = '添加成功';
            }else{
                $data['status'] = 0;
                $data['message'] = $sth->errorInfo();
            }
        }else{
            $data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '添加失败';
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;

    }

    public function editAction() {}
    public function deleteAction() {}
}
