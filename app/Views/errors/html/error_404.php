<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Seite nicht gefunden</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-600 to-blue-500 min-h-screen flex items-center justify-center p-4">
    
    <div class="text-center">
        <div class="text-white mb-8">
            <h1 class="text-9xl font-bold mb-4">404</h1>
            <h2 class="text-3xl font-semibold mb-2">Seite nicht gefunden</h2>
            <p class="text-xl opacity-90">Die angeforderte Seite existiert nicht.</p>
        </div>
        
        <a href="<?= base_url() ?>" 
           class="inline-block px-8 py-3 bg-white text-purple-600 font-semibold rounded-lg hover:bg-gray-100 transition shadow-lg">
            Zurück zur Startseite
        </a>
    </div>

</body>
</html>