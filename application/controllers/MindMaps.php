<?php
use base\controller\ControllerBase;
use Yaf\Registry;

class MindMapsController extends ControllerBase {
	public function indexAction() {
        $data = MindMapsModel::getList();
		
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
	}

	public function showAction() {
		$errors  = [];
		$key = $this->getRequest()->getPost("key", null);
		if(!isset($key)) { $errors["key"] = "key 参数是必须的"; }
		if(empty($errors)) {
			$data = MindMapsModel::getByKey($key);
		} else {
			$data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '添加失败';
		}
        
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
	}

	public function addAction() {
		$errors = [];
		$key = $this->getRequest()->getPost("key", null);
		$name = $this->getRequest()->getPost("name", null);
		$thumbnail = $this->getRequest()->getPost("thumbnail", "");
		$content = $this->getRequest()->getPost("content", null);
		$remark = $this->getRequest()->getPost("remark", "");
		
		if(!isset($key) || empty($key)) { $errors["key"] = "key 参数是必须的"; }
		if(!isset($name) || empty($name)) { $errors["name"] = "name 参数是必须的"; }
		if(!isset($content) || empty($content)) { $errors["content"] = "content 参数是必须的"; }
		if(empty($errors)) {
			$diary = new MindMapsModel($key, $name, $thumbnail, $content, $remark);
			$data = $diary->create();
		} else {
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
		$key = $this->getRequest()->getPost("key", null);
		$name = $this->getRequest()->getPost("name", null);
		$thumbnail = $this->getRequest()->getPost("thumbnail", null);
		$remark = $this->getRequest()->getPost("remark", null);
		if(!isset($id) || empty($id)) { $errors["id"] = "id 参数是必须的"; }
		if(!isset($key) || empty($key)) { $errors["key"] = "key 参数是必须的"; }
		if(!isset($name) || empty($name)) { $errors["name"] = "name 参数是必须的"; }
		if(empty($errors)) {
			$diary = new MindMapsModel($key, $name, $thumbnail, null, $remark);
			$data = $diary->edit($id);
		} else {
			$data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '修改失败';
		}

		echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
	}

	public function editByKeyAction() {
		$errors = [];
		$key = $this->getRequest()->getPost("key", null);
		$content = $this->getRequest()->getPost("content", null);
		if(!isset($key) || empty($key)) { $errors["key"] = "key 参数是必须的"; }
		if(!isset($content) || empty($content)) { $errors["content"] = "content 参数是必须的"; }
		if(empty($errors)) {
			$data = MindMapsModel::editByKey($key, $content);
		} else {
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
        if(!isset($id) || empty($id)){
            $errors['id'] = "id是必须的";
        }
        if(empty($errors)) {
            $n = MindMapsModel::getById($id);
            if (!isset($n)) {
                $errors['id'] = "id不存在";
            }
        }
        $data = [];
        if(empty($errors)){
            $data = MindMapsModel::delete($id);
        }else{
            $data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '删除失败';
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
    }
}