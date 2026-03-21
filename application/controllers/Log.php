<?php
use base\controller\ControllerBase;


class LogController extends ControllerBase {
    public function indexAction() {
		$userid = $this->getRequest()->getPost("userid", null);
		$size = $this->getRequest()->getPost("size", 1);
		$rows = $this->getRequest()->getPost("rows", 0);

        $query = LogModel::getByUid($userid);
		
		if($query){
            $data['status'] = 1;
            $data['data'] = $query;
            $data['message'] = '获取成功';
        }else{
            $data['status'] = 0;
            $data['message'] = "没有数据";
        }

        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
	}

}