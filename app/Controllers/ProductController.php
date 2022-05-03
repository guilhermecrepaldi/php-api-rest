<?php
class ProductController {
    private function db(): PDO { return Database::getInstance(); }
    
    public function index(array $params): void {
        $products = $this->db()->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
        Response::json(["products" => $products]);
    }
    
    public function show(array $params): void {
        $stmt = $this->db()->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$params["id"]]);
        $product = $stmt->fetch();
        $product ? Response::json(["product" => $product]) : Response::json(["erro" => "Nao encontrado"], 404);
    }
    
    public function create(array $params): void {
        $data = $params["body"];
        $stmt = $this->db()->prepare("INSERT INTO products (name, price, description) VALUES (?, ?, ?)");
        $stmt->execute([$data["name"], $data["price"], $data["description"] ?? ""]);
        Response::json(["id" => $this->db()->lastInsertId(), "message" => "Produto criado"], 201);
    }
    
    public function update(array $params): void {
        $data = $params["body"];
        $stmt = $this->db()->prepare("UPDATE products SET name=?, price=?, description=? WHERE id=?");
        $stmt->execute([$data["name"], $data["price"], $data["description"] ?? "", $params["id"]]);
        Response::json(["message" => "Produto atualizado"]);
    }
    
    public function delete(array $params): void {
        $this->db()->prepare("DELETE FROM products WHERE id=?")->execute([$params["id"]]);
        Response::json(["message" => "Produto deletado"]);
    }
}
