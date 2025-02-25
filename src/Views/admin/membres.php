<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Admin Dashboard</title>
    <link rel="icon" href="/assets/Logo - Copie.png"/>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/plugin/relativeTime.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/locale/fr.js"></script>
    <script>
        dayjs.extend(window.dayjs_plugin_relativeTime);
        dayjs.locale('fr');

        const members = <?= json_encode($members); ?>;
    </script>
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
    <main class="sm:ml-56 py-6 px-16"
        x-data="{
            members: members,
            selectedTab: 'all',
            searchQuery: '',
            showActionModal: false,
            selectedMember: null,
            newMember: {
                name: '',
                email: '',
                password: '',
                role: 'user',
                avatar: 'https://i.pinimg.com/736x/57/00/c0/5700c04197ee9a4372a35ef16eb78f4e.jpg'
            },
            filteredMembers() {
                return this.members.filter(member => {
                    const matchesSearch = member.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                        member.email.toLowerCase().includes(this.searchQuery.toLowerCase());
                    const matchesTab = this.selectedTab === 'all' || 
                                    (this.selectedTab === 'responsables' && member.role === 'responsable') ||
                                    (this.selectedTab === 'membres' && member.role === 'membre');
                    return matchesSearch && matchesTab;
                });
            },
            currentPage: 1,
            itemsPerPage: 8,
            get totalPages() {
                return Math.ceil(this.filteredMembers().length / this.itemsPerPage);
            },
            get paginatedMembers() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                return this.filteredMembers().slice(start, end);
            },
            nextPage() {
                this.currentPage++;
            },
            prevPage() {
                this.currentPage--;
            }
        }">
        <!-- Header -->
        <div class="">
            <div class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Gestion des Membres</h1>
                    <button @click="showActionModal = true; selectedMember = null;" class="px-2 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center gap-2">
                        <span class="w-6 h-6">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </span>
                        Ajouter membre
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Filters and Search -->
            <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex gap-1">
                    <button @click="selectedTab = 'all'" 
                            :class="selectedTab === 'all' ? 'bg-blue-100 text-blue-700' : 'bg-white text-gray-600'"
                            class="px-4 py-2 rounded-sm shadow-sm hover:bg-blue-50 transition-colors duration-200">
                        Tous
                    </button>
                    <button @click="selectedTab = 'responsables'"
                            :class="selectedTab === 'responsables' ? 'bg-blue-100 text-blue-700' : 'bg-white text-gray-600'"
                            class="px-4 py-2 rounded-sm shadow-sm hover:bg-blue-50 transition-colors duration-200">
                        Responsables
                    </button>
                    <button @click="selectedTab = 'membres'"
                            :class="selectedTab === 'membres' ? 'bg-blue-100 text-blue-700' : 'bg-white text-gray-600'"
                            class="px-4 py-2 rounded-sm shadow-sm hover:bg-blue-50 transition-colors duration-200">
                        Membres
                    </button>
                </div>
                <div></div>
                <div class="relative">
                    <input type="text" 
                           x-model="searchQuery"
                           placeholder="Rechercher un membre..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:border focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-3" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>

            <!-- Members Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Membre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Projets</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="member in paginatedMembers" :key="member.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" :src="member.avatar" alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900" x-text="member.name"></div>
                                            <div class="text-sm text-gray-500" x-text="member.email"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="member.role === 'responsable' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'"
                                        x-text="member.role"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="relative" x-data="{ showTooltip: false }">
                                        <button @mouseenter="showTooltip = true" @mouseleave="showTooltip = false" class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-info-circle"></i>
                                            <span x-text="member.projects.length"></span>
                                        </button>

                                        <!-- Tooltip affiché sur hover -->
                                        <div 
                                            x-show="showTooltip" 
                                            class="absolute left-0 bottom-full mb-2 w-64 p-3 bg-white border border-gray-300 rounded-lg shadow-lg z-50"
                                            x-cloak
                                        >
                                            <!-- Affichage des projets -->
                                            <template x-if="member.projects.length === 0">
                                                <p class="text-sm text-gray-500">Aucun projet disponible</p>
                                            </template>
                                            <template x-if="member.projects.length > 0">
                                                <ul class="space-y-1 text-sm">
                                                    <template x-for="(project, index) in member.projects" :key="project.id">
                                                        <li>
                                                            <span x-text="project.name"></span>
                                                            <!-- Ajouter une ligne horizontale pour séparer les projets -->
                                                            <template x-if="index < member.projects.length - 1">
                                                                <hr class="border-t border-gray-300 my-2">
                                                            </template>
                                                        </li>
                                                    </template>
                                                </ul>
                                            </template>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="member.status === 'actif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                        x-text="member.status"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex gap-2">
                                        <button
                                            @click="selectedMember = member; showActionModal = true" 
                                            class="text-indigo-600 hover:text-indigo-900">Modifier</button>  
                                            <button
                                                class="text-red-600 hover:text-red-900"
                                                @click="
                                                    if (confirm('Êtes-vous sûr ?')) {
                                                        fetch(`/user/${member.id}`, {
                                                            method: 'DELETE'
                                                        })
                                                        .then(response => {
                                                            if (response.ok) {
                                                                window.location.reload();
                                                            }
                                                        });
                                                    }
                                                "
                                                >Supprimer</button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-center items-center space-x-2 py-4 px-6 bg-gray-50">
                <button @click="prevPage" 
                        :disabled="currentPage === 1" 
                        class="px-3 py-1 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 disabled:opacity-50">
                    Précédent
                </button>
                <span x-text="`Page ${currentPage} sur ${totalPages}`" class="text-sm text-gray-600"></span>
                <button @click="nextPage" 
                        :disabled="currentPage === totalPages" 
                        class="px-3 py-1 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 disabled:opacity-50">
                    Suivant
                </button>
            </div>
        </div>

        <!-- Add/Edit Member Modal -->
        <div x-show="showActionModal" 
            @click.self="showActionModal = false"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Ajouter un membre</h2>
                    <button @click="showActionModal = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form class="space-y-4"
                    :action="selectedMember ? `/user/${selectedMember.id}` : '/user'"
                    method="POST"
                    @submit.prevent="
                        const formDatas = new FormData($event.target);
                        fetch($event.target.action, {
                            method: 'POST',
                            body: formDatas
                        })
                        .then(response => {
                            if(response.ok) {
                                showActionModal = false;
                                selectedMember = null;
                                location.reload();
                            } else {
                                throw new Error('Erreur lors de la sauvegarde');
                            }
                        })
                        .catch(error => {
                            console.error(error);
                        });
                    ">
                    <input type="hidden" name="id" :value="selectedMember ? selectedMember.id : ''">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" 
                            name="name"
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm p-2"
                            x-model="newMember.name"
                            :value="selectedMember ? selectedMember.name : ''">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" 
                            name="email"
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm p-2"
                            x-model="newMember.email"
                            :value="selectedMember ? selectedMember.email : ''">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
                        <input type="password" 
                            name="password"
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm p-2"
                            x-model="newMember.password">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Rôle</label>
                        <select 
                        name="role"
                        class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm p-2"
                        x-model="newMember.role"
                        :value="selectedMember ? selectedMember.role : 'user'">
                            <option value="membre">Membre</option>
                            <option value="responsable">Responsable</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Avatar</label>
                        <input type="text" 
                            name="avatar"
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm p-2"
                            x-model="newMember.avatar"
                            :value="selectedMember ? selectedMember.avatar : 'https://i.pinimg.com/736x/57/00/c0/5700c04197ee9a4372a35ef16eb78f4e.jpg'">
                    </div>
                    <div class="flex justify-end gap-4">
                        <button type="reset" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                            Annuler
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md">
                            Sauvegarder
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </main>

</body>
</html>