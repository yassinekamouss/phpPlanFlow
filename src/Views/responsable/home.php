<?php 
    session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">    
    <link rel="icon" href="/assets/Logo - Copie.png"/>
    <title>Home</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.2/dist/tailwind.min.css" rel="stylesheet">
    <script>
        const projects = <?= json_encode($projects); ?>;
        const ProjectState = <?= json_encode($ProjectState); ?>;
        const ProjectsUrgent = <?= json_encode($projectUrgent); ?>;
        const notifications = <?= json_encode($notifications); ?>;
    </script>
</head>
<body class="bg-gray-50 min-h-screen"
    x-data="{
        openProfileModal: false,
        role: '<?= $_SESSION['user']['role'] ?>',
        avatar: '<?= $_SESSION['user']['avatar'] ?>',
        name: '<?= $_SESSION['user']['name'] ?>',
        email: '<?= $_SESSION['user']['email'] ?>',
        alert: { show: false, type: '', message: '' }
    }">
    <!-- NavBar -->
    <?php require_once __DIR__ . '/../templates/responsableNavBar.php' ;?>
    <main class="min-h-screen p-8"
        x-data="{
            projects: projects,
            projectState: ProjectState,
            urgentProjects: ProjectsUrgent,
            notifications: notifications,
            showAllNotifications: false,
            getProgresseColor(progress) {
                if (progress < 30) {
                    return 'bg-red-600'; // Rouge pour faible progression
                } else if (progress < 50) {
                    return 'bg-yellow-500'; // Jaune pour progression moyenne
                } else if (progress < 75) {
                    return 'bg-blue-500'; // Jaune pour progression moyenne
                } else {
                    return 'bg-green-500'; // Vert pour progression élevée
                }
            }
        }">

        <!-- Grid principal -->
        <main class="mx-auto max-w-7xl grid grid-cols-12 gap-6">
            <!-- statistics and porject's progress -->
            <div class="col-span-12 lg:col-span-8">
                <!-- Statistiques des projets -->
                <div class="bg-white rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-semibold mb-4">Vue d'ensemble des projets</h2>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="p-4 rounded-lg bg-green-50 border border-green-100">
                            <div class="text-3xl font-bold text-green-600" x-text="projectState.termine"></div>
                            <div class="text-sm text-green-800">Projets Terminés</div>
                        </div>
                        <div class="p-4 rounded-lg bg-orange-50 border border-orange-100">
                            <div class="text-3xl font-bold text-orange-600" x-text="projectState.en_cours"></div>
                            <div class="text-sm text-orange-800">Projets En-Cours</div>
                        </div>
                        <div class="p-4 rounded-lg bg-red-50 border border-red-100">
                            <div class="text-3xl font-bold text-red-600" x-text="projectState.en_retard"></div>
                            <div class="text-sm text-red-800">Projets En-retard</div>
                        </div>
                    </div>
                </div>

                <!-- Performance équipe -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Performance des équipes</h2>
                    <div class="space-y-4">
                        <template x-for="project in projects">
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span x-text="project.name"></span>
                                    <span x-text="`${project.progress}%`"></span>
                                </div>
                                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500 bg-gray-500"
                                            :style="`width: ${project.progress}%`"
                                            :class="getProgresseColor(project.progress)"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-span-12 lg:col-span-4 space-y-6">
                <!-- projets urgentes -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Actions urgentes</h2>
                    <div class="space-y-4">
                        <template x-for="urgentProject in urgentProjects">
                            <div class="p-3 rounded-lg bg-red-50  border border-gray-100">
                                <div class="font-medium text-red-800" x-text="urgentProject.name"></div>
                                <!-- <div class="text-sm text-red-600" x-text="urgentProject.urgentProject"></div> -->
                                <div class="text-xs text-red-500 mt-1" x-text="'Échéance: ' + new Date(urgentProject.end_date).toLocaleDateString('fr-FR')"></div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="rounded-lg shadow-sm p-6 bg-white">
                    <div class="flex justify-between items-center mb-4"> 
                        <h2 class="text-xl font-semibold text-gray-800">Notifications</h2> 
                        <span class="text-sm text-gray-500" x-text="`${notifications.length} non lues`"></span> 
                    </div> 
                    <div class="space-y-4">
                        <!-- Afficher un message si la liste des notifications est vide -->
                        <template x-if="notifications.length === 0">
                            <div class="text-center text-gray-500">
                                Aucun notification disponible pour le moment.
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
        
    </main>
</body>
</html>