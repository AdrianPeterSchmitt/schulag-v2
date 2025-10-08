<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="fade-in">
    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-900">AG-Zuteilung</h1>
        <p class="text-gray-600 mt-2">Schuljahr <?= esc($schoolyear) ?></p>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Schüler Status -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Schüler mit Wahlen</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?= $stats['students_with_choices'] ?> / <?= $stats['total_students'] ?>
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" 
                         style="width: <?= $stats['total_students'] > 0 ? ($stats['students_with_choices'] / $stats['total_students'] * 100) : 0 ?>%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    <?= $stats['total_students'] > 0 ? round($stats['students_with_choices'] / $stats['total_students'] * 100, 1) : 0 ?>% vollständig
                </p>
            </div>
        </div>

        <!-- Klassen Status -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Klassen vollständig</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?= $stats['klassen_complete'] ?> / <?= $stats['klassen_total'] ?>
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" 
                         style="width: <?= $stats['klassen_total'] > 0 ? ($stats['klassen_complete'] / $stats['klassen_total'] * 100) : 0 ?>%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    <?= $stats['klassen_total'] > 0 ? round($stats['klassen_complete'] / $stats['klassen_total'] * 100, 1) : 0 ?>% vollständig
                </p>
            </div>
        </div>

        <!-- AG Angebote -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">AG-Angebote</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $stats['total_offers'] ?></p>
                    <p class="text-xs text-gray-500"><?= $stats['total_capacity'] ?> Plätze</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Zuteilungen -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Zuteilungen</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $stats['allocations_done'] ?></p>
                    <p class="text-xs text-gray-500">Letzte Durchläufe</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Hauptaktionen -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Losverfahren -->
        <div class="bg-white rounded-xl shadow-md p-8">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-primary to-secondary rounded-xl text-white text-2xl font-bold flex items-center justify-center mx-auto mb-4">
                    🎲
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Losverfahren starten</h3>
                <p class="text-gray-600">Führt die automatische AG-Zuteilung basierend auf den Schülerwünschen durch</p>
            </div>

            <!-- Prüfungen -->
            <div class="space-y-3 mb-6">
                <div class="flex items-center space-x-3">
                    <?php if ($stats['klassen_complete'] === $stats['klassen_total']): ?>
                        <span class="text-green-600 text-xl">✅</span>
                        <span class="text-sm text-gray-700">Alle Klassen haben Wahlen abgegeben</span>
                    <?php else: ?>
                        <span class="text-orange-600 text-xl">⏳</span>
                        <span class="text-sm text-gray-700"><?= $stats['klassen_total'] - $stats['klassen_complete'] ?> Klassen noch nicht vollständig</span>
                    <?php endif; ?>
                </div>

                <div class="flex items-center space-x-3">
                    <?php if ($stats['total_capacity'] >= $stats['total_students']): ?>
                        <span class="text-green-600 text-xl">✅</span>
                        <span class="text-sm text-gray-700">Ausreichend AG-Plätze verfügbar</span>
                    <?php else: ?>
                        <span class="text-red-600 text-xl">❌</span>
                        <span class="text-sm text-gray-700">Zu wenige AG-Plätze (<?= $stats['total_capacity'] ?> / <?= $stats['total_students'] ?>)</span>
                    <?php endif; ?>
                </div>
            </div>

            <button hx-post="<?= base_url('allocation/run') ?>"
                    hx-target="#lottery-result"
                    hx-indicator="#lottery-loading"
                    <?= ($stats['klassen_complete'] !== $stats['klassen_total'] || $stats['total_capacity'] < $stats['total_students']) ? 'disabled' : '' ?>
                    class="w-full px-6 py-3 bg-gradient-to-r from-primary to-secondary text-white rounded-lg hover:shadow-lg transition flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="lottery-loading" class="htmx-indicator">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                <span>Losverfahren starten</span>
            </button>

            <div id="lottery-result" class="mt-4"></div>
        </div>

        <!-- Schnellzugriff -->
        <div class="bg-white rounded-xl shadow-md p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Schnellzugriff</h3>
            
            <div class="space-y-4">
                <a href="<?= base_url('allocation/results') ?>" 
                   class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Ergebnisse anzeigen</p>
                            <p class="text-sm text-gray-600">AG-Zuteilungen einsehen</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="<?= base_url('allocation/swaps') ?>" 
                   class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Tausche verwalten</p>
                            <p class="text-sm text-gray-600">Manuelle Zuteilungsänderungen</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="<?= base_url('allocation/statistics') ?>" 
                   class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Statistiken</p>
                            <p class="text-sm text-gray-600">Auswertungen und Berichte</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Klassen Status -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Klassen-Status</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($klassenStatus as $klasse): ?>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Klasse <?= esc($klasse['name']) ?></p>
                        <p class="text-sm text-gray-600">
                            <?= $klasse['completed_students'] ?> / <?= $klasse['total_students'] ?> Schüler
                        </p>
                    </div>
                    <div class="text-right">
                        <?php if ($klasse['is_complete']): ?>
                            <span class="text-green-600 text-xl">✅</span>
                        <?php else: ?>
                            <span class="text-orange-600 text-xl">⏳</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
// Listen for lottery completion
document.body.addEventListener('htmx:afterSwap', function(event) {
    if (event.detail.pathInfo.requestPath.includes('allocation/run')) {
        // Reload page after successful lottery to show updated stats
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    }
});
</script>

<?= $this->endSection() ?>
