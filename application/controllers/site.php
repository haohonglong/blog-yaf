<?php
use base\controller\ControllerBase;
use Yaf\Registry;

class SiteController extends ControllerBase {
    
    public function loginAction() {
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

    }










}