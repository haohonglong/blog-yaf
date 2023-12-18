<?php

use Yaf\Registry;

class MindMapsModel
{
	private $key, $name, $thumbnail, $content, $remark;

    public function __construct($key, $name, $thumbnail="", $content, $remark="")
    {
        $this->key = $key;
        $this->name = $name;
        $this->thumbnail = $thumbnail;
        $this->content = $content;
        $this->remark = $remark;
    }

    public static function tableName()
    {
        return 'mindmaps';
    }

    public static function getById($id) {
        return Registry::get('db')->get(static::tableName(), "id", ["id"=>$id]);
    }

    public static function getList(){
        try{
            $querys = Registry::get('db')->query("SELECT `id`, `key`, `name`, `thumbnail`, `content`, `remark`, `created_at`, `updated_at` FROM ". static::tableName() ." WHERE is_show=1 ORDER BY id DESC")->fetchAll(\PDO::FETCH_ASSOC);
            $query = [];
            foreach ($querys as $k => $item) {
                $query[$item['id']] = [
                    'id'=>$item['id'],
                    'key'=>$item['key'],
                    'name'=>$item['name'],
                    'thumbnail'=>$item['thumbnail'],
                    'content'=>$item['content'],
                    'remark'=>$item['remark'],
                    'created_at'=>date("Y-m-d H:i:s", $item['created_at']),
                    'updated_at'=>date("Y-m-d H:i:s", $item['updated_at']),
                ];
            }
            $data = [
                "data"=>array_values($query),
                "status"=>1,
            ];
        } catch(Exception $e)  {
            $data = [
                "message"=> $e->getMessage(),
                "status"=> 0,
            ];
        }
		return $data;
	}

	public static function getByKey($key){
		$querys = Registry::get('db')->query("SELECT `key`, `name`, `content`, `remark`, `created_at`, `updated_at` FROM ". static::tableName() ." WHERE `key`='{$key}' limit 1")->fetchAll(\PDO::FETCH_ASSOC);
        $query = [];

		if(isset($querys) && !empty($querys)) {
			$query = [
				'key'=>$querys[0]['key'],
				'name'=>$querys[0]['name'],
				'content'=>$querys[0]['content'],
				'remark'=>$querys[0]['remark'],
				'created_at'=>date("Y-m-d H:i:s", $querys[0]['created_at']),
				'updated_at'=>date("Y-m-d H:i:s", $querys[0]['updated_at']),
			];

			$data = [
				"data"=>$query,
				"status"=>1,
			];
		} else {
			$data = [
				"status"=>0,
			];
		}
        
		return $data;
	}



	public function create() {
		$today = time();
        $sth  = Registry::get('db')->pdo->prepare("INSERT INTO ".static::tableName() ." SET `key`=:key, `name`=:name, `thumbnail`=:thumbnail, `content`=:content, `remark`=:remark, `created_at`=:created_at, `updated_at`=:updated_at");
        $sth->bindParam(':key', $this->key, \PDO::PARAM_STR);
        $sth->bindParam(':name', $this->name, \PDO::PARAM_STR);
        $sth->bindParam(':thumbnail', $this->thumbnail, \PDO::PARAM_STR);
        $sth->bindParam(':content', $this->content, \PDO::PARAM_STR);
        $sth->bindParam(':remark', $this->remark, \PDO::PARAM_STR);
        $sth->bindParam(':created_at', $today, \PDO::PARAM_INT);
        $sth->bindParam(':updated_at', $today, \PDO::PARAM_INT);
        if($sth->execute()){
            $data['status'] = 1;
            $data['message'] = '添加成功';
        }else{
            $data['status'] = 0;
            $data['message'] = $sth->errorInfo();
        }
        return $data;
    }
    /**
     * 
     */
    public function edit($id) {
		$today = time();
        $columns = "";
        if(isset($this->thumbnail)) {
            $columns .= "`thumbnail`=:thumbnail,";
        }

        if(isset($this->remark)) {
            $columns .= " `remark`=:remark,";
        }

        $sth  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET `key`= :key, `name`=:name, {$columns} `updated_at`=:updated_at WHERE `id`=:id limit 1");
        $sth->bindParam(':id', $id, \PDO::PARAM_INT);
        $sth->bindParam(':key', $this->key, \PDO::PARAM_STR);
        $sth->bindParam(':name', $this->name, \PDO::PARAM_STR);
        $sth->bindParam(':updated_at', $today, \PDO::PARAM_INT);
        if(isset($this->thumbnail)) {
            $sth->bindParam(':thumbnail', $this->thumbnail, \PDO::PARAM_STR);
        }
        if(isset($this->remark)) {
            $sth->bindParam(':remark', $this->remark, \PDO::PARAM_STR);
        }

        if($sth->execute()){
            $data['status'] = 1;
            $data['message'] = '修改成功';
        }else{
            $data['status'] = 0;
            $data['message'] = $sth->errorInfo();
        }
        return $data;
    }

    public static function editByKey($key, $content) {
		$today = time();
        $sth  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET `content`=:content, `updated_at`=:updated_at WHERE `key`=:key limit 1");
        $sth->bindParam(':key', $key, \PDO::PARAM_STR);
        $sth->bindParam(':content', $content, \PDO::PARAM_STR);
        $sth->bindParam(':updated_at', $today, \PDO::PARAM_INT);
        if($sth->execute()){
            $data['status'] = 1;
            $data['message'] = '修改成功';
        }else{
            $data['status'] = 0;
            $data['message'] = $sth->errorInfo();
        }
        return $data;
    }

    public static function delete($id) {
        $sth  = Registry::get('db')->pdo->prepare("DELETE FROM ".static::tableName() ." WHERE `id`=:id limit 1");
        $sth->bindParam(':id', $id, \PDO::PARAM_INT);
        if($sth->execute()){
            $data['status'] = 1;
            $data['message'] = '删除成功';
        }else{
            $data['status'] = 0;
            $data['message'] = $sth->errorInfo();
        }
        return $data;
    }

}