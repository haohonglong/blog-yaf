<?php

use Yaf\Registry;

class LogModel
{
    private $uid, $cause, $content, $created_at;

    public function __construct($uid, $cause, $content, $created_at = null)
    {
        $this->uid = $uid;
        $this->cause = $cause;
        $this->content = $content;
        $this->created_at = $created_at ?? date("Y-m-d H:i:s");
    }

    public static function tableName() {
        return 'logs';
    }

    public static function getByUid($uid) {
        return Registry::get('db')->query("SELECT cause, content, created_at FROM " .static::tableName(). " WHERE uid = {$uid} ORDER BY id DESC")->fetchAll();
        
    }

    public function create(){
        $database = Registry::get('db');
        try{
            $log  = $database->pdo->prepare("INSERT INTO ".static::tableName() ." SET uid=:uid, cause=:cause, content=:content, created_at=:created_at");
            $log->bindParam(':uid', $this->uid, \PDO::PARAM_STR);
            $log->bindParam(':cause', $this->cause, \PDO::PARAM_STR);
            $log->bindParam(':content', $this->content, \PDO::PARAM_STR);
            $log->bindParam(':created_at', $this->created_at, \PDO::PARAM_STR);
            if($log->execute()){
                $data['status'] = 1;
                $data['message'] = '添加成功';
            }else{
                $data['status'] = 0;
                $data['message'] = $log->errorInfo() . " in " . __FILE__;
            }
        }catch(\Exception $e){
            $data['status'] = 0;
            $data['message'] = $e->getMessage() . " in " . __FILE__;
        }
        return $data;
    }


}