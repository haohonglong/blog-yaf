<?php

use app\models\Sorts;

class SortsController extends Base {

    public function indexAction() {
        $query = static::$DB->query("SELECT id,name,pid FROM ".Sorts::tableName()." WHERE pid=0")->fetchAll(PDO::FETCH_ASSOC);
        $json = json_encode($query,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
    }

    public function allAction() {
        $query = static::$DB->query("SELECT id,name,pid FROM ".Sorts::tableName())->fetchAll(PDO::FETCH_ASSOC);
        $json = json_encode($query,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
    }

    public function showAction() {
        $id = $this->getRequest()->getQuery("id", 0);
        $query = static::$DB->get(Sorts::tableName(),["id","name","pid"],["id"=>$id]);
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
        $pid = $this->getRequest()->getPost("pid", 0);
        if(empty($name)){
            $errors['name'] = "请填写名称";
        }
        if(!is_numeric($pid)){
            $pid = 0;
        }

        $data = [];
        if(empty($errors)){
            $sth  = static::$DB->pdo->prepare("INSERT INTO ".Sorts::tableName()." SET name=:name, pid=:pid");
            $sth->bindParam(':pid', $pid, PDO::PARAM_INT);
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

    public function editAction() {
        $errors = [];
        $id   = $this->getRequest()->getQuery("id", "");
        $name = $this->getRequest()->getPost("name", "");
        $pid  = $this->getRequest()->getPost("pid", "");
        if(empty($id)){
            $errors['id'] = "id cannot be empty";
        }
        if(empty($name)){
            $errors['name'] = "请填写名称";
        }
        if(empty($pid)){
            $errors['pid'] = "请选择类别id";
        }
        if(empty($errors)) {
            $n = static::$DB->get(Sorts::tableName(),"id",["id"=>$id]);
            if (!isset($n)) {
                $errors['id'] = "id不存在";
            }
        }

        $data = [];
        if(empty($errors)){
            $sth  = static::$DB->pdo->prepare("UPDATE ".Sorts::tableName() ." SET pid=:pid, name=:name WHERE id = :id limit 1");
            $sth->bindParam(':id', $id, PDO::PARAM_INT);
            $sth->bindParam(':pid', $pid, PDO::PARAM_INT);
            $sth->bindParam(':name', $name, PDO::PARAM_STR);
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
            $n = static::$DB->get("url","id",["id"=>$id]);
            if (!isset($n)) {
                $errors['id'] = "id不存在";
            }
        }
        $data = [];
        if(empty($errors)){
            $sth  = static::$DB->pdo->prepare("DELETE FROM ".Sorts::tableName()." WHERE id = :id limit 1");
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