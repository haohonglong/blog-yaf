<?php
use base\controller\ControllerBase;
use Yaf\Registry;

class ArticleController extends ControllerBase {
    
    public function indexAction() {
        $data = ArticleModel::getList();
        
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
    }

    public function searchAction() {
        $title = $this->getRequest()->getPost("title", null);
        $data = ArticleModel::search($title);
        
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
    }

    public function showAction() {
        $id = $this->getRequest()->getQuery("id", null);

        if(!isset($id) || empty($id)) { $errors["id"] = "sid 参数是必须的"; }
        $query = ArticleModel::getById($id);
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
        $userid = $this->getRequest()->getPost("userid", 1);
        $sid = $this->getRequest()->getPost("sid", null);
		$title = $this->getRequest()->getPost("title", null);
		$content = $this->getRequest()->getPost("content", null);
		
		if(!isset($sid) || empty($sid)) { $errors["sid"] = "sid 参数是必须的"; }
		if(!isset($title) || empty($title)) { $errors["title"] = "title 参数是必须的"; }
		if(!isset($content) || empty($content)) { $errors["content"] = "content 参数是必须的"; }
        
        if(empty($errors)) {
            $n = UserModel::getByid($userid);
            if (!isset($n)) {
                $errors['sid'] = "没有此用户";
            }
        }

        if(empty($errors)) {
            $n = SortsModel::getByid($sid);
            if (!isset($n)) {
                $errors['sid'] = "没有此类别id";
            }
        }
        

        $data = [];
        if(empty($errors)){
            $article = new ArticleModel($userid, $sid, $title, $content);
			
            $data = $article->create();
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
        $id = $this->getRequest()->getPost("id", null);
        $userid = $this->getRequest()->getPost("userid", 1);
        $sid = $this->getRequest()->getPost("sid", null);
		$title = $this->getRequest()->getPost("title", null);
		$content = $this->getRequest()->getPost("content", null);

        if(!isset($id) || empty($id)) { $errors["id"] = "sid 参数是必须的"; }
        if(!isset($sid) || empty($sid)) { $errors["sid"] = "sid 参数是必须的"; }
		if(!isset($title) || empty($title)) { $errors["title"] = "title 参数是必须的"; }
		if(!isset($content) || empty($content)) { $errors["content"] = "content 参数是必须的"; }

        if(empty($errors)) {
            $n = UserModel::getByid($userid);
            if (!isset($n)) {
                $errors['sid'] = "没有此用户";
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
            $article = new ArticleModel($userid, $sid, $title, $content);
			
            $data = $article->edit($id);
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
        $id   = $this->getRequest()->getQuery("id", null);
        if(empty($id)){
            $errors['id'] = "id是必须的";
        }
        if(empty($errors)) {
            $n = ArticleModel::getById($id);
            if (!isset($n)) {
                $errors['id'] = "id不存在";
            }
        }
        $data = [];
        if(empty($errors)){
            $data = ArticleModel::delete($id);
        }else{
            $data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '删除失败';
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
    }






}