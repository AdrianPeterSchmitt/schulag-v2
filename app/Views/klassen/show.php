<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="fade-in">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Klasse <?= esc($klasse['name']) ?></h1>
            <p class="text-gray-600 mt-2">AG-Wünsche für <?= esc($klasse['klassenleitung']) ?></p>
        </div>
        
        <!-- Completion Status -->
        <div id="completion-status" class="text-right">
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 <?= $isComplete ? 'border-green-500' : 'border-orange-500' ?>">
                <div class="flex items-center space-x-2">
                    <?php if ($isComplete): ?>
                        <span class="text-green-600 text-2xl">✅</span>
                    <?php else: ?>
                        <span class="text-orange-600 text-2xl">⏳</span>
                    <?php endif; ?>
                    <div>
                        <p class="font-semibold text-gray-900">
                            <?= $completedCount ?> / <?= $totalCount ?> Schüler
                        </p>
                        <p class="text-sm text-gray-600">
                            <?= $isComplete ? 'Alle Wahlen vollständig!' : 'Noch nicht alle Wahlen abgegeben' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schüler Liste -->
    <div class="space-y-4">
        <?php foreach ($klasse['schueler'] as $student): ?>
            <?= view('klassen/partials/student_card', [
                'student' => $student,
                'schoolyear' => $schoolyear
            ]) ?>
        <?php endforeach; ?>
    </div>

    <?php if (empty($klasse['schueler'])): ?>
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-md p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Keine Schüler in dieser Klasse</h3>
            <p class="text-gray-500">Bitte fügen Sie Schüler über die Verwaltung hinzu</p>
        </div>
    <?php endif; ?>
</div>

<!-- Success Toast Script -->
<script>
    // Listen for HTMX success events
    document.body.addEventListener('htmx:afterSwap', function(event) {
        // Check if this was a successful choice save
        if (event.detail.xhr.status === 200 && event.detail.pathInfo.requestPath.includes('choices')) {
            // Update completion status
            htmx.ajax('GET', '<?= base_url('klassen/' . $klasse['id'] . '/check') ?>', {
                target: '#completion-status',
                swap: 'innerHTML'
            });
            
            // Show success toast
            showToast('Erfolg', 'AG-Wünsche gespeichert!', '✅');
        }
    });
</script>

<?= $this->endSection() ?>
