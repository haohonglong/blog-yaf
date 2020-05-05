<?php


class Base extends Yaf_Controller_Abstract {
    protected static $DB;
    public function init() {
        static::$DB = Yaf_Registry::get('db');
        $allow_origin = [
            'http://lam2.local',
            'http://localhost:3001',
        ];
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';  //跨域访问的时候才会存在此字段
        if (in_array($origin, $allow_origin)) {
            header('Access-Control-Allow-Origin:' . $origin);
        } else {
            return;
        }
    }

}