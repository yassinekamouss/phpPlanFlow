<?php
namespace App\Repositories;

use App\Models\Task;
use Config\Database;
use PDO;

class TaskRepositorie {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function create(Task $task) {
        try {
            // Insérer les données dans la base de données
            $query = "INSERT INTO task (name, description, assignee_id, start_date, end_date, status, priority, project_id) 
                     VALUES (:name, :description, :assignee_id, :start_date, :end_date, :status, :priority, :project_id)";
            $stmt = $this->db->prepare($query);
            // Create a new task
            $stmt->execute([
                'name' => $task->getName(),
                'description' => $task->getDescription(),
                'assignee_id' => $task->getAssigneeId(),
                'start_date' => $task->getStartDate(),
                'end_date' => $task->getEndDate(),
                'status' => $task->getStatus(),
                'priority' => $task->getPriority(),
                'project_id' => $task->getProjectId()
            ]);

            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Exception("Error creating task: " . $e->getMessage());
        }
    }

    public function update(Task $task) {
        try {
            // Insérer les données dans la base de données
            $query = "UPDATE task SET name = :name, description = :description, assignee_id = :assignee_id, start_date = :start_date, end_date = :end_date, status = :status, priority = :priority, project_id = :project_id WHERE id = :id";
            $stmt = $this->db->prepare($query);

            // Create a new task
            $stmt->execute([
                'id' => $task->getId(),
                'name' => $task->getName(),
                'description' => $task->getDescription(),
                'assignee_id' => $task->getAssigneeId(),
                'start_date' => $task->getStartDate(),
                'end_date' => $task->getEndDate(),
                'status' => $task->getStatus(),
                'priority' => $task->getPriority(),
                'project_id' => $task->getProjectId()
            ]);

            // Récupérer le nombre de lignes affectées
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            throw new \Exception("Error updating task: " . $e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $query = "DELETE FROM task WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new \Exception("Error deleting task: " . $e->getMessage());
        }
    }

    public function deleteAllByProjectId($projectId) {
        try {
            $query = "DELETE FROM task WHERE project_id = :project_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':project_id', $projectId);
            $stmt->execute();

            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new \Exception("Error deleting tasks by project id: " . $e->getMessage());
        }
    }

    public function findById($id) {
        try {
            $query = "SELECT * FROM task WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if($result) {
                $task = new Task($result['name'], $result['description'], $result['assignee_id'], $result['start_date'], $result['end_date'], $result['status'], $result['priority'], $result['project_id']);
                $task->setId($result['id']);
                return $task;
            }
            return null;
        } catch (\PDOException $e) {
            throw new \Exception("Error finding task: " . $e->getMessage());
        }
    }

    public function findAll() {
        try {
            $query = "SELECT * FROM task";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if($results) {
                // Créer un tableau d'objets Task
                $tasks = [];
                foreach($results as $result) {
                    $task = new Task($result['name'], $result['description'], $result['assignee_id'], $result['start_date'], $result['end_date'], $result['status'], $result['priority'], $result['project_id']);
                    $task->setId($result['id']);
                    $tasks[] = $task;
                }
                return $tasks;
            }
            return [];
        } catch (\PDOException $e) {
            throw new \Exception("Error finding tasks: " . $e->getMessage());
        }
    }

    public function findByUserId($userId){
        try{
            $sql = "
                SELECT t.*, u.name as assignee 
                FROM task t
                JOIN user u ON t.assignee_id = u.id
                WHERE t.assignee_id = :userId
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            throw new \Exception("Error finding tasks by user id:".$e->getMessage());
        }
    }

    public function findByProjectId($projectId){
        try{
            $sql = "
                SELECT t.*, u.name as assignee 
                FROM task t
                JOIN user u ON t.assignee_id = u.id
                WHERE t.project_id = :projectId
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['projectId' => $projectId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            throw new \Exception("Error finding tasks by project id:".$e->getMessage());
        }
    }

    public function getTaskToDoByUserId($userId){
        try{
            $sql = "
                SELECT t.priority, t.id, t.name, p.name as project_name
                FROM task t
                INNER JOIN project p ON t.project_id = p.id
                WHERE t.assignee_id = :userId
                AND t.status = 'a faire'
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            throw new \Exception("Error finding tasks to do by user id:".$e->getMessage());
        }
    }

    public function tasksProjectRepport($id){
        try{
            $sql = "
                SELECT t.name, u.name as assignee, t.status, t.end_date
                FROM task t
                JOIN user u ON t.assignee_id = u.id
                WHERE t.project_id = :id
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            throw new \Exception("Error finding tasks project repport:".$e->getMessage());
        }
    }

    public function taskMembreRepport(){
        try{
            $sql = "
                select 
                    user.name,
                    count(task.id) as total_tasks,
                    sum(case when task.status = 'termine' then 1 else 0 end) as tasks_terminees,
                    sum(case when task.status = 'en cours' then 1 else 0 end) as tasks_en_cours
                from user, task
                where user.id = task.assignee_id
                and task.created_at >= DATE_FORMAT(now() , '%Y-%m-01')
                and task.created_at < DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 MONTH), '%Y-%m-01')
                group by user.name
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            throw new \Exception("Error finding tasks member repport:".$e->getMessage());
        }
    }

    public function count(){
        try{
            $sql = "SELECT COUNT(*) as total FROM task";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        }catch(\PDOException $e){
            throw new \Exception("Error counting tasks:".$e->getMessage());
        }
    }

    public function countByStatus($status){
        try{
            $sql = "SELECT COUNT(*) as total FROM task WHERE status = :status";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['status' => $status]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        }catch(\PDOException $e){
            throw new \Exception("Error counting tasks:".$e->getMessage());
        }
    }

}