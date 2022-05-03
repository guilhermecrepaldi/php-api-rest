<?php
class Router {
    private array $routes = [];
    
    public function get(string $path, string $handler, bool $auth = false): void {
        $this->routes[] = ["GET", $path, $handler, $auth];
    }
    public function post(string $path, string $handler, bool $auth = false): void {
        $this->routes[] = ["POST", $path, $handler, $auth];
    }
    public function put(string $path, string $handler, bool $auth = false): void {
        $this->routes[] = ["PUT", $path, $handler, $auth];
    }
    public function delete(string $path, string $handler, bool $auth = false): void {
        $this->routes[] = ["DELETE", $path, $handler, $auth];
    }
    
    public function dispatch(): void {
        $method = $_SERVER["REQUEST_METHOD"];
        $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $body = json_decode(file_get_contents("php://input"), true) ?? [];
        
        $authHeader = $_SERVER["HTTP_AUTHORIZATION"] ?? "";
        $token = str_replace("Bearer ", "", $authHeader);
        $user = null;
        
        if ($token) {
            try { $user = JWT::decode($token); } catch (Exception $e) {}
        }
        
        foreach ($this->routes as [$routeMethod, $path, $handler, $auth]) {
            if ($routeMethod !== $method) continue;
            
            $pattern = preg_replace("/\{(\w+)\}/", "(?P<$1>[^/]+)", $path);
            $pattern = "#^" . $pattern . "$#";
            
            if (preg_match($pattern, $uri, $matches)) {
                if ($auth && !$user) {
                    Response::json(["erro" => "Nao autorizado"], 401);
                    return;
                }
                [$controller, $action] = explode("@", $handler);
                $ctrl = new $controller();
                $params = array_filter($matches, "is_string", ARRAY_FILTER_USE_KEY);
                $params["body"] = $body;
                $params["user"] = $user;
                $ctrl->$action(...$params);
                return;
            }
        }
        Response::json(["erro" => "Rota nao encontrada"], 404);
    }
}
