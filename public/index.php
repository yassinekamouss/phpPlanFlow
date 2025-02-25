<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\UserController;
use App\Controllers\TaskController;
use App\Controllers\CommentController;
use App\Controllers\ProjectController;
use App\Controllers\ResponsableController;
use App\Controllers\AdminController;
use App\Controllers\ContactController;
use App\Router;

$router = new Router();


$router->get('/', function () {
    require_once __DIR__ . '/../src/Views/login.php';
    exit ;
});

$router->get('/forbidden', function () {
    require_once __DIR__ . '/../src/Views/errors/403.php';
});
$router->get('/not-found', function () {
    require_once __DIR__ . '/../src/Views/errors/404.php';
});

$router->post('/login', [UserController::class, 'login']);
$router->get('/logout', [UserController::class, 'logout']);

$router->get('/contact', [ContactController::class, 'index']);
$router->post('/contact', [ContactController::class, 'send']);
$router->post('/contact/{id}', [ContactController::class, 'update']);

// pages des utilisateurs :
$router->get('/user/home' , [UserController::class, 'home']);
$router->get('/user/task' , [UserController::class, 'task']);
$router->get('/user/project' , [UserController::class, 'project']);
$router->post('/user/profile' , [UserController::class, 'profile']);

// pages des responsables :
$router->get('/responsable/home' , [ResponsableController::class, 'home']);
$router->get('/responsable/project' , [ResponsableController::class, 'project']);

// pages des responsables :
$router->get('/admin/home' , [AdminController::class, 'home']);
$router->get('/admin/project' , [AdminController::class, 'project']);
$router->get('/admin/calander' , [AdminController::class, 'calander']);
$router->get('/admin/membres' , [AdminController::class, 'membres']);
$router->get('/admin/rapport' , [AdminController::class, 'rapport']);
$router->get('/admin/support' , [AdminController::class, 'support']);

// Route pour la gestion des utilisateurs
$router->post('/user', [UserController::class, 'create']);
$router->post('/user/{id}', [UserController::class, 'update']);
$router->delete('/user/{id}', [UserController::class, 'delete']);
$router->get('/user', [UserController::class, 'findAll']);

// Route pour la gestion des taches
$router->post('/task', [TaskController::class, 'create']);
$router->post('/task/{id}', [TaskController::class, 'update']);
$router->delete('/task/{id}', [TaskController::class, 'delete']);
$router->get('/task', [TaskController::class, 'findAll']);
$router->get('/task/{id}', [TaskController::class, 'findById']);

// Route pour la gestion des commentaire 
$router->post('/comment', [CommentController::class, 'create']);
$router->post('/comment/{id}', [CommentController::class, 'update']);
$router->delete('/comment/{id}', [CommentController::class, 'delete']);
$router->get('/comment', [CommentController::class, 'findAll']);
$router->get('/comment/{id}', [CommentController::class, 'findById']);

// Route pour la gestion des projets
$router->post('/project', [ProjectController::class, 'create']);
$router->post('/project/{id}', [ProjectController::class, 'update']);
$router->delete('/project/{id}', [ProjectController::class, 'delete']);
$router->get('/project', [ProjectController::class, 'findAll']);
$router->get('/project/{id}', [ProjectController::class, 'findById']);

// Dispatch la requête
try {
    $response = $router->dispatch(
        $_SERVER['REQUEST_METHOD'],
        parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
    );
    exit ;
} catch (\Exception $e) {
    http_response_code($e->getCode());
    header('Location: /not-found');
    exit;
}