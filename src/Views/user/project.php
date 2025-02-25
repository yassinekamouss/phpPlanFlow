<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/assets/Logo - Copie.png"/>
    <title>Mes Projets</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/plugin/relativeTime.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/locale/fr.js"></script>
    <!-- <link rel="stylesheet" href="/css/styles.css"> -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.2/dist/tailwind.min.css" rel="stylesheet">
    <script>
        dayjs.extend(window.dayjs_plugin_relativeTime);
        dayjs.locale('fr');
        const projects = <?= json_encode($projects); ?>;
    </script>
</head>
<body class="bg-gray-50"
    x-data="{
        openProfileModal: false,
        role: '<?= $_SESSION['user']['role'] ?>',
        avatar: '<?= $_SESSION['user']['avatar'] ?>',
        name: '<?= $_SESSION['user']['name'] ?>',
        email: '<?= $_SESSION['user']['email'] ?>',
        alert: { show: false, type: '', message: '' }
      }">
    <!-- Navbar -->
    <?php require_once __DIR__ . '/../templates/userNavBar.php'; ?>

    <main class="min-h-screen p-8"  
        x-data="{ 
                  newComment: '',
                  currentPage: 1,
                  projectsPerPage: 2,
                  projects: projects,
                  selectedTask: null,
                  getStatusColor(status) {
                    return {
                        'annule': 'bg-red-100 text-red-800',
                        'en cours': 'bg-blue-100 text-gray-800',
                        'termine': 'bg-green-100 text-green-800'
                    }[status];
                   }, 
                }">
        <div class="mx-auto max-w-7xl">
            <!-- Afficher un message si la liste des projets est vide -->
            <template x-if="projects.length === 0">
                <div class="text-center text-gray-500">
                    Aucun projet disponible pour le moment.
                </div>
            </template>
            <template x-for="(project, index) in projects.slice((currentPage - 1) * projectsPerPage, currentPage * projectsPerPage)" :key="index">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <!-- Titre du projet -->
                    <div class="flex justify-between">
                        <div>
                            <h1 class="text-xl font-bold text-gray-800 mb-2" x-text="project.name"></h1>
                            <p class="text-sm text-gray-600" x-text="project.description"></p>
                        </div>
                        <div>
                            <span :class="getStatusColor(project.status)" class="px-3 py-1 text-sm font-semibold rounded-lg" x-text="project.status === 'annule' ? 'Status: Annulé' : project.status === 'en cours' ? 'Status: En cours' : 'Status: Terminé'"></span><br>
                            <div class="text-right mt-2">
                                <span class="text-sm text-gray-800 font-medium" x-text="project.end_date"></span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h2 class="text-sm font-bold text-gray-700 mb-2">Avancement</h2>
                        <div class="w-full bg-gray-200 rounded-full h2 mt-2">
                            <div class="h-2 rounded-full" 
                                :class="{
                                    'bg-red-500': project.progress < 20,
                                    'bg-yellow-500': project.progress >= 20 && project.progress < 50,
                                    'bg-blue-500': project.progress >= 50 && project.progress < 75,
                                    'bg-green-500': project.progress >= 75
                                }"
                                :style="`width: ${project.progress}%`">
                            </div>
                        </div>
                        <span class="text-sm text-gray-500 mt-2 block" x-text="project.progress + '%'"></span>
                    </div>
                    <!-- Taches -->
                    <div class="mt-4">
                        <h2 class="text-xl font-bold text-gray-800 mb-2">Tâches</h2>
                        <ul class="divide-y divide-gray-200">
                            <template x-for="task in project.tasks" :key="task.id">
                                <li class="py-2 flex justify-between items-center">
                                    <div>
                                        <h3 class="font-semibold text-gray-700" x-text="task.name"></h3>
                                        <p class="text-sm text-gray-500" x-text="'Échéance : ' + task.end_date"></p>
                                    </div>
                                    <button @click="selectedTask = task" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <!-- Modal des détails -->
                    <div x-show="selectedTask" 
                        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4"
                        @click.self="selectedTask = null">
                        <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                            <div class="p-6">
                                <div class="flex justify-between items-start">
                                    <h2 class="text-xl font-bold" x-text="selectedTask?.name"></h2>
                                    <button @click="selectedTask = null" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-gray-600 mt-2" x-text="selectedTask?.description"></p>

                                <div class="mt-6 space-y-6">
                                    <!-- Informations de la tâche -->
                                    <div class="flex items-center justify-between text-sm text-gray-500">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span x-text="selectedTask?.assignee"></span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span x-text="selectedTask?.end_date"></span>
                                        </div>
                                    </div>

                                    <!-- Section commentaires -->
                                    <div class="border-t pt-6">
                                        <h3 class="font-medium mb-4">Commentaires</h3>
                                        <div class="space-y-4">
                                            <template x-for="comment in selectedTask?.comments" :key="comment.id">
                                                <div class="bg-gray-50 rounded-lg p-3">
                                                    <div class="flex items-center justify-between">
                                                        <span class="font-medium text-sm" x-text="comment.user"></span>
                                                        <span class="text-xs text-gray-500" x-text="dayjs(comment.created_at).fromNow()"></span>
                                                    </div>
                                                    <p class="text-sm mt-1" x-text="comment.text"></p>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Formulaire de commentaire -->
                                        <div class="mt-4">
                                            <form method="POST" action="/comment" @submit.prevent="
                                                const formData = new FormData($event.target);  // Récupérer les données du formulaire
                                                fetch($event.target.action, {
                                                    method: 'POST',
                                                    body: formData
                                                })
                                                .then(response => {
                                                    if (response.ok) {
                                                        // Ajout du commentaire localement après la soumission réussie
                                                        selectedTask.comments.push({
                                                            id: selectedTask.comments.length + 1,
                                                            user: 'Vous',
                                                            text: newComment,  // Utiliser le commentaire saisi
                                                            created_at: new Date()
                                                        });
                                                        newComment = '';  // Réinitialiser le commentaire
                                                        $event.target.reset();  // Réinitialiser le formulaire
                                                    } else {
                                                        throw new Error('Erreur lors de la création du commentaire');
                                                    }
                                                })
                                                .catch(error => {
                                                    console.error(error);
                                                });
                                            ">
                                                <!-- Champs cachés -->
                                                <input type="hidden" name="task_id" :value="selectedTask?.id">
                                                <input type="hidden" name="author_id" value="109">
                                                
                                                <!-- Zone de texte pour le commentaire -->
                                                <textarea 
                                                    name="text"
                                                    x-model="newComment"
                                                    class="w-full rounded-lg p-2 border border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                                    rows="3"
                                                    placeholder="Ajouter un commentaire..."></textarea>
                                                
                                                <!-- Bouton pour soumettre -->
                                                <button 
                                                    type="submit" 
                                                    class="mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                                    Commenter
                                                </button>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Équipe -->
                    <div class="mt-4" x-data="{ showModal: false }">
                        <h2 class="text-xl font-bold text-gray-800 mb-2">Équipe</h2>
                        
                        <!-- Chef de Projet -->
                        <div class="flex items-center mb-4">
                            <img class="w-12 h-12 rounded-full border-2 border-gray-300" :src="project.project_manager_avatar" alt="Chef de Projet">
                            <div class="ml-3">
                                <h3 class="font-semibold text-gray-700" x-text="project.project_manager_name"></h3>
                                <p class="text-sm text-gray-500">Chef de Projet</p>
                            </div>
                        </div>
                        
                        <!-- Avatars des membres -->
                        <div class="flex overflow-hidden -space-x-2">
                            <!-- Afficher un maximum de 5 membres -->
                            <template x-for="(member, index) in project.members.slice(0, 3)" :key="index">
                                <img :src="member.avatar" class="w-10 h-10 rounded-full border-2 border-white">
                            </template>

                            <!-- Indicateur pour les membres restants -->
                            <div 
                                class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-white bg-gray-50 text-gray-600 text-xs font-medium cursor-pointer" 
                                x-show="true" 
                                @click="showModal = true">
                                <span class="font-bold text-lg">+</span>
                            </div>
                        </div>
                        <!-- Modal pour afficher tous les membres -->
                        <div x-show="showModal"
                             @click.self="showModal = false" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                            <div class="bg-white rounded-lg shadow-lg p-6 w-3/4 max-w-lg">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-lg font-bold text-gray-800">Tous les Membres</h2>
                                    <button @click="showModal = false" class="text-gray-500 hover:text-gray-700">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <ul class="divide-y divide-gray-200">
                                    <!-- <li class="py-3 flex items-center hover:bg-gray-50">
                                        <img :src="project.project_manager_avatar" class="w-10 h-10 rounded-full" alt="">
                                        <div class="ml-3">
                                            <h3 class="font-semibold text-gray-700" x-text="project.project_manager_name"></h3>
                                            <p class="text-sm text-gray-500" x-text="'Chef de prjet'"></p>
                                        </div>
                                    </li> -->
                                    <template x-for="(member, index) in project.members" :key="index">
                                        <li class="py-3 flex items-center hover:bg-gray-50">
                                            <img :src="member.avatar" class="w-10 h-10 rounded-full">
                                            <div class="ml-3">
                                                <h3 class="font-semibold text-gray-700" x-text="member.name"></h3>
                                                <p class="text-sm text-gray-500" x-text="member.role === 'Manager' ? 'Chef de projet' : 'Membre'"></p>
                                            </div>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        <!-- Pagination -->
        <div class="flex justify-center space-x-3 items-center mb-6"
            x-show="projects.length > projectsPerPage">
            <button 
                @click="currentPage = Math.max(1, currentPage - 1)"
                :disabled="currentPage === 1"
                class="px-4 py-2 bg-gray-200 text-gray-600 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fa-solid fa-arrow-left mr-2"></i>Précédent
            </button>
            <span class="text-gray-600">Page <span x-text="currentPage"></span> sur <span x-text="Math.ceil(projects.length / projectsPerPage)"></span></span>
            <button 
                @click="currentPage = Math.min(Math.ceil(projects.length / projectsPerPage), currentPage + 1)"
                :disabled="currentPage === Math.ceil(projects.length / projectsPerPage)"
                class="px-4 py-2 bg-gray-200 text-gray-600 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                Suivant<i class="fa-solid fa-arrow-right ml-2"></i>
            </button>
        </div>
    </main>

    <!-- Modal pour modifier les info du profile -->
    <?php require_once __DIR__ . '/../templates/userProfileModal.php'; ?>
    
    <!-- Alert -->
    <?php require_once __DIR__ . '/../templates/userAlert.php'; ?>

</body>
</html>