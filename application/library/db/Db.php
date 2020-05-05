<?php
namespace app\library\db;


use Medoo\Medoo;
/**
 * Created by PhpStorm.
 * User: long
 * Date: 08/04/2020
 * Time: 8:33 PM
 */

class Db
{
    private static $DATABASE = null;

    /**
     * @author: lhh
     * 创建日期：2020-04-08
     * 修改日期：2020-04-08
     * 名称： init
     * 功能：
     * 说明：
     * 注意：
     * @param array $data
     * @return null
     */
    static public function init($data=[]) {

        if(!(static::$DATABASE instanceof Medoo) || !empty($data)){
            $data = array_merge([
                'database_type' => 'mysql',
                'database_name' => 'blog',
                'server' => 'mysql',
                'username' => 'lam',
                'password' => '123456',
                'charset' => 'utf8',
            ],$data);
            static::$DATABASE = new Medoo($data);
        }
        return static::$DATABASE;
    }
}