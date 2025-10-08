<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="fade-in" x-data="{ showCreateModal: false, showEditModal: false, editKlasse: null }"
     @open-edit-klasse.window="editKlasse = $event.detail; showEditModal = true;">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Klassen verwalten</h1>
            <p class="text-gray-600 mt-2">Klassen anlegen, bearbeiten und löschen</p>
        </div>
        <button @click="showCreateModal = true" 
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

    <!-- Create Klasse Modal -->
    <div x-show="showCreateModal" 
         x-cloak
         @click.self="showCreateModal = false"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
         style="display: none;"
         x-data="{ errors: {} }">
        <div class="bg-white rounded-xl max-w-lg w-full" @click.stop>
            <div class="bg-gradient-to-r from-primary to-secondary text-white p-6 rounded-t-xl">
                <h3 class="text-xl font-bold">Neue Klasse anlegen</h3>
            </div>
            
            <form hx-post="<?= base_url('admin/klassen/create') ?>"
                  hx-target="#klassen-list"
                  hx-swap="innerHTML"
                  @htmx:after-request="if(event.detail.successful) { showCreateModal = false; errors = {}; $el.reset(); }"
                  @htmx:response-error="
                      const resp = JSON.parse(event.detail.xhr.response);
                      if (resp.errors) errors = resp.errors;
                  "
                  class="p-6 space-y-4">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Klassenname *</label>
                    <input type="text" 
                           name="name" 
                           required
                           placeholder="z.B. 5a"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           :class="{ 'border-red-500': errors.name }">
                    <p x-show="errors.name" x-text="errors.name" class="text-red-500 text-sm mt-1"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jahrgang *</label>
                    <input type="number" 
                           name="jahrgang" 
                           required
                           min="1"
                           max="13"
                           placeholder="z.B. 5"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           :class="{ 'border-red-500': errors.jahrgang }">
                    <p x-show="errors.jahrgang" x-text="errors.jahrgang" class="text-red-500 text-sm mt-1"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Klassenleitung *</label>
                    <input type="text" 
                           name="klassenleitung" 
                           required
                           placeholder="z.B. Frau Müller"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           :class="{ 'border-red-500': errors.klassenleitung }">
                    <p x-show="errors.klassenleitung" x-text="errors.klassenleitung" class="text-red-500 text-sm mt-1"></p>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" 
                            @click="showCreateModal = false; errors = {}; $el.closest('form').reset();"
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
    </div>

    <!-- Edit Klasse Modal -->
    <template x-if="showEditModal">
    <div @click.self="showEditModal = false" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
         x-data="{ errors: {} }">
        <div class="bg-white rounded-xl max-w-lg w-full" @click.stop x-init="htmx.process($el)">
            <div class="bg-gradient-to-r from-primary to-secondary text-white p-6 rounded-t-xl">
                <h3 class="text-xl font-bold">Klasse bearbeiten</h3>
            </div>
            <form x-init="$nextTick(() => {
                      if (editKlasse) {
                          const meta = document.querySelector('meta[name=\'<?= csrf_header() ?>\']');
                          const token = meta ? meta.content : '';
                          $el.setAttribute('hx-put', '<?= base_url('admin/klassen/') ?>' + editKlasse.id);
                          $el.setAttribute('hx-headers', JSON.stringify({'<?= csrf_header() ?>': token}));
                          htmx.process($el);
                      }
                  })"
                  hx-target="#klassen-list" hx-swap="innerHTML"
                  @htmx:after-request="if(event.detail.successful) { showEditModal = false; errors = {}; }"
                  @htmx:response-error="
                      const resp = JSON.parse(event.detail.xhr.response);
                      if (resp.errors) errors = resp.errors;
                  "
                  class="p-6 space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Klassenname *</label>
                    <input type="text" name="name" required :value="editKlasse?.name"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           :class="{ 'border-red-500': errors.name }">
                    <p x-show="errors.name" x-text="errors.name" class="text-red-500 text-sm mt-1"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jahrgang *</label>
                    <input type="number" name="jahrgang" required min="1" max="13" :value="editKlasse?.jahrgang"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           :class="{ 'border-red-500': errors.jahrgang }">
                    <p x-show="errors.jahrgang" x-text="errors.jahrgang" class="text-red-500 text-sm mt-1"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Klassenleitung *</label>
                    <input type="text" name="klassenleitung" required :value="editKlasse?.klassenleitung"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           :class="{ 'border-red-500': errors.klassenleitung }">
                    <p x-show="errors.klassenleitung" x-text="errors.klassenleitung" class="text-red-500 text-sm mt-1"></p>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" @click="showEditModal = false; errors = {};"
                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Abbrechen</button>
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-lg hover:shadow-lg transition">
                        <span class="htmx-indicator">
                            <svg class="animate-spin h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                        <span>Speichern</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>
</div>

<?= $this->endSection() ?>
