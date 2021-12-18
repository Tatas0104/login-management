<?php
namespace Tatas\Belajar\PHP\MVC\Service;
require_once __DIR__ . '/../Helper/helper.php';
use PHPUnit\Framework\TestCase;
use Tatas\Belajar\PHP\MVC\Config\Database;
use Tatas\Belajar\PHP\MVC\Domain\Session;
use Tatas\Belajar\PHP\MVC\Domain\User;
use Tatas\Belajar\PHP\MVC\Repository\SessionRepository;
use Tatas\Belajar\PHP\MVC\Repository\UserRepository;

class SessionServiceTest extends TestCase{
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;
    private SessionService $sessionService;
    protected function setUp():void{
        $this->sessionRepository=new SessionRepository(Database::getConnection());
        $this->userRepository=new UserRepository(Database::getConnection());
        $this->sessionService=new SessionService($this->sessionRepository,$this->userRepository);
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
        $user=new User();
        $user->id="tatas";
        $user->name="Tatas";
        $user->password="rahasia";
        $this->userRepository->save($user);
    }
    function testCreate(){
        $session=$this->sessionService->create('tatas');
        $this->expectOutputRegex("[X-Tatas-COOKIE: $session->id]");
        $result=$this->sessionRepository->findById($session->id);
        self::assertEquals("tatas",$result->user_id);
    }
    function testDestroy(){
        $session=new Session();
        $session->id=uniqid();
        $session->user_id="tatas";
        $this->sessionRepository->save($session);
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        $this->sessionService->destroy();
        $this->expectOutputRegex("[X-Tatas-COOKIE: ]");
        $result=$this->sessionRepository->findById($session->id);
        self::assertNull($result);
    }
    function testCurrent(){
        $session=new Session();
        $session->id=uniqid();
        $session->user_id="tatas";
        $this->sessionRepository->save($session);
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        $user=$this->sessionService->current();
        self::assertEquals($user->id,$session->user_id);
    }

}