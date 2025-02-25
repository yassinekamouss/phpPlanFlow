<?php
namespace App\Controllers;

use DateTime;
use App\Models\Comment;
use App\Repositories\CommentRepositorie;
use App\Repositories\NotificationRepositorie;
use App\Repositories\TaskRepositorie;
use App\Repositories\ProjectRepositorie;

class CommentController{
    private $commentRepositorie;
    private $taskRepositorie;
    private $projectRepositorie;
    private $notificationRepositorie;

    public function __construct() {
        $this->commentRepositorie = new CommentRepositorie();
        $this->taskRepositorie = new TaskRepositorie();
        $this->projectRepositorie = new ProjectRepositorie();
        $this->notificationRepositorie = new NotificationRepositorie();
    }

    public function create() {
        try{
            // Récupérer les données du formulaire
            $task_id = htmlspecialchars($_POST['task_id']);
            $author_id = htmlspecialchars($_POST['author_id']);
            $text = htmlspecialchars($_POST['text']);

            // Créer un objet Comment
            $comment = new Comment($task_id, $author_id, $text);
            $result = $this->commentRepositorie->create($comment);
            if(!$result){
                throw new \Exception("Error creating comment");
            }
            // Récupérer le projet associé à la tâche
            $task = $this->taskRepositorie->findById($task_id);
            $project_id = $task->getProjectId();
            $message = "A commenter sur la tâche " . $task->getName();
            // Notifier les utilisateur de la création du commentaire
            $this->notificationRepositorie->notifyProjectMembers($project_id,$message);
            
            return $result;
        }catch(\PDOException $e){
            throw new \Exception("Error creating comment: " . $e->getMessage());
        }
    }

    // public function update($id) {
    //    try{
    //         // Test si le commentaire existe
    //         $comment = $this->commentRepositorie->findById($id);
    //         if(!$comment){
    //             throw new \Exception("Comment not found");
    //         }
    //         // Récupérer les données existantes et les valeurs mises à jour
    //         $task_id = isset($_POST['task_id']) ? htmlspecialchars($_POST['task_id']) : $comment->getTaskId();
    //         $author_id = isset($_POST['author_id']) ? htmlspecialchars($_POST['author_id']) : $comment->getAuthorId();
    //         $text = isset($_POST['text']) ? htmlspecialchars($_POST['text']) : $comment->getText();

    //         // Mettre à jour les données de l'objet Comment
    //         $comment->setTaskId($task_id);
    //         $comment->setAuthorId($author_id);
    //         $comment->setText($text);

    //         return $this->commentRepositorie->update($comment);
    //    }catch(\PDOException $e){
    //        throw new \Exception("Error updating comment: " . $e->getMessage());
    //    }
    // }

    public function delete($id) {
        return $this->commentRepositorie->delete($id);
    }

    public function findAll() {
        return $this->commentRepositorie->findAll();
    }

    public function findById($id) {
        return $this->commentRepositorie->findById($id);
    }
}