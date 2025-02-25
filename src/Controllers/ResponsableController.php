<?php
namespace App\Controllers;

use DateTime;
use App\Repositories\UserRepositorie;
use App\Repositories\ProjectRepositorie;
use App\Repositories\NotificationRepositorie;
use App\Repositories\TaskRepositorie;
use App\Repositories\CommentRepositorie;

class ResponsableController extends UserController{
    private $userRepositorie;
    private $projectRepository;

    public function __construct()
    {
        $this->userRepositorie = new UserRepositorie;
        $this->projectRepository = new ProjectRepositorie;
    }

    private function checkResponsable() {
        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'responsable') {
            header('Location: /login');
            exit();
        }
        return true;
    }

    public function home(){
        // check if user is responsable
        $this->checkResponsable();

        try{
            // Charger le repository des projets
            $projects = $this->projectRepository->findByProjectManagerId($_SESSION['user']['id']);
            $taskRepository = new TaskRepositorie();

            // Réorganiser $projects pour ne garder que le nom et le progress
            $filteredProjects = [];
            $ProjectState = [
                'termine' => 0,
                'en_cours' => 0,
                'en_retard' => 0,
            ];
            $projectUrgent = [];
            foreach ($projects as $project) {
                // Récupérer toutes les tâches du projet
                $tasks = $taskRepository->findByProjectId($project['id']);
                
                // Calculer l'avancement du projet
                $totalTasks = count($tasks);
                $completedTasks = 0;
                foreach ($tasks as $task) {
                    if ($task['status'] == 'termine') {
                        $completedTasks++;
                    }
                }
            
                $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
            
                // Ajouter le projet avec seulement son nom et son avancement
                $filteredProjects[] = [
                    'id'  => $project['id'],
                    'name' => $project['name'],
                    'progress' => $progress,
                    'end_date' => $project['end_date']
                ];
                // Récupérer les statistique sur les projets 
                if ($progress == 100) {
                    $ProjectState['termine']++;
                } else {
                    $currentDate = new DateTime(); // Date actuelle
                    $endDate = new DateTime($project['end_date']); // Date de fin du projet
            
                    if ($endDate < $currentDate) {
                        $ProjectState['en_retard']++;
                    } else {
                        $ProjectState['en_cours']++;
                    }
                }
            }
            // Trier les projets par date d'échéance (du plus proche au plus éloigné)
            usort($filteredProjects, function($a, $b) {
                $currentDate = new DateTime();
                $endDateA = new DateTime($a['end_date']);
                $endDateB = new DateTime($b['end_date']);
                
                // Calculer la différence en jours
                $diffA = $endDateA->diff($currentDate)->days;
                $diffB = $endDateB->diff($currentDate)->days;
                
                return $diffA - $diffB; // Tri croissant (du plus proche au plus éloigné)
            });

            // Récupérer les trois projets les plus proches de la date d'échéance
            $projectUrgent = array_slice($filteredProjects, 0, 3);
            // Réaffecter la nouvelle structure à $projects si nécessaire
            $projects = $filteredProjects;
            // Récupérer les notifications de l'utilisateur
            $notificationRepository = new NotificationRepositorie();    
            $notifications = $notificationRepository->findByUserId($_SESSION['user']['id']);

        }catch(\Exception $e){
             
        }
    
        require_once __DIR__ . '/../Views/responsable/home.php';
    }

    public function project(){
        // check if user is responsable
        $this->checkResponsable();
    
        // Récupérer les données de l'utilisateur connecté
        $user = $_SESSION['user'];
        $userId = $user['id'];

        // Charger le repository des projets
        $projects = $this->projectRepository->findByProjectManagerId($userId);
    
        // Charger le repository des tâches et ajouter les tâches à chaque projet
        $taskRepository = new TaskRepositorie();
        $commentRepository = new CommentRepositorie();
        foreach ($projects as &$project) {
            // Récupérer toutes les tâches du projet
            $tasks = $taskRepository->findByProjectId($project['id']);
            
            // Calculer l'avancement du projet en fonction des tâches terminées
            $totalTasks = count($tasks);
            $completedTasks = 0;
            // Compter le nombre de tâches terminées
            foreach ($tasks as &$task) {
                if ($task['status'] == 'termine') {
                    $completedTasks++;
                }
                $task['comments'] = $commentRepository->findByTaskId($task['id']);
            }
    
            $project['progress'] = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
            $project['tasks'] = $tasks;

            // Récupérer les membres du projet
            $members = $this->userRepositorie->findMembersByProjectId($project['id']);
            $project['members'] = $members; // Ajouter les membres au projet
        }
        
        require_once __DIR__ . '/../Views/responsable/project.php';
    }

}
?>