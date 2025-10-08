<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="fade-in">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">AG-Zuteilung Ergebnisse</h1>
            <p class="text-gray-600 mt-2">Durchlauf vom <?= esc($run['created_at']) ?></p>
        </div>
        
        <!-- Export Buttons -->
        <div class="flex space-x-3">
            <a href="<?= base_url('allocation/export/pdf?run_id=' . $run['id']) ?>" 
               class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>PDF Export</span>
            </a>
            
            <a href="<?= base_url('allocation/export/excel?run_id=' . $run['id']) ?>" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Excel Export</span>
            </a>
        </div>
    </div>

    <!-- Run Selector -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">Andere Durchläufe anzeigen</h3>
            <select onchange="window.location.href = '<?= base_url('allocation/results?run_id=') ?>' + this.value" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">Neuesten Durchlauf auswählen...</option>
                <?php foreach ($allRuns as $runOption): ?>
                    <option value="<?= $runOption['id'] ?>" <?= $runOption['id'] == $run['id'] ? 'selected' : '' ?>>
                        <?= esc($runOption['created_at']) ?> 
                        (<?= $runOption['allocated_count'] ?> Zuteilungen)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Statistiken -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Zugewiesene Schüler</p>
                    <p class="text-2xl font-bold text-gray-900"><?= count($run['results']) ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">1. Wunsch erfüllt</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?php
                        $firstChoice = array_filter($run['results'], function($r) { return $r['choice_priority'] == 1; });
                        echo count($firstChoice);
                        ?>
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <span class="text-green-600 text-xl">🥇</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">2. Wunsch erfüllt</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?php
                        $secondChoice = array_filter($run['results'], function($r) { return $r['choice_priority'] == 2; });
                        echo count($secondChoice);
                        ?>
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <span class="text-yellow-600 text-xl">🥈</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">3. Wunsch erfüllt</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?php
                        $thirdChoice = array_filter($run['results'], function($r) { return $r['choice_priority'] == 3; });
                        echo count($thirdChoice);
                        ?>
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <span class="text-orange-600 text-xl">🥉</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Ergebnisse nach Klassen -->
    <?php if (empty($resultsByKlasse)): ?>
        <div class="bg-white rounded-xl shadow-md p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Keine Ergebnisse verfügbar</h3>
            <p class="text-gray-500">Für diesen Durchlauf wurden keine Zuteilungen gefunden</p>
        </div>
    <?php else: ?>
        <div class="space-y-8">
            <?php foreach ($resultsByKlasse as $klasseData): ?>
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <!-- Klassen Header -->
                    <div class="bg-gradient-to-r from-primary to-secondary text-white p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold">Klasse <?= esc($klasseData['klasse']['name']) ?></h3>
                                <p class="text-primary-100">
                                    <?= esc($klasseData['klasse']['klassenleitung']) ?> • 
                                    <?= count($klasseData['allocations']) ?> Schüler zugeteilt
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-primary-100">Jahrgang <?= esc($klasseData['klasse']['jahrgang']) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Schüler Liste -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($klasseData['allocations'] as $allocation): ?>
                                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-primary">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-medium text-gray-900"><?= esc($allocation['student']['name']) ?></h4>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                  <?= $allocation['choice_priority'] == 1 ? 'bg-green-100 text-green-800' : 
                                                      ($allocation['choice_priority'] == 2 ? 'bg-yellow-100 text-yellow-800' : 
                                                       'bg-orange-100 text-orange-800') ?>">
                                            <?= $allocation['choice_priority'] ?>. Wunsch
                                        </span>
                                    </div>
                                    
                                    <div class="text-sm text-gray-600">
                                        <p class="font-medium"><?= esc($allocation['offer']['club']['titel']) ?></p>
                                        <p class="text-xs">AG: <?= esc($allocation['offer']['club']['lehrkraft']) ?></p>
                                    </div>

                                    <?php if (!empty($allocation['student']['choices'])): ?>
                                        <div class="mt-3 pt-3 border-t border-gray-200">
                                            <p class="text-xs text-gray-500 mb-1">Alle Wünsche:</p>
                                            <div class="space-y-1">
                                                <?php foreach ($allocation['student']['choices'] as $choice): ?>
                                                    <div class="flex items-center justify-between text-xs">
                                                        <span class="text-gray-600">
                                                            <?= $choice['priority'] == 'no_participation' ? 'Nimmt nicht teil' : $choice['priority'] . '. Wunsch' ?>
                                                        </span>
                                                        <?php if ($choice['priority'] !== 'no_participation' && !empty($choice['offer'])): ?>
                                                            <span class="<?= $choice['offer']['id'] == $allocation['offer']['id'] ? 'font-medium text-primary' : 'text-gray-400' ?>">
                                                                <?= esc($choice['offer']['club']['titel']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
