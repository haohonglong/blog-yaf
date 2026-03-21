<?php

use Yaf\Registry;


class UserModel
{
    public static function tableName() {
        return 'user';
    }

    public static function getByid($id) {
        return Registry::get('db')->get(static::tableName(),"*",["id"=>$id]);
    }
    
    public static function getByWallet($id) {
        return Registry::get('db')->get(static::tableName(),"wallet",["id"=>$id]);
    }

    /**
     * @author: lhh
     * 创建日期：2026-1-18
     * 修改日期：2026-1-19
     * 名称： setWallet
     * 功能：股票交易
     * 说明：
     * 注意：
     * @return Array
     */
    public static function setWallet($userid, $wallet='0.00',$cause = "交易", $content, $updated_at){
        $updated_at = $updated_at ?? date("Y-m-d H:i:s");

        $database = Registry::get('db');
        try{
            $sth = $database->pdo->prepare("UPDATE ".static::tableName() ." SET wallet = :wallet, updated_at = :updated_at WHERE id = :id LIMIT 1");
            $sth->bindParam(':wallet', $wallet, \PDO::PARAM_STR);
            $sth->bindParam(':id', $userid, \PDO::PARAM_STR);
            $sth->bindParam(':updated_at', $updated_at, \PDO::PARAM_STR);
            if($sth->execute()){
                $logModel = new LogModel($userid, $cause, $content, $updated_at);
                $log = $logModel->create();
                if(1 == $log['status']){
                    $data['status'] = 1;
                    $data['message'] = $cause. '成功';
                }else{
                    $data['status'] = 0;
                    $data['message'] = $log['message'];
                }
            }else{
                $data['status'] = 0;
                $data['message'] = $sth->errorInfo() . " in " . __FILE__ . " on line " . __LINE__;
            }
        } catch (\Exception $e) {
            $data['status'] = 0;
            $data['message'] = $e->getMessage();
        }
        
        return $data;

    }

    /**
     * @author: lhh
     * 创建日期：2026-1-18
     * 修改日期：2026-1-19
     * 名称： topUp
     * 功能：钱包充值
     * 说明：
     * 注意：
     * @return Array
     */
    public  static function topUp($userid, $amount='0.00'){
        $updated_at = date("Y-m-d H:i:s");
        $cause = "充值";

        $database = Registry::get('db');
        try{
            $database->pdo->beginTransaction();
            $sth = $database->pdo->prepare("UPDATE ".static::tableName() ." SET wallet = wallet + :amount, updated_at = :updated_at WHERE id = :id LIMIT 1");
            $sth->bindParam(':amount', $amount, \PDO::PARAM_STR);
            $sth->bindParam(':id', $userid, \PDO::PARAM_STR);
            $sth->bindParam(':updated_at', $updated_at, \PDO::PARAM_STR);
            if($sth->execute()){
                $logModel = new LogModel($userid, $cause, "+" .number_format(floatval($amount), 2). "，余额:". number_format(static::getByWallet($userid), 2), $updated_at);
                $log = $logModel->create();
                if(1 == $log['status']){
                    $database->pdo->commit();
                    $data['status'] = 1;
                    $data['message'] = $cause. '成功';
                }else{
                    $database->pdo->rollBack();
                    $data['status'] = 0;
                    $data['message'] = $log['message'];
                }
            }else{
                $database->pdo->rollBack();
                $data['status'] = 0;
                $data['message'] = $sth->errorInfo() . " in " . __FILE__ . " on line " . __LINE__;
            }
        } catch (\Exception $e) {
            $database->pdo->rollBack();
            $data['status'] = 0;
            $data['message'] = $e->getMessage();
        }
        
        return $data;

    }

}