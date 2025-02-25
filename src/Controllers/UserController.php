<?php
namespace App\Controllers;

use App\Models\User;
use App\Repositories\UserRepositorie;
use App\Repositories\TaskRepositorie;
use App\Repositories\CommentRepositorie;
use App\Repositories\ProjectRepositorie;
use App\Repositories\NotificationRepositorie;

class UserController {
    private $userRepositorie;

    public function __construct() {
        $this->userRepositorie = new UserRepositorie();
    }

    // Vérifier si l'utilisateur est un administrateur
    private function checkAdmin() {
        session_start();
        if (!isset($_SESSION['user'])) {
            header('Location: /');
            exit();
        }
        if ($_SESSION['user']['role'] !== 'admin') {
            header('Location: /forbidden'); 
            exit();
        }
    }

    public function create() {
        //Test si l'utilisateur est un administrateur
        $this->checkAdmin();
        try {
            // Récupérer les données du formulaire
            $name = htmlspecialchars($_POST['name']);
            $email = htmlspecialchars($_POST['email']);
            $password = password_hash(htmlspecialchars($_POST['password']), PASSWORD_BCRYPT);
            $role = htmlspecialchars($_POST['role']);
            $avatar = htmlspecialchars($_POST['avatar']);
            
            // Créer un nouvel modèle utilisateur
            $user = new User($name , $email, $password, $role, $avatar);
            
            return $this->userRepositorie->create($user);
        } catch (\Exception $e) {
            throw new \Exception("Error creating user: " . $e->getMessage());
        }
    }

    public function update($id) {
        //Test si l'utilisateur est un administrateur ou l'utilisateur concerné
        session_start();
        if ($_SESSION['user']['role'] != 'admin' && $_SESSION['user']['id'] != $id) {
            header('Location: /forbidden');
            exit();
        }
        try {
            // Test si l'utilisateur existe
            $user = $this->userRepositorie->findById($id);
            if (!$user) {
                throw new \Exception("User not found");
            }

            // Récupérer les données existantes et les valeurs mises à jour
            $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : $user->getName();
            $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : $user->getEmail();
            $password = isset($_POST['password']) && !empty($_POST['password']) ? password_hash(htmlspecialchars($_POST['password']), PASSWORD_BCRYPT) : $user->getPassword();
            $role = isset($_POST['role']) ? htmlspecialchars($_POST['role']) : $user->getRole();
            $avatar = isset($_POST['avatar']) ? htmlspecialchars($_POST['avatar']) : $user->getAvatar();

            // Mettre à jour les données de l'objet utilisateur
            $user->setName($name);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setRole($role);
            $user->setAvatar($avatar);
            $this->userRepositorie->update($user);
            
            header('Content-Type: application/json');
            // Créer des données pour la réponse
            $response = [
                'status' => 'success',
                'message' => 'Le profil a été mis à jour avec succès'
            ];

            // Envoyer la réponse JSON
            echo json_encode($response);
            exit();
        } catch (\Exception $e) {
            throw new \Exception("Error updating user: " . $e->getMessage());
        }
    }

    public function delete($id) {
        //Test si l'utilisateur est un administrateur
        $this->checkAdmin();
        return $this->userRepositorie->delete($id);
    }

    public function findAll() {
        $users =  $this->userRepositorie->findAll();
        return json_encode($users);
    }

    public function findById($id) {
        return $this->userRepositorie->findById($id);
    }

    public function login() {
        try {
            // Récupérer et valider les données du formulaire
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
    
            if (empty($email) || empty($password)) {
                $this->redirectWithError("Email et mot de passe requis.", '/login');
                return;
            }
            // Récupérer l'utilisateur par email
            $user = $this->userRepositorie->findByEmail($email);
    
            // Vérifier si l'utilisateur existe
            if (!$user) {
                $this->redirectWithError("Utilisateur non trouvé.", '/');
                return;
            }
    
            // Vérifier le mot de passe
            if (!password_verify($password, $user->getPassword())) {
                $this->redirectWithError("Mot de passe invalide.", '/');
                return;
            }
    
            // Initialiser la session
            session_start();
            session_regenerate_id(true); 
            $_SESSION['user'] = [
                'id' => $user->getId(),
                'role' => $user->getRole(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'avatar' => $user->getAvatar(),
            ];
    
            // Redirection basée sur le rôle
            $redirectMap = [
                'admin' => '/admin/home',
                'membre' => '/user/home',
                'responsable' => '/responsable/home',
            ];
    
            $role = $user->getRole();
            if (array_key_exists($role, $redirectMap)) {
                header("Location: {$redirectMap[$role]}");
                exit();
            }
    
            // Rôle non reconnu
            $this->redirectWithError("Rôle utilisateur inconnu.", '/');
        } catch (\Exception $e) {
            // Journaliser l'erreur pour un diagnostic ultérieur
            error_log("Erreur de connexion : " . $e->getMessage());
            $this->redirectWithError("Erreur inattendue. Veuillez réessayer.", '/');
        }
    }
    
    private function redirectWithError($message, $url) {
        session_start();
        $_SESSION['error'] = $message;
        header("Location: $url");
        exit();
    }

    public function logout() {
        // Démarrer la session
        session_start();
    
        // Détruire toutes les variables de session
        $_SESSION = [];
    
        // Supprimer le cookie de session (si utilisé)
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
    
        // Détruire complètement la session
        session_destroy();
    
        // Rediriger vers la page de connexion ou d'accueil
        header("Location: /");
        exit();
    }
    
    public function home() {
        // Vérifier si l'utilisateur est connecté
        session_start();
        if (!isset($_SESSION['user'])) {
            header('Location: /');
            exit();
        }
    
        // Récupérer les données de l'utilisateur
        $user = $_SESSION['user'];
        $userId = $user['id'];

        // Récupérer les projets de l'utilisateur
        $projectRepository = new ProjectRepositorie();
        $projects = $projectRepository->findByUserId($userId);

        // Calculer le progrès de chaque projet
        $taskRepository = new TaskRepositorie();
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
            }
    
            $project['progress'] = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        }
        // Garder uniquement les projets dont le progrès est inférieur à 100 %
        $projects = array_filter($projects, function ($project) {
            return $project['progress'] < 100;
        });

        // Réindexer les clés après filtrage
        $projects = array_values($projects);

        // Récupérer les tâches a faire de l'utilisateur
        $taskToDo = $taskRepository->getTaskToDoByUserId($userId);

        // Récupérer les notifications de l'utilisateur
        $notificationRepository = new NotificationRepositorie();    
        $notifications = $notificationRepository->findByUserId($userId);

        // Afficher la page d'accueil de l'utilisateur
        require_once __DIR__ . '/../Views/user/home.php';
    }

    public function task() {
        // Vérifier si l'utilisateur est connecté
        session_start();
        if (!isset($_SESSION['user'])) {
            header('Location: /');
            exit();
        }
    
        // Récupérer les données de l'utilisateur connecté
        $user = $_SESSION['user'];
        $userId = $user['id'];
    
        // Charger le repository des tâches
        $taskRepository = new TaskRepositorie();
        $tasks = $taskRepository->findByUserId($userId);
    
        // Charger le repository des commentaires et Ajouter les commentaires à chaque tâche
        $commentRepository = new CommentRepositorie();
        foreach ($tasks as &$task) {
            $task['comments'] = $commentRepository->findByTaskId($task['id']);
        }

        // Afficher la page des tâches de l'utilisateur
        require_once __DIR__ . '/../Views/user/task.php';
    }
    
    public function project(){
        // Vérifier si l'utilisateur est connecté
        session_start();
        if (!isset($_SESSION['user'])) {
            header('Location: /');
            exit();
        }
    
        // Récupérer les données de l'utilisateur connecté
        $user = $_SESSION['user'];
        $userId = $user['id'];
    
        // Charger le repository des projets
        $projectRepository = new ProjectRepositorie();
        $projects = $projectRepository->findByUserId($userId);
    
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
    
        // Afficher la page des projets de l'utilisateur
        require_once __DIR__ . '/../Views/user/project.php';
    }
    
}