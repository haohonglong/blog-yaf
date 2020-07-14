<?php


class SiteController extends Base {
    
    public function loginAction() {
        $username = $this->getRequest()->getPost("username", "");
        $password = $this->getRequest()->getPost("password", "");
        $errors = [];
        if(empty($username)){
            $errors['username'] = "请填写名称";
        }
        if(empty($password)){
            $errors['password'] = "密码不能为空";
        }

        if(empty($errors)){
            $sth  = Registry::get('db')->pdo->prepare("SELECT * from ".User::tableName() ." WHERE username = :username and password = :password");
            $sth->bindParam(':username', $username, PDO::PARAM_STR);
            $sth->bindParam(':password', $password, PDO::PARAM_STR);
            if($sth->execute()){
                $data['status'] = 1;
                $data['message'] = '添加成功';
            }else{
                $data['status'] = 0;
                $data['message'] = $sth->errorInfo();
            }
        }else{
            $data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '添加失败';
        }
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
    }

    public function logoutAction() {

    }










}