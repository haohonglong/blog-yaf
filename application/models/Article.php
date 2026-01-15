<?php


use Yaf\Registry;

class ArticleModel
{
    public $userid, $sid, $title, $content;
    public static function tableName()
    {
        return 'article';
    }

    public function __construct($userid, $sid, $title, $content)
    {
        $this->userid = $userid;
        $this->sid = $sid;
        $this->title = $title;
        $this->content = htmlentities($content);
    }

    public static function getList(){
		$querys = Registry::get('db')->query("SELECT a.*, b.name FROM ". static::tableName() ." AS a, ". SortsModel::tableName() ." AS b WHERE a.sorts_id = b.id AND is_show=1 ORDER BY id DESC")->fetchAll(\PDO::FETCH_ASSOC);
        
        $query = [];
        foreach ($querys as $k => $item) {
            $query[] = [
                'id'=>$item['id'],
                'sid'=>$item['sorts_id'],
                'sortName'=>$item['name'],
                'title'=>$item['title'],
                'content'=>html_entity_decode($item['content']),
                'created_at'=>$item['cdate'],
                'updated_at'=>$item['udate'],
                
            ];
        }
        $data = [
            "data"=>$query,
            "status"=>1,
        ];
		return $data;
	}


    public static function search($title){
        $querys = Registry::get('db')->query("SELECT id, title, content FROM ".static::tableName() ." WHERE title LIKE '%{$title}%'")->fetchAll(\PDO::FETCH_ASSOC);
        $query = [];
        foreach ($querys as $k => $item) {
            $query[] = [
                'id'=>$item['id'],
                'title'=>$item['title'],
                'content'=>html_entity_decode($item['content']),
            ];
        }
        $data = [
            "data"=>$query,
            "status"=>1,
        ];

        return $data;
    }

    public static function getById($id) {

        $columns = explode(",", "id, sorts_id, title, content, cdate, udate");

        $item = Registry::get('db')->get(static::tableName(), $columns, ["id"=>$id]);
        
        if(isset($item) || !empty($item)) {
            $data = [
                'id'=>$item['id'],
                'sid'=>$item['sorts_id'],
                'title'=>$item['title'],
                'content'=>html_entity_decode($item['content']),
                'created_at'=>$item['cdate'],
                'updated_at'=>$item['udate'],
                
            ];
            return $data;
        } else {
            return null;
        }
        
    }


    public function create() {
        $created_at = date("Y-m-d H:i:s");
        $sth  = Registry::get('db')->pdo->prepare("INSERT INTO ".static::tableName() ." SET user_id=:userid, sorts_id=:sid, title=:title, content=:content, cdate=:created_at, udate=:created_at");
        $sth->bindParam(':userid', $this->userid, \PDO::PARAM_STR);
        $sth->bindParam(':sid', $this->sid, \PDO::PARAM_STR);
        $sth->bindParam(':title', $this->title, \PDO::PARAM_STR);
        $sth->bindParam(':content', $this->content, \PDO::PARAM_STR);
        $sth->bindParam(':created_at', $created_at, \PDO::PARAM_STR);
        $sth->bindParam(':created_at', $created_at, \PDO::PARAM_STR);

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
        $updated_at = date("Y-m-d H:i:s");
        $sth  = Registry::get('db')->pdo->prepare("UPDATE ".static::tableName() ." SET user_id=:userid, sorts_id=:sid, title=:title, content=:content, udate=:updated_at WHERE id = :id limit 1");
        $sth->bindParam(':id', $id, \PDO::PARAM_STR);
        $sth->bindParam(':userid', $this->userid, \PDO::PARAM_STR);
        $sth->bindParam(':sid', $this->sid, \PDO::PARAM_STR);
        $sth->bindParam(':title', $this->title, \PDO::PARAM_STR);
        $sth->bindParam(':content', $this->content, \PDO::PARAM_STR);
        $sth->bindParam(':updated_at', $updated_at, \PDO::PARAM_STR);
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
        $sth->bindParam(':id', $id, \PDO::PARAM_STR);
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