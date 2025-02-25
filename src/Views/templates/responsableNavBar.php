<nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex justify-between items-center h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="/responsable/home"><img class="h-10 w-auto" src="/assets/Logo.png" alt="Logo"></a>
                </div>
                <div x-data="{ activePage : window.location.pathname }" class="hidden sm:ml-6 sm:flex sm:space-x-4">
                    <a 
                        href="/responsable/home" 
                        class="text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                        :class="activePage === '/responsable/home' ? 'text-blue-500' : 'hover:text-gray-900'"
                        @click="activePage = '/responsable/home'">
                        Tableau de Bord</a>
                    <a 
                        href="/responsable/project" 
                        class="text-gray-600 px-3 py-2 rounded-md text-sm font-medium"
                        :class="activePage === '/responsable/project' ? 'text-blue-500' : 'hover:text-gray-900'"
                        @click="activePage = '/responsable/project'">
                        Mes Projets</a>
                </div>
            </div>
            <div class="flex items-center">
                <div x-data="{ open: false }" class="ml-3 relative">
                    <div>
                        <button @click="open = !open" type="button" class="bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="user-menu-button">
                            <img class="h-10 w-10 rounded-full" src="<?= $_SESSION['user']['avatar'] ?>" alt="Avatar utilisateur">
                        </button>
                    </div>
                    <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                        <button @click="openProfileModal = true" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</button>    
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Paramètres</a>
                        <a href="/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Déconnexion</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>