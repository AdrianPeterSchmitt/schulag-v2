<?php /** @var array $clubs */ ?>
<div id="clubs-list" class="space-y-4">
    <?php if (empty($clubs)): ?>
        <div class="bg-white rounded-xl shadow-md p-12 text-center">
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Noch keine AGs vorhanden</h3>
            <p class="text-gray-600">Erstellen Sie Ihre erste Arbeitsgemeinschaft</p>
        </div>
    <?php else: ?>
        <?php foreach ($clubs as $club): ?>
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition">
                <div class="p-6 flex items-center justify-between">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-900"><?= esc($club['titel']) ?></h3>
                    </div>
                    <div class="flex items-center space-x-2 ml-4">
                        <button class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                @click="$dispatch('open-modal', {modal: 'editClubModal', id: <?= (int)$club['id'] ?>})"
                                title="Bearbeiten">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                        <button hx-delete="<?= base_url('admin/clubs/' . $club['id']) ?>"
                                hx-target="#clubs-list" hx-swap="innerHTML"
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                title="Löschen">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

