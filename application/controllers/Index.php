<?php
/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends Yaf_Controller_Abstract {

	/** 
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/sample_yaf/index/index/index/name/root 的时候, 你就会发现不同
     */
	public function indexAction($name = "Stranger") {



// Initialize

		//1. fetch query
		$get = $this->getRequest()->getQuery("get", "default value");

		//2. fetch model
		$model = new SampleModel();

		//3. assign
		$this->getView()->assign("content", $model->selectSample());
		$this->getView()->assign("name", $name);

		//4. render by Yaf, 如果这里返回FALSE, Yaf将不会调用自动视图引擎Render模板
        return TRUE;
	}

	public function testAction() {
	    $filename = '/usr/local/nginx/html/lamborghiniJS/LAM2-demos/brandhall/data/empower.json';
	    $json =file_get_contents($filename);
	    echo $json;
        $search = array(" ","　","\n","\r","\t");
        $replace = array("","","","","");
	    $json = str_replace($search, $replace,$json);
	    $json = trim($json);
	    file_put_contents($filename,$json);
//	    echo $json;
	    $arr = json_decode($json,true);
	    var_dump($arr);

	    exit;



    }

	public function incrementAction() {
	    exit;
        $db = Yaf_Registry::get('db');
        $query = $db->query("select id from sorts order by id DESC")->fetchAll();
        echo "<pre>";
//        print_r($query);
        echo "</pre>";


        $datas = [];
        $db->pdo->beginTransaction();
        try{
            foreach ($query as $k => $v) {
                $id = intval($v['id'])+1;
                $sth = $db->pdo->prepare('update sorts set id=:id where id=:id2');
                $sth->bindParam(':id', $id, PDO::PARAM_INT);
                $sth->bindParam(':id2', $v['id'], PDO::PARAM_INT);
                $sth->execute();
                $datas[$v['id']] = $id;

            }
            $db->pdo->commit();

        }catch (Exception $e) {
            $db->pdo->rollBack();
            echo $e->getMessage();
            echo '<br>';
            echo 'has a Error';
            exit;
        }



        $db->pdo->beginTransaction();
        try{
            foreach ($datas as $k => $v) {
                //url
                $sth = $db->pdo->prepare('update url set sorts_id=:id where sorts_id=:id2');
                $sth->bindParam(':id', $v, PDO::PARAM_INT);
                $sth->bindParam(':id2', $k, PDO::PARAM_INT);
                $sth->execute();

                $sth = $db->pdo->prepare('update article set sorts_id=:id where sorts_id=:id2');
                $sth->bindParam(':id', $v, PDO::PARAM_INT);
                $sth->bindParam(':id2', $k, PDO::PARAM_INT);
                $sth->execute();
            }
            $db->pdo->commit();
        }catch (Exception $e){
            $db->pdo->rollBack();
            echo $e->getMessage();
            echo '<br>';
            echo 'has a Error';
            exit;
        }
        echo 'has be done';

        exit;
    }


}
