<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SchulAG v2</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-md">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-2xl mb-4">
                <span class="text-4xl">🎓</span>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">SchulAG v2</h1>
            <p class="text-white/80">AG-Verwaltungssystem</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Anmelden</h2>

            <!-- Error Messages -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                    <p class="text-red-700"><?= session()->getFlashdata('error') ?></p>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                    <p class="text-green-700"><?= session()->getFlashdata('success') ?></p>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="<?= base_url('login') ?>" class="space-y-6">
                <?= csrf_field() ?>
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        E-Mail-Adresse
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required
                           value="<?= old('email') ?>"
                           placeholder="ihre.email@schule.de"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Passwort
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           placeholder="••••••••"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="remember" 
                               class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-2 text-sm text-gray-600">Angemeldet bleiben</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-purple-800 transition shadow-lg">
                    Anmelden
                </button>
            </form>

            <!-- Info -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    Probleme beim Anmelden? Wenden Sie sich an den Administrator.
                </p>
            </div>
        </div>

        <!-- Test-Credentials (nur Development) -->
        <?php if (ENVIRONMENT === 'development'): ?>
            <div class="mt-6 bg-white/10 backdrop-blur-sm rounded-xl p-4 text-white text-sm">
                <p class="font-semibold mb-2">🔧 Test-Zugangsdaten:</p>
                <p>Email: <code class="bg-white/20 px-2 py-1 rounded">admin@schulag.test</code></p>
                <p>Passwort: <code class="bg-white/20 px-2 py-1 rounded">admin123</code></p>
            </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="mt-8 text-center text-white/60 text-sm">
            <p>&copy; 2025 SchulAG v2 - CodeIgniter 4</p>
        </div>
    </div>

</body>
</html>
