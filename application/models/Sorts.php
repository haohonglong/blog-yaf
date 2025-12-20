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
    public $name,$pid, $code=null;
    public function __construct($name,$pid=0, $code=null)
    {
        $this->name = $name;
        $this->pid = $pid;

        if(isset($code) && empty($code)) $code = null;
        $this->code = $code;
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
        return Registry::get('db')->query("SELECT id, name, pid FROM ".static::tableName()." WHERE pid=0")->fetchAll(\PDO::FETCH_ASSOC);
    }

    private static function tree(&$arr = [], &$list = []) {
        foreach ($arr as $k => $v){
            if (0 == $v['pid']) {
                $list[$v['id']] = $v;
                unset($arr[$k]);
            } else {
                foreach ($list as $k => $v){
                    if($list[$v["id"]] == $v["pid"]) {

                    }
                }
            }
        }

    }

    public static function showListOfAllLevels() {
        $arr = Registry::get('db')->query("SELECT id,name,pid,code FROM ".static::tableName())->fetchAll(\PDO::FETCH_ASSOC);

        return $arr;
    }
    public static function has_name($name, $pid=0) {
        $n = Registry::get('db')->get(static::tableName(),"id",["name"=>$name, "pid"=>$pid]);
        if ($n > 0) {
            return true;
        }
        return false;
    }

    public static function has_code($code) {
        if(isset($code) && !empty($code)){
            $n = Registry::get('db')->get(static::tableName(),"code",["code"=>$code]);
            if ($n > 0) {
                return true;
            }
        }
        
        return false;
    }

    public function create() {
        $sth  = Registry::get('db')->pdo->prepare("INSERT INTO ".static::tableName()." SET name=:name, pid=:pid, code=:code");
        $sth->bindParam(':pid', $this->pid, \PDO::PARAM_STR);
        $sth->bindParam(':name', $this->name, \PDO::PARAM_STR);
        $sth->bindParam(':code', $this->code, \PDO::PARAM_STR);
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
        if(is_int(strpos($id, ','))){ // 批处理
            $sth  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET pid=:pid WHERE id IN(:id)");
            $sth->bindParam(':id', $id, \PDO::PARAM_STR);
            $sth->bindParam(':pid', $this->pid, \PDO::PARAM_STR);
        } else { 
            $sth  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET pid=:pid, name=:name, code=:code WHERE id = :id limit 1");
            $sth->bindParam(':id', $id, \PDO::PARAM_STR);
            $sth->bindParam(':pid', $this->pid, \PDO::PARAM_STR);
            $sth->bindParam(':name', $this->name, \PDO::PARAM_STR);
            $sth->bindParam(':code', $this->code, \PDO::PARAM_STR);
            
        }
        
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
        $count2 = Registry::get('db')->count(static::tableName(), ['pid' => $sid]);
        if($count > 0  || $count2 > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function delete($id, $force = 0) {
        $database = Registry::get('db');
        try {
            $database->pdo->beginTransaction();
            
            $sth  = $database->pdo->prepare("DELETE FROM ".static::tableName()." WHERE id = :id limit 1");
            $sth->bindParam(':id', $id, \PDO::PARAM_STR);
            if($sth->execute()){
                $data['status'] = 1;
                $data['message'] = '删除成功';
                if($force > 0){
                    $url  = $database->pdo->prepare("DELETE FROM ".UrlModel::tableName()." WHERE sorts_id = :id");
                    $url->bindParam(':id', $id, \PDO::PARAM_STR);
                    if($url->execute()){
                        $database->pdo->commit();
                        $data['status'] = 1;
                        $data['message'] = '删除成功(包括其以下的全部数据)';

                    }else{
                        $database->pdo->rollBack();
                        $data['status'] = 0;
                        $data['message'] = $url->errorInfo();
                    }

                }else {
                    $database->pdo->commit();

                }

            }else{
                $data['status'] = 0;
                $data['message'] = $sth->errorInfo();
            }
        } catch (Exception $e) {
            $database->pdo->rollBack();
            $data['status'] = 0;
            $data['message'] = $e->getMessage();
        }
        
        return $data;
    }
}