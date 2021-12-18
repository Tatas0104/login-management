<?php
namespace Tatas\Belajar\PHP\MVC\Repository;

use PDO;
use Tatas\Belajar\PHP\MVC\Domain\Session;

class SessionRepository{
    private PDO $connection;
    public function __construct(PDO $connection)
    {
        $this->connection=$connection;
    }
    public function save(Session $session){
        $statement=$this->connection->prepare("INSERT INTO sessions (id,user_id) VALUES(?,?)");
        $statement->execute([$session->id,$session->user_id]);
    }
    public function findById(string $id):?Session{
        $statement=$this->connection->prepare("SELECT id,user_id FROM sessions WHERE id=?");
        $statement->execute([$id]);
        try{
        if($row=$statement->fetch()){
            $session=new Session();
            $session->id=$row['id'];
            $session->user_id=$row['user_id'];
            return $session;
        }else{
            return null;
        }
    }finally{
        $statement->closeCursor();
    }
    }
    public function deleteById(string $id):void
    {
        $statement=$this->connection->prepare("DELETE  FROM sessions WHERE id=?");
        $statement->execute([$id]);
    }
    public function deleteAll(){
        $this->connection->exec("DELETE  FROM sessions");
    }
}