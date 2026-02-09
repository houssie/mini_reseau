<?php
class UserModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare('SELECT id, firstName, lastName, email FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare('SELECT id, firstName, lastName, email FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listAll() {
        $stmt = $this->pdo->query('SELECT id, firstName, lastName, CONCAT(firstName, " ", lastName) AS name, email FROM users ORDER BY firstName, lastName');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
