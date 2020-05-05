<?php

use app\models\Sorts;
use app\models\Url;

class UrlController extends Base {
    
    public function indexAction() {
        $sid = $this->getRequest()->getQuery("sid", 1);
        $querys = static::$DB->query("SELECT id,name,url FROM ".Url::tableName() ." WHERE sorts_id={$sid}")->fetchAll(PDO::FETCH_ASSOC);
        $query = [];
        foreach ($querys as $k => $item) {
            $query[$item['id']] = [
                'id'=>$item['id'],
                'name'=>$item['name'],
                'url'=>$item['url'],
            ];
        }
        $data = [
            "data"=>$query,
            "status"=>1,
        ];
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
    }

    public function showAction() {
        $id = $this->getRequest()->getQuery("id", 0);
        $query = static::$DB->get(Url::tableName(),["id","name","url","sorts_id","info"],["id"=>$id]);
        if(isset($query)){
            $data['status'] = 1;
            $data['data'] = $query;
        }else{
            $data['status'] = 0;
        }
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
    }

    public function addAction() {
        $errors = [];
        $name = $this->getRequest()->getPost("name", "");
        $url  = $this->getRequest()->getPost("url", "");
        $info = $this->getRequest()->getPost("info", "");
        $sid  = $this->getRequest()->getPost("sid", "");
        if(empty($name)){
            $errors['name'] = "请填写名称";
        }
        if(empty($url)){
            $errors['url'] = "请填写地址链接";
        }
        if(empty($info)){
            $errors['info'] = "请填写简介";
        }
        if(empty($sid)){
            $errors['sid'] = "请选择类别id";
        }
        if(empty($errors)) {
            $n = static::$DB->get(Sorts::tableName(),"id",["id"=>$sid]);
            if (!isset($n)) {
                $errors['sid'] = "没有此类别id";
            }
        }
        if(empty($errors)) {
            $n = static::$DB->get(Url::tableName(),"id",["url"=>$url]);
            if ($n > 0) {
                $errors['url'] = "此地址链接已存在";
            }
        }

        $data = [];
        if(empty($errors)){
            $sth  = static::$DB->pdo->prepare("INSERT INTO ".Url::tableName() ." SET sorts_id=:sid, name=:name, url=:url, info=:info");
            $sth->bindParam(':sid', $sid, PDO::PARAM_INT);
            $sth->bindParam(':name', $name, PDO::PARAM_STR);
            $sth->bindParam(':url', $url, PDO::PARAM_STR);
            $sth->bindParam(':info', $info, PDO::PARAM_STR);
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

    public function editAction() {
        $errors = [];
        $id   = $this->getRequest()->getQuery("id", "");
        $name = $this->getRequest()->getPost("name", "");
        $url  = $this->getRequest()->getPost("url", "");
        $info = $this->getRequest()->getPost("info", "");
        $sid  = $this->getRequest()->getPost("sid", "");
        if(empty($id)){
            $errors['id'] = "id cannot be empty";
        }
        if(empty($name)){
            $errors['name'] = "请填写名称";
        }
        if(empty($url)){
            $errors['url'] = "请填写地址链接";
        }
        if(empty($info)){
            $errors['info'] = "请填写简介";
        }
        if(empty($sid)){
            $errors['sid'] = "请选择类别id";
        }
        if(empty($errors)) {
            $n = static::$DB->get(Url::tableName(),"id",["id"=>$id]);
            if (!isset($n)) {
                $errors['id'] = "id不存在";
            }
        }
        if(empty($errors)) {
            $n = static::$DB->get(Sorts::tableName(),"id",["id"=>$sid]);
            if (!isset($n)) {
                $errors['sid'] = "没有此类别id";
            }
        }

        $data = [];
        if(empty($errors)){
            $sth  = static::$DB->pdo->prepare("UPDATE ".Url::tableName() ." SET sorts_id=:sid, name=:name, url=:url, info=:info WHERE id = :id limit 1");
            $sth->bindParam(':id', $id, PDO::PARAM_INT);
            $sth->bindParam(':sid', $sid, PDO::PARAM_INT);
            $sth->bindParam(':name', $name, PDO::PARAM_STR);
            $sth->bindParam(':url', $url, PDO::PARAM_STR);
            $sth->bindParam(':info', $info, PDO::PARAM_STR);
            if($sth->execute()){
                $data['status'] = 1;
                $data['message'] = '修改成功';
            }else{
                $data['status'] = 0;
                $data['message'] = $sth->errorInfo();
            }
        }else{
            $data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '修改失败';
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;

    }

    public function deleteAction() {
        $errors = [];
        $id   = $this->getRequest()->getQuery("id", "");
        if(empty($id)){
            $errors['id'] = "id是必须的";
        }
        if(empty($errors)) {
            $n = static::$DB->get(Url::tableName(),"id",["id"=>$id]);
            if (!isset($n)) {
                $errors['id'] = "id不存在";
            }
        }
        $data = [];
        if(empty($errors)){
            $sth  = static::$DB->pdo->prepare("DELETE FROM ".Url::tableName() ." WHERE id = :id limit 1");
            $sth->bindParam(':id', $id, PDO::PARAM_INT);
            if($sth->execute()){
                $data['status'] = 1;
                $data['message'] = '删除成功';
            }else{
                $data['status'] = 0;
                $data['message'] = $sth->errorInfo();
            }
        }else{
            $data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '删除失败';
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
    }






}