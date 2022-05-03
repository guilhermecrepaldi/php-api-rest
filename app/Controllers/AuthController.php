<?php
class AuthController {
    public function login(array $params): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$params["body"]["email"] ?? ""]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($params["body"]["password"] ?? "", $user["password"])) {
            $token = JWT::encode(["id" => $user["id"], "email" => $user["email"]]);
            Response::json(["token" => $token, "user" => ["id" => $user["id"], "name" => $user["name"], "email" => $user["email"]]]);
        }
        Response::json(["erro" => "Credenciais invalidas"], 401);
    }
    
    public function register(array $params): void {
        $data = $params["body"];
        $db = Database::getInstance();
        $hash = password_hash($data["password"], PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$data["name"], $data["email"], $hash]);
        $id = $db->lastInsertId();
        $token = JWT::encode(["id" => $id, "email" => $data["email"]]);
        Response::json(["token" => $token, "message" => "Usuario criado"], 201);
    }
}
