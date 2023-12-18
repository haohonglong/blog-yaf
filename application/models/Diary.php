<?php

use Yaf\Registry;

class DiaryModel
{
	private $title, $content;

    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    public static function tableName()
    {
        return 'diary';
    }

	public static function getList(){
		$querys = Registry::get('db')->query("SELECT id, title, content, created_at, updated_at FROM ". static::tableName() ." WHERE is_show=1 ORDER BY id DESC")->fetchAll(\PDO::FETCH_ASSOC);
        $query = [];
        foreach ($querys as $k => $item) {
            $query[$item['id']] = [
                'id'=>$item['id'],
                'title'=>$item['title'],
                'content'=>$item['content'],
                'created_at'=>date("Y-m-d H:i:s", $item['created_at']),
                'updated_at'=>date("Y-m-d H:i:s", $item['updated_at']),
            ];
        }
        $data = [
            "data"=>array_values($query),
            "status"=>1,
        ];
		return $data;
	}

	public static function getById($id) {
        return Registry::get('db')->get(static::tableName(),"id",["id"=>$id]);
    }


	public function create() {
		$today = time();
        $sth  = Registry::get('db')->pdo->prepare("INSERT INTO ".static::tableName() ." SET title=:title, content=:content, created_at=:created_at, updated_at=:updated_at");
        $sth->bindParam(':title', $this->title, \PDO::PARAM_STR);
        $sth->bindParam(':content', $this->content, \PDO::PARAM_STR);
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

    public function edit($id) {
		$today = time();
        $sth  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET title=:title, content=:content, updated_at=:updated_at WHERE id = :id limit 1");
        $sth->bindParam(':id', $id, \PDO::PARAM_INT);
        $sth->bindParam(':title', $this->title, \PDO::PARAM_STR);
        $sth->bindParam(':content', $this->content, \PDO::PARAM_STR);
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
        $sth  = Registry::get('db')->pdo->prepare("DELETE FROM ".static::tableName() ." WHERE id = :id limit 1");
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