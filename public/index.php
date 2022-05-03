<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204); exit;
}

require_once __DIR__ . "/../core/Router.php";
require_once __DIR__ . "/../core/Database.php";
require_once __DIR__ . "/../core/JWT.php";
require_once __DIR__ . "/../core/Response.php";
require_once __DIR__ . "/../app/Controllers/AuthController.php";
require_once __DIR__ . "/../app/Controllers/UserController.php";
require_once __DIR__ . "/../app/Controllers/ProductController.php";

$router = new Router();
$router->post("/api/auth/login", "AuthController@login");
$router->post("/api/auth/register", "AuthController@register");
$router->get("/api/users", "UserController@index", true);
$router->get("/api/users/{id}", "UserController@show", true);
$router->get("/api/products", "ProductController@index", true);
$router->get("/api/products/{id}", "ProductController@show", true);
$router->post("/api/products", "ProductController@create", true);
$router->put("/api/products/{id}", "ProductController@update", true);
$router->delete("/api/products/{id}", "ProductController@delete", true);
$router->dispatch();
