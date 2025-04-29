<?php
use base\controller\ControllerBase;

class Video2Controller extends ControllerBase {
	public function indexAction() {
        $data = [
            "data"=>Video2Model::listAll(),
            "status"=>1,
        ];
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        return false;
    }


    public function addAction() {
        $errors = [];
        $src = $this->getRequest()->getPost("src", "");
		
        if(empty($src)){
            $errors['src'] = "please fill a src of the website";
        }
        if(empty($errors)) {
            $n = Video2Model::getBySrc($src);
            if ($n > 0) {
                $errors['src'] = "src 已存在";
            }
        }
        $data = [];
        if(empty($errors)){
            $VideoModel = new Video2Model($src);
            $data = $VideoModel->create();
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
        $src = $this->getRequest()->getPost("src", "");
        if(empty($id)){
            $errors['id'] = "id cannot be empty";
        }
        if(empty($errors)) {
            $n = Video2Model::getById($id);
            if (!isset($n)) {
                $errors['id'] = "id不存在";
            }
        }
        if(empty($errors)) {
            $n = Video2Model::getBySrc($src);
            if ($n > 0) {
                $errors['src'] = "src 已存在";
            }
        }
        
        if(empty($src)){
            $errors['src'] = "please fill a src of the website";
        }
        

        $data = [];
        if(empty($errors)){
            $VideoModel = new Video2Model($src);
            $data = $VideoModel->edit($id);
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
            $n = Video2Model::getById($id);
            if (!isset($n)) {
                $errors['id'] = "id不存在";
            }
        }
        $data = [];
        if(empty($errors)){
            $data = Video2Model::delete($id);
        }else{
            $data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '删除失败';
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
    }
}
