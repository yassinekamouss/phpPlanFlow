<?php
namespace App\Controllers;


use DateTime;
use App\Repositories\UserRepositorie;
use App\Repositories\ProjectRepositorie;
use App\Repositories\TaskRepositorie;
use App\Repositories\CommentRepositorie;
use App\Repositories\NotificationRepositorie;
use App\Repositories\ContactRepositorie;

class AdminController extends UserController{
    private $userRepositorie;
    private $projectRepository;
    private $taskRepositorie;
    private $notificationRepositorie;
    private $contactRepositorie;

    public function __construct()
    {
        $this->userRepositorie = new UserRepositorie;
        $this->projectRepository = new ProjectRepositorie;
        $this->taskRepositorie = new TaskRepositorie;
        $this->notificationRepositorie = new NotificationRepositorie;
        $this->contactRepositorie = new ContactRepositorie;
    }

    // Tester si l'utilisateur est un administrateur
    private function checkAdmin(){
        session_start();
        if (!isset($_SESSION['user'])) {
            header('Location: /');
            exit();
        }
        //Test si l'utilisateur est un administrateur
        if ($_SESSION['user']['role'] != 'admin') {
            header('Location: /forbidden');
            exit();
        }
    }

    public function home(){
        //Test si l'utilisateur est un administrateur
        $this->checkAdmin();

        // Récupérer le nombre d'utilisateurs
        $users = $this->userRepositorie->findAll();
        $usersCount = count($users);

        // Récupérer le nombre de projets
        $projects = $this->projectRepository->findAll();
        $projectsCount = count($projects);
        // Récupérer le nombre de projets terminés
        $completedProjects = 0;
        foreach ($projects as $project) {
            if ($project['status'] == 'termine') {
                $completedProjects++;
            }
        }

        // Récupérer le nombre de tâches
        $tasks = $this->taskRepositorie->findAll();
        $tasksCount = count($tasks);

        // Calculer l'avencement des projets
        $projectsProgress = [];
        foreach ($projects as $project) {
            $projectTasks = $this->taskRepositorie->findByProjectId($project['id']);
            $completedTasks = 0;
            if($projectTasks == null){
                $projectsProgress[$project['name']] = 0;
                continue;
            }
            foreach ($projectTasks as $task) {
                if ($task['status'] == 'termine') {
                    $completedTasks++;
                }
            }
            $progress = ($completedTasks / count($projectTasks)) * 100;
            $projectsProgress[$project['name']] = $progress;
        }
    
        // Récupérer les notifications pour l'administrateur
        $activitesRecentes = $this->notificationRepositorie->findByUserId($_SESSION['user']['id']);


        require_once __DIR__ . '/../Views/admin/home.php';
    }

    public function project(){
        //Test si l'utilisateur est un administrateur
        $this->checkAdmin();
        // Charger le repository des projets
        $projectRepository = new ProjectRepositorie();
        $projects = $projectRepository->findAll();
    
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

        require_once __DIR__ . '/../Views/admin/project.php';
    }

    public function calander(){
        //Test si l'utilisateur est un administrateur
        $this->checkAdmin();

        require_once __DIR__ . '/../Views/admin/calander.php';
    }

    private function getMemberProjects($userId) {
        $projects = $this->projectRepository->findByUserId($userId);
        if ($projects === null) {
            return [];
        }
        return array_map(function ($project) {
            return [
                'id' => $project['id'], 
                'name' => $project['name'],
            ];
        }, $projects);
    }

    private function transformUserToMember($user) {
        $projects = $this->getMemberProjects($user->getId());
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'role' => $user->getRole(),
            'avatar' => $user->getAvatar(),
            'status' => 'actif',
            'projects' => $projects
        ];
    }

    public function membres() {
        //Test si l'utilisateur est un administrateur
        $this->checkAdmin();
    
        $users = $this->userRepositorie->findAll();
        $members = array_map([$this, 'transformUserToMember'], $users);
    
        require_once __DIR__ . '/../Views/admin/membres.php';
    }

    public function rapport(){
        // Test si l'utilisateur est un administrateur
        $this->checkAdmin();
        // Récupérer le nombre total de projets et de tâches
        $projectsCount = $this->projectRepository->count();
        $tasksCount = $this->taskRepositorie->count();
        $taskTermineCount = $this->taskRepositorie->countByStatus('termine');
        $taskEnCoursCount = $this->taskRepositorie->countByStatus('en cours');

        // Récupérer les projets du mois actuel
        $projects = $this->projectRepository->projectsRepport(date('Y-m-d'));
        foreach($projects as &$project){
            $project['tasks'] = $this->taskRepositorie->tasksProjectRepport($project['id']);
        }

        // Récupérer les tâches du membre du mois actuel
        $membersTasks = $this->taskRepositorie->taskMembreRepport();


        require_once __DIR__ . '/../Views/admin/rapport.php';
    }

    public function support(){
        //Test si l'utilisateur est un administrateur
        $this->checkAdmin();

        // Récupérer les messages de contact
        $tickets = $this->contactRepositorie->findAll();
        $newTicketsCount = count(array_filter($tickets, function($ticket) {
            return $ticket['status'] === 'nouveau';
        }));

        require_once __DIR__ . '/../Views/admin/support.php';
    }

}