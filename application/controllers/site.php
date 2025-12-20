<?php
use base\controller\ControllerBase;
use Yaf\Registry;




class SiteController extends ControllerBase {
    
    public function signupAction() {
        $username = $this->getRequest()->getPost("username", "");
        $password = $this->getRequest()->getPost("password", "");
        $phone = $this->getRequest()->getPost("phone", "");
        $email = $this->getRequest()->getPost("email", "");
        $errors = [];
        $data = [];
        if(empty($username)){
            $errors['username'] = "请填写名称";
        }
        if(empty($password)){
            $errors['password'] = "密码不能为空";
        }
        if(empty($phone)){
            $errors['phone'] = "手机号必填";
        }
        if(empty($email)){
            $errors['email'] = "email必填";
        }

        if(empty($errors)){

            
            $url = "http://blog.admin/api/site/signup";

            $params = [
                "username" => $username,
                "password" => $password,
                "phone" => $phone,
                "email" => $email,
            ];


            /**
             * 
             */
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

            $resp = curl_exec($curl);
            
            if ($resp === false) {
                throw new Exception(curl_error($curl), curl_errno($curl));
            }
            
            curl_close($curl);
            $arr = json_decode($resp, true);
            // =========================================================
            

            if(0 === $arr["status"]) {
                $data['status'] = 0;
                $data['data'] = $arr["data"];
                $data['message'] = 'successful';
            } else {
                $data['status'] = 1;
                $data['error'] = $arr["error"];
                $data['message'] = 'failed';
                
            }


            
        }else{
            $data['status'] = 1;
            $data['error'] = $errors;
            $data['message'] = 'failed';
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
        
    }

    public function loginAction() {
        $redis = Registry::get('redis');

        $username = $this->getRequest()->getPost("username", "");
        $password = $this->getRequest()->getPost("password", "");
        $errors = [];
        $data = [];
        if(empty($username)){
            $errors['username'] = "请填写名称";
        }
        if(empty($password)){
            $errors['password'] = "密码不能为空";
        }

        if(empty($errors)){
            $url = "http://blog.admin/api/site/login";

            $params = [
                "username" => $username,
                "password" => $password,
            ];

            // $security = new Security();
            // $hash = $security->generatePasswordHash($password);
            
            // var_dump($hash, $security->validatePassword($password, $hash));
            // exit;

            /**
             * file_get_contents
             */
            // $options = [
            //     'http' => [
            //         'header'  => "Content-type: application/x-www-form-urlencoded",
            //         'method'  => 'POST',
            //         'content' => http_build_query($params)
            //     ],
            // ];
            // $context  = stream_context_create($options);
            // $resp = file_get_contents($url, false, $context);
            // $arr = json_decode($resp, true);
            // ==========================================================

            /**
             * 
             */
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

            $resp = curl_exec($curl);
            if ($resp === false) {
                throw new Exception(curl_error($curl), curl_errno($curl));
            }
            
            curl_close($curl);
            $arr = json_decode($resp, true);
            // =========================================================
            

            if(0 === $arr["status"]) {
                $data['status'] = 0;
                $data['data'] = $arr["data"];
                $data['message'] = 'successful';

                $user = "user:{$data['data']['id']}";
                if ($redis->exists($user)) {
                    $data['status'] = 1;
                    $data['error'] = '此用户已经在登录中了，您不能再次登录';
                    $data['message'] = 'failed';
                }else{
                    $redis->set($user, json_encode($data['data'], JSON_UNESCAPED_UNICODE));

                }


            } else {
                switch($arr["status"]){
                    case 1:
                        $data['status'] = 1;
                        $data['error'] = "用户名或密码错误";
                        $data['message'] = 'failed';
                        break;
                    case 2:
                        $data['status'] = 2;
                        $data['error'] = $arr["error"];
                        $data['message'] = 'failed';
                        break;
                }
                
            }


            
        }else{
            $data['status'] = 1;
            $data['error'] = $errors;
            $data['message'] = 'failed';
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function logoutAction() {
        $userid = $this->getRequest()->getPost("userid", 1);
        $redis = Registry::get('redis');
        $user = "user:{$userid}";
        $result = $redis->del($user);
        if ($result === 1) {
            $data['status'] = 1;
            $data['message'] = 'successful';
        } else if ($result === 0) {
            $data['status'] = 0;
            $data['message'] = 'failed';
        }
        
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;

    }










}