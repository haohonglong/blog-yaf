<?php


class SortsController extends Base {

    private function getTree($list,$pk='id', $pid='pid', $child='child', $root=0) {
        $tree=[];
        foreach($list as $key=> $val){
          if($val[$pid]==$root){
            //获取当前$pid所有子类
              unset($list[$key]);
              if(! empty($list)){
                $child=$this->getTree($list,$pk,$pid,$child,$val[$pk]);
                if(!empty($child)){
                  $val['child']=$child;
                }
              }
              $tree[]=$val;
          }
        }
        return $tree;
    }

    public function indexAction() {
        $query = SortsModel::showListOfAllLevels();
        $json = json_encode($this->getTree($query),JSON_UNESCAPED_UNICODE);
        echo $json;
        return false;
    }

    public function allAction() {
        $query = SortsModel::showListOfAllLevels();
        $json = json_encode($query,JSON_UNESCAPED_UNICODE);
        echo $json;
        return false;
    }

    public function showAction() {
        $id = $this->getRequest()->getQuery("id", 0);
        $query = SortsModel::findById($id);
        if(isset($query)){
            $data['status'] = 1;
            $data['data'] = $query;
        }else{
            $data['status'] = 0;
        }
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        return false;
    }

    public function addAction() {
        $errors = [];
        $name = $this->getRequest()->getPost("name", "");
        $pid = $this->getRequest()->getPost("pid", 0);
        if(empty($name)){
            $errors['name'] = "请填写名称";
        }
        if(!is_numeric($pid)){
            $pid = 0;
        }

        if(empty($errors)) {
            if (SortsModel::has_name($name, $pid)) {
                $errors['name'] = "类别名称已存在";
            }
        }

        $data = [];
        if(empty($errors)){
            $data = (new SortsModel($name,$pid))->create();
        }else{
            $data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '添加失败';
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        return false;

    }

    public function editAction() {
        $errors = [];
        $id   = $this->getRequest()->getPost("id", "");
        $name = $this->getRequest()->getPost("name", "");
        $pid  = $this->getRequest()->getPost("pid", 0);
        if(empty($id)){
            $errors['id'] = "id cannot be empty";
        }
        if(empty($name)){
            $errors['name'] = "请填写名称";
        }
        if(empty($errors)) {
            $n = SortsModel::getByid($id);
            if (!isset($n)) {
                $errors['id'] = "id不存在";
            }
        }
        if(empty($errors)) {
            if (SortsModel::has_name($name, $pid)) {
                $errors['name'] = "类别名称已存在";
            }
        }
        
        if($id == $pid) {
            $errors['name'] = "当前的类名称不能选择自己为父类";
        }

        $data = [];
        if(empty($errors)){
            $data = (new SortsModel($name,$pid))->edit($id);
        }else{
            $data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '修改失败';
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        return false;

    }

    public function deleteAction() {
        $errors = [];
        $id   = $this->getRequest()->getQuery("id", "");
        if(empty($id)){
            $errors['id'] = "id是必须的";
        }
        if(empty($errors)) {
            $n = SortsModel::getById($id);
            if (!isset($n)) {
                $errors['id'] = "id不存在";
            } else {
                if(SortsModel::hasUrl($id)) {
                    $errors['id'] = "此类别下有数据，不能删除！！！";
                }
            }
        }

        $data = [];
        if(empty($errors)){
            $data = SortsModel::delete($id);
        }else{
            $data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '删除失败';
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        return false;

    }



}