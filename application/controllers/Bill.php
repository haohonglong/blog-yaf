<?php


class BillController extends Base {
	public function getOneAction() {
		$bill_id   = $this->getRequest()->getQuery("bill_id", null);
        $data = BillsModel::geOnetByBillId($bill_id);
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $json;
        return false;
    }


}
