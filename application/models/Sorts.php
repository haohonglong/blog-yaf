<?php
/**
 * Created by PhpStorm.
 * User: long
 * Date: 05/05/2020
 * Time: 12:35 PM
 */


use Yaf\Registry;

class SortsModel
{
    public $name,$pid;
    public function __construct($name,$pid=0)
    {
        $this->name = $name;
        $this->pid = $pid;
    }

    public static function tableName()
    {
        return 'sorts';
    }

    public static function getById($id) {
        return Registry::get('db')->get(static::tableName(),"id",["id"=>$id]);
    }

    public static function findById($id) {
        return Registry::get('db')->get(static::tableName(),["id","name","pid"],["id"=>$id]);
    }

    public static function showListOfOneLevel() {
        return Registry::get('db')->query("SELECT id,name,pid FROM ".static::tableName()." WHERE pid=0")->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function showListOfAllLevels() {
        return Registry::get('db')->query("SELECT id,name,pid FROM ".static::tableName())->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function create() {
        $sth  = Registry::get('db')->pdo->prepare("INSERT INTO ".static::tableName()." SET name=:name, pid=:pid");
        $sth->bindParam(':pid', $this->pid, \PDO::PARAM_INT);
        $sth->bindParam(':name', $this->name, \PDO::PARAM_STR);
        if($sth->execute()){
            $data['status'] = 1;
            $data['message'] = '添加成功';
        }else{
            $data['status'] = 0;
            $data['message'] = $sth->errorInfo();
        }
        return $data;
    }

    public function edit($id) {
        $sth  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET pid=:pid, name=:name WHERE id = :id limit 1");
        $sth->bindParam(':id', $id, \PDO::PARAM_INT);
        $sth->bindParam(':pid', $this->pid, \PDO::PARAM_INT);
        $sth->bindParam(':name', $this->name, \PDO::PARAM_STR);
        if($sth->execute()){
            $data['status'] = 1;
            $data['message'] = '修改成功';
        }else{
            $data['status'] = 0;
            $data['message'] = $sth->errorInfo();
        }
        return $data;
    }

    public static function hasUrl($sid) {
        $count = Registry::get('db')->count(UrlModel::tableName(), ['sorts_id' => $sid]);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function delete($id) {
        $sth  = Registry::get('db')->pdo->prepare("DELETE FROM ".static::tableName()." WHERE id = :id limit 1");
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