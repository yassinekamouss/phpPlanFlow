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
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>403 - Action non autorisée</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center text-white">
    <div class="text-center px-6">
        <div class="relative">
            <h1 class="text-9xl font-bold tracking-tight text-red-500">403</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <svg class="h-32 w-32 text-gray-700 opacity-20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m7 1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14z" />
                </svg>
            </div>
        </div>
        <p class="mt-6 text-lg">
            Désolé, vous n'avez pas la permission d'accéder à cette page.
        </p>
        <div class="mt-8">
            <a href="<?= $redirectTo; ?>" class="inline-block px-6 py-3 text-sm font-medium text-gray-900 bg-white rounded-lg shadow hover:bg-gray-200 transition">
                Retour à l'accueil
            </a>
            <a href="/contact" class="inline-block ml-4 px-6 py-3 text-sm font-medium text-red-500 bg-gray-800 border border-red-500 rounded-lg shadow hover:bg-red-600 hover:text-white transition">
                Contacter l'administrateur
            </a>
        </div>
        <p class="mt-8 text-gray-500">
            Code erreur <span class="font-semibold">403</span> - Accès interdit.
        </p>
        <!-- ajouter une footer -->
    </div>
</body>
</html>