<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Contact</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">

    <div class="max-w-4xl mx-auto p-8 mt-8">
        <div x-data="{ submitted: false, error: false }">
            <!-- Title -->
            <h1 class="text-3xl font-semibold text-center text-gray-800 mb-8">Contacter l'administrateur</h1>
            
            <!-- Form -->
            <form 
                method="POST"
                action="/contact"
                @submit.prevent="
                    const formData = new FormData($event.target);    
                    fetch($event.target.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response =>{
                        if(response.ok){
                            submitted = true;
                            $event.target.reset();
                        }else {
                                error = true;
                                $event.target.reset();
                                throw new Error('Erreur lors de la sauvegarde');
                            }
                        })
                    .catch(error => {
                        console.error(error);
                    });
                " 
                class="bg-white p-6 rounded-lg shadow-sm space-y-6">
                <div class="flex flex-col">
                    <label for="name" class="text-gray-700 text-sm font-medium">Nom</label>
                    <input type="text" name="name" id="name" placeholder="Votre nom" class="mt-1 p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="flex flex-col">
                    <label for="email" class="text-gray-700 text-sm font-medium">Email</label>
                    <input type="email" name="email" id="email" placeholder="Votre email" class="mt-1 p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="flex flex-col">
                    <label for="subject" class="text-gray-700 text-sm font-medium">Sujet</label>
                    <input type="subject" name="subject" id="subject" placeholder="Sujet de contact" class="mt-1 p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="flex flex-col">
                    <label for="message" class="text-gray-700 text-sm font-medium">Message</label>
                    <textarea id="message" name="message" rows="6" placeholder="Votre message..." class="mt-1 p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                </div>
                <div class="flex justify-start">
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">Envoyer le message</button>
                </div>
            </form>

            <!-- Confirmation Modal -->
            <div x-show="submitted" x-transition class="fixed inset-0 bg-gray-800 bg-opacity-70 flex justify-center items-center z-50">
                <div class="bg-white p-8 rounded-lg shadow-lg w-96 text-center">
                    <h2 class="text-xl font-semibold text-gray-800">Message envoyé ✅</h2>
                    <p class="mt-2 text-gray-600">Nous avons bien reçu votre message et nous vous répondrons dans les plus brefs délais.</p>
                    <div class="mt-4">
                        <button @click="submitted = false" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">Fermer</button>
                    </div>
                </div>
            </div>
            <!-- Error Modal -->
            <div x-show="error" x-transition class="fixed inset-0 bg-gray-800 bg-opacity-70 flex justify-center items-center z-50">
                <div class="bg-white p-8 rounded-lg shadow-lg w-96 text-center">
                    <h2 class="text-xl font-semibold text-gray-800">Message n'est pas envoyé ❌</h2>
                    <p class="mt-2 text-gray-600">Une erreur est survenue lors de l'envoi de votre message.</p>
                    <div class="mt-4">
                        <button @click="error = false" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
