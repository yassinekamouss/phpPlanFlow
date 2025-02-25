<?php
    session_start();
    $dataPoints1 = [] ;
    foreach ($projectsProgress as $key => $value) {
        $dataPoints1[] = array("y" => $value , "label" => $key);
    }
    $dataPoints1 = json_encode($dataPoints1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Admin Dashboard</title>
    <link rel="icon" href="/assets/Logo - Copie.png"/>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/plugin/relativeTime.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/locale/fr.js"></script>
    <script>
        dayjs.extend(window.dayjs_plugin_relativeTime);
        dayjs.locale('fr');

        const dataPoints = <?= $dataPoints1; ?>;
        const activities = <?= json_encode($activitesRecentes); ?>;
    </script>
</head>
    <body class="min-h-screen bg-gray-50" 
        x-data="{
            activities: activities
        }">

        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../templates/aside.php'; ?>

        <!-- Top Bar -->
        <div class="sm:ml-56 mb-8 bg-white shadow-sm p-4 flex justify-between items-center">
            <div class="text-xl font-bold italic">
                Tableau de Bord Super Admin
            </div>
            <div class="flex items-center gap-4">
                <div 
                    class="relative"
                    x-data="{ 
                        notificationDropdownOpen: false, 
                        hasBeenClicked: false 
                    }">
                    <!-- Bouton de notification -->
                    <button 
                        class="relative p-2"
                        @click="
                            notificationDropdownOpen = !notificationDropdownOpen;
                            if (!hasBeenClicked) {
                                activities.forEach(activity => activity.isRead = true); // Marquer toutes les notifications comme lues.
                            }
                            hasBeenClicked = true;
                        ">
                        <span 
                            class="absolute top-0 right-0 w-4 h-4 text-xs rounded-full flex items-center justify-center" 
                            :class="activities.some(activity => !activity.isRead) ? 'bg-red-500 text-white' : 'bg-gray-300 text-gray-600'">
                            <span x-text="activities.filter(activity => !activity.isRead).length"></span>
                        </span>
                        🔔
                    </button>

                    <!-- Menu de dropdown -->
                    <div 
                        class="absolute top-12 right-0 bg-white rounded-lg shadow-md p-2"
                        style="width : 500px;"
                        x-show="notificationDropdownOpen"
                        x-transition
                        @click.away="notificationDropdownOpen = false">
                        <template x-if="activities.length > 0">
                            <ul>
                                <template x-for="activity in activities.slice(0,5)" :key="activity.id">
                                    <li class="flex justify-between mb-2">
                                        <div class="flex items-center">
                                            <span class="flex items-center justify-center">
                                                <img class="w-8 h-8 rounded-full" :src="activity.user_avatar" alt="">
                                            </span>
                                            <div class="ml-4">
                                                <p x-text="activity.message"></p>
                                            </div>
                                        </div>
                                        <span class="text-sm text-gray-500" x-text="dayjs(activity.created_at).fromNow()"></span>
                                    </li>
                                </template>
                            </ul>
                        </template>
                        <template x-if="activities.length === 0">
                            <p class="text-gray-500 text-center">Aucune notification à afficher.</p>
                        </template>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <img src="<?= $_SESSION['user']['avatar']; ?>" alt="Admin" class="w-8 h-8 rounded-full">
                    <div>
                        <p class="text-sm font-medium"><?= $_SESSION['user']['name']; ?></p>
                        <p class="text-xs text-gray-500">Administrateur</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="sm:ml-56 p-6">

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <p class="text-gray-500 text-sm mb-2">Projets Actifs</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-2xl font-bold"><?= $projectsCount; ?></h3>
                        <span class="text-sm text-green-500">+12</span>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <p class="text-gray-500 text-sm mb-2">Projets termine</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-2xl font-bold"><?= $completedProjects; ?></h3>
                        <span class="text-sm text-red-500">-5</span>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <p class="text-gray-500 text-sm mb-2">Tâches en Cours</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-2xl font-bold"><?= $tasksCount; ?></h3>
                        <span class="text-sm text-green-500">+8</span>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <p class="text-gray-500 text-sm mb-2">Utilisateurs</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-2xl font-bold"><?= $usersCount; ?></h3>
                        <span class="text-sm text-green-500">+15</span>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <h3 class="text-lg font-semibold mb-6">Activité des Projets</h3>
                    <canvas id="lineChart"></canvas>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <h3 class="text-lg font-semibold mb-6">Avancement des projets</h3>
                    <canvas id="projectsProgressChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white w-1/2 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-6">Activités Récentes</h3>
                <ul class="space-y-4">
                    <template x-for="activity in activities.slice(0, 5)" :key="activity.id">
                    <li class="flex justify-between">
                        <div class="flex items-center">
                        <span class="flex items-center justify-center">
                            <img class="w-8 h-8 rounded-full" :src="activity.user_avatar" alt="">
                        </span>
                        <div class="ml-4">
                            <p class="" x-text="activity.message"></p>
                        </div>
                        </div>
                        <span class="text-sm text-gray-500" x-text="dayjs(activity.created_at).fromNow()"></span>
                    </li>
                    </template>
                </ul>
            </div>
        </div>

        <script>
            document.addEventListener('alpine:init', () => {

                // Charts configuration
                const lineCtx = document.getElementById('lineChart').getContext('2d');
                const barCtx = document.getElementById('projectsProgressChart').getContext('2d');
                
                new Chart(lineCtx, {
                    type: 'line',
                    data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Projets',
                        data: [4, 6, 8, 7, 9, 10],
                        borderColor: '#3b82f6',
                        borderWidth: 2
                    }]
                    }
                });
                // Préparez les données pour le graphique
                const labels = dataPoints.map(dp => dp.label); 
                const data = dataPoints.map(dp => dp.y);    
                new Chart(barCtx, {
                    type: 'bar', 
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Progression des projets',
                            data: data, 
                            backgroundColor: [
                                '#3b82f6',
                            ],
                            borderColor: [
                                '#3b82f6',
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true // Commencez l'échelle à 0
                            }
                        }
                    }
                });
        
            });
        </script>
    </body>
</html>
