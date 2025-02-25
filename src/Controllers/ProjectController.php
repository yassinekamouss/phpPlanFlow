<?php
namespace App\Controllers;

use App\Models\Project;
use App\Repositories\ProjectRepositorie;
use App\Repositories\TaskRepositorie;
use App\Repositories\NotificationRepositorie;
use App\Controllers\TaskController;

class ProjectController {
    private $projectRepositorie;
    private $taskRepository;
    private $notificationRepositorie;
    private $taskController;

    public function __construct() {
        $this->projectRepositorie = new ProjectRepositorie();
        $this->taskRepository = new TaskRepositorie();
        $this->notificationRepositorie = new NotificationRepositorie();
        $this->taskController = new TaskController();
    }

    // Vérifier si l'utilisateur est un responsable
    private function checkResponsable() {
        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'responsable') {
            header('Location: /forbidden');
            exit();
        }
        return true;
    }

    public function create() {
        // Vérifier si l'utilisateur est un responsable
        $this->checkResponsable();
        try {

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception("La méthode HTTP n'est pas supportée");
            }
    
            // Récupérer le project_manager_id depuis la session
            $project_manager_id = $_SESSION['user']['id'];
            // Récupérer les données du formulaire
            $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : null;
            $description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : null;
            $status = isset($_POST['status']) && !empty($_POST['status']) ? htmlspecialchars($_POST['status']) : 'en cours';
            $start_date = isset($_POST['start_date']) && !empty($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : date('Y-m-d');
            $end_date = isset($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : null;
    
            // Vérifier que les données requises sont présentes
            if (!$name || !$description || !$end_date) {
                throw new \Exception("Les données du projet sont manquantes");
            }
    
            // Créer le projet avec l'ID du manager de la session
            $project = new Project(
                $name,
                $description,
                $status,
                $start_date,
                $end_date,
                $project_manager_id
            );
            
            $project_id = $this->projectRepositorie->create($project);
            if (!$project_id) {
                throw new \Exception("Échec de la création du projet");
            }
    
            // Le reste du code pour les tâches reste identique...
            $tasks = isset($_POST['tasks']) && !empty($_POST['tasks']) ? json_decode($_POST['tasks'], true) : [];
            if (is_array($tasks)) {
                foreach ($tasks as $taskData) {
                    if (!isset($taskData['name'], $taskData['description'], 
                              $taskData['start_date'], $taskData['end_date'], 
                              $taskData['assignee_id'])) {
                        continue;
                    }
    
                    $taskData['project_id'] = $project_id;
    
                    $this->taskController->create($taskData);
                }
            }

            // Notify admin
            $message = "Un nouveau projet '$name' vient d'etre créer par " . $_SESSION['user']['name'];
            $this->notificationRepositorie->notifyAdmin($message);
            http_response_code(201);
            echo json_encode(['success' => true, 'message' => 'Project created successfully']);
    
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update($id) {
        $this->checkResponsable();
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception("La méthode HTTP n'est pas supportée");
            }
            
            $project_manager_id = $_SESSION['user']['id'];
            $project = $this->projectRepositorie->findById($id);
            
            if(!$project) {
                throw new \Exception("Project not found");
            }
    
            // Mise à jour du projet
            $project->setName(isset($_POST['name']) ? htmlspecialchars($_POST['name']) : $project->getName());
            $project->setDescription(isset($_POST['description']) ? htmlspecialchars($_POST['description']) : $project->getDescription());
            $project->setStatus(isset($_POST['status']) ? htmlspecialchars($_POST['status']) : $project->getStatus());
            $project->setStartDate(isset($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : $project->getStartDate());
            $project->setEndDate(isset($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : $project->getEndDate());
            $project->setProjectManagerId(isset($_POST['project_manager_id']) ? htmlspecialchars($_POST['project_manager_id']) : $project->getProjectManagerId());
    
            // Gestion des tâches
            $tasks = isset($_POST['tasks']) && !empty($_POST['tasks']) ? json_decode($_POST['tasks'], true) : [];
            if (is_array($tasks)) {
                $existingTasks = $this->taskRepository->findByProjectId($id);
                $existingTasksMap = array_column($existingTasks, null, 'id');
    
                foreach ($tasks as $taskData) {
                    if (!isset($taskData['name'], $taskData['description'], 
                              $taskData['start_date'], $taskData['end_date'], 
                              $taskData['assignee_id'])) {
                        continue;
                    }
    
                    $taskData['project_id'] = $id;
    
                    if (isset($taskData['id']) && isset($existingTasksMap[$taskData['id']])) {
                        // Mise à jour d'une tâche existante
                        $this->taskController->update($taskData['id'], $taskData);
                        unset($existingTasksMap[$taskData['id']]);
                    } else {
                        // Création d'une nouvelle tâche
                        $this->taskController->create($taskData);
                    }
                }
    
                // Suppression des tâches qui n'existent plus
                foreach ($existingTasksMap as $taskId => $task) {
                    $this->taskRepository->delete($taskId);
                }
            }
            $this->projectRepositorie->update($project);
            // if (!$this->projectRepositorie->update($project)) {
                // throw new \Exception("Error updating project");
            // }
    
            $message = "Le projet " . $project->getName() . " a été mis à jour";
            $this->notificationRepositorie->notifyAdmin($message);
            $this->notificationRepositorie->notifyProjectMembers($id, $message);
    
            http_response_code(201);
            echo json_encode(['success' => true, 'message' => 'Project updated successfully']);
    
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function delete($id) {
        // Vérifier si l'utilisateur est un responsable
        $this->checkResponsable();
        try{

            $project = $this->projectRepositorie->findById($id);
            if(!$project){
                throw new \Exception("Project not found");
            }

            $result = $this->projectRepositorie->delete($id);
            if(!$result){
                throw new \Exception("Error deleting project");
            }

            // Notify project members
            $message = "Le projet" .$project->getName()." a été supprimé";
            $this->notificationRepositorie->notifyProjectMembers($id, $message);
            $this->notificationRepositorie->notifyAdmin($message);
            
            http_response_code(201);
            echo json_encode(['success' => true, 'message' => 'Project deleted successfully']);

        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function findAll() {
        return $this->projectRepositorie->findAll();
    }

    public function findById($id) {
        return $this->projectRepositorie->findById($id);
    }
}