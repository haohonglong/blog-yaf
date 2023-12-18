<?php

use Yaf\Registry;


class UserModel
{
    public static function tableName() {
        return 'user';
    }

    public static function getByid($id) {
        return Registry::get('db')->get(static::tableName(),"id",["id"=>$id]);
    }

}