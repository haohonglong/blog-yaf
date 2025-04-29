<?php
use base\controller\ControllerBase;
use Yaf\Registry;

class DiaryController extends ControllerBase {
	public function indexAction() {
        $data = DiaryModel::getList();
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
	}
	public function addAction() {
		$errors = [];
		$title = $this->getRequest()->getPost("title", null);
		$content = $this->getRequest()->getPost("content", null);
		if(!isset($title) || empty($title)) { $errors["title"] = "title 参数是必须的"; }
		if(!isset($content) || empty($content)) { $errors["content"] = "content 参数是必须的"; }
		if(empty($errors)) {
			$diary = new DiaryModel($title, $content);
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
		$title = $this->getRequest()->getPost("title", null);
		$content = $this->getRequest()->getPost("content", null);
		if(!isset($id) || empty($id)){ $errors['id'] = "id cannot be empty";}
		if(!isset($title) || empty($title)) { $errors["title"] = "title 参数是必须的"; }
		if(!isset($content) || empty($content)) { $errors["content"] = "content 参数是必须的"; }
		if(empty($errors)) {
			$diary = new DiaryModel($title, $content);
			$data = $diary->edit($id);
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
            $n = DiaryModel::getById($id);
            if (!isset($n)) {
                $errors['id'] = "id不存在";
            }
        }
        $data = [];
        if(empty($errors)){
            $data = DiaryModel::delete($id);
        }else{
            $data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '删除失败';
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
	}
}