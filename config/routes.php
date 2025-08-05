<?php

/** @var \PHPFramework\Application $app */

use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Controllers\PostController;
use App\Controllers\ProductController;
use App\Controllers\OrderController;
use App\Controllers\CompanyController;

const MIDDLEWARE = [
    'auth' => \PHPFramework\Middleware\Auth::class,
    'guest' => \PHPFramework\Middleware\Guest::class,
];

$app->router->get('/test', [\App\Controllers\TestController::class, 'index']);
$app->router->post('/test', [\App\Controllers\TestController::class, 'send']);

$app->router->add('/api/v1/test', function () {
    response()->json(['status' => 'ok', 'message' => 'Success page']);
}, ['get', 'post', 'put'])->withoutCsrfToken();

$app->router->post('/register/send', [UserController::class, 'sendVerificationCode'])->middleware(['guest']);
$app->router->post('/register', [UserController::class, 'registerByPhone'])->middleware(['guest']);
$app->router->post('/login', [UserController::class, 'auth'])->middleware(['guest']);
$app->router->post('/logout', [UserController::class, 'logout'])->middleware(['auth']);

// Products
$app->router->get('/products', [ProductController::class, 'index']);

// Order
$app->router->post('/orders', [OrderController::class, 'create'])->middleware(['auth']);
$app->router->get('/orders', [OrderController::class, 'getUserOrders'])->middleware(['auth']);;

//dump(__FILE__ . __LINE__, $app->router->getRoutes());
