<?php

use Yaf\Registry;

class UnitModel
{

    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public static function tableName()
    {
        return 'unit';
    }


    /**
     * @author: lhh
     * 创建日期：2023-12-01
     * 修改日期：2023-12-01
     * 名称： getByName
     * 功能：
     * 说明：
     * 注意：
     * @param $name
     * @return mixed
     */
    public static function getByName($name) {
        return Registry::get('db')->get(static::tableName(),"unit_name",["unit_name"=>$name]);
    }

    public static function getById($id) {
        return Registry::get('db')->get(static::tableName(),"unit_id",["unit_id"=>$id]);
    }


    /**
     * @author: lhh
     * 创建日期：2023-12-01
     * 修改日期：2023-12-01
     * 名称： listAll
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public static function listAll() {
        $query = Registry::get('db')->query("SELECT unit_id, unit_name FROM ".static::tableName())->fetchAll(\PDO::FETCH_ASSOC);
        return $query;
    }

    /**
     * @author: lhh
     * 创建日期：2023-12-01
     * 修改日期：2023-12-01
     * 名称： create
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public function create() {
        $sth  = Registry::get('db')->pdo->prepare("INSERT INTO ".static::tableName() ." SET unit_name=:name");
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

    /**
     * @author: lhh
     * 创建日期：2023-12-01
     * 修改日期：2023-12-01
     * 名称： edit
     * 功能：
     * 说明：
     * 注意：
     * @param $id
     * @return mixed
     */
    public function edit($id) {
        $sth  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET unit_name=:name WHERE unit_id = :id limit 1");
        $sth->bindParam(':id', $id, \PDO::PARAM_INT);
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

    public static function delete($id) {
        $sth  = Registry::get('db')->pdo->prepare("DELETE FROM ".static::tableName()." WHERE unit_id = :id limit 1");
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