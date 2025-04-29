<?php
use base\controller\ControllerBase;

class GoodsnameController extends ControllerBase {
	public function indexAction() {
        $data = [
            "data"=>GoodsnameModel::listAll(),
            "status"=>1,
        ];
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        return false;
    }


    public function addAction() {
        $errors = [];
        $name = $this->getRequest()->getPost("name", "");
        if(empty($name)){
            $errors['name'] = "请填写名称";
        }
        if(empty($errors)) {
            $n = GoodsnameModel::getByName($name);
            if ($n > 0) {
                $errors['name'] = "名称已存在";
            }
        }
        $data = [];
        if(empty($errors)){
            $GoodsnameModel = new GoodsnameModel($name);
            $data = $GoodsnameModel->create();
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
        $id   = $this->getRequest()->getQuery("id", "");
        $name = $this->getRequest()->getPost("name", "");
        if(empty($id)){
            $errors['id'] = "id cannot be empty";
        }
        if(empty($name)){
            $errors['name'] = "请填写名称";
        }
        if(empty($errors)) {
            $n = GoodsnameModel::getById($id);
            if (!isset($n)) {
                $errors['id'] = "id不存在";
            }
        }

        $data = [];
        if(empty($errors)){
            $GoodsnameModel = new GoodsnameModel($name);
            $data = $GoodsnameModel->edit($id);
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
            $n = GoodsnameModel::getById($id);
            if (!isset($n)) {
                $errors['id'] = "id不存在";
            }
        }
        $data = [];
        if(empty($errors)){
            $data = GoodsnameModel::delete($id);
        }else{
            $data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '删除失败';
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
    }
}
