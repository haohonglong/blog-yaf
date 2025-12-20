<?php

require 'vendor/autoload.php';
define('APPLICATION_PATH', dirname(__FILE__));
define('APPLICATION_ENV', 'development');


$application = new \Yaf\Application( APPLICATION_PATH . "/conf/application.ini");
$application->bootstrap()->run();





