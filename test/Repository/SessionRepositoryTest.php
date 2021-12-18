<?php
namespace Tatas\Belajar\PHP\MVC\Repository;
require_once __DIR__.'/../Helper/helper.php';
use PHPUnit\Framework\TestCase;
use Tatas\Belajar\PHP\MVC\Config\Database;
use Tatas\Belajar\PHP\MVC\Domain\Session;
use Tatas\Belajar\PHP\MVC\Domain\User;

class SessionRepositoryTest extends TestCase{
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;
    protected function setUp():void{
        $this->userRepository=new UserRepository(Database::getConnection());
        $this->sessionRepository=new SessionRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
        $user=new User();
        $user->id="tatas";
        $user->name="Tatas";
        $user->password="rahasia";
        $this->userRepository->save($user);
    }
    function testSaveSuccess(){
        $session=new Session();
        $session->id=uniqid();
        $session->user_id="tatas";
        $this->sessionRepository->save($session);
        $result=$this->sessionRepository->findById($session->id);
        self::assertEquals($session->id,$result->id);
        self::assertEquals($session->user_id,$result->user_id);
    }
    function testDeleteByIdSuccess(){
        $session=new Session();
        $session->id=uniqid();
        $session->user_id="tatas";
        $this->sessionRepository->save($session);
        $result=$this->sessionRepository->findById($session->id);
        self::assertEquals($session->id,$result->id);
        self::assertEquals($session->user_id,$result->user_id);
        $this->sessionRepository->deleteById($session->id);
        $result=$this->sessionRepository->findById($session->id);
        self::assertNull($result);
    }
    function testFindByIdNotFound(){
        $result=$this->sessionRepository->findById("notfound");
        self::assertNull($result);
    }
}