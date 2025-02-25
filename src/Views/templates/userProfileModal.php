<div 
    x-show="openProfileModal" 
    x-transition.opacity 
    class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 space-y-6">
        <!-- Modal Header -->
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold text-gray-800">Mon Profil</h2>
            <button @click="openProfileModal = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Profile Picture -->
        <div class="flex justify-center items-center">
            <div class="text-center">
                <img :src="avatar" alt="User Avatar" class="w-32 h-32 rounded-full ring-indigo-500 ring-2 ring-offset-2 mb-2">
                <span class="text-gray-700 text-sm font-medium capitalize" x-text="role"></span> 
            </div>
        </div>


        <form 
            action="/user/<?php echo $_SESSION['user']['id']; ?>" 
            method="POST"
            @submit.prevent="
                const formData = new FormData($event.target);
                fetch($event.target.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        alert.type = 'success';
                        alert.message = 'Votre profil a été mis à jour avec succès !';
                        alert.show = true;
                        setTimeout(() => alert.show = false, 5000);
                        openProfileModal = false;
                    } else {
                        throw new Error('Network response was not ok.');
                    }
                })
                .catch(error => {
                    alert.type = 'error';
                    alert.message = 'Une erreur est survenue. Veuillez réessayer.';
                    alert.show = true;
                    setTimeout(() => alert.show = false, 5000);
                    console.error('Error:', error);
                });
            ">
            <!-- Name -->
            <input type="hidden" name="role" id="role" x-model="role" hidden>
            <input type="hidden" name="avatar" id="avatar" x-model="avatar" hidden>
            <div class="relative mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                <input type="text" name="name" id="name" x-model="name" class="mt-2 px-4 py-2 w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent">
            </div>

            <!-- email -->
            <div class="relative mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" x-model="email" class="mt-2 px-4 py-2 w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" />
            </div>

            <!-- password -->
            <div class="relative mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" placeholder="Saisir le nouveau mot de passe" id="password" class="mt-2 px-4 py-2 w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" />
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4">
                <button @click="openProfileModal = false" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 focus:outline-none">Annuler</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none">Enregistrer</button>
            </div>
        </form>
    </div>
</div>