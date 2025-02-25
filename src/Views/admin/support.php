<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support administrateur</title>
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

        const tickets = <?= json_encode($tickets); ?> ;
    </script>
</head>
<body class="min-h-screen bg-gray-50">

    <!-- Sidebar -->
    <?php require_once __DIR__ . '/../templates/aside.php'; ?>

    <!-- Top Bar -->
    <header class="sm:ml-56 mb-8 bg-white shadow-sm p-4 flex justify-between items-center">
        <div class="text-xl font-bold italic">
            Panel de gestion des demande de support
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

    <main 
        x-data="{
            selectedFilter: 'all',
            searchTerm: '',
            tickets: tickets,
            getStatusBadgeClass(status) {
                return {
                    'nouveau': 'bg-blue-100 text-blue-800',
                    'en_cours': 'bg-yellow-100 text-yellow-800',
                    'archive': 'bg-green-100 text-green-800'
                }[status];
            },
            getStatusLabel(status) {
                return {
                    'nouveau': 'Nouveau',
                    'en_cours': 'En cours',
                    'archive': 'Résolu',
                }[status];
            },
            // Filtre les tickets selon le statut sélectionné et le terme de recherche
            filteredTickets() {
                return this.tickets.filter(t => 
                    (this.selectedFilter === 'all' || t.status === this.selectedFilter) &&
                    (this.searchTerm === '' || t.name.toLowerCase().includes(this.searchTerm.toLowerCase()))
                );
            }
        }" 
        class="sm:ml-56 py-6 px-16">
        <div class="mx-auto">
            <div class="bg-white rounded-md shadow">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-g ray-200">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Support Administrateur</h1>
                        <div class="flex items-center space-x-4">
                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full flex items-center justify-center text-xs text-white">
                                    <?= $newTicketsCount; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtres et Recherche -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-4">
                            <div class="relative">
                                <select 
                                    x-model="selectedFilter"
                                    class="appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="all">Tous les tickets</option>
                                    <option value="nouveau">Nouveaux</option>
                                    <option value="en_cours">En cours</option>
                                    <option value="archive">Résolus</option>
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-3 top-2.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                            </div>
                        </div>
                        <div class="relative">
                            <input
                                type="text"
                                placeholder="Rechercher un ticket..."
                                x-model="searchTerm"
                                class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Liste des tickets -->
                <div class="divide-y divide-gray-200">
                    <template x-for="ticket in filteredTickets()" :key="ticket.id">
                        <div :class="{'bg-blue-50': ticket.status === 'nouveau' }" class="p-6 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <h3 class="text-lg font-medium text-gray-900" x-text="ticket.subject"></h3>
                                        <template x-if="ticket.status === 'nouveau'">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Nouveau
                                            </span>
                                        </template>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-500">
                                        De: <span x-text="ticket.name"></span> (<span x-text="ticket.email"></span>)
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600" x-text="ticket.message"></p>
                                    <div class="mt-4 flex items-center space-x-4">
                                        <span :class="getStatusBadgeClass(ticket.status)" class="px-2 py-1 rounded-full text-xs font-medium" x-text="getStatusLabel(ticket.status)"></span>
                                        <span class="flex items-center text-sm text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span x-text="dayjs(ticket.created_at).fromNow()"></span>
                                        </span>
                                    </div>
                                </div>
                                <div x-data="{ open: false, status: '' }" class="relative">
                                    <button @click="open = !open" class="p-2 hover:bg-gray-100 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg py-2 bg-white ring-1 ring-black ring-opacity-5">
                                        <a href="#" class="block px-4 py-2 text-sm text-gray- hover:bg-gray-100">Détails</a>
                                        <form :action="`/contact/${ticket.id}`" method="POST"
                                            @submit.prevent="
                                                const formData = new FormData(event.target);
                                                formData.set('status', status);
                                                fetch($event.target.action, {
                                                    method: 'POST',
                                                    body: formData
                                                })
                                                .then(response => {
                                                    if (response.ok) {
                                                        window.location.reload();
                                                    }
                                                });">
                                            <input type="hidden" name="status" :value="status">
                                            <button @click="status = 'en_cours'" type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Marquer comme lu</button>
                                            <button @click="status = 'archive'" type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Archiver</button>
                                        </form>         
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </main>

</body>
</html>

