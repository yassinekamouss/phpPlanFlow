<div x-show="alert.show" 
    x-transition 
    :class="alert.type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'" 
    class="fixed bottom-5 right-5 w-full max-w-sm p-4 border rounded-lg shadow-lg">
    <div class="flex items-center">
        <!-- Icône pour succès -->
        <svg x-show="alert.type === 'success'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4m0 0l4 4M12 16v1m0 3a4 4 0 11-8 0a4 4 0 018 0z" />
        </svg>
        <!-- Icône pour erreur -->
        <svg x-show="alert.type === 'error'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        <span class="ml-2 text-sm" x-text="alert.message"></span>
    </div>
</div>