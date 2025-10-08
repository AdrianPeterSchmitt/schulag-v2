<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="<?= csrf_header() ?>" content="<?= csrf_hash() ?>">
    <title><?= $title ?? 'SchulAG v2' ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- HTMX -->
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Debug System -->
    <script>
        // Debug Logger für die Konsole
        window.SchulAGDebug = {
            enabled: true, // auf false setzen um Debugging zu deaktivieren
            
            log: function(category, message, data = null) {
                if (!this.enabled) return;
                
                const timestamp = new Date().toLocaleTimeString('de-DE');
                const style = this.getStyle(category);
                
                console.log(
                    `%c[${timestamp}] [${category}]%c ${message}`,
                    style,
                    'color: inherit'
                );
                
                if (data !== null) {
                    console.log('📊 Data:', data);
                }
            },
            
            getStyle: function(category) {
                const styles = {
                    'Alpine': 'background: #77C1D2; color: white; padding: 2px 6px; border-radius: 3px; font-weight: bold',
                    'HTMX': 'background: #3D72A4; color: white; padding: 2px 6px; border-radius: 3px; font-weight: bold',
                    'Modal': 'background: #9B59B6; color: white; padding: 2px 6px; border-radius: 3px; font-weight: bold',
                    'Error': 'background: #E74C3C; color: white; padding: 2px 6px; border-radius: 3px; font-weight: bold',
                    'Success': 'background: #27AE60; color: white; padding: 2px 6px; border-radius: 3px; font-weight: bold',
                    'Info': 'background: #3498DB; color: white; padding: 2px 6px; border-radius: 3px; font-weight: bold'
                };
                return styles[category] || styles['Info'];
            },
            
            error: function(message, error = null) {
                this.log('Error', message, error);
                if (error && error.stack) {
                    console.error('Stack Trace:', error.stack);
                }
            }
        };
        
        // Global Error Handler
        window.addEventListener('error', function(event) {
            SchulAGDebug.error('JavaScript Error aufgetreten', {
                message: event.message,
                filename: event.filename,
                lineno: event.lineno,
                colno: event.colno
            });
        });
    </script>
    
    <!-- Global Alpine Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            SchulAGDebug.log('Alpine', 'Alpine.js wird initialisiert...');
            
            Alpine.store('editModal', {
                show: false,
                schueler: null,
                
                open(data) {
                    SchulAGDebug.log('Modal', 'Modal wird geöffnet', data);
                    this.schueler = data;
                    this.show = true;
                    SchulAGDebug.log('Modal', 'Modal Status: show=' + this.show);
                },
                
                close() {
                    SchulAGDebug.log('Modal', 'Modal wird geschlossen');
                    this.show = false;
                    this.schueler = null;
                }
            });
            
            // Reusable Bestätigungsdialog für htmx (ersetzt Browser confirm)
            Alpine.store('confirm', {
                show: false,
                message: '',
                onConfirm: null,
                open(message, onConfirm) {
                    this.message = message || 'Aktion wirklich ausführen?';
                    this.onConfirm = onConfirm;
                    this.show = true;
                },
                confirm() {
                    if (typeof this.onConfirm === 'function') {
                        this.onConfirm();
                    }
                    this.close();
                },
                close() {
                    this.show = false;
                    this.message = '';
                    this.onConfirm = null;
                }
            });
            
            SchulAGDebug.log('Alpine', 'editModal Store wurde registriert');
        });
        
        // Alpine.js geladen Event
        document.addEventListener('alpine:initialized', () => {
            SchulAGDebug.log('Alpine', '✅ Alpine.js erfolgreich initialisiert!');
            
            // Überprüfe, ob der editModal Store verfügbar ist
            if (Alpine.store('editModal')) {
                SchulAGDebug.log('Alpine', '✅ editModal Store ist verfügbar', Alpine.store('editModal'));
            } else {
                SchulAGDebug.error('editModal Store ist NICHT verfügbar!');
            }
        });
        
        // DOMContentLoaded Event
        document.addEventListener('DOMContentLoaded', () => {
            SchulAGDebug.log('Info', '📄 Seite vollständig geladen');
            
            // Gib eine Übersicht aus
            console.log('%c🎓 SchulAG v2 Debug System', 'font-size: 20px; font-weight: bold; color: #667eea;');
            console.log('%c──────────────────────────────────────', 'color: #667eea;');
            console.log('Verwenden Sie SchulAGDebug.enabled = false um das Debugging zu deaktivieren');
            console.log('Debug-Kategorien: Alpine, HTMX, Modal, Error, Success, Info');
            console.log('%c──────────────────────────────────────', 'color: #667eea;');
            
            // Überprüfe wichtige Komponenten
            SchulAGDebug.log('Info', 'Alpine.js verfügbar: ' + (typeof Alpine !== 'undefined'));
            SchulAGDebug.log('Info', 'HTMX verfügbar: ' + (typeof htmx !== 'undefined'));
            SchulAGDebug.log('Info', 'Tailwind verfügbar: ' + (typeof tailwind !== 'undefined'));
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
        /* Alpine.js Cloak - nur vor Initialisierung verstecken */
        [x-cloak] {
            display: none !important;
        }
        
        /* Sicherstellen, dass Modals sichtbar sind wenn show=true */
        [x-show] {
            display: none;
        }
        
        [x-show][style*="display: flex"] {
            display: flex !important;
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

<!-- Global Confirm Modal -->
<div x-data
     x-show="$store.confirm.show"
     x-cloak
     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900">Bestätigung</h3>
            <button @click="$store.confirm.close()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <p class="text-gray-800" x-text="$store.confirm.message"></p>
            <div class="flex space-x-3 pt-6">
                <button @click="$store.confirm.close()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Abbrechen</button>
                <button @click="$store.confirm.confirm()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">OK</button>
            </div>
        </div>
    </div>
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
        document.addEventListener('htmx:beforeRequest', function(event) {
            SchulAGDebug.log('HTMX', '🚀 Request wird gestartet...', {
                method: event.detail.verb,
                url: event.detail.path
            });
            const loading = document.getElementById('global-loading');
            loading.style.display = 'flex';
        });
        
        document.addEventListener('htmx:afterRequest', function(event) {
            SchulAGDebug.log('HTMX', '✅ Request abgeschlossen', {
                successful: event.detail.successful,
                status: event.detail.xhr?.status
            });
            const loading = document.getElementById('global-loading');
            loading.style.display = 'none';
        });
        
        document.addEventListener('htmx:responseError', function(event) {
            SchulAGDebug.error('HTMX Response Error', {
                status: event.detail.xhr?.status,
                response: event.detail.xhr?.responseText
            });
        });
        
        document.addEventListener('htmx:sendError', function(event) {
            SchulAGDebug.error('HTMX Send Error', {
                error: event.detail.error
            });
        });

        // Verhindere Content-Tausch bei Fehlerstatus (422/4xx/5xx) und zeige stattdessen Toasts
        document.addEventListener('htmx:beforeSwap', function(event) {
            const status = event.detail.xhr?.status || 0;
            if (status >= 400) {
                event.detail.shouldSwap = false;
                const errText = event.detail.xhr?.responseText || '';
                SchulAGDebug.error('Swap bei Fehler unterdrückt', { status, errText });
                try {
                    const json = JSON.parse(errText);
                    const firstMsg = json?.errors ? Object.values(json.errors)[0] : null;
                    if (firstMsg) {
                        showToast?.('Validierung', firstMsg, '⚠️');
                    }
                } catch (_) {
                    // ignore parse errors, already logged
                }
            }
        });
    </script>

    <!-- HTMX Config -->
    <script>
        // Configure HTMX
        document.body.addEventListener('htmx:configRequest', (event) => {
            event.detail.headers['X-Requested-With'] = 'XMLHttpRequest';
            // CSRF Header für alle HTMX-Requests setzen (POST/PUT/DELETE)
            const csrfMeta = document.querySelector('meta[name="<?= csrf_header() ?>"]');
            if (csrfMeta && csrfMeta.content) {
                event.detail.headers['<?= csrf_header() ?>'] = csrfMeta.content;
            }
            SchulAGDebug.log('HTMX', 'Request konfiguriert', {
                method: event.detail.verb,
                path: event.detail.path,
                headers: event.detail.headers,
                parameters: event.detail.parameters
            });
        });
        
        // Show toast notifications
        function showToast(title, message, icon = '✓') {
            SchulAGDebug.log('Info', `Toast: ${title} - ${message}`);
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
            SchulAGDebug.log('HTMX', '🔄 Content wurde getauscht', {
                target: event.detail.target?.id || 'unknown'
            });
            
            // Check for success messages
            const successMsg = event.detail.xhr.getResponseHeader('X-Success-Message');
            if (successMsg) {
                showToast('Erfolg', successMsg, '✅');
            }

            // Re-initialize Alpine.js for dynamically loaded content
            const target = event.detail.target || event.target;
            if (target && window.Alpine && typeof Alpine.initTree === 'function') {
                SchulAGDebug.log('Alpine', 'Initialisiere Alpine für neuen Content...', {
                    target: target.id || target.className
                });
                try {
                    Alpine.mutateDom(() => {
                        Alpine.initTree(target);
                    });
                    SchulAGDebug.log('Alpine', '✅ Alpine für neuen Content initialisiert');
                } catch (error) {
                    SchulAGDebug.error('Fehler beim Initialisieren von Alpine', error);
                }
            }
        });
        
        // Log when content is settled
        document.body.addEventListener('htmx:afterSettle', (event) => {
            SchulAGDebug.log('HTMX', '✨ Content ist vollständig geladen und settled');
        });
    </script>
</body>
</html>
