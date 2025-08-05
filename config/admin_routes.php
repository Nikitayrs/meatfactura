<?php

/** @var \PHPFramework\Application $app */

use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Controllers\Admin\AdminController;
use App\Controllers\Admin\AdminCompanyController;
use App\Controllers\Admin\AdminReviewController;
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

$app->router->get('/api/v1/categories', [\App\Controllers\Api\V1\CategoryController::class, 'index'])->withoutCsrfToken();
$app->router->get('/api/v1/categories/(?P<slug>[a-z0-9-]path+)', [\App\Controllers\Api\V1\CategoryController::class, 'view']);

$app->router->get('/admin/dashboard', [HomeController::class, 'dashboard'])->middleware(['auth']);
$app->router->post('/admin/register/user', [AdminController::class, 'storeUser'])->middleware(['guest']);
$app->router->post('/admin/register/company', [AdminController::class, 'storeCompany'])->middleware(['guest']);
$app->router->get('/admin/logout', [AdminController::class, 'logout'])->middleware(['auth']);
$app->router->get('/admin/login', [AdminController::class, 'login'])->middleware(['guest']);
$app->router->post('/admin/login', [AdminController::class, 'auth'])->middleware(['guest']);
$app->router->get('/admin/users', [AdminController::class, 'index'])->middleware(['auth']);
$app->router->get('/admin/posts', [PostController::class, 'index'])->middleware(['auth']);

// Company
$app->router->get('/admin/company/(?P<id>\d+)', [AdminCompanyController::class, 'index']);
$app->router->get('/admin/company/(?P<id>\d+)/review/(?P<id_review>\d+)', [AdminReviewController::class, 'index'])->middleware(['auth']);
$app->router->post('/admin/company/(?P<id>\d+)/approve/(?P<review>\d+)', [AdminReviewController::class, 'approveReview'])->middleware(['auth'])->withoutCsrfToken();
$app->router->post('/admin/company/(?P<id>\d+)/denied/(?P<id_review>\d+)', [AdminReviewController::class, 'deniedReview'])->middleware(['auth'])->withoutCsrfToken();

$app->router->post('/admin/company/(?P<id>\d+)/review', [CompanyController::class, 'review'])->withoutCsrfToken();
$app->router->post('/admin/company/delete-logo', [AdminController::class, 'deleteLogoAction'])->middleware(['auth']);
$app->router->post('/admin/company/delete-logo', [AdminController::class, 'deleteLogoAction'])->middleware(['auth']);
$app->router->post('/admin/reviews/update/(?P<id>\d+)', [AdminController::class, 'updateReview'])->middleware(['auth'])->withoutCsrfToken();
$app->router->post('/admin/company/update/(?P<id>\d+)', [AdminController::class, 'updateCompany'])->middleware(['auth']);

$app->router->get('/admin/post/(?P<slug>[a-z0-9-]+)', function () {
    return "Post " . get_route_param('slug', 'test');
});

$app->router->get('/admin', [HomeController::class, 'index']);

//dump(__FILE__ . __LINE__, $app->router->getRoutes());
