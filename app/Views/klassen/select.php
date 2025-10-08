<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="fade-in">
    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-900">Klasse auswählen</h1>
        <p class="text-gray-600 mt-2">Wählen Sie Ihre Klasse aus, um AG-Wünsche zu verwalten</p>
    </div>

    <?php if (empty($klassen)): ?>
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-md p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Keine Klassen vorhanden</h3>
            <p class="text-gray-500">Bitte wenden Sie sich an den Administrator</p>
        </div>
    <?php else: ?>
        <!-- Klassen Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($klassen as $klasse): ?>
                <a href="<?= base_url('klassen/' . $klasse['id']) ?>" 
                   class="group bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border-2 border-transparent hover:border-primary/20">
                    <div class="p-6">
                        <!-- Klassen Icon -->
                        <div class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-primary to-secondary rounded-xl text-white text-2xl font-bold mb-4 mx-auto">
                            <?= esc($klasse['jahrgang']) ?>
                        </div>

                        <!-- Klassen Info -->
                        <div class="text-center">
                            <h3 class="text-xl font-bold text-gray-900 group-hover:text-primary transition">
                                Klasse <?= esc($klasse['name']) ?>
                            </h3>
                            
                            <?php if (!empty($klasse['klassenleitung'])): ?>
                                <p class="text-sm text-gray-600 mt-1">
                                    👤 <?= esc($klasse['klassenleitung']) ?>
                                </p>
                            <?php endif; ?>

                            <p class="text-sm text-gray-500 mt-2">
                                Jahrgang <?= esc($klasse['jahrgang']) ?>
                            </p>

                            <!-- Schüler Count -->
                            <div class="mt-4 flex items-center justify-center space-x-4 text-sm">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    <span class="text-gray-600"><?= $klasse['schueler_count'] ?? 0 ?> Schüler</span>
                                </div>

                                <div class="flex items-center space-x-1">
                                    <?php if ($klasse['is_complete'] ?? false): ?>
                                        <span class="text-green-600">✅ Vollständig</span>
                                    <?php else: ?>
                                        <span class="text-orange-600">⏳ In Bearbeitung</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Hover Arrow -->
                        <div class="mt-4 text-center">
                            <div class="inline-flex items-center text-primary group-hover:translate-x-1 transition-transform">
                                <span class="text-sm font-medium">Wahlen verwalten</span>
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Info Box -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
            <div class="flex items-start space-x-3">
                <svg class="w-6 h-6 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h4 class="font-medium text-blue-900">Informationen zur AG-Wahl</h4>
                    <ul class="mt-2 text-sm text-blue-800 space-y-1">
                        <li>• Jeder Schüler kann 3 AG-Wünsche angeben oder "Nimmt nicht teil" wählen</li>
                        <li>• Die Zuteilung erfolgt nach dem Losverfahren</li>
                        <li>• Nach Abschluss der Wahlen können manuelle Tausche vorgenommen werden</li>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
