<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="fade-in" 
     x-data="{ showModal: false }">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <div class="flex items-center space-x-3">
                <a href="<?= base_url('admin/klassen') ?>" 
                   class="text-gray-600 hover:text-gray-900 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Klasse <?= esc($klasse['name']) ?></h1>
            </div>
            <p class="text-gray-600 mt-2 ml-9">
                Klassenleitung: <?= esc($klasse['klassenleitung']) ?> • Jahrgang <?= esc($klasse['jahrgang']) ?>
            </p>
        </div>
        
        <!-- Schüler hinzufügen Button -->
        <div>
            <p x-text="'showModal Status: ' + showModal" class="text-xs text-gray-500 mb-2"></p>
            <button @click="showModal = true; console.log('Button geklickt, showModal:', showModal)" 
                    class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition flex items-center space-x-2 shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Neuer Schüler</span>
            </button>
        </div>
    </div>

    <!-- Content Container (HTMX Target) -->
    <div id="klasse-content">
        <?= view('admin/partials/klasse_content', ['klasse' => $klasse]) ?>
    </div>

    <!-- Modal: Neuer Schüler -->
    <template x-if="showModal">
    <div @click.self="showModal = false"
         x-init="htmx.process($el)"
         x-data="{ errors: {} }"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all"
         @click.stop>
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900">Neuer Schüler</h3>
            <button @click="showModal = false; errors = {};" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form hx-post="<?= base_url('admin/klassen/' . $klasse['id'] . '/schueler') ?>"
              hx-target="#klasse-content"
              hx-swap="innerHTML"
              @htmx:after-request="if(event.detail.successful) { showModal = false; errors = {}; $el.reset(); }"
              @htmx:response-error="
                  const resp = JSON.parse(event.detail.xhr.response);
                  if (resp.errors) errors = resp.errors;
              "
              class="p-6 space-y-4">
            
            <?= csrf_field() ?>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                <input type="text" 
                       name="name" 
                       required
                       placeholder="z.B. Max Mustermann"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                       :class="{ 'border-red-500': errors.name }">
                <p x-show="errors.name" x-text="errors.name" class="text-red-500 text-sm mt-1"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Typ (G/LE)</label>
                <select name="typ_gl" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        :class="{ 'border-red-500': errors.typ_gl }">
                    <option value="">Bitte wählen...</option>
                    <option value="G">G (Geistige Entwicklung)</option>
                    <option value="LE">LE (Lernen)</option>
                </select>
                <p x-show="errors.typ_gl" x-text="errors.typ_gl" class="text-red-500 text-sm mt-1"></p>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 pt-4">
                <button type="button" 
                        @click="showModal = false; errors = {}; $el.closest('form').reset();"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Abbrechen
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition">
                    Speichern
                </button>
            </div>
        </form>
    </div>
    </div>
    </template>

    <!-- Modal: Schüler bearbeiten (Alpine Store) -->
    <template x-if="$store.editModal.show">
    <div @click.self="$store.editModal.close()"
         x-init="htmx.process($el)"
         x-data="{ errors: {} }"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all"
         @click.stop>
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900">Schüler bearbeiten</h3>
            <button @click="$store.editModal.close(); errors = {};" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form x-init="$nextTick(() => {
                const schuelerId = $store.editModal.schueler?.id;
                if (schuelerId) {
                    const csrfMeta = document.querySelector('meta[name=\'<?= csrf_header() ?>\']');
                    const csrfToken = csrfMeta ? csrfMeta.content : '';
                    $el.setAttribute('hx-put', '<?= base_url('admin/klassen/' . $klasse['id'] . '/schueler/') ?>' + schuelerId);
                    $el.setAttribute('hx-headers', JSON.stringify({'<?= csrf_header() ?>': csrfToken}));
                    htmx.process($el);
                    console.log('Bearbeiten-Form URL:', $el.getAttribute('hx-put'), 'CSRF:', csrfToken);
                }
              })"
              hx-target="#klasse-content"
              hx-swap="innerHTML"
              @htmx:after-request="if(event.detail.successful) { $store.editModal.close(); errors = {}; }"
              @htmx:response-error="
                  const resp = JSON.parse(event.detail.xhr.response);
                  if (resp.errors) errors = resp.errors;
              "
              class="p-6 space-y-4">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                <input type="text" 
                       name="name" 
                       required
                       :value="$store.editModal.schueler?.name"
                       placeholder="z.B. Max Mustermann"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                       :class="{ 'border-red-500': errors.name }">
                <p x-show="errors.name" x-text="errors.name" class="text-red-500 text-sm mt-1"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Typ (G/LE)</label>
                <select name="typ_gl" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        :class="{ 'border-red-500': errors.typ_gl }">
                    <option value="">Bitte wählen...</option>
                    <option value="G" :selected="$store.editModal.schueler?.typ_gl === 'G'">G (Geistige Entwicklung)</option>
                    <option value="LE" :selected="$store.editModal.schueler?.typ_gl === 'LE'">LE (Lernen)</option>
                </select>
                <p x-show="errors.typ_gl" x-text="errors.typ_gl" class="text-red-500 text-sm mt-1"></p>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 pt-4">
                <button type="button" 
                        @click="$store.editModal.close(); errors = {};"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Abbrechen
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Aktualisieren
                </button>
            </div>
        </form>
    </div>
    </div>
    </template>
</div>

<?= $this->endSection() ?>
