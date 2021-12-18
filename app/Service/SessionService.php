<?php
namespace Tatas\Belajar\PHP\MVC\Service;

use Tatas\Belajar\PHP\MVC\Domain\Session;
use Tatas\Belajar\PHP\MVC\Domain\User;
use Tatas\Belajar\PHP\MVC\Repository\SessionRepository;
use Tatas\Belajar\PHP\MVC\Repository\UserRepository;

class SessionService {
    public static string $COOKIE_NAME="X-Tatas-COOKIE";
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    function __construct(SessionRepository $sessionRepository,UserRepository $userRepository)
    {
        $this->sessionRepository=$sessionRepository;
        $this->userRepository=$userRepository;
    }
    public function create(string $userid):Session{
        $session=new Session();
        $session->id=uniqid();
        $session->user_id=$userid;
        $this->sessionRepository->save($session);
        setcookie(self::$COOKIE_NAME, $session->id, time() + (60 * 60 * 24 * 30), "/");
        return $session;
    }
    public function destroy(){
        $sessionid=$_COOKIE[self::$COOKIE_NAME]??'';
        $this->sessionRepository->deleteById($sessionid);
        setcookie(self::$COOKIE_NAME, '', 1, "/");
    }
    public function current():?User{
        $sessionid=$_COOKIE[self::$COOKIE_NAME]??'';
        $result=$this->sessionRepository->findById($sessionid);
        if($result==null){
            return null;
        }
        return $this->userRepository->findById($result->user_id);
    }
}