<?php
namespace App\Controllers;
session_start();

use DateTime;
use App\Models\Task;
use App\Repositories\NotificationRepositorie;
use App\Repositories\TaskRepositorie;

class TaskController{
    private $taskRepositorie;
    private $notificationRepositorie;

    public function __construct() {
        $this->taskRepositorie = new TaskRepositorie();
        $this->notificationRepositorie = new NotificationRepositorie();
    }

    public function create($taskData) {
        try{
            $name = $taskData['name'];
            $description = $taskData['description'];
            $assignee_id = (int)$taskData['assignee_id'];
            $start_date = $taskData['start_date'];
            $end_date = $taskData['end_date'];
            $status = 'a faire';
            $priority = $taskData['priority'];
            $project_id = (int)$taskData['project_id'];
            
            // Créer une nouvvel modèle de tâche
            $task = new Task($name, $description, $assignee_id, $start_date, $end_date, $status, $priority, $project_id);

            $result = $this->taskRepositorie->create($task);
            if(!$result){
                throw new \Exception("Error creating task");
            }
            $message = "A créer la tâche " . $name . " Pour Vous"; 
            // Notifier les utilisateur de la création de la tache
            $this->notificationRepositorie->notifyUserById($assignee_id,$message);

            return $result;
        } catch (\Exception $e) {
            throw new \Exception("Error creating task: " . $e->getMessage());
        }
    }

    public function update($id , $taskData=[]) {
        if(!$taskData){
            $taskData = $_POST;
        }
        try {
            // Test si la tâche existe
            $task = $this->taskRepositorie->findById($id);
            if (!$task) {
                throw new \Exception("Task not found");
            }
            
            // Récupérer les données existantes et les valeurs mises à jour
            $name = isset($taskData['name']) ? htmlspecialchars($taskData['name']) : $task->getName();
            $description = isset($taskData['description']) ? htmlspecialchars($taskData['description']) : $task->getDescription();
            $assignee_id = isset($taskData['assignee_id']) ? htmlspecialchars($taskData['assignee_id']) : $task->getAssigneeId();
            $start_date = isset($taskData['start_date']) ? htmlspecialchars($taskData['start_date']) : $task->getStartDate();
            $end_date = isset($taskData['end_date']) ? htmlspecialchars($taskData['end_date']) : $task->getEndDate();
            $status = isset($taskData['status']) ? htmlspecialchars($taskData['status']) : $task->getStatus();
            $priority = isset($taskData['priority']) ? htmlspecialchars($taskData['priority']) : $task->getPriority();
            $project_id = isset($taskData['project_id']) ? htmlspecialchars($taskData['project_id']) : $task->getProjectId();
            
            // Mettre à jour les données de l'objet tâche
            $task->setName($name);
            $task->setDescription($description);
            $task->setAssigneeId($assignee_id);
            $task->setStartDate($start_date);
            $task->setEndDate($end_date);
            $task->setStatus($status);
            $task->setPriority($priority);
            $task->setProjectId($project_id);
            
            $result = $this->taskRepositorie->update($task);
            
            if($result){
                // Récupérer le projet associé à la tâche
                $project_id = $task->getProjectId();
                $message = "A changer le status de la tâche " . $task->getName();
            
                // Notifier les utilisateur de la création du commentaire
                $this->notificationRepositorie->notifyProjectMembers($project_id,$message);
            }
            
            return true;
        } catch (\Exception $e) {
            throw new \Exception("Error updating task: " . $e->getMessage());
        }
    }

    public function delete($id) {
        return $this->taskRepositorie->delete($id);
    }

    public function findAll() {
        return $this->taskRepositorie->findAll();
    }

    public function findById($id) {
        return $this->taskRepositorie->findById($id);
    }
}