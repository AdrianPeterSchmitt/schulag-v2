<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="fade-in">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Klassen verwalten</h1>
            <p class="text-gray-600 mt-2">Klassen anlegen, bearbeiten und löschen</p>
        </div>
        <button @click="$refs.createModal.showModal()" 
                class="px-6 py-3 bg-gradient-to-r from-primary to-secondary text-white rounded-lg hover:shadow-lg transition flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>Neue Klasse</span>
        </button>
    </div>

    <!-- Klassen Liste (HTMX Target) -->
    <div id="klassen-list" class="bg-white rounded-xl shadow-md overflow-hidden">
        <?= view('admin/partials/klassen_list', ['klassen' => $klassen]) ?>
    </div>
</div>

<!-- Create Klasse Modal -->
<dialog x-ref="createModal" class="rounded-xl p-0 backdrop:bg-black/50 max-w-lg w-full">
    <div class="bg-white rounded-xl">
        <div class="bg-gradient-to-r from-primary to-secondary text-white p-6 rounded-t-xl">
            <h3 class="text-xl font-bold">Neue Klasse anlegen</h3>
        </div>
        
        <form hx-post="<?= base_url('admin/klassen/create') ?>"
              hx-target="#klassen-list"
              hx-swap="innerHTML"
              @htmx:after-request="if(event.detail.successful) $refs.createModal.close()"
              class="p-6 space-y-4">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Klassenname *</label>
                <input type="text" 
                       name="name" 
                       required
                       placeholder="z.B. 5a"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jahrgang *</label>
                <input type="number" 
                       name="jahrgang" 
                       required
                       min="1"
                       max="13"
                       placeholder="z.B. 5"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Klassenleitung</label>
                <input type="text" 
                       name="klassenleitung" 
                       placeholder="z.B. Frau Müller"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" 
                        @click="$refs.createModal.close()"
                        class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Abbrechen
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-lg hover:shadow-lg transition">
                    <span class="htmx-indicator">
                        <svg class="animate-spin h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    <span>Anlegen</span>
                </button>
            </div>
        </form>
    </div>
</dialog>

<?= $this->endSection() ?>
