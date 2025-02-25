<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/assets/Logo - Copie.png"/>
    <title>Mes Projets</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/plugin/relativeTime.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.7/locale/fr.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <!-- NavBar -->
    <?php require_once __DIR__ . '/../templates/responsableNavBar.php' ;?>

    <!-- Contenu principale -->
    <main 
        x-data="{
            projects: projects,
            selectedProjectMembers: null,
            selectedProjectAction: null,
            viewType: 'grid',
            showMembersModal: false,
            showCerateProjectModal: false,
            showEditProjectModal: false,
            selectedProjectId: null,
            selectedTask: false,
            showProjectTasksModal: false,
            selectedProjectTasks: null,
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
                    return 'bg-green-500'; // Vert pour progression élevée
                }
            }
        }" 
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- En-tête -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Mes Projets</h1>
                <p class="mt-2 text-gray-600">Gérez et suivez vos projets assignés</p>
            </div>
            
            <!-- Contrôles -->
            <div class="flex items-center space-x-4">
                <!-- Toggle vue -->
                <div class="flex items-center bg-white rounded-lg border p-1">
                    <button 
                        @click="viewType = 'grid'" 
                        :class="{'bg-blue-50 text-blue-600': viewType === 'grid'}"
                        class="p-2 rounded-md hover:bg-gray-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </button>
                    <button 
                        @click="viewType = 'list'" 
                        :class="{'bg-blue-50 text-blue-600': viewType === 'list'}"
                        class="p-2 rounded-md hover:bg-gray-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <!-- Nouveau projet -->
                <button
                    @click="showCerateProjectModal = true" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span>Nouveau Projet</span>
                </button>
            </div>
        </div>

        <!-- Vue Grille -->
        <div x-show="viewType === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="project in projects" :key="project.id">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden relative">
                <!-- En-tête du projet -->
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <h3 class="text-lg font-semibold text-gray-900" x-text="project.name"></h3>
                        <div class="flex items-center space-x-2 relative">
                            <!-- Badge statut -->
                            <span :class="getStatusColor(project.status)" 
                            class="px-2.5 py-1 rounded-full text-xs border"
                            x-text="project.status === 'en cours' ? 'En.cours' : project.status === 'termine' ? 'Terminé' : project.status === 'a faire' ? 'A Faire' : 'Annulé'">
                            </span>
                            <!-- Menu -->
                            <button @click="selectedProjectId = selectedProjectId === project.id ? null : project.id"
                                class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                </svg>
                            </button>
                            <!-- Dropdown -->
                            <div x-show="selectedProjectId === project.id"
                                @click.away="selectedProjectId = null" 
                                class="absolute right-0 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50" style="top: 25px;">
                                <button @click="showEditProjectModal = true ; selectedProjectAction = project;" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Editer</button>
                                <button @click="showProjectTasksModal = true ; selectedProjectTasks = project;" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Taches</button>
                                <button
                                    class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    @click="
                                        if (confirm('Êtes-vous sûr ?')) {
                                            fetch(`/project/${project.id}`, {
                                                method: 'DELETE'
                                            })
                                            .then(response => {
                                                if (response.ok) {
                                                    window.location.reload();
                                                }
                                            });
                                        }
                                    "
                                    >Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-600" x-text="project.description"></p>
                </div>

                <!-- Barre de progression -->
                <div class="px-6 py-4 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700" x-text="`${project.progress}%`"></span>
                        <span class="text-sm text-gray-500" x-text="`${project.tasks.length} tâches`"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full" :class="getProgresseColor(project.progress)" :style="`width: ${project.progress}%`"></div>
                    </div>
                </div>

                <!-- Pied de carte -->
                <div class="px-6 py-4 bg-white flex items-center justify-between">
                    <!-- Équipe -->
                    <div class="flex -space-x-2 overflow-hidden">
                        <!-- <img :src="project.project_manager_avatar" class="w-10 h-10 rounded-full border-2 border-white"> -->
                        <template x-for="(member, index) in project.members.slice(0, 2)" :key="index">
                            <img :src="member.avatar" class="w-10 h-10 rounded-full border-2 border-white">
                        </template>
                        <!-- Indicateur pour les membres restants -->
                        <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-white bg-gray-50 text-gray-600 text-xs font-medium cursor-pointer"
                            x-show="true" @click="selectedProjectMembers = project ; showMembersModal = true">
                            <span class="font-bold text-lg">+</span>
                        </div>
                    </div>
                    <!-- Date limite -->
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span x-text="project.end_date"></span>
                    </div>
                </div>
                </div>
            </template>
        </div>

        <!-- Vue Liste -->
        <div x-show="viewType === 'list'" class="bg-white shadow-sm rounded-lg overflow-hidden">
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
                                        <div class="h-2 rounded-full" :class="getProgresseColor(project.progress)" :style="`width: ${project.progress}%`"></div>
                                    </div>
                                    <span class="text-sm text-gray-500" x-text="`${project.progress}%`"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500" x-text="project.end_date"></td>
                            <td class="px-6 py-4 text-right relative">
                                <!-- Menu -->
                                <button @click="selectedProjectId = selectedProjectId === project.id ? null : project.id"
                                    class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </button>
                                <!-- Dropdown -->
                                <div x-show="selectedProjectId === project.id"
                                    @click.away="selectedProjectId = null" 
                                    class="absolute w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50" style="top: 45px; right: 10px;">
                                    <button @click="showEditProjectModal = true ; selectedProjectAction = project;" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Editer</button>
                                    <button @click="showProjectTasksModal = true ; selectedProjectTasks = project;" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Taches</button>
                                    <button
                                        class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        @click="
                                            if (confirm('Êtes-vous sûr ?')) {
                                                fetch(`/user/${project.id}`, {
                                                    method: 'DELETE'
                                                })
                                                .then(response => {
                                                    if (response.ok) {
                                                        window.location.reload();
                                                    }
                                                });
                                            }
                                        "
                                        >Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Afficher un message si la liste des projets est vide -->
        <template x-if="projects.length === 0">
            <div class="text-center text-gray-500">
                Aucun projet disponible pour le moment.
            </div>
        </template>
        
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

        <!-- Modal pour la création des projets -->
        <div x-show="showCerateProjectModal"
            @click.self="showCerateProjectModal = false"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-800">Créer un Nouveau Projet</h2>
                    <button @click="showCerateProjectModal = false" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div x-data="{
                    project: {
                        name: '',
                        description: '',
                        end_date: '',
                        tasks: []
                    },
                    addTask() {
                        this.project.tasks.push({
                            name: '',
                            description: '',
                            start_date: '',
                            end_date: '',
                            assignee_id: '',
                            status: 'a faire'
                        });
                    },
                    removeTask(index) {
                        this.project.tasks.splice(index, 1);
                    },
                   async submitForm() {
                        try {
                            const formData = new FormData();
                            formData.append('name', this.project.name);
                            formData.append('description', this.project.description);
                            formData.append('end_date', this.project.end_date);
                            formData.append('tasks', JSON.stringify(this.project.tasks));
                            const response = await fetch('/project', {
                                method: 'POST',
                                body: formData
                            });
                            const data = await response.json();
                            if (data.success) {
                                // Réinitialiser le formulaire après succès
                                this.project = {
                                    name: '',
                                    description: '',
                                    end_date: '',
                                    tasks: []
                                };
                                showCerateProjectModal = false;
                                alert.type = 'success';
                                alert.message = data.message;
                                alert.show = true;
                                setTimeout(() => alert.show = false, 5000);
                            } else {
                                throw new Error('Erreur lors de la création du projet');
                            }
                        } catch (error) {
                            console.error('Erreur:', error);
                        }
                    }
                }">
                    <form @submit.prevent="submitForm">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Informations du projet -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nom du Projet</label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name"
                                    x-model="project.name"
                                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                    placeholder="Entrez le nom"
                                    required>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea 
                                    x-model="project.description"
                                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    rows="3"
                                    name="description"
                                    placeholder="Entrez la description"
                                    required></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date Limite</label>
                                <input 
                                    type="date" 
                                    x-model="project.end_date"
                                    name="end_date"
                                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    required>
                            </div>

                            <!-- Gestion des tâches -->
                            <div>
                                <div class="flex justify-between items-center mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Tâches</label>
                                    <button 
                                        type="button"
                                        @click="addTask()"
                                        class="flex items-center text-blue-600 hover:text-blue-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        <span class="ml-2">Ajouter une tâche</span>
                                    </button>
                                </div>

                                <!-- Liste des tâches -->
                                <template x-for="(task, index) in project.tasks" :key="index">
                                    <div class="flex flex-col space-y-2 mt-2 bg-gray-50 p-4 rounded-lg">
                                        <!-- Bouton de suppression -->
                                        <button 
                                            type="button"
                                            @click="removeTask(index)"
                                            class="text-right text-red-600 hover:text-red-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>

                                        <input 
                                            type="text" 
                                            x-model="task.name"
                                            class="mt-3 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm"
                                            :placeholder="`Nom de la Tâche ${index + 1}`"
                                            required>

                                        <textarea 
                                            x-model="task.description"
                                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm"
                                            rows="3"
                                            placeholder="Description de la tâche"
                                            required></textarea>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Date de début</label>
                                                <input 
                                                    type="date" 
                                                    x-model="task.start_date"
                                                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm"
                                                    required>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Date de fin</label>
                                                <input 
                                                    type="date" 
                                                    x-model="task.end_date"
                                                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Assignée à</label>
                                                <input 
                                                    x-model="task.assignee_id"
                                                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm"
                                                    required
                                                    placeholder="ID de l'utilisateur">
                                                </input>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Priorité</label>
                                                <select 
                                                    x-model="task.priority"
                                                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm"
                                                    required>
                                                    <option value="basses">Basses</option>
                                                    <option value="moyenne">Moyenne</option>
                                                    <option value="haute">Haute</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="flex space-x-2 justify-end">
                                <button 
                                    type="button"
                                    @click="project = {name: '', description: '', end_date: '', tasks: []}"
                                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                                    Annuler
                                </button>
                                <button 
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Créer le projet
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal pour édité un projet -->
        <div x-show="showEditProjectModal"
            @click.self="showEditProjectModal = false"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-800">Edité le projet</h2>
                    <button @click="showEditProjectModal = false" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>    
                <div x-data="{
                    project: {
                        name: '',
                        description: '',
                        start_date: '',
                        end_date: '',
                        tasks: []
                    },
                    init() {
                        this.$watch('selectedProjectAction', (newVal) => {
                            if (newVal) {
                                this.project = { 
                                    name: newVal.name || '', 
                                    description: newVal.description || '', 
                                    end_date: newVal.start_date || '', 
                                    end_date: newVal.end_date || '', 
                                    tasks: [...(newVal.tasks || [])]
                                };
                            }
                        });
                    },
                    addTask() {
                        this.project.tasks.push({
                            name: '',
                            description: '',
                            start_date: '',
                            end_date: '',
                            assignee_id: '',
                            priority: 'basse'
                        });
                    },
                    removeTask(index) {
                        this.project.tasks.splice(index, 1);
                    },
                    async submitForm() {
                        try {
                            const formData = new FormData();
                            formData.append('name', this.project.name);
                            formData.append('description', this.project.description);
                            formData.append('start_date', this.project.end_date);
                            formData.append('end_date', this.project.end_date);
                            formData.append('tasks', JSON.stringify(this.project.tasks));
                            const response = await fetch(`/project/${selectedProjectAction.id}`, {
                                method: 'POST',
                                body: formData
                            });

                            const data = await response.json();
                            if (data.success) {
                                // Réinitialiser le formulaire après succès
                                this.project = {
                                    name: '',
                                    description: '',
                                    end_date: '',
                                    tasks: []
                                };
                                showEditProjectModal = false;   
                                alert.type = 'success';
                                alert.message = data.message;
                                alert.show = true;
                                setTimeout(() => alert.show = false, 5000);
                            } else {
                                console.log(data.message);
                                throw new Error('Erreur lors de la modification du projet');
                            }
                        } catch (error) {
                            console.error('Erreur:', error);
                        }
                    }
                }" x-init="init()">
                    <form @submit.prevent="submitForm">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Informations du projet -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nom du Projet</label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name"
                                    x-model="project.name"
                                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                    placeholder="Entrez le nom"
                                    required>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea 
                                    x-model="project.description"
                                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    rows="3"
                                    name="description"
                                    placeholder="Entrez la description"
                                    required></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date Limite</label>
                                <input 
                                    type="date"
                                    x-model="project.end_date"
                                    name="end_date"
                                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    required>
                            </div>

                            <!-- Gestion des tâches -->
                            <div>
                                <div class="flex justify-between items-center mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Tâches</label>
                                    <button 
                                        type="button"
                                        @click="addTask()"
                                        class="flex items-center text-blue-600 hover:text-blue-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        <span class="ml-2">Ajouter une tâche</span>
                                    </button>
                                </div>

                                <!-- Liste des tâches -->
                                <template x-for="(task, index) in project?.tasks" :key="index">
                                    <div class="flex flex-col space-y-2 mt-2 bg-gray-50 p-4 rounded-lg">
                                        <!-- Bouton de suppression -->
                                        <button 
                                            type="button"
                                            @click="removeTask(index)"
                                            class="text-right text-red-600 hover:text-red-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>

                                        <input 
                                            type="text" 
                                            x-model="task.name"
                                            class="mt-3 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm"
                                            :placeholder="`Nom de la Tâche ${index + 1}`"
                                            required>

                                        <textarea 
                                            x-model="task.description"
                                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm"
                                            rows="3"
                                            placeholder="Description de la tâche"
                                            required></textarea>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Date de début</label>
                                                <input 
                                                    type="date" 
                                                    x-model="task.start_date"
                                                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm"
                                                    required>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Date de fin</label>
                                                <input 
                                                    type="date" 
                                                    x-model="task.end_date"
                                                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Assignée à</label>
                                                <input 
                                                    x-model="task.assignee_id"
                                                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm"
                                                    required
                                                    placeholder="ID de l'utilisateur">
                                                </input>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Priorité</label>
                                                <select 
                                                    x-model="task.priority"
                                                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm"
                                                    required>
                                                    <option value="basses">Basses</option>
                                                    <option value="moyenne">Moyenne</option>
                                                    <option value="haute">Haute</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="flex space-x-2 justify-end">
                                <button 
                                    type="button"
                                    @click="project = {name: '', description: '', end_date: '', tasks: []}"
                                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                                    Annuler
                                </button>
                                <button 
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Modifier le projet
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
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
                            <button @click="selectedTask = task ; showProjectTasksModal= false;" class="text-gray-400 hover:text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
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
                            <div class="mt-4" x-data="{ newComment: '' }">
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

        <!-- Modal pour modifier les info du profile -->
        <?php require_once __DIR__ . '/../templates/userProfileModal.php'; ?>
        
        <!-- Alert -->
        <?php require_once __DIR__ . '/../templates/userAlert.php'; ?>
    </main>
</body>
</html>