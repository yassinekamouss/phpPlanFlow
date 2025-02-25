<?php
namespace App\Repositories;

use App\Models\Comment;
use Config\Database;
use PDO;

class CommentRepositorie{
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function create(Comment $comment) {
       try{
            // Insérer les données dans la base de données
            $query = "INSERT INTO comment (task_id, author_id, text) VALUES (:task_id, :author_id, :text)";
            $stmt = $this->db->prepare($query);

            $stmt->execute([
                'task_id' => $comment->getTaskId(),
                'author_id' => $comment->getAuthorId(),
                'text' => $comment->getText()
            ]);

            return $this->db->lastInsertId();
       }catch(\PDOException $e){
           throw new \Exception("Error creating comment: " . $e->getMessage());
       }
    }

    public function update(Comment $comment) {
        try {
            // Mettre à jour les données dans la base de données
            $query = "UPDATE comment SET task_id = :task_id, author_id = :author_id, text = :text WHERE id = :id";
            $stmt = $this->db->prepare($query);
            
            $stmt->execute([
                'task_id' => $comment->getTaskId(),
                'author_id' => $comment->getAuthorId(),
                'text' => $comment->getText(),
                'id' => $comment->getId()
            ]);

            // Récupérer le nombre de lignes affectées
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new \Exception("Error updating comment: " . $e->getMessage());
        }
    }

    public function delete(int $id) {
        try {
            $query = "DELETE FROM comment WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new \Exception("Error deleting comment: " . $e->getMessage());
        }
    }

    public function findAll() {
        try {
            $query = "SELECT * FROM comment";
            $stmt = $this->db->query($query);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($results) {
                // Créer un tableau d'objets Comment
                $comments = [];
                foreach ($results as $result) {
                    $comment = new Comment($result['task_id'], $result['author_id'], $result['text']);
                    $comment->setId($result['id']);
                    $comments[] = $comment;
                }
                return $comments;
            }
            return [];
        } catch (\PDOException $e) {
            throw new \Exception("Error finding comments: " . $e->getMessage());
        }
    }

    public function findById(int $id) {
        try {
            $query = "SELECT * FROM comment WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $comment = new Comment($result['task_id'], $result['author_id'], $result['text']);
                $comment->setId($result['id']);
                return $comment;
            }
            return null ;
        } catch (\PDOException $e) {
            throw new \Exception("Error finding comment: " . $e->getMessage());
        }
    }

    public function findByTaskId($taskId){
        try{
            $sql = "
                SELECT 
                    comment.*, 
                    user.name AS user
                FROM 
                    comment
                INNER JOIN 
                    user ON comment.author_id = user.id
                WHERE 
                    comment.task_id = :taskId
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['taskId' => $taskId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            throw new \Exception("Error finding comments by task id: " . $e->getMessage());
        }
    }
    
}