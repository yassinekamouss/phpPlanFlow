<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.2/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/plugin/relativeTime.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/locale/fr.js"></script>
    <script>
        dayjs.extend(window.dayjs_plugin_relativeTime);
        dayjs.locale('fr');
        const projects = <?= json_encode($projects); ?>;
        const tasksToDo = <?= json_encode($taskToDo); ?>;
        const notifications = <?= json_encode($notifications); ?>;
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

    <!-- Contenu Principal -->
    <main class="max-w-7xl mx-auto px-4 sm:px-2 py-8">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4"
             x-data="{
                projects: projects,
                tasksToDo: tasksToDo,
                notifications: notifications,
                showAllNotifications: false
             }">
            <!-- Carte Projets en Cours --> 
            <div class="bg-white rounded-lg shadow-md p-6"> 
                <div class="flex justify-between items-center mb-4"> 
                    <h2 class="text-xl font-semibold text-gray-800">Projets en Cours</h2> 
                    <span class="text-sm text-gray-500" x-text="`${projects.length} projets`"></span> 
                </div> 
                <div class="space-y-4"> 
                    <!-- Afficher un message si la liste des projets est vide -->
                    <template x-if="projects.length === 0">
                        <div class="text-center text-gray-500">
                            Aucun projet disponible pour le moment.
                        </div>
                    </template>
                    <template x-for="project in projects" :key="project.id"> 
                        <div class="border-b pb-3 last:border-b-0"> 
                            <div class="flex justify-between items-center"> 
                                <span x-text="project.name" class="font-medium"></span> 
                                <span  class="text-xs px-2 py-1 rounded-full" 
                                      :class="project.progress < 30 ? 'bg-red-100 text-red-800' : 
                                                project.progress < 50 ? 'bg-yellow-100 text-yellow-800' : 
                                                project.progress < 75 ? 'bg-blue-100 text-blue-800' : 
                                                'bg-green-100 text-green-800'" 
                                       x-text="project.progress + '%'"></span>    
                            </div> 
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2"> 
                                <div class="h-2 rounded-full" 
                                     :class="project.progress < 30 ? 'bg-red-500' : 
                                                project.progress < 50 ? 'bg-yellow-500' : 
                                                project.progress < 75 ? 'bg-blue-500' : 
                                                'bg-green-500'"    
                                     :style="`width: ${project.progress}%`"></div> 
                            </div> 
                        </div> 
                    </template> 
                </div> 
            </div> 
            <!-- Carte Tâches à Faire --> 
            <div class="bg-white rounded-lg shadow-md p-6"> 
                <div class="flex justify-between items-center mb-4"> 
                    <h2 class="text-xl font-semibold text-gray-800">Tâches à Faire</h2> 
                    <span class="text-sm text-gray-500" x-text="`${tasksToDo.length} tâches`"></span> 
                </div> 
                <div class="space-y-4"> 
                    <!-- Afficher un message si la liste des taches est vide -->
                    <template x-if="tasksToDo.length === 0">
                        <div class="text-center text-gray-500">
                            Aucun taches disponible pour le moment.
                        </div>
                    </template>
                    <template x-for="task in tasksToDo" :key="task.id"> 
                        <div class="flex justify-between items-center border-b pb-3 hover:bg-slate-50 last:border-b-0"> <div> 
                            <span x-text="task.name" class="block font-medium text-sm"></span> 
                            <span x-text="task.project_name" class="text-xs text-gray-500"></span> 
                        </div> 
                        <span class="text-xs px-2 py-1 rounded-full" :class="{ 'bg-red-100 text-red-800': task.priority === 'haute', 'bg-yellow-100 text-yellow-800': task.priority === 'moyenne', 'bg-green-100 text-green-800': task.priority === 'basse' }" x-text="task.priority"></span> 
                    </div> 
                </template> 
                </div>
            </div> 
             <!-- Carte Notifications --> 
            <div class="bg-white rounded-lg shadow-md p-6"> 
                <div class="flex justify-between items-center mb-4"> 
                    <h2 class="text-xl font-semibold text-gray-800">Notifications</h2> 
                    <span class="text-sm text-gray-500" x-text="`${notifications.length} non lues`"></span> 
                </div> 
                <div class="space-y-4">
                    <!-- Afficher un message si la liste des notifications est vide -->
                    <template x-if="notifications.length === 0">
                        <div class="text-center text-gray-500">
                            Aucun Notification disponible pour le moment.
                        </div>
                    </template>
                    <template x-for="notification in showAllNotifications ? notifications : notifications.slice(0, 5)" :key="notification.id">
                        <div class="flex items-start border-b pb-3 hover:bg-slate-50 last:border-b-0">
                            <div class="mr-3">
                                <img :src="notification.user_avatar" class="w-8 h-8 rounded-full">
                            </div>
                            <div>
                                <p class="text-sm">
                                    <span x-text="notification.user_name" class="font-medium mr-1"></span>
                                    <span x-text="notification.message" class="text-gray-600"></span>
                                </p>
                                <span x-text="dayjs(notification.created_at).fromNow()" class="text-xs text-gray-500"></span>
                            </div>
                        </div>
                    </template>

                    <button x-show="notifications.length > 5"
                            @click="showAllNotifications = !showAllNotifications"
                            class="mt-4 w-full py-1 text-sm font-medium text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 flex items-center justify-center space-x-2">
                        <!-- Texte dynamique -->
                        <span x-text="showAllNotifications ? 'Voir moins' : 'Voir plus'"></span>

                        <!-- Icônes -->
                        <span class="text-sm">
                            <!-- Icône Voir moins (flèches vers le bas) -->
                            <svg x-show="!showAllNotifications" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 5.25 7.5 7.5 7.5-7.5m-15 6 7.5 7.5 7.5-7.5" />
                            </svg>

                            <!-- Icône Voir plus (flèches vers le haut) -->
                            <svg x-show="showAllNotifications" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 18.75 7.5-7.5 7.5 7.5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 7.5-7.5 7.5 7.5" />
                            </svg>
                        </span>
                    </button>

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