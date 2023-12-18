<?php

class VideoController extends Base {
	public function indexAction() {
        $data = [
            "data"=>VideoModel::listAll(),
            "status"=>1,
        ];
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        return false;
    }


    public function addAction() {
        $errors = [];
        $title = $this->getRequest()->getPost("title", "");
        $source = $this->getRequest()->getPost("source", "");
        if(empty($title)){
            $errors['title'] = "请填写名称";
        }
        if(empty($errors)) {
            $n = VideoModel::getByTitle($title);
            if ($n > 0) {
                $errors['title'] = "名称已存在";
            }
        }
        $data = [];
        if(empty($errors)){
            $VideoModel = new VideoModel($title, $source);
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
        $title = $this->getRequest()->getPost("title", "");
        $source = $this->getRequest()->getPost("source", "");
        if(empty($id)){
            $errors['id'] = "id cannot be empty";
        }
        if(empty($title)){
            $errors['title'] = "请填写名称";
        }
        if(empty($source)){
            $errors['title'] = "请填写源码";
        }
        if(empty($errors)) {
            $n = VideoModel::getById($id);
            if (!isset($n)) {
                $errors['id'] = "id不存在";
            }
        }

        $data = [];
        if(empty($errors)){
            $VideoModel = new VideoModel($title, $source);
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
            $n = VideoModel::getById($id);
            if (!isset($n)) {
                $errors['id'] = "id不存在";
            }
        }
        $data = [];
        if(empty($errors)){
            $data = VideoModel::delete($id);
        }else{
            $data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '删除失败';
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
    }
}
