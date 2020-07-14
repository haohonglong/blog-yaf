<?php
use Yaf\Config\Ini;
use Yaf\Bootstrap_Abstract;
use Yaf\Application;
use Yaf\Dispatcher;
use Yaf\Registry;
use Yaf\Loader;
/**
 * @name Bootstrap
 * @author root
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:\Yaf\Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Bootstrap_Abstract {

    /**
     * @param Dispatcher $dispatcher
     */
    function _initComposerAutoload(Dispatcher $dispatcher)
    {
        $autoload = APPLICATION_PATH . '/vendor/autoload.php';
        if (file_exists($autoload)) {
            Loader::import($autoload);
        }
    }

    public function _initConfig() {
		//把配置保存起来
		$arrConfig = Application::app()->getConfig();
		Registry::set('config', $arrConfig);

	}

	public function _initPlugin(Dispatcher $dispatcher) {
		//注册一个插件
		$objSamplePlugin = new SamplePlugin();
		$dispatcher->registerPlugin($objSamplePlugin);
	}

	public function _initRoute(Dispatcher $dispatcher) {
		//在这里注册自己的路由协议,默认使用简单路由
	}
	
	public function _initView(Dispatcher $dispatcher) {
		//在这里注册自己的view控制器，例如smarty,firekylin
	}
    public function _initDatabase() {

        $config = new Ini(APPLICATION_PATH . "/conf/application.ini", 'mysql');
        $db = $config->get("database");
        $option = [
            'database_type' => $db->params->type,
            'database_name' => $db->params->dbname,
            'server' => $db->params->host,
            'username' => $db->params->username,
            'password' => $db->params->password,
            'prefix' => $db->params->prefix,
            'logging' => $db->params->log,
            'charset' => $db->params->charset
        ];
        Registry::set('db', new \Medoo\Medoo($option));
    }
}
