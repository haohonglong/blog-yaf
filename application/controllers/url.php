<?php
use base\controller\ControllerBase;
use Yaf\Registry;

class UrlController extends ControllerBase {
    
    public function indexAction() {
        $sid = $this->getRequest()->getQuery("sid", 1);
        $querys = Registry::get('db')->query("SELECT id, name, url, info FROM ".UrlModel::tableName() ." WHERE sorts_id={$sid} ORDER BY id DESC")->fetchAll(\PDO::FETCH_ASSOC);
        $query = [];
        foreach ($querys as $k => $item) {
            $query[$item['id']] = [
                'id'=>$item['id'],
                'name'=>$item['name'],
                'url'=>$item['url'],
                'info'=>$item['info'],
            ];
        }
        $data = [
            "data"=>array_values($query),
            "status"=>1,
        ];
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
    }

    public function searchAction() {
        $sid = $this->getRequest()->getQuery("sid", 0);
        $name = $this->getRequest()->getQuery("name", "");
        $querys = Registry::get('db')->query("SELECT id, name, url, info FROM ".UrlModel::tableName() ." WHERE sorts_id={$sid} AND name LIKE '%{$name}%'")->fetchAll(\PDO::FETCH_ASSOC);
        $query = [];
        foreach ($querys as $k => $item) {
            $query[$item['id']] = [
                'id'=>$item['id'],
                'name'=>$item['name'],
                'url'=>$item['url'],
                'info'=>$item['info'],
            ];
        }
        $data = [
            "data"=>array_values($query),
            "status"=>1,
        ];
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
    }

    public function showAction() {
        $id = $this->getRequest()->getQuery("id", 0);
        $query = Registry::get('db')->get(UrlModel::tableName(),["id","name","url","sorts_id","info"],["id"=>$id]);
        if(isset($query)){
            $data['status'] = 1;
            $data['data'] = $query;
        }else{
            $data['status'] = 0;
        }
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
    }

    public function addAction() {
        $errors = [];
        $name = $this->getRequest()->getPost("name", "");
        $url  = $this->getRequest()->getPost("url", "");
        $info = $this->getRequest()->getPost("info", "");
        $sid  = $this->getRequest()->getPost("sid", "");
        
        if(empty($name)){
            $errors['name'] = "请填写名称";
        }
        if(empty($url)){
            $errors['url'] = "请填写地址链接";
        }
        if(empty($info)){
            $errors['info'] = "请填写简介";
        }
        if(empty($sid)){
            $errors['sid'] = "请选择类别id";
        }
        if(empty($errors)) {
            $n = SortsModel::getByid($sid);
            if (!isset($n)) {
                $errors['sid'] = "没有此类别id";
            }
        }
        if(empty($errors)) {
            $arr = UrlModel::getByUrl($url);
            if (isset($arr) && is_array($arr)) {
                $name = SortsModel::findById($arr["sorts_id"])["name"];
                $errors['url'] = "此地址链接已存在\"{$name}\"里";
            }
        }

        $data = [];
        if(empty($errors)){
            $urlModel = new UrlModel();
            $urlModel->sid = $sid;
            $urlModel->name = $name;
            $urlModel->url = $url;
            $urlModel->info = $info;
            $data = $urlModel->create();
        }else{
            $data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '添加失败';
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;

    }

    public function editAction() {
        $errors = [];
        $id   = $this->getRequest()->getPost("id", "");
        $name = $this->getRequest()->getPost("name", "");
        $url  = $this->getRequest()->getPost("url", "");
        $info = $this->getRequest()->getPost("info", "");
        $sid  = $this->getRequest()->getPost("sid", "");
        if(empty($id)){
            $errors['id'] = "id cannot be empty";
        }
        if(empty($name)){
            $errors['name'] = "请填写名称";
        }
        if(empty($url)){
            $errors['url'] = "请填写地址链接";
        }
        if(empty($info)){
            $errors['info'] = "请填写简介";
        }
        if(empty($sid)){
            $errors['sid'] = "请选择类别id";
        }
        if(empty($errors)) {
            $n = UrlModel::getById($id);
            if (!isset($n)) {
                $errors['id'] = "id不存在";
            }
        }
        if(empty($errors)) {
            $n = SortsModel::getById($sid);
            if (!isset($n)) {
                $errors['sid'] = "没有此类别id";
            }
        }

        $data = [];
        if(empty($errors)){
            $urlModel = new UrlModel();
            $urlModel->sid = $sid;
            $urlModel->name = $name;
            $urlModel->url = $url;
            $urlModel->info = $info;
            $data = $urlModel->edit($id);
        }else{
            $data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '修改失败';
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;

    }

    public function deleteAction() {
        $errors = [];
        $id   = $this->getRequest()->getQuery("id", "");
        if(empty($id)){
            $errors['id'] = "id是必须的";
        }
        if(empty($errors)) {
            $n = UrlModel::getById($id);
            if (!isset($n)) {
                $errors['id'] = "id不存在";
            }
        }
        $data = [];
        if(empty($errors)){
            $data = UrlModel::delete($id);
        }else{
            $data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '删除失败';
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
    }






}