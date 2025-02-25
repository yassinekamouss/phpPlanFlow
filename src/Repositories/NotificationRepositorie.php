<?php
namespace App\Repositories;
session_start();

use Config\Database;
use PDO;

class NotificationRepositorie{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function create($userId, $message ,$triggered_by) {
        try{
            // Insérer les données dans la bdd
            $sql = "INSERT INTO notification (user_id, message, triggered_by, read_status) 
                    VALUES (?, ?, ?, 0)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $message, $triggered_by]);

        }catch(\PDOException $e){
            throw new \Exception("Error creating notification: " . $e->getMessage());
        }
    }

    public function delete($notificationId){
        try {
            $sql = "DELETE FROM notification WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$notificationId]);
        }catch(\PDOException $e){
            throw new \Exception("Error deleting notification: " . $e->getMessage());
        }
    }

    public function notifyProjectMembers($projectId, $message) {
        try{
            // Récupérer les membres du projet
            $userRepositorie = new UserRepositorie();
            $members = $userRepositorie->findMembersByProjectId($projectId); 
            if(empty($members)){
                return;
            }
            // Notifier chaque membre
            $userId = $_SESSION['user']['id'];
            foreach ($members as $member) {
                $this->create($member['user_id'], $message, $userId);
            }
        }catch(\PDOException $e){
                throw new \Exception("Error creating notification: " . $e->getMessage());
        }
    }

    public function notifyUserById($userId, $message) {
        try{
            $triggered_by = $_SESSION['user']['id'];
            $this->create($userId, $message, $triggered_by);
        }catch(\PDOException $e){
            throw new \Exception("Error creating notification: " . $e->getMessage());
        }
    }

    public function getUnreadNotifications($userId) {
        try{
            $sql = "SELECT * FROM notification 
                WHERE user_id = ? AND read_status = 0 
                ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            throw new \Exception("Error getting notifications: " . $e->getMessage());
        }
    }

    public function markAsRead($notificationId) {
        $sql = "UPDATE notification SET read_status = 1 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$notificationId]);
    }

    public function findById($notificationId){
        try {
            $sql = "SELECT * FROM notification WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$notificationId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            throw new \Exception("Error getting notification: " . $e->getMessage());
        }
    }

    public function findByUserId($userId){
        try {
            $sql = "SELECT nt.id, nt.message, u.name as user_name,u.avatar as user_avatar, nt.created_at
                    FROM notification nt
                    INNER JOIN user u ON nt.triggered_by = u.id
                    WHERE user_id = ?
                    ORDER BY nt.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            throw new \Exception("Error getting notification: " . $e->getMessage());
        }
    }

    public function notifyAdmin($message) {
        try {
            // admin ID is 101
            $this->create(101, $message, $_SESSION['user']['id']);
        } catch (\PDOException $e) {
            throw new \Exception("Error creating notification: " . $e->getMessage());
        }
    }
}