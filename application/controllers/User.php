<?php
use base\controller\ControllerBase;
use Yaf\Registry;

class UserController extends ControllerBase {
    
    public function topUpAction() {
		$errors = [];
        $amount = $this->getRequest()->getPost("amount", '0.00');
        $userid = $this->getRequest()->getPost("userid", null);


		if(!$userid){
			$errors['userid'] = 'userid 非法';
		}
		
		if(floatval($amount) < 0){
			$errors['amount'] = '充值不能是负数';
		}

		if(empty($errors)){
			$data = UserModel::topUp($userid, $amount);

		}else{
			$data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '充值失败';
		}

        
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
    }
	
    public function getWalletAction() {
		$errors = [];
        $userid = $this->getRequest()->getPost("userid", null);


		if(!$userid){
			$errors['userid'] = 'userid 非法';
		}
		

		if(empty($errors)){
			$amount = UserModel::getByWallet($userid);
			if($amount){
				$data['status'] = 1;
				$data['amount'] = $amount;
				$data['message'] = '获取成功';
			}

		}else{
			$data['status'] = 0;
            $data['errors'] = $errors;
            $data['message'] = '获取失败';
		}

        
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
    }





}