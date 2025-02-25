<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/assets/Logo - Copie.png"/>
    <title>Mes Tâches</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/plugin/relativeTime.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/locale/fr.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script> 
        dayjs.extend(window.dayjs_plugin_relativeTime);
        dayjs.locale('fr');
        const tasksJson = <?= json_encode($tasks); ?>;
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

    <main x-data="{
        selectedTask: null,
        tasks: tasksJson,
        newComment: '',
        getPriorityColor(priority) {
            return {
                haute: 'bg-red-100 text-red-800',
                moyenne: 'bg-yellow-100 text-yellow-800',
                basse: 'bg-green-100 text-green-800'
            }[priority];
        },
        getStatusColor(status) {
            return {
                'a faire': 'bg-gray-100 text-gray-800',
                'en cours': 'bg-blue-100 text-gray-800',
                'termine': 'bg-green-100 text-green-800'
            }[status];
        },
    }" class="mt-5 p-8">

        <div class="max-w-7xl mx-auto">
            <!-- En-tête -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Mes Tâches</h1>
            </div>

            <!-- Liste des tâches -->
            <div class="grid gap-6">
                <!-- Afficher un message si la liste des taches est vide -->
                <template x-if="tasks.length === 0">
                    <div class="text-center text-gray-500">
                        Aucun projet disponible pour le moment.
                    </div>
                </template>
                <template x-for="task in tasks" :key="task.id">
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-6">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center space-x-4">
                                <!-- Avatar -->
                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <!-- Titre et description -->
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-900" x-text="task.name"></h2>
                                    <p class="text-gray-500 mt-1" x-text="task.description"></p>
                                </div>
                            </div>
                            <!-- Menu -->
                            <div x-data="{ changeTaskStatus: false , status: '' }" class="ml-3 relative">
                                <div>
                                    <button @click="changeTaskStatus = !changeTaskStatus" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
                                </div>

                                <form 
                                    @click.away="changeTaskStatus = false"
                                    x-show="changeTaskStatus" 
                                    :action="`/task/${task.id}`" 
                                    method="POST" 
                                    @submit.prevent="
                                        const formData = new FormData($event.target);
                                        fetch($event.target.action, {
                                            method: 'POST',
                                            body: formData
                                        })
                                        .then(response => {
                                            if (response.ok) {
                                                task.status = status;  // Met à jour l'état de la tâche localement
                                                changeTaskStatus = false;
                                            } else {
                                                throw new Error('Erreur lors de la mise à jour du statut de la tâche');
                                            }
                                        })
                                        .catch(error => {
                                            console.error(error);
                                        });"
                                    class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">

                                    <!-- Liens entre le statut et le champ caché -->
                                    <input type="hidden" name="status" x-bind:value="status">  

                                    <button type="submit" @click="status = 'a faire';" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">A Faire</button>
                                    <button type="submit" @click="status = 'en cours';" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">En cours</button>
                                    <button type="submit" @click="status = 'termine'" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Terminé</button>
                                </form>

                            </div>
                        </div>
                        
                        <div class="mt-6 flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <!-- Badges -->
                                <span :class="getPriorityColor(task.priority)" class="px-3 py-1 rounded-full text-sm" 
                                        x-text="task.priority === 'haute' ? 'Priorité: Haute' : task.priority === 'moyenne' ? 'Priorité: Moyenne' : 'Priorité: Basse'">
                                </span>
                                <span :class="getStatusColor(task.status)" class="px-3 py-1 rounded-full text-sm"
                                        x-text="task.status === 'a faire' ? 'À faire' : task.status === 'en cours' ? 'En cours' : 'Terminé'">
                                </span>
                            </div>

                            <div class="flex items-center space-x-6 text-gray-500">
                                <!-- Date -->
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm" x-text="task.end_date"></span>
                                </div>
                                <!-- Commentaires -->
                                <button @click="selectedTask = task" class="flex items-center space-x-2 hover:text-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                    </svg>
                                    <span class="text-sm" x-text="task.comments.length"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
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
                                        const formData = new FormData($event.target);
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
        </div>
    </main>

    <!-- Modal pour modifier les info du profile -->
    <?php require_once __DIR__ . '/../templates/userProfileModal.php'; ?>
    
    <!-- Alert -->
    <?php require_once __DIR__ . '/../templates/userAlert.php'; ?>

</body>
</html>