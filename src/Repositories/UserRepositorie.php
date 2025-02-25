<?php
namespace App\Repositories;
session_start();

use Config\Database;
use App\Models\User;
use PDO;

class UserRepositorie {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function create(User $user) {
        try{
            // Insérer les données dans la base de données
            $query = "INSERT INTO user (name, email, password_hash, role, avatar) 
                     VALUES (:name, :email, :password, :role, :avatar)";
            $stmt = $this->db->prepare($query);
            
            $stmt->execute([
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'role' => $user->getRole(),
                'avatar' => $user->getAvatar()
            ]);

            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Exception("Error creating user: " . $e->getMessage());
        }
    }

    public function update(User $user) {
        try {
            // Insérer les données dans la base de données
            $query = "UPDATE user 
                     SET name = :name, 
                         email = :email, 
                         password_hash = :password, 
                         role = :role, 
                         avatar = :avatar 
                     WHERE id = :id";
                     
            $stmt = $this->db->prepare($query);
            
            $stmt->execute([
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'role' => $user->getRole(),
                'avatar' => $user->getAvatar(),
                'id' => $user->getId()
            ]);

            // Récupérer le nombre de lignes affectées
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new \Exception("Error updating user: " . $e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $query = "DELETE FROM user WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new \Exception("Error deleting user: " . $e->getMessage());
        }
    }

    public function findById($id) {
        try {
            $query = "SELECT * FROM user WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $user = new User($result['name'], $result['email'], $result['password_hash'], $result['role'], $result['avatar'], $result['id']);
                $user->setId($result['id']);
                return $user;
            }
            return null;
        } catch (\PDOException $e) {
            throw new \Exception("Error finding user: " . $e->getMessage());
        }
    }

    public function findByEmail($email) {
        try {
            $query = "SELECT * FROM user WHERE email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $user = new User($result['name'], $result['email'], $result['password_hash'], $result['role'], $result['avatar'], $result['id']);
                $user->setId($result['id']);
                return $user;
            }
            return null;
        } catch (\PDOException $e) {
            throw new \Exception("Error finding user: " . $e->getMessage());
        }
    }

    public function findAll() {
        try {
            $query = "SELECT * FROM user";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($results) {
                $users = [];
                foreach ($results as $result) {
                    $user = new User($result['name'], $result['email'], $result['password_hash'], $result['role'], $result['avatar'], $result['id']);
                    $user->setId($result['id']);
                    $users[] = $user;
                }
                return $users;
            }
            return [];
        } catch (\PDOException $e) {
            throw new \Exception("Error finding users: " . $e->getMessage());
        }
    }

    public function findMembersByProjectId($projectId){
        try {
            // SQL pour récupérer tous les membres d'un projet
            $sql = "
                SELECT 
                    u.name, 
                    u.avatar, 
                    u.id as user_id,
                    CASE 
                        WHEN p.project_manager_id = u.id THEN 'Manager' 
                        ELSE 'Member' 
                    END as role
                FROM 
                    user u
                LEFT JOIN task t ON u.id = t.assignee_id
                LEFT JOIN project p ON p.project_manager_id = u.id OR t.project_id = p.id
                WHERE 
                    p.id = :project_id
                GROUP BY 
                    u.id
            ";
    
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['project_id' => $projectId]);
    
            // Récupérer tous les membres
            $members = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $members;
        } catch (\PDOException $e) {
            throw new \Exception("Error finding members for project ID {$projectId}: " . $e->getMessage());
        }
    }
}