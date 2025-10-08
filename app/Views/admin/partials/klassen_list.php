<?php if (empty($klassen)): ?>
    <div class="p-12 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
        </svg>
        <p class="text-gray-600 text-lg font-medium">Noch keine Klassen vorhanden</p>
        <p class="text-gray-500 text-sm mt-2">Legen Sie Ihre erste Klasse an</p>
    </div>
<?php else: ?>
    <div class="divide-y divide-gray-200">
        <?php foreach ($klassen as $klasse): ?>
            <div class="p-6 hover:bg-gray-50 transition flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center text-white font-bold text-lg">
                        <?= esc($klasse['jahrgang']) ?>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg"><?= esc($klasse['name']) ?></h3>
                        <?php if (!empty($klasse['klassenleitung'])): ?>
                            <p class="text-sm text-gray-600">👤 <?= esc($klasse['klassenleitung']) ?></p>
                        <?php endif; ?>
                        <p class="text-xs text-gray-500 mt-1">Jahrgang <?= esc($klasse['jahrgang']) ?></p>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <button type="button"
                            x-data
                            data-id="<?= (int)$klasse['id'] ?>"
                            data-name="<?= esc($klasse['name']) ?>"
                            data-jahrgang="<?= (int)$klasse['jahrgang'] ?>"
                            data-klassenleitung="<?= esc($klasse['klassenleitung'] ?? '') ?>"
                            @click="$dispatch('open-edit-klasse', { id: +$el.dataset.id, name: $el.dataset.name, jahrgang: +$el.dataset.jahrgang, klassenleitung: $el.dataset.klassenleitung })"
                            class="px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Bearbeiten</span>
                    </button>
                    <a href="<?= base_url('admin/klassen/' . $klasse['id']) ?>" 
                       class="px-4 py-2 text-primary hover:bg-primary/10 rounded-lg transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Schüler</span>
                    </a>

                    <button type="button"
                            @click="$store.confirm.open(`Klasse '<?= esc($klasse['name']) ?>' wirklich löschen? Alle Schüler werden ebenfalls gelöscht!`, () => { htmx.ajax('DELETE', '<?= base_url('admin/klassen/' . $klasse['id']) ?>', { target: '#klassen-list', swap: 'innerHTML' }) })"
                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
