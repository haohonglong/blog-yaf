<?php
namespace base;

use Yaf\Controller_Abstract;
use Yaf\Registry;

class Base extends Controller_Abstract {
    protected static $DB;
    public function init() {
        static::$DB = Registry::get('db');
        $allow_origin = [
            'http://lam2.local',
            // 'http://192.168.3.10:9527',
            $_SERVER['HOSTIP'],
            // 'https://3000-f616f4ed-2356-43c9-bac3-f17f751ddf7a.ws-us02.gitpod.io/',
        ];
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';  //跨域访问的时候才会存在此字段
        if (in_array($origin, $allow_origin)) {
            header('Access-Control-Allow-Origin:' . $origin);
        } else {
            return;
        }
    }

}
