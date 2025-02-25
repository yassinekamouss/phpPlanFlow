<aside class="fixed top-0 left-0 z-40 w-56 h-screen bg-gray-700 text-white" 
    x-data="{ activeLink: window.location.pathname }">
    <div class="px-4 py-8 h-full">
        <h2 class="mt-6 text-center text-2xl font-bold">Admin Pro</h2>
        <div class="flex flex-col justify-between h-full">
            <ul class="mt-20 space-y-2">
                <li>
                    <a href="/admin/home" 
                    class="flex items-center px-4 py-3 rounded-lg"
                    :class="activeLink === '/admin/home' ? 'bg-blue-500' : 'hover:bg-gray-800'"
                    @click="activeLink = '/admin/home'">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                </svg>
                            </span>
                            <span>Dashboard</span>
                        </div>  
                    </a>
                </li>
                <li>
                    <a href="/admin/project" 
                    class="flex items-center px-4 py-3 rounded-lg"
                    :class="activeLink === '/admin/project' ? 'bg-blue-500' : 'hover:bg-gray-800'"
                    @click="activeLink = '/admin/project'">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                </svg>
                            </span>
                            <span>Projets</span>
                        </div>  
                    </a>
                </li>
                <li>
                    <a href="/admin/membres" 
                    class="flex items-center px-4 py-3 rounded-lg"
                    :class="activeLink === '/admin/membres' ? 'bg-blue-500' : 'hover:bg-gray-800'"
                    @click="activeLink = '/admin/membres'">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                </svg>
                            </span>
                            <span>Membres</span>
                        </div>  
                    </a>
                </li>
                <li>
                    <a href="/admin/support" 
                    class="flex items-center px-4 py-3 rounded-lg"
                    :class="activeLink === '/admin/support' ? 'bg-blue-500' : 'hover:bg-gray-800'"
                    @click="activeLink = '/admin/support'">
                    <div class="flex items-center gap-2">
                            <span class="w-6 h-6">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75a4.5 4.5 0 0 1-4.884 4.484c-1.076-.091-2.264.071-2.95.904l-7.152 8.684a2.548 2.548 0 1 1-3.586-3.586l8.684-7.152c.833-.686.995-1.874.904-2.95a4.5 4.5 0 0 1 6.336-4.486l-3.276 3.276a3.004 3.004 0 0 0 2.25 2.25l3.276-3.276c.256.565.398 1.192.398 1.852Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.867 19.125h.008v.008h-.008v-.008Z" />
                                </svg>
                            </span>
                            <span>Support</span>
                        </div>  
                    </a>
                </li>
                <li>
                    <a href="/admin/rapport" 
                    class="flex items-center px-4 py-3 rounded-lg"
                    :class="activeLink === '/admin/rapport' ? 'bg-blue-500' : 'hover:bg-gray-800'"
                    @click="activeLink = '/admin/rapport'">
                    <div class="flex items-center gap-2">
                            <span class="w-6 h-6">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            </span>
                            <span>Rapport</span>
                        </div>  
                    </a>
                </li>
                <li>
                    <a href="/admin/calander" 
                    class="flex items-center px-4 py-3 rounded-lg"
                    :class="activeLink === '/admin/calander' ? 'bg-blue-500' : 'hover:bg-gray-800'"
                    @click="activeLink = '/admin/calander'">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                            </span>
                            <span>Calendrier</span>
                        </div>  
                    </a>
                </li>
            </ul>
            <!-- Continuez pour les autres liens ... -->
            <div class="mb-24">
                <a href="/logout" class="text-end flex items-center px-4 py-3 rounded-lg hover:bg-gray-800">
                    <span class="w-6 h-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                        </svg>
                    </span>
                    <span>Déconnexion</span>
                </a>
            </div>
        </div>
    </div>
</aside>