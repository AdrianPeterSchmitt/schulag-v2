<!-- Statistik-Karten -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Schüler gesamt</p>
                <p class="text-3xl font-bold text-gray-900"><?= count($klasse['schueler']) ?></p>
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
                <p class="text-sm font-medium text-gray-600">Typ G</p>
                <p class="text-3xl font-bold text-gray-900">
                    <?= count(array_filter($klasse['schueler'], fn($s) => $s['typ_gl'] === 'G')) ?>
                </p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <span class="text-2xl font-bold text-green-600">G</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Typ LE</p>
                <p class="text-3xl font-bold text-gray-900">
                    <?= count(array_filter($klasse['schueler'], fn($s) => $s['typ_gl'] === 'LE')) ?>
                </p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <span class="text-xl font-bold text-purple-600">LE</span>
            </div>
        </div>
    </div>
</div>

<!-- Schüler Liste -->
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-900">Schüler-Liste</h2>
    </div>

    <?php if (empty($klasse['schueler'])): ?>
        <!-- Empty State -->
        <div class="p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Noch keine Schüler</h3>
            <p class="text-gray-500">Fügen Sie den ersten Schüler für diese Klasse hinzu</p>
        </div>
    <?php else: ?>
        <!-- Schüler Tabelle -->
        <div id="schueler-list" class="divide-y divide-gray-200">
            <?php foreach ($klasse['schueler'] as $schueler): ?>
                <div class="p-6 hover:bg-gray-50 transition flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Avatar -->
                        <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center text-white font-bold">
                            <?= esc(substr($schueler['name'], 0, 2)) ?>
                        </div>
                        
                        <!-- Info -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900"><?= esc($schueler['name']) ?></h3>
                            <div class="flex items-center space-x-3 text-sm text-gray-600">
                                <span class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <span><?= esc($schueler['typ_gl']) ?></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-2">
                        <button type="button"
                                data-schueler-id="<?= esc($schueler['id']) ?>"
                                data-schueler-name="<?= esc($schueler['name']) ?>"
                                data-schueler-typ="<?= esc($schueler['typ_gl']) ?>"
                                @click="$store.editModal.open({
                                    id: parseInt($el.dataset.schuelerId),
                                    name: $el.dataset.schuelerName,
                                    typ_gl: $el.dataset.schuelerTyp
                                })"
                                class="px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg transition flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <span>Bearbeiten</span>
                        </button>
                        <button type="button"
                                @click="$store.confirm.open(`Schüler '<?= esc($schueler['name']) ?>' wirklich löschen?`, () => { htmx.ajax('DELETE', '<?= base_url('admin/klassen/' . $klasse['id'] . '/schueler/' . $schueler['id']) ?>', { target: '#klasse-content', swap: 'innerHTML' }) })"
                                class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span>Löschen</span>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

