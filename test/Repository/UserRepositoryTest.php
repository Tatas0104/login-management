<?php
namespace Tatas\Belajar\PHP\MVC\Repository;

use PHPUnit\Framework\TestCase;
use Tatas\Belajar\PHP\MVC\Config\Database;
use Tatas\Belajar\PHP\MVC\Domain\User;

class UserRepositoryTest extends TestCase{
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    protected function setUp():void{
        $this->sessionRepository=new SessionRepository(Database::getConnection());
        $this->userRepository=new UserRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }
    public function testSaveSuccess(){
        $user=new User();
        $user->id="tatas";
        $user->name="Tatas";
        $user->password="rahasia";
        $this->userRepository->save($user);
        $result=$this->userRepository->findById($user->id);
        self::assertEquals($user->id,$result->id);
        self::assertEquals($user->name,$result->name);
        self::assertEquals($user->password,$result->password);
    }
    public function  testFindByIdNotFound(){
        $user=$this->userRepository->findById("notfound");
        self::assertNull($user);
    }
}