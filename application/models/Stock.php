<?php

use Yaf\Registry;
use base\model\StockModelBase;

class StockModel extends StockModelBase
{

    private $stock_code, $stock_name,  $stock_remark, $updated_at, $stock_cost, $flag, $bought, $stock_number, $userid, $tax;

    public function __construct($userid, $stock_id, $stock_code, $stock_name, $data)
    {
        parent::__construct($stock_id, $data);
        $this->stock_code = $stock_code;
        $this->stock_name = $stock_name;
        $this->userid = $userid;
        $this->stock_remark = $data['stock_remark'] ?? "";
        $this->updated_at = $data['updated_at'] ?? null;
        $this->stock_cost = $data['stock_cost'] ?? null;
        $this->tax = $data['tax'] ?? 5.00;
        $this->bought = $data['bought'] ?? null;
        $this->stock_number = $data['stock_number'] ?? null;
        $this->flag = $data['flag'] ?? 0;
        $this->flag = (int)($this->flag);

    }

    public static function tableName()
    {
        return 'stock';
    }


    /**
     * @author: lhh
     * 创建日期：2024-5-06
     * 修改日期：2024-5-06
     * 名称： getByName
     * 功能：
     * 说明：
     * 注意：
     * @param $name
     * @return mixed
     */
    public static function getByName($name) {
        return Registry::get('db')->get(static::tableName(),"stock_id",["stock_name"=>$name]);
    }

    public static function getById($stock_id) {
        return Registry::get('db')->get(static::tableName(), "*", ["stock_id"=>$stock_id]);
    }
    
    public static function geCodetById($stock_id) {
        return Registry::get('db')->get(static::tableName(), "stock_code", ["stock_id"=>$stock_id]);
    }

    public static function getByCode($stock_code) {
        return Registry::get('db')->get(static::tableName(), "stock_code", ["stock_code"=>$stock_code]);
    }


    public static function getLastOneByStockId($stock_id) {
        $query = Registry::get('db')->get(static::tableName(), "*", ["stock_id"=>$stock_id]);
        return $query;
    }


    /**
     * @author: lhh
     * 创建日期：2024-5-06
     * 修改日期：2025-8-21
     * 名称： getList
     * 功能：只列出属于当前用户的股票
     * 说明：
     * 注意：
     * @return mixed
     */
    public static function getList($userid, $size = 1, $rows = 0) {
        $sql = "";
        if($rows > 0 && $size > 0){
            $size = ($size-1) * $rows;
            $sql = " LIMIT {$size}, {$rows}";
        }


        $select = "SELECT 
                a.amount, a.amplitude, a.average, a.change, a.close, a.created_at, a.flag, a.highest, a.ldown, a.level, a.lowest, a.lup, a.open, a.stock_code, a.stock_id, a.stock_name, a.stock_number, a.stock_price, a.stock_remark, a.updated_at, a.volume,
                b.id, b.bought, b.cost, b.tax
            FROM ".static::tableName()." as a 
            LEFT JOIN ".UserAndStockModel::tableName()." as b ON a.stock_id = b.stock_id
            ";
        $query = Registry::get('db')->query($select. " WHERE b.userid = {$userid} ORDER BY created_at DESC ". $sql)->fetchAll(\PDO::FETCH_ASSOC);
        foreach($query as $k => $v){
            $query[$k]['level'] = +$v['level'];
            $query[$k]['bought'] = +$v['bought'];
            $query[$k]['tax'] = +$v['tax'];
            $query[$k]['stock_number'] = +$v['stock_number'];
            
            
        }
        return $query;
    }

    /**
     * @author: lhh
     * 创建日期：2025-12-19
     * 修改日期：2025-12-19
     * 名称： getAllList
     * 功能：获取所有股票
     * 说明：
     * 注意：
     * @return mixed
     */
    public static function getAllList() {

        $all = "amount, amplitude, average, bought, change, close, cost, created_at, flag, highest, ldown, level, lowest, lup, open, stock_code, stock_id, stock_name, stock_number, stock_price, stock_remark, tax, updated_at, volume";

        $query = Registry::get('db')->query("SELECT * FROM ".static::tableName()." WHERE flag = 0 ORDER BY created_at DESC")->fetchAll(\PDO::FETCH_ASSOC);
        foreach($query as $k => $v){
            $query[$k]['level'] = +$v['level'];
            $query[$k]['bought'] = +$v['bought'];
            $query[$k]['tax'] = +$v['tax'];
            $query[$k]['stock_number'] = +$v['stock_number'];
            
            
        }
        return $query;
    }


    
    /**
     * @author: lhh
     * 创建日期：2024-5-08
     * 修改日期：2024-5-09
     * 名称： setStockNumbers
     * 功能：
     * 说明：
     * 注意：
     * @param $id
     * @return mixed
     */
    public static function setStockNumber($stock_id, $stock_number) {
        $stock  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET stock_number=:stock_number WHERE stock_id = :stock_id");
        $stock->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);
        $stock->bindParam(':stock_number', $stock_number, \PDO::PARAM_STR);
        if($stock->execute()){
            return true;
        }

        return false;
        
    }

    /**
     * @author: lhh
     * 创建日期：2024-11-03
     * 修改日期：2024-11-03
     * 名称： search
     * 功能：搜索值按照指定的字段
     * 说明：
     * 注意：
     * @param $id
     * @return mixed
     */
    public static function search($key, $value) {
        $query = Registry::get('db')->query("SELECT * FROM ".static::tableName()." WHERE {$key} = {$value}")->fetchAll(\PDO::FETCH_ASSOC);
        return $query;
        
    }

    /**
     * @author: lhh
     * 创建日期：2024-10-29
     * 修改日期：2024-10-29
     * 名称： setLevel
     * 功能：五星推荐
     * 说明：
     * 注意：
     * @param $id
     * @return mixed
     */
    public static function setLevel($stock_id, $level=0) {
        $stock  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET level=:level WHERE stock_id = :stock_id limit 1");
        $stock->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);
        $stock->bindParam(':level', $level, \PDO::PARAM_STR);
        if($stock->execute()){
            return true;
        }

        return false;
        
    }



    /**
     * @author: lhh
     * 创建日期：2024-5-21
     * 修改日期：2025-7-30
     * 名称： update
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public function update(){
        $setFields = [
            "`stock_price`=:stock_price",
            "`open`=:open",
            "`close`=:close", 
            "`lup`=:lup",
            "`ldown`=:ldown",
            "`highest`=:highest",
            "`lowest`=:lowest", 
            "`average`=:average",
            "`change`=:change",
            "`amplitude`=:amplitude",
            "`volume`=:volume",
            "`amount`=:amount",
            "`updated_at`=:updated_at"
        ];

        // 动态添加可选字段
        if(!is_null($this->bought)){
            $setFields[] = "`bought`=:bought";
            $setFields[] = "`cost`=:cost";
        }

        if(!is_null($this->stock_number)){
            $setFields[] = "`stock_number`=:stock_number";
        }

        $setClause = implode(", ", $setFields);

        $sql = "UPDATE ".static::tableName() ." SET " . $setClause . " WHERE `stock_id`=:stock_id";
        // error_log("执行的SQL: " . $sql . " in " . __FILE__ . " on line " . __LINE__); // 调试用
        

        $stock = Registry::get('db')->pdo->prepare($sql);

        if(!is_null($this->bought)){
            $stock->bindParam(':bought', $this->bought, \PDO::PARAM_STR);
            $stock->bindParam(':cost', $this->stock_cost, \PDO::PARAM_STR);
        }

        if(!is_null($this->stock_number)){
            $stock->bindParam(':stock_number', $this->stock_number, \PDO::PARAM_STR);
        }

        // 绑定固定参数
        $stock->bindParam(':stock_id', $this->stock_id, \PDO::PARAM_STR);
        $stock->bindParam(':stock_price', $this->stock_price, \PDO::PARAM_STR);
        $stock->bindParam(':open', $this->open, \PDO::PARAM_STR);
        $stock->bindParam(':close', $this->close, \PDO::PARAM_STR);
        $stock->bindParam(':lup', $this->lup, \PDO::PARAM_STR);
        $stock->bindParam(':ldown', $this->ldown, \PDO::PARAM_STR);
        $stock->bindParam(':highest', $this->highest, \PDO::PARAM_STR);
        $stock->bindParam(':lowest', $this->lowest, \PDO::PARAM_STR);
        $stock->bindParam(':average', $this->average, \PDO::PARAM_STR);
        $stock->bindParam(':change', $this->change, \PDO::PARAM_STR);
        $stock->bindParam(':amplitude', $this->amplitude, \PDO::PARAM_STR);
        $stock->bindParam(':volume', $this->volume, \PDO::PARAM_STR);
        $stock->bindParam(':amount', $this->amount, \PDO::PARAM_STR);
        $stock->bindParam(':updated_at', $this->updated_at, \PDO::PARAM_STR);

        if($stock->execute()){
            $data['status'] = 1;
            $data['message'] = '更新成功';
        }else{
            $error = $stock->errorInfo();
            error_log("SQL错误详情: " . print_r($error, true));
            $data['status'] = 0;
            $data['message'] = $error . " in " . __FILE__ . " on line " . __LINE__;
        }

        return $data;
    }
    /**
     * @author: lhh
     * 创建日期：2025-12-22
     * 修改日期：2025-12-22
     * 名称： updateTests
     * 功能：只更新flag = 1，批量更新
     * 说明：交易时不会执行，只有请求api更新股票信息时才执行，考虑到网络性能问题，自动更新股票信息时每个股票代码只更新一次,其余同名股票代码数据更新，通过这条更新过的数据复制来更新它们
     * 注意：
     * @return mixed
     */
    public function updateTests(){
        $setFields = [
            "`stock_price`=:stock_price",
            "`open`=:open",
            "`close`=:close", 
            "`lup`=:lup",
            "`ldown`=:ldown",
            "`highest`=:highest",
            "`lowest`=:lowest", 
            "`average`=:average",
            "`change`=:change",
            "`amplitude`=:amplitude",
            "`volume`=:volume",
            "`amount`=:amount",
            "`updated_at`=:updated_at"
        ];


        $setClause = implode(", ", $setFields);

        $sql = "UPDATE ".static::tableName() ." SET " . $setClause . " WHERE `flag` = 1 AND `stock_code`=:stock_code";
        // error_log("执行的SQL: " . $sql . " in " . __FILE__ . " on line " . __LINE__); // 调试用
        

        $stock = Registry::get('db')->pdo->prepare($sql);


        // 绑定固定参数
        $stock->bindParam(':stock_code', $this->stock_code, \PDO::PARAM_STR);
        $stock->bindParam(':stock_price', $this->stock_price, \PDO::PARAM_STR);
        $stock->bindParam(':open', $this->open, \PDO::PARAM_STR);
        $stock->bindParam(':close', $this->close, \PDO::PARAM_STR);
        $stock->bindParam(':lup', $this->lup, \PDO::PARAM_STR);
        $stock->bindParam(':ldown', $this->ldown, \PDO::PARAM_STR);
        $stock->bindParam(':highest', $this->highest, \PDO::PARAM_STR);
        $stock->bindParam(':lowest', $this->lowest, \PDO::PARAM_STR);
        $stock->bindParam(':average', $this->average, \PDO::PARAM_STR);
        $stock->bindParam(':change', $this->change, \PDO::PARAM_STR);
        $stock->bindParam(':amplitude', $this->amplitude, \PDO::PARAM_STR);
        $stock->bindParam(':volume', $this->volume, \PDO::PARAM_STR);
        $stock->bindParam(':amount', $this->amount, \PDO::PARAM_STR);
        $stock->bindParam(':updated_at', $this->updated_at, \PDO::PARAM_STR);

        if($stock->execute()){
            $data['status'] = 1;
            $data['message'] = '更新成功';
        }else{
            $error = $stock->errorInfo();
            error_log("SQL错误详情: " . print_r($error, true));
            $data['status'] = 0;
            $data['message'] = $error . " in " . __FILE__ . " on line " . __LINE__;
        }

        return $data;
    }

    /**
     * @author: lhh
     * 创建日期：2024-5-06
     * 修改日期：2025-12-19
     * 名称： create
     * 功能：
     * 说明：
     * 注意：
     * @return mixed
     */
    public function create() {
        $created_at = date("Y-m-d H:i:s");
        $database = Registry::get('db');
        if(0 == $this->flag && !is_null(static::getByCode($this->stock_code))){
            $this->flag = 1;
        }

        try{
            $database->pdo->beginTransaction();
            $stock  = $database->pdo->prepare("INSERT INTO ".static::tableName() ." SET stock_id=:stock_id, stock_code=:stock_code, stock_name=:stock_name, stock_remark=:stock_remark, created_at=:created_at, updated_at=:created_at, flag=:flag, tax=:tax");
            $stock->bindParam(':stock_id', $this->stock_id, \PDO::PARAM_STR);
            $stock->bindParam(':stock_code', $this->stock_code, \PDO::PARAM_STR);
            $stock->bindParam(':stock_name', $this->stock_name, \PDO::PARAM_STR);
            $stock->bindParam(':stock_remark', $this->stock_remark, \PDO::PARAM_STR);
            $stock->bindParam(':created_at', $created_at, \PDO::PARAM_STR);
            $stock->bindParam(':flag', $this->flag, \PDO::PARAM_STR);
            $stock->bindParam(':tax', $this->tax, \PDO::PARAM_STR);
            if($stock->execute()){

                $userAndStockModel = new UserAndStockModel($this->userid, $this->stock_id, [
                    'created_at' => $created_at,
                    'flag' => $this->flag,
                ]);
                $userAndStockModelData = $userAndStockModel->create();

                if(0  == $userAndStockModelData['status']){
                    $database->pdo->rollBack();
                    return $userAndStockModelData;
                }
                $data['status'] = 1;
                $data['message'] = '添加成功';
                $database->pdo->commit();
                
            }else{
                $data['status'] = 0;
                $data['message'] = $stock->errorInfo();
                $database->pdo->rollBack();
            }

        }catch (Exception $e) {
            $database->pdo->rollBack();
            $data['status'] = 0;
            $data['message'] = $e->getMessage() . " in " . __FILE__ . " on line " . __LINE__;
			
        }
        
        return $data;
    }

    /**
     * @author: lhh
     * 创建日期：2025-6-27
     * 修改日期：2026-1-14
     * 名称： edit
     * 功能：
     * 说明：
     * 注意：如果修改了stock_code ，那么所有的stock_code 都要改变
     * @return mixed
     */
    public function edit() {
        $updated_at = date("Y-m-d H:i:s");
        $old_stock_code = static::geCodetById($this->stock_id);

        $database = Registry::get('db');

        try {
            $database->pdo->beginTransaction();

            $stock  = $database->pdo->prepare("UPDATE ".static::tableName() ." SET stock_code=:stock_code, stock_name=:stock_name, cost=:stock_cost, tax=:tax, stock_remark=:stock_remark, updated_at=:updated_at WHERE stock_id=:stock_id");
            $stock->bindParam(':stock_id', $this->stock_id, \PDO::PARAM_STR);
            $stock->bindParam(':stock_code', $this->stock_code, \PDO::PARAM_STR);
            $stock->bindParam(':stock_name', $this->stock_name, \PDO::PARAM_STR);
            $stock->bindParam(':stock_remark', $this->stock_remark, \PDO::PARAM_STR);
            $stock->bindParam(':updated_at', $updated_at, \PDO::PARAM_STR);
            $stock->bindParam(':stock_cost', $this->stock_cost, \PDO::PARAM_STR);
            $stock->bindParam(':tax', $this->tax, \PDO::PARAM_STR);
            if($stock->execute()){
                $data['status'] = 1;
                $data['message'] = '修改成功';
                $history = StockHistoryModel::setCost($this->stock_id, $this->stock_cost, $this->tax, $updated_at);
                $userAndStock = UserAndStockModel::setCost($this->userid, $this->stock_id, $this->stock_cost, $this->tax, $updated_at);
                if(0 == $history['status']){
                    $database->pdo->rollBack();
                    $data = $history;
                    return $data;
                }

                if(0 == $userAndStock['status']){
                    $database->pdo->rollBack();
                    $data = $userAndStock;
                    return $data;
                }
                if($old_stock_code != $this->stock_code){
                    $stock2  = $database->pdo->prepare("UPDATE ".static::tableName() ." SET stock_code=:stock_code, updated_at=:updated_at WHERE stock_code=:old_stock_code");
                    $stock2->bindParam(':old_stock_code', $old_stock_code, \PDO::PARAM_STR);
                    $stock2->bindParam(':stock_code', $this->stock_code, \PDO::PARAM_STR);
                    $stock2->bindParam(':updated_at', $updated_at, \PDO::PARAM_STR);
                    if(!$stock2->execute()){
                        $data['status'] = 0;
                        $data['message'] = $stock2->errorInfo();
                        $database->pdo->rollBack();
                        return $data;
                    }
                }
                $database->pdo->commit();
            }else{
                $database->pdo->rollBack();
                $data['status'] = 0;
                $data['message'] = $stock->errorInfo();
            }
        }catch (Exception $e) {
            $database->pdo->rollBack();
            $data['status'] = 0;
            $data['message'] = $e->getMessage() . " in " . __FILE__ . " on line " . __LINE__;
			
        }

        

        
        return $data;
    }



    public static function delete($userid, $stock_id) {
        $database = Registry::get('db');
        if(0  == static::getById($stock_id)['flag']){ //非测试股票不能被删除
            return [
                'status' => 1,  // 1=失败
                'message' => '非测试股票不能被删除',
            ];
        }
        
        // 添加调试信息
        error_log("第 " . __LINE__ . " 行: 开始删除股票数据, stock_id: " . $stock_id);

        try {
            $database->pdo->beginTransaction();
            
            // 定义删除顺序（根据外键约束调整）
            $tablesToDelete = [
                StockDetailModel::tableName(),
                StockDateModel::tableName(), 
                StockHistoryModel::tableName(),
                UserAndStockModel::tableName(),
                static::tableName() // 主表最后删除
            ];
            
            foreach ($tablesToDelete as $table) {
                if(UserAndStockModel::tableName() == $tabl){ // 只有user_stock 表需要userid
                    $stmt = $database->pdo->prepare("DELETE FROM {$table} WHERE userid=:userid AND stock_id = :stock_id");
                    $stmt->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);
                    $stmt->bindParam(':userid', $userid, \PDO::PARAM_STR);
                }else {
                    $stmt = $database->pdo->prepare("DELETE FROM {$table} WHERE stock_id = :stock_id");
                    $stmt->bindParam(':stock_id', $stock_id, \PDO::PARAM_STR);

                }
                
                if (!$stmt->execute()) {
                    $error = $stmt->errorInfo();
                    error_log("第 " . __LINE__ . " 行: 删除 {$table} 失败 - " . print_r($error, true));
                    throw new Exception("删除 {$table} 失败: " . $error[2]);
                }
                
                $affectedRows = $stmt->rowCount();
                error_log("第 " . __LINE__ . " 行: 从 {$table} 删除了 {$affectedRows} 条记录");
            }
            
            $database->pdo->commit();
            
            error_log("第 " . __LINE__ . " 行: 股票数据删除成功");
            return [
                'status' => 0,  // 0=成功
                'message' => '删除成功'
            ];
            
        } catch (Exception $e) {
            $database->pdo->rollBack();
            
            error_log("第 " . __LINE__ . " 行: 删除过程发生异常 - " . $e->getMessage());
            return [
                'status' => 1,  // 1=失败
                'message' => '删除失败: ' . $e->getMessage()
            ];
        }
    }

    

}