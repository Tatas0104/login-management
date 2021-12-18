<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Tatas\Belajar\PHP\MVC\App\Router;
use Tatas\Belajar\PHP\MVC\Config\Database;
use Tatas\Belajar\PHP\MVC\Controller\DashboardController;
use Tatas\Belajar\PHP\MVC\Controller\HomeController;
use Tatas\Belajar\PHP\MVC\Middleware\MustNotLoginMiddleware;
use Tatas\Belajar\PHP\MVC\Middleware\MustLoginMiddleware;

Database::getConnection("prod");
// Router::add('GET', '/products/([0-9a-zA-Z]*)/categories/([0-9a-zA-Z]*)', ProductController::class, 'categories');

Router::add('GET', '/', DashboardController::class, 'index');
Router::add('GET', '/users/register', HomeController::class, 'register',[MustNotLoginMiddleware::class]);
Router::add('GET', '/users/login', HomeController::class, 'login',
[MustNotLoginMiddleware::class]);
Router::add('GET', '/users/logout', HomeController::class, 'logout',
[MustLoginMiddleware::class]);
Router::add('POST', '/users/register', HomeController::class, 'postRegister',[MustNotLoginMiddleware::class]);
Router::add('POST', '/users/login', HomeController::class, 'postLogin',[MustNotLoginMiddleware::class]);

Router::run();