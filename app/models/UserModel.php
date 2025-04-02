<?php

class UserModel {
    private $vDb;

    public function __construct($vDb) {
        $this->vDb = $vDb;
    }

    // Function that's likely causing the error around line 75
    public function getUserById($id) {
        try {
            $vSql = "SELECT * FROM usuarios WHERE id = :id";
            $vStmt = $this->vDb->prepare($vSql);
            $vStmt->bindParam(':id', $id);
            $vStmt->execute();
            
            return $vStmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar usuário: " . $e->getMessage());
            throw new Exception("Erro ao buscar usuário: " . $e->getMessage());
        }
    }

    // Check other functions for syntax errors
    public function getAllUsers() {
        try {
            $vSql = "SELECT * FROM usuarios ORDER BY nome";
            $vStmt = $this->vDb->prepare($vSql);
            $vStmt->execute();
            
            return $vStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao listar usuários: " . $e->getMessage());
            throw new Exception("Erro ao listar usuários: " . $e->getMessage());
        }
    }

    public function createUser($nome, $email, $senha, $cargo = 'usuario') {
        try {
            // Check if email already exists
            $vSql = "SELECT COUNT(*) as count FROM usuarios WHERE email = :email";
            $vStmt = $this->vDb->prepare($vSql);
            $vStmt->bindParam(':email', $email);
            $vStmt->execute();
            $result = $vStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                throw new Exception("Email já cadastrado");
            }
            
            // Hash the password
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            
            // Insert the new user
            $vSql = "INSERT INTO usuarios (nome, email, senha, cargo) VALUES (:nome, :email, :senha, :cargo)";
            $vStmt = $this->vDb->prepare($vSql);
            $vStmt->bindParam(':nome', $nome);
            $vStmt->bindParam(':email', $email);
            $vStmt->bindParam(':senha', $senha_hash);
            $vStmt->bindParam(':cargo', $cargo);
            $vStmt->execute();
            
            return $this->vDb->lastInsertId();
        } catch(PDOException $e) {
            error_log("Erro ao criar usuário: " . $e->getMessage());
            throw new Exception("Erro ao criar usuário: " . $e->getMessage());
        }
    }

    public function updateUser($id, $nome, $email, $cargo, $senha = null) {
        try {
            // Check if email already exists for another user
            $vSql = "SELECT COUNT(*) as count FROM usuarios WHERE email = :email AND id != :id";
            $vStmt = $this->vDb->prepare($vSql);
            $vStmt->bindParam(':email', $email);
            $vStmt->bindParam(':id', $id);
            $vStmt->execute();
            $result = $vStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                throw new Exception("Email já cadastrado para outro usuário");
            }
            
            // Update user information
            if ($senha) {
                // If password is provided, update it too
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $vSql = "UPDATE usuarios SET nome = :nome, email = :email, cargo = :cargo, senha = :senha WHERE id = :id";
                $vStmt = $this->vDb->prepare($vSql);
                $vStmt->bindParam(':senha', $senha_hash);
            } else {
                // Otherwise, update only other fields
                $vSql = "UPDATE usuarios SET nome = :nome, email = :email, cargo = :cargo WHERE id = :id";
                $vStmt = $this->vDb->prepare($vSql);
            }
            
            $vStmt->bindParam(':nome', $nome);
            $vStmt->bindParam(':email', $email);
            $vStmt->bindParam(':cargo', $cargo);
            $vStmt->bindParam(':id', $id);
            $vStmt->execute();
            
            return true;
        } catch(PDOException $e) {
            error_log("Erro ao atualizar usuário: " . $e->getMessage());
            throw new Exception("Erro ao atualizar usuário: " . $e->getMessage());
        }
    }

    public function deleteUser($id) {
        try {
            $vSql = "DELETE FROM usuarios WHERE id = :id";
            $vStmt = $this->vDb->prepare($vSql);
            $vStmt->bindParam(':id', $id);
            $vStmt->execute();
            
            return true;
        } catch(PDOException $e) {
            error_log("Erro ao excluir usuário: " . $e->getMessage());
            throw new Exception("Erro ao excluir usuário: " . $e->getMessage());
        }
    }

    public function authenticateUser($email, $senha) {
        try {
            $vSql = "SELECT * FROM usuarios WHERE email = :email AND ativo = 1";
            $vStmt = $this->vDb->prepare($vSql);
            $vStmt->bindParam(':email', $email);
            $vStmt->execute();
            
            $user = $vStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($senha, $user['senha'])) {
                return $user;
            }
            
            return false;
        } catch(PDOException $e) {
            error_log("Erro ao autenticar usuário: " . $e->getMessage());
            throw new Exception("Erro ao autenticar usuário: " . $e->getMessage());
        }
    }
}
?>