<?php
class UserController {
    public function index(array $params): void {
        $db = Database::getInstance();
        $users = $db->query("SELECT id, name, email, created_at FROM users")->fetchAll();
        Response::json(["users" => $users]);
    }
    
    public function show(array $params): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, name, email, created_at FROM users WHERE id = ?");
        $stmt->execute([$params["id"]]);
        $user = $stmt->fetch();
        $user ? Response::json(["user" => $user]) : Response::json(["erro" => "Nao encontrado"], 404);
    }
}
