<?php


use Yaf\Registry;

class UrlModel
{
    public $name, $url, $info, $sid;
    public static function tableName()
    {
        return 'url';
    }

    public static function getById($id) {
        return Registry::get('db')->get(static::tableName(),"id",["id"=>$id]);
    }
    public static function has_url($url) {
        $n = Registry::get('db')->get(static::tableName(),"id",["url"=>$url]);
        if ($n > 0) {
            return true;
        }
        return false;
    }

    public static function getByUrl($url) {
        return Registry::get('db')->get(static::tableName(),["id","sorts_id"],["url"=>$url]);
    }


    public function create() {
        $sth  = Registry::get('db')->pdo->prepare("INSERT INTO ".static::tableName() ." SET sorts_id=:sid, name=:name, url=:url, info=:info");
        $sth->bindParam(':sid', $this->sid, \PDO::PARAM_INT);
        $sth->bindParam(':name', $this->name, \PDO::PARAM_STR);
        $sth->bindParam(':url', $this->url, \PDO::PARAM_STR);
        $sth->bindParam(':info', $this->info, \PDO::PARAM_STR);
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
        $sth  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET sorts_id=:sid, name=:name, url=:url, info=:info WHERE id = :id limit 1");
        $sth->bindParam(':id', $id, \PDO::PARAM_INT);
        $sth->bindParam(':sid', $this->sid, \PDO::PARAM_INT);
        $sth->bindParam(':name', $this->name, \PDO::PARAM_STR);
        $sth->bindParam(':url', $this->url, \PDO::PARAM_STR);
        $sth->bindParam(':info', $this->info, \PDO::PARAM_STR);
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