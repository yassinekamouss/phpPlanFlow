<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport</title>
    <link rel="icon" href="/assets/Logo - Copie.png"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <?php require_once __DIR__ . '/../templates/aside.php'; ?>

    <!-- Top Bar -->
    <header class="sm:ml-56 mb-8 bg-white shadow-sm p-4 flex justify-between items-center">
        <div class="text-xl font-bold italic">
            Panel de gestion des membres
        </div>
        <div class="flex items-center gap-4">
            <!-- <button class="relative p-2">
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

    <!-- Main content -->
    <main class="sm:ml-56 py-6 px-16 text-gray-800"
        x-data="{
            date: new Date().toLocaleDateString('fr-FR', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            })
        }">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg p-6 mb-8">
            <h1 class="text-3xl font-semibold mb-2">Rapport de Suivi de Projets</h1>
            <p class="text-gray-600 mb-4">Date de génération : <span x-text="date"></span></p>
            <button class="bg-blue-500 text-white py-2 px-6 rounded hover:bg-blue-600 float-right mt-2" onclick="printMainContent('/rapport/imprimer')">Imprimer le rapport</button>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white shadow-sm rounded-lg p-6 text-center">
                <h3 class="text-xl font-semibold mb-3">Total des Projets</h3>
                <div class="text-3xl font-bold text-blue-500" id="totalTasks"><?= $projectsCount; ?></div>
            </div>
            <div class="bg-white shadow-sm rounded-lg p-6 text-center">
                <h3 class="text-xl font-semibold mb-3">Total des Tâches</h3>
                <div class="text-3xl font-bold text-blue-500" id="totalTasks"><?= $tasksCount; ?></div>
            </div>
            <div class="bg-white shadow-sm rounded-lg p-6 text-center">
                <h3 class="text-xl font-semibold mb-3">Tâches Terminées</h3>
                <div class="text-3xl font-bold text-green-500" id="completedTasks"><?= $taskTermineCount; ?></div>
            </div>
            <div class="bg-white shadow-sm rounded-lg p-6 text-center">
                <h3 class="text-xl font-semibold mb-3">Tâches En Cours</h3>
                <div class="text-3xl font-bold text-yellow-500" id="pendingTasks"><?= $taskEnCoursCount; ?></div>
            </div>
        </div>

        <!-- État des tâches par projet -->
        <h2 class="text-2xl font-semibold mb-4">État des Tâches par Projet</h2>
        <div class="overflow-x-auto mb-8">
            <table class="w-full bg-white shadow-sm rounded-lg">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-6 text-left">Projet</th>
                        <th class="py-3 px-6 text-left">Tâche</th>
                        <th class="py-3 px-6 text-left">Assigné à</th>
                        <th class="py-3 px-6 text-left">État</th>
                        <th class="py-3 px-6 text-left">Date d'échéance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <?php $taskCount = count($project['tasks']); ?>
                        
                        <!-- Afficher le nom du projet une seule fois, avec rowspan -->
                        <tr class="border-b">
                            <td class="py-4 px-6" rowspan="<?= $taskCount; ?>"><?= $project['name']; ?></td>
                            <!-- La première tâche du projet -->
                            <?php if (isset($project['tasks'][0])): ?>
                                <td class="py-4 px-6"><?= $project['tasks'][0]['name']; ?></td>
                                <td class="py-4 px-6"><?= $project['tasks'][0]['assignee']; ?></td>
                                <td 
                                    x-data="{ status: '<?= $project['tasks'][0]['status']; ?>' }"
                                    class="py-4 px-6 bg-green-100 rounded"
                                    :class="{
                                        'bg-green-100 text-green-800': status === 'termine',
                                        'bg-blue-100 text-blue-800': status === 'en cours',
                                        'bg-gray-100 text-gray-800': status === 'a faire'
                                    }" >
                                    <?= $project['tasks'][0]['status']; ?>
                                </td>
                                <td class="py-4 px-6"><?= $project['tasks'][0]['end_date']; ?></td>
                            <?php endif; ?>
                        </tr>

                        <!-- Boucle à travers les autres tâches du projet -->
                        <?php for ($i = 1; $i < $taskCount; $i++): ?>
                            <tr class="border-b">
                                <td class="py-4 px-6"><?= $project['tasks'][$i]['name']; ?></td>
                                <td class="py-4 px-6"><?= $project['tasks'][$i]['assignee']; ?></td>
                                <td x-data="{ status: '<?= $project['tasks'][$i]['status']; ?>' }"
                                    class="py-4 px-6 bg-green-100 rounded"
                                    :class="{
                                        'bg-green-100 text-green-800': status === 'termine',
                                        'bg-blue-100 text-blue-800': status === 'en cours',
                                        'bg-gray-100 text-gray-800': status === 'a faire'
                                    }" >
                                    <?= $project['tasks'][$i]['status']; ?>
                                </td>
                                <td class="py-4 px-6"><?= $project['tasks'][$i]['end_date']; ?></td>
                            </tr>
                        <?php endfor; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Répartition des tâches par personne -->
        <h2 class="text-2xl font-semibold mt-8 mb-4">Répartition des Tâches par Personne</h2>
        <div class="overflow-x-auto">
            <table class="w-full bg-white shadow-sm rounded-lg">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-6 text-left">Membre</th>
                        <th class="py-3 px-6 text-left">Total Tâches</th>
                        <th class="py-3 px-6 text-left">Tâches Terminées</th>
                        <th class="py-3 px-6 text-left">Tâches En Cours</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($membersTasks as $memberTask): ?>
                    <tr class="border-b">
                        <td class="py-4 px-6"><?= $memberTask['name']; ?></td>
                        <td class="py-4 px-6"><?= $memberTask['total_tasks'] ?></td>
                        <td class="py-4 px-6"><?= $memberTask['tasks_terminees'] ?></td>
                        <td class="py-4 px-6"><?= $memberTask['tasks_en_cours'] ?></td>
                    </tr>
                    <?php endforeach?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function printMainContent(url) {
            const content = document.querySelector('main').innerHTML; // Récupère le contenu de <main>
            const printWindow = window.open('', '', 'width=800,height=600');

            // Ajoute le contenu dans une nouvelle fenêtre avec styles
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Rapport</title>
                        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
                    </head>
                    <body>
                        ${content}
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.onload = function () {
                printWindow.print();
                printWindow.onafterprint = function () {
                    printWindow.close();
                };
            };
        }
    </script>
</body>
</html>