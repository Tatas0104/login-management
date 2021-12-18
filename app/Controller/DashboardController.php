<?php
namespace Tatas\Belajar\PHP\MVC\Controller;
use Tatas\Belajar\PHP\MVC\App\View;
use Tatas\Belajar\PHP\MVC\Config\Database;
use Tatas\Belajar\PHP\MVC\Repository\SessionRepository;
use Tatas\Belajar\PHP\MVC\Repository\UserRepository;
use Tatas\Belajar\PHP\MVC\Service\SessionService;

class DashboardController{
    private SessionService $sessionService;
    public function __construct()
    {
        $connection=Database::getConnection();
        $userRepository=new UserRepository($connection);
        $sessionRepository=new SessionRepository($connection);
        $this->sessionService=new SessionService($sessionRepository,$userRepository);
    }
    function index(): void
    {
        $user=$this->sessionService->current();
        if($user==null){
        $model = [
            "title" => "Index"
        ];
        View::render('index', $model);
    }else{
        $model = [
            "title" => "Dashboard",
            "user"=>[
                "name"=>$user->name
            ]
        ];
        View::render('dashboard', $model);
    }
    
    }
}