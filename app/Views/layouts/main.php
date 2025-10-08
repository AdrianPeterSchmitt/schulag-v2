<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SchulAG v2' ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- HTMX -->
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Global Alpine Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('editModal', {
                show: false,
                schueler: null,
                
                open(data) {
                    this.schueler = data;
                    this.show = true;
                },
                
                close() {
                    this.show = false;
                    this.schueler = null;
                }
            });
        });
    </script>
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#667eea',
                        secondary: '#764ba2',
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Alpine.js Cloak */
        [x-cloak] {
            display: none !important;
        }
        
        /* HTMX Loading Indicator */
        .htmx-indicator {
            display: none;
        }
        .htmx-request .htmx-indicator {
            display: inline-block;
        }
        .htmx-request.htmx-indicator {
            display: inline-block;
        }
        
        /* Smooth transitions */
        .htmx-swapping {
            opacity: 0;
            transition: opacity 200ms ease-out;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-primary to-secondary text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="<?= base_url() ?>" class="flex items-center space-x-2">
                        <span class="text-2xl">🎓</span>
                        <span class="font-bold text-xl">SchulAG v2</span>
                    </a>
                    
                    <div class="hidden md:flex space-x-4">
                        <a href="<?= base_url('admin') ?>" 
                           class="px-3 py-2 rounded-md hover:bg-white/20 transition">
                            Verwaltung
                        </a>
                        <a href="<?= base_url('klassen') ?>" 
                           class="px-3 py-2 rounded-md hover:bg-white/20 transition">
                            Klassen
                        </a>
                        <a href="<?= base_url('allocation') ?>" 
                           class="px-3 py-2 rounded-md hover:bg-white/20 transition">
                            Losverfahren
                        </a>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <?php if (session()->get('logged_in')): ?>
                        <span class="text-sm opacity-90"><?= esc(session()->get('user_name')) ?></span>
                        <a href="<?= base_url('logout') ?>" 
                           class="px-4 py-2 bg-white/20 rounded-lg hover:bg-white/30 transition">
                            Logout
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" 
                           class="px-4 py-2 bg-white/20 rounded-lg hover:bg-white/30 transition">
                            Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Toast Notifications -->
    <div id="toast-container" 
         class="fixed bottom-4 right-4 z-50 space-y-2"
         x-data="{ toasts: [] }"
         @toast.window="toasts.push($event.detail); setTimeout(() => toasts.shift(), 3000)">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-x-full opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0 opacity-100"
                 x-transition:leave-end="translate-x-full opacity-0"
                 class="bg-white rounded-lg shadow-lg p-4 max-w-sm">
                <div class="flex items-center space-x-3">
                    <span x-text="toast.icon" class="text-2xl"></span>
                    <div>
                        <p class="font-semibold" x-text="toast.title"></p>
                        <p class="text-sm text-gray-600" x-text="toast.message"></p>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Global Loading Indicator (hidden by default, shown only during HTMX requests) -->
    <div id="global-loading" 
         class="htmx-indicator fixed inset-0 bg-black/50 items-center justify-center z-50"
         style="display: none;">
        <div class="bg-white rounded-lg p-6 shadow-2xl">
            <div class="flex items-center space-x-3">
                <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-700 font-medium">Lädt...</span>
            </div>
        </div>
    </div>
    
    <!-- HTMX Loading Events -->
    <script>
        document.addEventListener('htmx:beforeRequest', function() {
            const loading = document.getElementById('global-loading');
            loading.style.display = 'flex';
        });
        document.addEventListener('htmx:afterRequest', function() {
            const loading = document.getElementById('global-loading');
            loading.style.display = 'none';
        });
    </script>

    <!-- HTMX Config -->
    <script>
        // Configure HTMX
        document.body.addEventListener('htmx:configRequest', (event) => {
            event.detail.headers['X-Requested-With'] = 'XMLHttpRequest';
        });
        
        // Show toast notifications
        function showToast(title, message, icon = '✓') {
            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    id: Date.now(),
                    title: title,
                    message: message,
                    icon: icon
                }
            }));
        }
        
        // Listen for HTMX events
        document.body.addEventListener('htmx:afterSwap', (event) => {
            // Check for success messages
            const successMsg = event.detail.xhr.getResponseHeader('X-Success-Message');
            if (successMsg) {
                showToast('Erfolg', successMsg, '✅');
            }
            
            // Re-attach edit button listeners nach HTMX-Swap
            attachEditButtonListeners();
        });
        
        // Event-Listener für Edit-Buttons (für dynamisch geladene Buttons)
        function attachEditButtonListeners() {
            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const data = JSON.parse(this.getAttribute('data-schueler'));
                    
                    // Trigger Alpine Store direkt
                    if (window.Alpine && window.Alpine.store) {
                        window.Alpine.store('editModal').open(data);
                    }
                });
            });
        }
        
        // Initial attachment beim Laden
        document.addEventListener('DOMContentLoaded', attachEditButtonListeners);
    </script>
</body>
</html>
