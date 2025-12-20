<?php

use Yaf\Registry;

class Video2Model
{

    private $src;

    public function __construct($src)
    {
        $this->src = $src;
    }

    public static function tableName()
    {
        return 'video2';
    }

    /**
     * @author: lhh
     * 创建日期：2022-12-17
     * 修改日期：2022-12-17
     * 名称： getBySrc
     * 功能：
     * 说明：
     * 注意：
     * @param $src
     * @return mixed
     */
    public static function getBySrc($src) {
        return Registry::get('db')->get(static::tableName(),"id",["src"=>$src]);
    }

    public static function getById($id) {
        return Registry::get('db')->get(static::tableName(),"id",["id"=>$id]);
    }


    /**
     * @author: lhh
     * 创建日期：2022-12-17
     * 修改日期：2022-12-17
     * 名称： listAll
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public static function listAll() {
        $query = Registry::get('db')->query("SELECT id, src FROM ".static::tableName(). " ORDER BY id DESC")->fetchAll(\PDO::FETCH_ASSOC);
        return $query;
    }

    /**
     * @author: lhh
     * 创建日期：2022-12-17
     * 修改日期：2022-12-17
     * 名称： create
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public function create() {
        $sth  = Registry::get('db')->pdo->prepare("INSERT INTO ".static::tableName() ." SET src=:src");
        $sth->bindParam(':src', $this->src, \PDO::PARAM_STR);
        if($sth->execute()){
            $data['status'] = 1;
            $data['message'] = '添加成功';
        }else{
            $data['status'] = 0;
            $data['message'] = $sth->errorInfo();
        }
        return $data;
    }

    /**
     * @author: lhh
     * 创建日期：2022-12-17
     * 修改日期：2022-12-17
     * 名称： edit
     * 功能：
     * 说明：
     * 注意：
     * @param $id
     * @return mixed
     */
    public function edit($id) {
        $sth  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET src=:src WHERE id = :id limit 1");
        $sth->bindParam(':id', $id, \PDO::PARAM_STR);
        $sth->bindParam(':src', $this->src, \PDO::PARAM_STR);
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
        $sth  = Registry::get('db')->pdo->prepare("DELETE FROM ".static::tableName()." WHERE id = :id limit 1");
        $sth->bindParam(':id', $id, \PDO::PARAM_STR);
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