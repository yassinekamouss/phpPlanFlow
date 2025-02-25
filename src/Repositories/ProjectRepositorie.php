<?php
namespace App\Repositories;

use App\Models\Project;
use Config\Database;
use PDO;

class ProjectRepositorie {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function create(Project $project) {
        try {
            // Insérer les données dans la base de données
            $query = "INSERT INTO project (name, description, status, start_date, end_date, project_manager_id ) 
                     VALUES (:name, :description, :status, :start_date, :end_date, :project_manager_id)";
            $stmt = $this->db->prepare($query);
            
            // Create a new project
            $stmt->execute([
                'name' => $project->getName(),
                'description' => $project->getDescription(),
                'status' => $project->getStatus(),
                'start_date' => $project->getStartDate(),
                'end_date' => $project->getEndDate(),
                'project_manager_id' => $project->getProjectManagerId(),
            ]);

            // Récupérer l'id de l'élément inséré
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Exception("Error creating project: " . $e->getMessage());
        }
    }

    public function update(Project $project) {
        try {
            // Insérer les données dans la base de données
            $query = "UPDATE project SET name = :name, description = :description, status = :status, start_date = :start_date, end_date = :end_date, project_manager_id = :project_manager_id WHERE id = :id";
            $stmt = $this->db->prepare($query);
            
            // Update a project
            $stmt->execute([
                'name' => $project->getName(),
                'description' => $project->getDescription(),
                'status' => $project->getStatus(),
                'start_date' => $project->getStartDate(),
                'end_date' => $project->getEndDate(),
                'project_manager_id' => $project->getProjectManagerId(),
                'id' => $project->getId()
            ]);

            // Récupérer le nombre de lignes affectées
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new \Exception("Error updating project: " . $e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $query = "DELETE FROM project WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Récupérer le nombre de lignes affectées
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new \Exception("Error deleting project: " . $e->getMessage());
        }
    }

    public function findAll() {
        try {
            $query = "
                    SELECT 
                        p.*, 
                        pm.name AS project_manager_name, 
                        pm.avatar AS project_manager_avatar 
                    FROM project p
                    INNER JOIN user pm ON p.project_manager_id = pm.id";
            $stmt = $this->db->query($query);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if($results){
                return $results;
            }
            return [];
        } catch (\PDOException $e) {
            throw new \Exception("Error finding all projects: " . $e->getMessage());
        }
    }

    public function findById($id) {
        try {
            $query = "SELECT * FROM project WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if($result) {
                $project = new Project($result['name'], $result['description'], $result['status'], $result['start_date'], $result['end_date'], $result['project_manager_id']);
                $project->setId($result['id']);
                return $project;
            }
            return null;
        } catch (\PDOException $e) {
            throw new \Exception("Error finding project by id: " . $e->getMessage());
        }
    }

    public function findByUserId($userId) {
        try {
            // SQL pour récupérer tous les projets dont l'utilisateur a une tâche, avec les informations du project manager
            $sql = "
                SELECT DISTINCT
                    p.*, 
                    pm.name AS project_manager_name, 
                    pm.avatar AS project_manager_avatar
                FROM project p
                INNER JOIN task t ON p.id = t.project_id
                INNER JOIN user pm ON p.project_manager_id = pm.id
                WHERE t.assignee_id = :user_id
            ";
    
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
    
            // Récupérer tous les projets avec les informations du project manager
            $projects = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $projects;
        } catch (\PDOException $e) {
            throw new \Exception("Error finding projects for user ID {$userId}: " . $e->getMessage());
        }
    }    

    public function findByProjectManagerId($project_manager_id){
        try{
            // Récupérer tous les project dont le manager est celui avec $project_manager_id
            $sql = "
                SELECT 
                    p.*,
                    pm.name as project_manager_name,
                    pm.avatar as project_manager_avatar
                FROM project p
                INNER JOIN user pm ON p.project_manager_id = pm.id
                WHERE project_manager_id = :project_manager_id
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['project_manager_id' => $project_manager_id]);

            // Récupérer tous les projets avec les informations du project manager
            $projects = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $projects;
        }catch(\PDOException $e){
            throw new \Exception("Error finnding projects for manager ID {$project_manager_id}: " . $e->getMessage());
        }
    }

    public function getProjectEnCoursByUserId($userId) {
        try {
            // SQL pour récupérer tous les projets en cours dont l'utilisateur a une tâche, avec les informations du project manager
            $sql = "
                SELECT 
                    p.id,
                    p.name
                FROM project p
                INNER JOIN task t ON p.id = t.project_id
                WHERE t.assignee_id = :user_id AND p.status = 'en cours'
            ";
    
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
    
            // Récupérer tous les projets avec les informations du project manager
            $projects = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $projects;
        } catch (\PDOException $e) {
            throw new \Exception("Error finding projects for user ID {$userId}: " . $e->getMessage());
        }
    }

    public function count(){
        try{
            $sql = "SELECT COUNT(*) as total FROM project";
            $stmt = $this->db->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        }catch(\PDOException $e){
            throw new \Exception("Error counting projects:".$e->getMessage());
        }
    }
    
    public function projectsRepport($date) {
        // Préparer la requête SQL pour filtrer par la date donnée
        $sql = "
            SELECT project.id, project.name
            FROM project
            WHERE MONTH(project.end_date) = MONTH(:date) 
            AND YEAR(project.end_date) = YEAR(:date);
        ";
    
        // Préparer et exécuter la requête
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR); // Lier le paramètre $date à la requête
        $stmt->execute();
        
        // Récupérer les résultats
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $results;
    }
}