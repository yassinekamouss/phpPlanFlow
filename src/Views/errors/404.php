<?php 
    session_start();
    $redirectTo = '/forbidden';
    if(isset($_SESSION['user'])) {
        switch($_SESSION['user']['role']) {
            case 'membre':
                $redirectTo = '/user/home';
                break;
            case 'responsable':
                $redirectTo = '/responsable/home';
                break;
            case 'admin':
                $redirectTo = '/admin/home';
                break;
            default:
                $redirectTo = '/forbidden';
                break;
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page non trouvée</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
    <body class="bg-gray-900 min-h-screen flex items-center justify-center text-white">
        <div class="text-center px-6">
            <div class="relative">
                <h1 class="text-9xl font-bold tracking-tight text-indigo-500">404</h1>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="h-32 w-32 text-gray-700 opacity-20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m0 0l-6-6m6 6H3m16 0a2 2 0 012 2v4a2 2 0 01-2 2m0 0a2 2 0 012 2v4a2 2 0 01-2 2" />
                    </svg>
                </div>
            </div>
            <p class="mt-6 text-lg">
                Oups ! La page que vous recherchez n'existe pas ou a été déplacée.
            </p>
            <div class="mt-8">
                <a href="<?= $redirectTo; ?>" class="inline-block px-6 py-3 text-sm font-medium text-gray-900 bg-white rounded-lg shadow hover:bg-gray-200 transition">
                    Retour à l'accueil
                </a>
                <a href="/contact" class="inline-block ml-4 px-6 py-3 text-sm font-medium text-indigo-500 bg-gray-800 border border-indigo-500 rounded-lg shadow hover:bg-indigo-600 hover:text-white transition">
                    Signaler un problème
                </a>
            </div>
            <p class="mt-8 text-gray-500">
                Code erreur <span class="font-semibold">404</span> - Page introuvable.
            </p>
        </div>
    </body>
</html>
