<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projets</title>
    <link rel="icon" href="/assets/Logo - Copie.png"/>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/plugin/relativeTime.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/locale/fr.js"></script>
    <script>
        dayjs.extend(window.dayjs_plugin_relativeTime);
        dayjs.locale('fr');

        const projects = <?= json_encode($projects); ?>;
    </script>
</head>
    <body class="min-h-screen bg-gray-50">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../templates/aside.php'; ?>

        <!-- Top Bar -->
        <header class="sm:ml-56 mb-8 bg-white shadow-sm p-4 flex justify-between items-center">
            <div class="text-xl font-bold italic">
                Panel de gestion des projets
            </div>
            <div class="flex items-center gap-4">
                <!-- <button class="relative p-2">
                    <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-xs rounded-full">3</span>
                    &#x1F514;  
                </button> -->
                <div class="flex items-center gap-2 sm:mr-5">
                    <img src="<?= $_SESSION['user']['avatar']; ?>" alt="Admin" class="w-8 h-8 rounded-full">
                    <div>
                        <p class="text-sm font-medium"><?= $_SESSION['user']['name']; ?></p>
                        <p class="text-xs text-gray-500">Administrateur</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="sm:ml-56 py-6 px-16"
            x-data="{
                projects: projects,
                selectedProjectMembers: null,
                selectedProjectTasks: null,
                selectedTask: null,
                showMembersModal: false,
                showProjectTasksModal: false,
                getStatusColor(status) {
                    return {
                        'en cours': 'bg-blue-100 text-blue-800 border-blue-200',
                        'termine': 'bg-green-100 text-green-800 border-green-200',
                        'a faire': 'bg-yellow-100 text-yellow-800 border-yellow-200',
                        'annule': 'bg-red-100 text-red-800 border-red-200'
                    }[status];
                },
                getPriorityColor(priority) {
                    return {
                        'haute': 'text-red-500',
                        'moyenne': 'text-yellow-500',
                        'basse': 'text-green-500'
                    }[priority];
                },
                getProgresseColor(progress) {
                    if (progress <= 30) {
                        return 'bg-red-600'; // Rouge pour faible progression
                    } else if (progress <= 50) {
                        return 'bg-yellow-500'; // Jaune pour progression moyenne
                    } else if (progress <= 75) {
                        return 'bg-blue-500'; // Jaune pour progression moyenne
                    } else {
                        return 'bg-green-600'; // Vert pour progression élevée
                    }
                }
            }">
            <!-- Vue Liste -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="min-w-full divide-y divide-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Projet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Équipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progression</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date limite</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="project in projects" :key="project.id">
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-900" x-text="project.name"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span 
                                        :class="getStatusColor(project.status)"
                                        class="px-2.5 py-1 rounded-full text-xs border"
                                        x-text="project.status === 'en cours' ? 'En cours' : project.status === 'termine' ? 'Terminé' : project.status === 'bloque' ? 'Bloqué' : 'Annulé'">
                                    </span>
                                </td>
                                <td 
                                    class="px-6 py-4">
                                    <div class="flex -space-x-2 overflow-hidden">
                                        <!-- <img :src="project.project_manager_avatar" class="w-10 h-10 rounded-full border-2 border-white"> -->
                                        <template x-for="(member, index) in project.members.slice(0, 2)" :key="index">
                                            <img :src="member.avatar" class="w-10 h-10 rounded-full border-2 border-white">
                                        </template>
                                        <!-- Indicateur pour les membres restants -->
                                        <div 
                                            class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-white bg-gray-50 text-gray-600 text-xs font-medium cursor-pointer" 
                                            x-show="true" 
                                            @click="selectedProjectMembers = project ; showMembersModal = true">
                                            <span class="font-bold text-lg">+</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-blue-600 h-2 rounded-full" :style="`width: ${project.progress}%`"></div>
                                        </div>
                                        <span class="text-sm text-gray-500" x-text="`${project.progress}%`"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500" x-text="project.end_date"></td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="showProjectTasksModal = true; selectedProjectTasks = project" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Modal pour afficher tous les membres -->
            <div x-show="showMembersModal"
                @click.self="showMembersModal = false" 
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded-lg shadow-lg p-6 w-3/4 max-w-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-bold text-gray-800">Tous les Membres</h2>
                        <button @click="showMembersModal = false" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        <!-- <li class="py-3 flex items-center hover:bg-gray-50">
                            <img :src="selectedProjectMembers?.project_manager_avatar" class="w-10 h-10 rounded-full" alt="">
                            <div class="ml-3">
                                <h3 class="font-semibold text-gray-700" x-text="selectedProjectMembers?.project_manager_name"></h3>
                                <p class="text-sm text-gray-500" x-text="'Chef de prjet'"></p>
                            </div>
                        </li> -->
                        <template x-for="(member, index) in selectedProjectMembers?.members" :key="index">
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

            <!-- Modal pour l'affichage des tashes -->
            <div x-show="showProjectTasksModal"
                @click.self="showProjectTasksModal = false"
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <!-- Taches -->
                <div class="bg-white rounded-lg shadow-lg p-6 w-3/4 max-w-lg">
                    <div class="flex justify-between items-start">
                        <h2 class="text-xl font-bold text-gray-800 mb-2">Tâches</h2>
                        <button @click="showProjectTasksModal = false ;" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        <template x-for="task in selectedProjectTasks?.tasks" :key="task.id">
                            <li class="py-2 flex justify-between items-center">
                                <div>
                                    <h3 class="font-semibold text-gray-700" x-text="task.name"></h3>
                                    <div class="flex items-center space-x-2 text-gray-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-sm" x-text="task.end_date"></p>
                                    </div>
                                </div>
                                <button @click="selectedTask = task ; showProjectTasksModal= false;" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </button>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            <!-- Modal pour commenter sur les taches -->
            <div x-show="selectedTask" 
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
                @click.self="selectedTask = null ; showProjectTasksModal = true"> 
                <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <h2 class="text-xl font-bold" x-text="selectedTask?.name"></h2>
                            <button @click="selectedTask = null ; showProjectTasksModal = true;" class="text-gray-400 hover:text-gray-600">
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
                                        <input type="hidden" name="author_id" value="<?= $_SESSION['user']['id'] ?>">
                                        
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
        </div>
    </body>
</html>