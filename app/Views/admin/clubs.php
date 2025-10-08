<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="fade-in">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">AG-Verwaltung</h1>
            <p class="text-gray-600 mt-2">Arbeitsgemeinschaften und Angebote verwalten</p>
        </div>
        
        <!-- Neue AG Button -->
        <button @click="$dispatch('open-modal', {modal: 'newClubModal'})"
                class="px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-purple-800 transition shadow-lg flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Neue AG</span>
        </button>
    </div>

    <!-- Statistik-Karten -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Gesamt AGs</p>
                    <p class="text-2xl font-bold text-gray-900"><?= count($clubs) ?></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Aktive Angebote</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $activeOffersCount ?></p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Gesamt-Kapazität</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $totalCapacity ?></p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Schuljahr</p>
                    <p class="text-2xl font-bold text-gray-900">2024/25</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- AG-Liste -->
    <div id="clubs-list" class="space-y-4">
        <?php if (empty($clubs)): ?>
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Noch keine AGs vorhanden</h3>
                <p class="text-gray-600 mb-6">Erstellen Sie Ihre erste Arbeitsgemeinschaft</p>
                <button @click="$dispatch('open-modal', {modal: 'newClubModal'})"
                        class="px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition">
                    Erste AG erstellen
                </button>
            </div>
        <?php else: ?>
            <?php foreach ($clubs as $club): ?>
                <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <!-- Club Info -->
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-3">
                                    <h3 class="text-xl font-bold text-gray-900"><?= esc($club['titel']) ?></h3>
                                    <?php if (!empty($club['offers']) && $club['offers'][0]['active']): ?>
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                            Aktiv
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <p class="text-gray-600 mb-4"><?= esc($club['beschreibung_kurz'] ?? 'Keine Beschreibung') ?></p>
                                
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">Zweite Lehrkraft:</span>
                                        <p class="font-semibold text-gray-900"><?= esc($club['zweite_lehrkraft_name'] ?? 'Keine') ?></p>
                                    </div>
                                    
                                    
                                    <div>
                                        <span class="text-gray-500">Jahrgänge:</span>
                                        <p class="font-semibold text-gray-900">
                                            <?= isset($club['min_grade']) && isset($club['max_grade']) 
                                                ? 'Klasse ' . $club['min_grade'] . ' - ' . $club['max_grade'] 
                                                : 'Alle' ?>
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <span class="text-gray-500">Max. Teilnehmer:</span>
                                        <p class="font-semibold text-gray-900"><?= esc($club['max_teilnehmer'] ?? 'Unbegrenzt') ?></p>
                                    </div>
                                    
                                    <?php if (!empty($club['offers'])): ?>
                                    <div>
                                        <span class="text-gray-500">Kapazität:</span>
                                        <p class="font-semibold text-gray-900"><?= $club['offers'][0]['capacity'] ?> Plätze</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex items-center space-x-2 ml-4">
                                <button class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        title="Bearbeiten">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                
                                <button hx-delete="<?= base_url('admin/clubs/' . $club['id']) ?>"
                                        hx-confirm="Möchten Sie diese AG wirklich löschen?"
                                        hx-target="#clubs-list"
                                        hx-swap="innerHTML"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                        title="Löschen">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Neue AG -->
<div x-data="{ open: false }"
     @open-modal.window="open = ($event.detail.modal === 'newClubModal')"
     @close-modal.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 transition-opacity"
         @click="open = false"></div>
    
    <!-- Modal -->
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-8"
             @click.stop>
            
            <!-- Close Button -->
            <button @click="open = false"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            
            <!-- Header -->
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Neue AG erstellen</h2>
            
            <!-- Form -->
            <form hx-post="<?= base_url('admin/clubs/create') ?>"
                  hx-headers='{"X-CSRF-TOKEN": "<?= csrf_hash() ?>"}'
                  hx-target="#clubs-list"
                  hx-swap="innerHTML"
                  @htmx:after-request="open = false"
                  class="space-y-4">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">AG-Titel *</label>
                        <input type="text" 
                               name="titel" 
                               required
                               placeholder="z.B. Fußball, Kunst, Musik"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lehrkraft *</label>
                        <input type="text" 
                               name="lehrkraft" 
                               required
                               placeholder="Name der Lehrkraft"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">2. Lehrkraft (optional)</label>
                        <input type="text" 
                               name="zweite_lehrkraft"
                               placeholder="Name der 2. Lehrkraft"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jahrgänge *</label>
                        <input type="text" 
                               name="jahrgaenge" 
                               required
                               placeholder="z.B. 5,6,7,8,9,10"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kapazität *</label>
                        <input type="number" 
                               name="capacity" 
                               required
                               min="1"
                               placeholder="Anzahl Plätze"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Beschreibung</label>
                        <textarea name="beschreibung" 
                                  rows="3"
                                  placeholder="Kurze Beschreibung der AG"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button"
                            @click="open = false"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Abbrechen
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        AG erstellen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
