<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="fade-in">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Verwaltung</h1>
        <p class="text-gray-600 mt-2">Klassen, Schüler und AGs verwalten</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Klassen Card -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Klassen</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1"><?= count($klassen) ?></p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
            <a href="<?= base_url('admin/klassen') ?>" class="mt-4 inline-block text-blue-600 hover:text-blue-700 text-sm font-medium">
                Verwalten →
            </a>
        </div>

        <!-- Schüler Card -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Schüler</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1"><?= $schueler_count ?></p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <a href="<?= base_url('admin/klassen') ?>" class="mt-4 inline-block text-green-600 hover:text-green-700 text-sm font-medium">
                Verwalten →
            </a>
        </div>

        <!-- AGs Card -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">AGs</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1"><?= count($clubs) ?></p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path>
                    </svg>
                </div>
            </div>
            <a href="<?= base_url('admin/clubs') ?>" class="mt-4 inline-block text-purple-600 hover:text-purple-700 text-sm font-medium">
                Verwalten →
            </a>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Schnellzugriff</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="<?= base_url('admin/klassen') ?>" 
               class="flex items-center space-x-3 p-4 rounded-lg border-2 border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition">
                <span class="text-2xl">📚</span>
                <div>
                    <p class="font-semibold text-gray-900">Klassen verwalten</p>
                    <p class="text-sm text-gray-600">Klassen anlegen und bearbeiten</p>
                </div>
            </a>

            <a href="<?= base_url('admin/clubs') ?>" 
               class="flex items-center space-x-3 p-4 rounded-lg border-2 border-gray-200 hover:border-purple-500 hover:bg-purple-50 transition">
                <span class="text-2xl">⚽</span>
                <div>
                    <p class="font-semibold text-gray-900">AGs verwalten</p>
                    <p class="text-sm text-gray-600">AG-Angebote pflegen</p>
                </div>
            </a>

            <a href="<?= base_url('allocation') ?>" 
               class="flex items-center space-x-3 p-4 rounded-lg border-2 border-gray-200 hover:border-green-500 hover:bg-green-50 transition">
                <span class="text-2xl">🎲</span>
                <div>
                    <p class="font-semibold text-gray-900">Losverfahren</p>
                    <p class="text-sm text-gray-600">Zuteilung durchführen</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Activity (Optional) -->
    <div class="mt-8 bg-white rounded-xl shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Letzte Aktivitäten</h2>
        <div class="space-y-3">
            <div class="flex items-center space-x-3 text-sm">
                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                <span class="text-gray-600"><?= count($klassen) ?> Klassen im System</span>
            </div>
            <div class="flex items-center space-x-3 text-sm">
                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                <span class="text-gray-600"><?= $schueler_count ?> Schüler registriert</span>
            </div>
            <div class="flex items-center space-x-3 text-sm">
                <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                <span class="text-gray-600"><?= count($clubs) ?> AGs verfügbar</span>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
